<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$country = \App\Models\Country::first();
echo "Country ID: " . $country->id . "\n";
$newsCount = \App\Models\News::where('country_id', $country->id)->count();
echo "News Count: " . $newsCount . "\n";
$newsWithSentiment = \App\Models\News::where('country_id', $country->id)->whereNotNull('sentiment')->count();
echo "News with sentiment: " . $newsWithSentiment . "\n";

$recentNews = \App\Models\News::where('country_id', $country->id)->whereNotNull('sentiment')->latest('published_at')->take(10)->get();
echo "Recent news count fetched by controller: " . count($recentNews) . "\n";

foreach ($recentNews as $news) {
    echo "News ID: {$news->id} | Sentiment: {$news->sentiment}\n";
}
