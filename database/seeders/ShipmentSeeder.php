<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shipment;
use App\Models\ShipmentContainer;
use App\Models\Company;
use App\Models\Country;
use App\Models\Port;
use App\Models\Commodity;
use App\Models\CommodityCategory;

class ShipmentSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();
        
        $country = Country::firstOrCreate(
            ['iso_code' => 'USA'],
            ['country_name' => 'United States']
        );
        
        $port = Port::firstOrCreate(
            ['port_code' => 'USLAX'],
            ['country_id' => $country->id, 'port_name' => 'Los Angeles Port']
        );
        
        $category = CommodityCategory::firstOrCreate(
            ['name' => 'Electronics']
        );
        
        $commodity = Commodity::firstOrCreate(
            ['commodity_code' => 'ELEC01'],
            ['category_id' => $category->id, 'commodity_name' => 'Laptops']
        );
        
        foreach ($companies as $company) {
            for ($i = 1; $i <= 5; $i++) {
                $shipment = Shipment::create([
                    'company_id' => $company->id,
                    'shipment_number' => 'SHP-2026-' . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT),
                    'shipment_type' => rand(0, 1) ? 'Export' : 'Import',
                    'commodity_id' => $commodity->id,
                    'origin_country_id' => $country->id,
                    'origin_port_id' => $port->id,
                    'destination_country_id' => $country->id,
                    'destination_port_id' => $port->id,
                    'quantity' => 100,
                    'status' => rand(0, 1) ? 'In Transit' : 'Preparing',
                    'estimated_arrival' => now()->addDays(rand(10, 30)),
                ]);

                ShipmentContainer::create([
                    'shipment_id' => $shipment->id,
                    'container_number' => 'MSCU' . rand(1000000, 9999999),
                    'container_type' => 'Standard',
                    'container_size' => '40ft',
                ]);
            }
        }
    }
}
