<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Station; // ✅ Make sure this is uncommented now!
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create the Super Admin
        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@bfp.gov.ph',
            'password' => Hash::make('password123'),
            'role' => 'super_admin',
        ]);

        // 2. ✅ CREATE THE MAIN STATION (ID 1)
        // This will always be the first one, so it gets ID=1
        Station::create([
            'name' => 'Main Office / Headquarters',
            'location' => 'Zamboanga City', // You can change this text
        ]);
        
        // 3. (Optional) Create a Station Admin if needed
        // ...
    }
}