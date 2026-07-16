<?php

namespace App\Services;

use App\Models\PositiveWord;
use App\Models\NegativeWord;
use Illuminate\Support\Facades\Cache;

class SentimentAnalyzerService
{
    /**
     * Analyze text sentiment using a Lexicon Based Approach.
     * 
     * @param string $text
     * @return array
     */
    public function analyze(string $text): array
    {
        // Get dictionaries (cached for performance)
        $positiveWords = Cache::rememberForever('dict_positive_words', function () {
            return PositiveWord::pluck('word')->toArray();
        });
        
        $negativeWords = Cache::rememberForever('dict_negative_words', function () {
            return NegativeWord::pluck('word')->toArray();
        });

        // Clean and tokenize text
        $text = strtolower($text);
        $text = preg_replace('/[^a-z0-9\s]/', '', $text); // Remove punctuation
        $words = explode(' ', $text);

        $positiveScore = 0;
        $negativeScore = 0;
        $positiveMatches = [];
        $negativeMatches = [];

        foreach ($words as $word) {
            $word = trim($word);
            if (empty($word)) continue;

            if (in_array($word, $positiveWords)) {
                $positiveScore++;
                $positiveMatches[] = $word;
            }
            if (in_array($word, $negativeWords)) {
                $negativeScore++;
                $negativeMatches[] = $word;
            }
        }

        // Calculate total score and percentages
        $totalMatches = $positiveScore + $negativeScore;
        
        // Base neutral score calculation (if no strong sentiment is found, it's neutral)
        // If total matches is 0, it's 100% neutral
        $positivePercentage = 0;
        $negativePercentage = 0;
        $neutralPercentage = 100;

        if ($totalMatches > 0) {
            // We assume that the overall sentence has some non-sentiment words which contribute to neutral
            // But for a simple Lexicon representation:
            $totalWords = count($words);
            
            $positivePercentage = round(($positiveScore / $totalWords) * 100);
            $negativePercentage = round(($negativeScore / $totalWords) * 100);
            $neutralPercentage = 100 - ($positivePercentage + $negativePercentage);
        }

        // Determine final label
        $sentimentLabel = 'Neutral';
        if ($positiveScore > $negativeScore) {
            $sentimentLabel = 'Positive';
        } elseif ($negativeScore > $positiveScore) {
            $sentimentLabel = 'Negative';
        }

        return [
            'sentiment' => $sentimentLabel,
            'scores' => [
                'positive' => $positiveScore,
                'negative' => $negativeScore
            ],
            'percentages' => [
                'positive' => $positivePercentage,
                'neutral' => $neutralPercentage,
                'negative' => $negativePercentage
            ],
            'matches' => [
                'positive' => array_unique($positiveMatches),
                'negative' => array_unique($negativeMatches)
            ]
        ];
    }
    
    /**
     * Analyzes and updates a news record
     */
    public function analyzeNews(\App\Models\News $news)
    {
        $text = $news->title . " " . $news->summary;
        $result = $this->analyze($text);
        
        $news->update([
            'sentiment' => $result['sentiment']
        ]);
        
        return $result;
    }
}
