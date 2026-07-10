<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DashboardMetric;
use App\Models\GlobalAlert;
use App\Models\Company;

class DashboardCoreSeeder extends Seeder
{
    public function run(): void
    {
        $companies = Company::all();

        foreach ($companies as $company) {
            DashboardMetric::updateOrCreate(
                ['company_id' => $company->id, 'metric_key' => 'total_shipments'],
                ['numeric_value' => rand(1000, 15000), 'calculated_at' => now()]
            );

            DashboardMetric::updateOrCreate(
                ['company_id' => $company->id, 'metric_key' => 'active_shipments'],
                ['numeric_value' => rand(500, 8500), 'calculated_at' => now()]
            );

            DashboardMetric::updateOrCreate(
                ['company_id' => $company->id, 'metric_key' => 'delayed_shipments'],
                ['numeric_value' => rand(10, 200), 'calculated_at' => now()]
            );

            DashboardMetric::updateOrCreate(
                ['company_id' => $company->id, 'metric_key' => 'revenue_mtd'],
                ['numeric_value' => rand(1000000, 5000000), 'calculated_at' => now()]
            );
        }

        GlobalAlert::updateOrCreate(
            ['title' => 'Typhoon Warning in Pacific'],
            [
                'category' => 'Weather Alert',
                'severity' => 'Critical',
                'message' => 'Port of Shanghai (CNSHG) - Operations suspended for 48 hours.',
                'impact_score' => 85,
                'is_active' => true,
            ]
        );

        GlobalAlert::updateOrCreate(
            ['title' => 'Port Congestion'],
            [
                'category' => 'Operations Alert',
                'severity' => 'Medium',
                'message' => 'Port of Los Angeles (USLAX) - 4 days average delay.',
                'impact_score' => 45,
                'is_active' => true,
            ]
        );
    }
}
