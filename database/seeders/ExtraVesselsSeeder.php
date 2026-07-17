<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Vessel;

class ExtraVesselsSeeder extends Seeder
{
    public function run(): void
    {
        $vessels = [
            ['name' => 'MAERSK MC-KINNEY MOLLER', 'type' => 'Container', 'capacity_teu' => 18270, 'mmsi' => '219018271', 'imo_number' => '9619907'],
            ['name' => 'OOCL HONG KONG', 'type' => 'Container', 'capacity_teu' => 21413, 'mmsi' => '477039600', 'imo_number' => '9776171'],
            ['name' => 'COSCO UNIVERSE', 'type' => 'Container', 'capacity_teu' => 21237, 'mmsi' => '477156900', 'imo_number' => '9795610'],
            ['name' => 'HAPAG-LLOYD COLOMBO EXPRESS', 'type' => 'Container', 'capacity_teu' => 8749, 'mmsi' => '211433000', 'imo_number' => '9295244'],
            ['name' => 'MSC OSCAR', 'type' => 'Container', 'capacity_teu' => 19224, 'mmsi' => '352777000', 'imo_number' => '9703291'],
            ['name' => 'MADRID MAERSK', 'type' => 'Container', 'capacity_teu' => 20568, 'mmsi' => '219836000', 'imo_number' => '9778791'],
            ['name' => 'EVER ACE', 'type' => 'Container', 'capacity_teu' => 23992, 'mmsi' => '352986146', 'imo_number' => '9890812'],
            ['name' => 'CMA CGM JACQUES SAADE', 'type' => 'Container', 'capacity_teu' => 23112, 'mmsi' => '228389300', 'imo_number' => '9839179'],
            ['name' => 'HMM ROTTERDAM', 'type' => 'Container', 'capacity_teu' => 23964, 'mmsi' => '440332000', 'imo_number' => '9863302'],
            ['name' => 'ONE TRUST', 'type' => 'Container', 'capacity_teu' => 20170, 'mmsi' => '372223000', 'imo_number' => '9777195'],
            ['name' => 'YANG MING WONDER', 'type' => 'Container', 'capacity_teu' => 14000, 'mmsi' => '374823000', 'imo_number' => '9704623'],
            ['name' => 'ZIM SAMMY OFER', 'type' => 'Container', 'capacity_teu' => 15000, 'mmsi' => '413289000', 'imo_number' => '9940186'],
            ['name' => 'HYUNDAI DREAM', 'type' => 'Container', 'capacity_teu' => 13154, 'mmsi' => '440156000', 'imo_number' => '9637258'],
            ['name' => 'APL RAFFLES', 'type' => 'Container', 'capacity_teu' => 13892, 'mmsi' => '566896000', 'imo_number' => '9632002'],
            ['name' => 'MOL TRIUMPH', 'type' => 'Container', 'capacity_teu' => 20170, 'mmsi' => '538007360', 'imo_number' => '9769271'],
        ];

        foreach ($vessels as $v) {
            Vessel::firstOrCreate(
                ['name' => $v['name']],
                [
                    'vessel_type' => $v['type'],
                    'deadweight_tonnage' => $v['capacity_teu'] * 14, // Rough conversion
                    'mmsi' => $v['mmsi'],
                    'imo_number' => $v['imo_number'],
                    'status' => 'Active',
                    'build_year' => rand(2010, 2023),
                    'length' => rand(300, 400),
                    'beam' => rand(45, 60),
                ]
            );
        }
    }
}
