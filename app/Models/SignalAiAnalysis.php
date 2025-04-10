<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SignalAiAnalysis extends Model
{
    protected $fillable = [
        'signal_id',
        'debris_detected',
        'confidence_score',
        'detected_waste_types',
        'media_analysis_results',
        'matches_reporter_selection',
        'analysis_notes'
    ];

    protected $casts = [
        'debris_detected' => 'boolean',
        'confidence_score' => 'float',
        'detected_waste_types' => 'array',
        'media_analysis_results' => 'array',
        'matches_reporter_selection' => 'boolean'
    ];

    public function signal(): BelongsTo
    {
        return $this->belongsTo(Signal::class);
    }
} 