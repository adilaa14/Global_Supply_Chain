<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Permission;
use App\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Default Permissions
        $permissions = [
            'view dashboard',
            'manage users',
            'manage companies',
            'manage shipments',
            'view intelligence',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // 2. Create Roles
        $adminRole = Role::firstOrCreate(['name' => 'Administrator']);
        $importerRole = Role::firstOrCreate(['name' => 'Importer']);
        $exporterRole = Role::firstOrCreate(['name' => 'Exporter']);

        $adminRole->syncPermissions(Permission::all());
        $importerRole->syncPermissions(['view dashboard', 'manage shipments', 'view intelligence']);
        $exporterRole->syncPermissions(['view dashboard', 'manage shipments', 'view intelligence']);

        // 3. Create Default Company
        $company = Company::firstOrCreate(
            ['email' => 'admin@globalsupply.com'],
            [
                'company_name' => 'Global Supply Chain Master HQ',
                'company_type' => 'Both',
                'status' => 'active',
                'phone' => '+1234567890',
            ]
        );

        $importerCompany = Company::firstOrCreate(
            ['email' => 'importer@acme.com'],
            [
                'company_name' => 'Acme Imports Ltd',
                'company_type' => 'Importer',
                'status' => 'active',
                'phone' => '+0987654321',
            ]
        );

        $exporterCompany = Company::firstOrCreate(
            ['email' => 'exporter@zenith.com'],
            [
                'company_name' => 'Zenith Exports Co',
                'company_type' => 'Exporter',
                'status' => 'active',
                'phone' => '+1122334455',
            ]
        );

        // 4. Create Default Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@globalsupply.com'],
            [
                'name' => 'Super Administrator',
                'password' => Hash::make('password'),
                'company_id' => $company->id,
                'status' => 'active',
            ]
        );
        if (!$admin->hasRole('Administrator')) {
            $admin->assignRole($adminRole);
        }

        // 5. Create Importer User
        $importer = User::firstOrCreate(
            ['email' => 'importer@acme.com'],
            [
                'name' => 'John Importer',
                'password' => Hash::make('password'),
                'company_id' => $importerCompany->id,
                'status' => 'active',
            ]
        );
        if (!$importer->hasRole('Importer')) {
            $importer->assignRole($importerRole);
        }

        // 6. Create Exporter User
        $exporter = User::firstOrCreate(
            ['email' => 'exporter@zenith.com'],
            [
                'name' => 'Jane Exporter',
                'password' => Hash::make('password'),
                'company_id' => $exporterCompany->id,
                'status' => 'active',
            ]
        );
        if (!$exporter->hasRole('Exporter')) {
            $exporter->assignRole($exporterRole);
        }

        // 7. Run additional seeders
        $this->call([
            DashboardCoreSeeder::class,
            CountryIntelligenceSeeder::class, // Run this first so countries exist
            ShipmentSeeder::class,
            VesselSeeder::class,
            CommodityIntelligenceSeeder::class,
            TradeIntelligenceSeeder::class,
        ]);
    }
}
