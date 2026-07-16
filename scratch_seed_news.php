<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\News;
use App\Models\Country;
use App\Services\SentimentAnalyzerService;
use Carbon\Carbon;

$analyzer = app(SentimentAnalyzerService::class);

$newsData = [
    // Negative News
    ['title' => 'Global inflation rises sharply', 'summary' => 'The current inflation decreases consumer spending and delays shipping globally.', 'category' => 'Economy'],
    ['title' => 'War disrupts trade', 'summary' => 'Ongoing war causes a severe crisis and disaster for global wheat supply chains.', 'category' => 'Geopolitics'],
    ['title' => 'Port strikes cause massive delay', 'summary' => 'Workers strike at major ports, leading to delay and decrease in overall productivity.', 'category' => 'Logistics'],
    
    // Positive News
    ['title' => 'Shipping profits surge in Q4', 'summary' => 'Excellent growth and stable conditions lead to a massive increase in profit.', 'category' => 'Economy'],
    ['title' => 'New trade agreements improve relations', 'summary' => 'The new treaties will improve growth and ensure stable success for international trade.', 'category' => 'Geopolitics'],
    ['title' => 'Supply chain stability returns', 'summary' => 'Conditions are stable and we see good increase in efficiency across all ports.', 'category' => 'Logistics'],
    
    // Mixed / Neutral
    ['title' => 'Container rates remain unchanged', 'summary' => 'The rates are stable, but some minor delay was observed due to weather.', 'category' => 'Logistics'],
    ['title' => 'New vessels added to fleet', 'summary' => 'Shipping companies purchase new vessels to replace old ones.', 'category' => 'Logistics'],
];

// Clean old news
News::truncate();

$countries = Country::all();
$seeded = 0;

foreach ($countries as $country) {
    // Pick 3-5 random news for each country
    $count = rand(3, 5);
    for ($i = 0; $i < $count; $i++) {
        $data = $newsData[array_rand($newsData)];
        $news = News::create([
            'title' => $data['title'],
            'summary' => $data['summary'],
            'category' => $data['category'],
            'country_id' => $country->id,
            'published_at' => Carbon::now()->subHours(rand(1, 48)),
            'source' => 'Global Logistics Times',
        ]);
        $analyzer->analyzeNews($news);
        $seeded++;
    }
}

echo "Successfully seeded and analyzed {$seeded} news articles for " . $countries->count() . " countries!\n";
