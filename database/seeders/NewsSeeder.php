<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\News;
use App\Models\Country;
use Faker\Factory as Faker;

class NewsSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();
        $countries = Country::has('economy')->get();

        if ($countries->isEmpty()) {
            return;
        }

        $categories = ['Logistics', 'Trade', 'Shipping', 'Economy'];
        $sentiments = ['Positive', 'Neutral', 'Negative'];
        $sources = ['Global Trade Review', 'Supply Chain Dive', 'Reuters', 'Bloomberg', 'Maritime Executive', 'Lloyd\'s List'];

        $headlines = [
            'Port congestion expected to ease in upcoming quarter.',
            'New trade agreement boosts export opportunities.',
            'Inflation impacts local manufacturing output.',
            'Supply chain disruptions cause delays in electronics shipment.',
            'Government announces new import tariffs on luxury goods.',
            'Major shipping line adds new routes to global network.',
            'Economic growth forecast revised upwards by World Bank.',
            'Labor strike at major port threatens logistics operations.',
            'Investments in port infrastructure to double next year.',
            'Fuel price volatility impacts shipping costs.'
        ];

        for ($i = 0; $i < 40; $i++) {
            $country = $countries->random();
            $sentiment = $faker->randomElement($sentiments);
            
            News::create([
                'title' => $faker->randomElement($headlines) . ' - ' . $country->country_name,
                'source' => $faker->randomElement($sources),
                'category' => $faker->randomElement($categories),
                'summary' => $faker->paragraph(3),
                'sentiment' => $sentiment,
                'published_at' => $faker->dateTimeBetween('-3 months', 'now'),
                'country_id' => $country->id,
            ]);
        }
    }
}
