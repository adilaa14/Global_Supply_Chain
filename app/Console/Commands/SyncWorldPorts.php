<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Port;
use App\Models\Country;
use Illuminate\Support\Str;

class SyncWorldPorts extends Command
{
    protected $signature = 'app:sync-world-ports';
    protected $description = 'Syncs all world ports from the external JSON to the database';

    public function handle()
    {
        $this->info('Fetching world ports...');
        
        try {
            $response = Http::withOptions(['verify' => false])->get('https://raw.githubusercontent.com/tayljordan/ports/master/ports.json');
            if (!$response->successful()) {
                $this->error('Failed to fetch ports.json');
                return Command::FAILURE;
            }
            
            $worldPorts = $response->json()['ports'] ?? [];
            $this->info('Found ' . count($worldPorts) . ' ports. Syncing...');

            $bar = $this->output->createProgressBar(count($worldPorts));
            $bar->start();

            // Cache countries to avoid excessive DB queries
            $countries = Country::all()->keyBy(function($c) {
                return strtolower(trim($c->country_name));
            });

            foreach ($worldPorts as $wp) {
                if (!isset($wp['latitude']) || !isset($wp['longitude']) || !isset($wp['point_of_interest']) || !isset($wp['country'])) {
                    $bar->advance();
                    continue;
                }

                $countryName = trim($wp['country']);
                $countryKey = strtolower($countryName);

                if (!isset($countries[$countryKey])) {
                    $isoCode = Str::upper(Str::substr($countryName, 0, 3));
                    if (Country::where('iso_code', $isoCode)->exists()) {
                        $isoCode = strtoupper(substr(md5($countryName), 0, 3));
                        if (Country::where('iso_code', $isoCode)->exists()) {
                            $isoCode = strtoupper(substr(uniqid(), -3));
                        }
                    }
                    
                    $country = Country::create([
                        'country_name' => $countryName,
                        'iso_code' => $isoCode
                    ]);
                    $countries[$countryKey] = $country;
                }
                
                $countryId = $countries[$countryKey]->id;
                
                // Generate a pseudo port code
                $portName = trim($wp['point_of_interest']);
                $portCode = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $portName), 0, 5)) . '-' . rand(100, 999);

                // Use updateOrCreate on lat/lng to prevent duplicates if names change slightly
                Port::firstOrCreate(
                    [
                        'latitude' => $wp['latitude'],
                        'longitude' => $wp['longitude']
                    ],
                    [
                        'port_code' => $portCode,
                        'port_name' => $portName,
                        'country_id' => $countryId
                    ]
                );

                $bar->advance();
            }

            $bar->finish();
            $this->newLine();
            $this->info('Successfully synced ' . Port::count() . ' ports to the database!');
            
        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
