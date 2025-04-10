<?php

namespace App\Services;

use App\Models\Signal;
use App\Models\SignalAiAnalysis;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiAnalysisService
{
    protected $apiUrl;
    protected $confidenceThreshold;

    public function __construct()
    {
        $this->apiUrl = config('services.ai.api_url', 'http://localhost:5000');
        $this->confidenceThreshold = config('services.ai.confidence_threshold', 0.25);
    }

    public function analyzeSignal(Signal $signal)
    {
        try {
            Log::info('Starting AI analysis for signal', [
                'signal_id' => $signal->id,
                'api_url' => $this->apiUrl
            ]);
            
            // Get media files from the signal
            $mediaFiles = $signal->media;
            if ($mediaFiles->isEmpty()) {
                Log::warning('No media files found for signal', ['signal_id' => $signal->id]);
                return null;
            }

            $results = [];
            foreach ($mediaFiles as $media) {
                // Get the media file path
                $filePath = storage_path('app/public/' . $media->file_path);
                
                Log::info('Attempting AI analysis for media:', [
                    'signal_id' => $signal->id,
                    'media_id' => $media->id,
                    'file_path' => $filePath,
                    'exists' => file_exists($filePath)
                ]);
                
                if (!file_exists($filePath)) {
                    Log::error('Media file not found:', ['path' => $filePath]);
                    continue;
                }
                
                // Log connection attempt details
                $wasteTypeNames = $signal->getWasteTypeNames();
                Log::info('Attempting to connect to AI API:', [
                    'endpoint' => $this->apiUrl . '/detect',
                    'confidence' => $this->confidenceThreshold,
                    'reported_types' => $wasteTypeNames
                ]);

                // Send the image to the AI API
                try {
                    $response = Http::timeout(30)->attach(
                        'file', 
                        file_get_contents($filePath), 
                        basename($filePath)
                    )->post($this->apiUrl . '/detect', [
                        'confidence' => $this->confidenceThreshold,
                        'reported_types' => json_encode($wasteTypeNames)
                    ]);
                    
                    Log::info('API response received', [
                        'status' => $response->status(),
                        'successful' => $response->successful(),
                        'body_length' => strlen($response->body())
                    ]);

                    if ($response->successful()) {
                        $result = $response->json();
                        $results[] = [
                            'media_id' => $media->id,
                            'detections' => $result['detections'] ?? [],
                            'validation_results' => $result['validation_results'] ?? [],
                            'annotated_image' => $result['image'] ?? null,
                            'process_time' => $result['process_time'] ?? 0
                        ];
                        
                        Log::info('AI analysis successful:', [
                            'signal_id' => $signal->id,
                            'media_id' => $media->id,
                            'detections' => $result['detections'] ?? []
                        ]);
                    } else {
                        Log::error('AI API error', [
                            'signal_id' => $signal->id,
                            'media_id' => $media->id,
                            'error' => $response->body(),
                            'status' => $response->status()
                        ]);
                    }
                } catch (\Exception $httpError) {
                    Log::error('HTTP request to AI API failed', [
                        'signal_id' => $signal->id,
                        'media_id' => $media->id,
                        'error' => $httpError->getMessage(),
                        'trace' => $httpError->getTraceAsString()
                    ]);
                }
            }

            // Process results and create AI analysis
            if (!empty($results)) {
                $analysis = $this->createAiAnalysis($signal, $results);
                Log::info('AI analysis record created', [
                    'signal_id' => $signal->id,
                    'analysis_id' => $analysis->id
                ]);
                return $analysis;
            } else {
                Log::warning('No results to save for AI analysis', ['signal_id' => $signal->id]);
            }

            return null;
        } catch (\Exception $e) {
            Log::error('Error analyzing signal', [
                'signal_id' => $signal->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    protected function createAiAnalysis(Signal $signal, array $results)
    {
        // Combine results from all media files
        $allDetections = [];
        $mediaResults = [];
        $totalConfidence = 0;
        $detectionCount = 0;

        foreach ($results as $result) {
            $mediaResults['media_' . $result['media_id']] = [
                'detections' => $result['detections'],
                'validation_results' => $result['validation_results'],
                'annotated_image' => $result['annotated_image'],
                'process_time' => $result['process_time']
            ];
            
            foreach ($result['detections'] as $detection) {
                $allDetections[] = $detection;
                $totalConfidence += $detection['confidence'];
                $detectionCount++;
            }
        }

        // Calculate average confidence
        $averageConfidence = $detectionCount > 0 ? $totalConfidence / $detectionCount : 0;

        // Check if any debris was detected
        $debrisDetected = !empty($allDetections);

        // Validate against reported waste types
        $validationResults = $this->validateWasteTypes($allDetections, $signal->getWasteTypeNames());

        // Create or update the AI analysis
        return SignalAiAnalysis::updateOrCreate(
            ['signal_id' => $signal->id],
            [
                'debris_detected' => $debrisDetected,
                'confidence_score' => $averageConfidence,
                'detected_waste_types' => $this->formatDetectedTypes($allDetections),
                'media_analysis_results' => $mediaResults,
                'matches_reporter_selection' => $validationResults['is_valid'],
                'analysis_notes' => $this->generateAnalysisNotes($validationResults)
            ]
        );
    }

    protected function validateWasteTypes(array $detections, array $reportedTypes)
    {
        // Get detected types above confidence threshold
        $detectedTypes = collect($detections)
            ->where('confidence', '>=', $this->confidenceThreshold)
            ->pluck('class')
            ->unique()
            ->values()
            ->toArray();
            
        // Define mappings between generic AI categories and specific waste types
        // Now updated to match the English names in the seeder
        $mappings = [
            'Plastic' => [
                'Plastic',  // General category
                'Plastic Bottles',
                'Food Packaging',
                'Plastic Bags and Sachets',
                'Microplastics'
            ],
            'Metal' => [
                'Metal',  // General category
                'Cans and Metal Containers',
                'Metal Fragments',
                'Construction Materials'
            ],
            'Glass' => [
                'Glass',  // General category
                'Glass Bottles',
                'Broken Glass'
            ],
            'Wood' => [
                'Wood',  // General category
                'Treated Wood',
                'Natural Wood',
                'Wooden Objects'
            ],
            'Fishing' => [
                'Fishing Equipment',  // General category
                'Fishing Nets',
                'Ropes and Fishing Lines',
                'Hooks and Weights'
            ]
        ];
        
        // Find reported types that should match with detected generic categories
        $expandedMatches = [];
        foreach ($detectedTypes as $aiType) {
            if (isset($mappings[$aiType])) {
                // For each detected generic type, find matches in reported types
                $matchingSpecificTypes = array_intersect($mappings[$aiType], $reportedTypes);
                if (!empty($matchingSpecificTypes)) {
                    $expandedMatches = array_merge($expandedMatches, $matchingSpecificTypes);
                }
            }
        }
        
        // Direct matches (exact same name)
        $directMatches = array_intersect($detectedTypes, $reportedTypes);
        
        // Combine all matches
        $allMatches = array_unique(array_merge($directMatches, $expandedMatches));
        
        // Find missing and extra types
        $missingTypes = array_diff($reportedTypes, $allMatches);
        
        // Generate the extra types with their generic category mapping
        $extraTypes = [];
        foreach ($detectedTypes as $aiType) {
            // Only add if this AI type doesn't have any matching specific types
            $hasMatchingSpecific = false;
            if (isset($mappings[$aiType])) {
                $matchingSpecificTypes = array_intersect($mappings[$aiType], $reportedTypes);
                $hasMatchingSpecific = !empty($matchingSpecificTypes);
            }
            
            // If no specific match and not a direct match, consider it "extra"
            if (!$hasMatchingSpecific && !in_array($aiType, $reportedTypes)) {
                $extraTypes[] = $aiType;
            }
        }
        
        return [
            'matches' => $allMatches,
            'missing_types' => $missingTypes,
            'extra_types' => $extraTypes,
            'is_valid' => !empty($allMatches) && empty($missingTypes)
        ];
    }

    protected function formatDetectedTypes(array $detections)
    {
        $formatted = [];
        foreach ($detections as $detection) {
            $class = $detection['class'];
            $confidence = $detection['confidence'];
            
            if (!isset($formatted[$class]) || $formatted[$class] < $confidence) {
                $formatted[$class] = $confidence;
            }
        }
        return $formatted;
    }

    protected function generateAnalysisNotes(array $validationResults)
    {
        $notes = [];
        
        if (!empty($validationResults['matches'])) {
            $notes[] = "Matched waste types: " . implode(', ', $validationResults['matches']);
        }
        
        if (!empty($validationResults['missing_types'])) {
            $notes[] = "Missing waste types: " . implode(', ', $validationResults['missing_types']);
        }
        
        if (!empty($validationResults['extra_types'])) {
            $notes[] = "Additional waste types detected: " . implode(', ', $validationResults['extra_types']);
        }
        
        return implode("\n", $notes);
    }
} 