<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@ajira.go.tz'],
            [
                'name' => 'System Admin',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'employer@example.com'],
            [
                'name' => 'Demo Employer',
                'company_name' => 'Zanzibar Tech Solutions',
                'password' => Hash::make('password'),
                'role' => 'employer',
                'is_active' => true,
            ]
        );

        User::firstOrCreate(
            ['email' => 'jobseeker@example.com'],
            [
                'name' => 'Demo Jobseeker',
                'password' => Hash::make('password'),
                'role' => 'jobseeker',
                'is_active' => true,
            ]
        );
    }
}
