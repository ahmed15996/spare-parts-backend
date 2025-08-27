<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        $this->call([
            CitySeeder::class,
            CategorySeeder::class,
            PageSeeder::class,
            RoleSeeder::class,
            UserTableSeeder::class,
            BrandSeeder::class,
            PackageSeeder::class,
            DaySeeder::class,
        ]);


    }
}
