<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('category', 'Logistics OR Trade OR Shipping OR Economy');
        $country = $request->query('country', 'us');
        
        $countryName = 'Global';
        if ($country !== 'us') {
            $countryData = \App\Models\Country::where('iso_code', strtoupper($country))->first();
            if ($countryData) {
                $countryName = $countryData->country_name;
            }
        } else {
            $countryName = 'United States';
        }

        // Reduced cache time so news refreshes faster
        $cacheKey = "gnews_rss_" . md5($category . $countryName);

        $news = Cache::remember($cacheKey, 1800, function () use ($category, $countryName) {
            $searchQuery = $category;
            if ($category === 'Logistics OR Trade OR Shipping OR Economy') {
                $searchQuery = '(Logistics OR Trade OR Shipping OR Economy)';
            }
            $searchQuery .= ' location:"' . $countryName . '"';

            $url = 'https://news.google.com/rss/search?q=' . urlencode($searchQuery) . '&hl=en-US&gl=US&ceid=US:en';
            
            $articles = [];
            
            try {
                $response = Http::withOptions(['verify' => false])->timeout(10)->get($url);
                
                if ($response->successful()) {
                    $xml = simplexml_load_string($response->body(), 'SimpleXMLElement', LIBXML_NOCDATA);
                    if ($xml !== false && isset($xml->channel->item)) {
                        
                        // Ultra-reliable Pexels Image Pools
                        $imagesLogistics = [
                            '6169052', '6169033', '2226458', '2800121', '1078973', 
                            '2873486', '2199293', '11115160', '4164844', '4481259', 
                            '12615797', '4481326', '5162464', '5162446', '6169046'
                        ];
                        
                        $imagesEconomy = [
                            '3183150', '3183153', '6802049', '187041', '730564', 
                            '259027', '3760067', '164527', '3184287', '5098061', 
                            '7652033', '4386404', '4386373', '6801874', '210515'
                        ];

                        $imagesGeo = [
                            '355938', '280204', '3033503', '3183173', '3760072', 
                            '708440', '159652', '414301', '714275', '8386423'
                        ];
                        
                        shuffle($imagesLogistics);
                        shuffle($imagesEconomy);
                        shuffle($imagesGeo);

                        $idxLogistics = 0;
                        $idxEconomy = 0;
                        $idxGeo = 0;

                        $count = 0;
                        foreach ($xml->channel->item as $item) {
                            if ($count >= 12) break;
                            
                            $titleParts = explode(' - ', (string)$item->title);
                            $sourceName = count($titleParts) > 1 ? array_pop($titleParts) : 'Google News';
                            $title = implode(' - ', $titleParts);
                            
                            $pubDate = (string)$item->pubDate;
                            $titleLower = strtolower($title);
                            
                            // Robust Image Assignment Logic with Modulo (Circular Array) to prevent exhaustion
                            $assignedImage = '';
                            if (strpos($titleLower, 'economy') !== false || strpos($titleLower, 'trade') !== false || strpos($titleLower, 'bank') !== false || strpos($titleLower, 'market') !== false || strpos($titleLower, 'growth') !== false || strpos($titleLower, 'finance') !== false) {
                                $assignedImage = $imagesEconomy[$idxEconomy % count($imagesEconomy)];
                                $idxEconomy++;
                            } elseif (strpos($titleLower, 'war') !== false || strpos($titleLower, 'election') !== false || strpos($titleLower, 'minister') !== false || strpos($titleLower, 'government') !== false || strpos($titleLower, 'sanction') !== false || strpos($titleLower, 'politics') !== false) {
                                $assignedImage = $imagesGeo[$idxGeo % count($imagesGeo)];
                                $idxGeo++;
                            } else {
                                $assignedImage = $imagesLogistics[$idxLogistics % count($imagesLogistics)];
                                $idxLogistics++;
                            }
                            
                            $articles[] = [
                                'title' => $title,
                                'description' => strip_tags((string)$item->description) ?: 'Click to read full article...',
                                'url' => (string)$item->link,
                                'image' => "https://images.pexels.com/photos/{$assignedImage}/pexels-photo-{$assignedImage}.jpeg?auto=compress&cs=tinysrgb&w=800",
                                'publishedAt' => date('c', strtotime($pubDate)),
                                'source' => ['name' => $sourceName]
                            ];
                            $count++;
                        }
                    }
                }
            } catch (\Exception $e) {
                // Return empty if fails
            }

            return ['articles' => $articles];
        });

        return response()->json($news);
    }
}
