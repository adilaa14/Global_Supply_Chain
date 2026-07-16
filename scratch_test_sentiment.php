<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Seed Dictionary
$positiveWords = ['growth', 'increase', 'increases', 'profit', 'stable', 'improve', 'good', 'surge', 'surges', 'excellent', 'success'];
$negativeWords = ['war', 'crisis', 'inflation', 'delay', 'disaster', 'decrease', 'decreases', 'bad', 'loss', 'decline', 'declines'];

foreach ($positiveWords as $word) {
    \App\Models\PositiveWord::firstOrCreate(['word' => $word]);
}
foreach ($negativeWords as $word) {
    \App\Models\NegativeWord::firstOrCreate(['word' => $word]);
}
\Illuminate\Support\Facades\Cache::forget('dict_positive_words');
\Illuminate\Support\Facades\Cache::forget('dict_negative_words');

// 2. Test the specific phrase from the user
$analyzer = app(\App\Services\SentimentAnalyzerService::class);
$sentence = "Inflation increases while exports decrease due to war.";

echo "=== SENTIMENT ANALYSIS TEST ===\n";
echo "Text: \"$sentence\"\n";
$result = $analyzer->analyze($sentence);

echo "\nResult:\n";
echo "Positive Score: " . $result['scores']['positive'] . " (" . implode(', ', $result['matches']['positive']) . ")\n";
echo "Negative Score: " . $result['scores']['negative'] . " (" . implode(', ', $result['matches']['negative']) . ")\n";
echo "Sentiment: " . $result['sentiment'] . "\n";
echo "Positive Percentage: " . $result['percentages']['positive'] . "%\n";
echo "Neutral Percentage: " . $result['percentages']['neutral'] . "%\n";
echo "Negative Percentage: " . $result['percentages']['negative'] . "%\n";
