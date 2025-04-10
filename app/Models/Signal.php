<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Services\SignalService;
use App\Models\WasteTypes;
use App\Services\AiAnalysisService;

class Signal extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'created_by',
        'volume',
        'waste_types',
        'location',
        'custom_type',
        'latitude',
        'longitude',
        'anomaly_flag',
        'signal_date',
        'status',
        'description',
        'viewed'
    ];

    protected $casts = [
        'anomaly_flag' => 'boolean',
        'signal_date' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'volume' => 'integer',
        'waste_types' => 'array',
        'viewed' => 'boolean'
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function wasteTypes()
    {
        return $this->belongsToMany(WasteTypes::class, 'signal_waste_types', 'signal_id', 'waste_type_id');
    }

    public function media()
    {
        return $this->hasMany(Media::class);
    }

    public function aiAnalysis()
    {
        return $this->hasOne(SignalAiAnalysis::class);
    }

    public function collectes()
    {
        // Don't use belongsToMany since we're using a JSON column
        return $this->hasMany(Collecte::class, 'signal_ids')
            ->where(function ($query) {
                $query->whereRaw('JSON_CONTAINS(signal_ids, ?)', [$this->id]);
            });
    }

    public function waste_types()
    {
        return $this->belongsToMany(WasteTypes::class, 'signal_waste_types', 'signal_id', 'waste_type_id');
    }

    /**
     * Calculate distance between two points in kilometers using Haversine formula
     */
    public static function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // Earth's radius in kilometers

        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);
            
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        
        return $earthRadius * $c;
    }

    public function getWasteTypeNames()
    {
        // The waste_types property is an array, not a collection, so we can't use pluck()
        // Instead, we need to use the relationship to get the waste type names
        return $this->waste_types()->pluck('name')->toArray();
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($signal) {
            $signalService = app(SignalService::class);
            $signal->status = $signalService->determineSignalStatus($signal, $signal->creator);
        });

        static::created(function ($signal) {
            try {
                // Ensure we have the latest media relationship
                $signal->refresh();
                $signal->load('media');
                
                // Trigger AI analysis when media is added during creation
                if ($signal->media->isNotEmpty()) {
                    \Illuminate\Support\Facades\Log::info('Triggering AI analysis after signal creation', [
                        'signal_id' => $signal->id,
                        'media_count' => $signal->media->count()
                    ]);
                    
                    $aiService = app(\App\Services\AiAnalysisService::class);
                    $aiService->analyzeSignal($signal);
                } else {
                    \Illuminate\Support\Facades\Log::info('No media found after signal creation, skipping AI analysis', [
                        'signal_id' => $signal->id
                    ]);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error triggering AI analysis after signal creation', [
                    'signal_id' => $signal->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        });

        static::saved(function ($signal) {
            try {
                if ($signal->isDirty('anomaly_flag') && $signal->anomaly_flag) {
                    $signalService = app(SignalService::class);
                    $signalService->handleAnomalyDetection($signal);
                }
                
                // Reload the model to get the latest relationships
                $signal->refresh();
                $signal->load(['media', 'aiAnalysis']);
                
                // Check if we need to run AI analysis
                $shouldRunAnalysis = $signal->media->isNotEmpty() && 
                                     (!$signal->aiAnalysis || 
                                     ($signal->wasChanged('waste_types') && $signal->aiAnalysis));
                
                if ($shouldRunAnalysis) {
                    \Illuminate\Support\Facades\Log::info('Triggering AI analysis after signal update', [
                        'signal_id' => $signal->id,
                        'media_count' => $signal->media->count(),
                        'has_analysis' => (bool)$signal->aiAnalysis
                    ]);
                    
                    $aiService = app(\App\Services\AiAnalysisService::class);
                    $aiService->analyzeSignal($signal);
                }
            } catch (\Exception $e) {
                \Illuminate\Support\Facades\Log::error('Error in signal saved event', [
                    'signal_id' => $signal->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
        });
    }
    
    /**
     * Manually trigger AI analysis after media has been attached
     * This should be called from the controller after all media is saved
     */
    public function triggerAiAnalysisAfterMediaAttached()
    {
        try {
            // Reload with fresh media
            $this->refresh();
            $this->load(['media', 'aiAnalysis']);
            
            if ($this->media->isNotEmpty() && !$this->aiAnalysis) {
                \Illuminate\Support\Facades\Log::info('Manually triggering AI analysis after media attachment', [
                    'signal_id' => $this->id,
                    'media_count' => $this->media->count()
                ]);
                
                $aiService = app(\App\Services\AiAnalysisService::class);
                $aiService->analyzeSignal($this);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error in manual AI analysis trigger', [
                'signal_id' => $this->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
        }
    }
}
