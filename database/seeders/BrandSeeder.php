<?php

namespace Database\Seeders;

use App\Models\Brand;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BrandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $brands = [
            [
                'name' => ['en' => 'Bmw', 'ar' => 'Bmw'],

            ],
            [
                'name' => ['en' => 'Audi', 'ar' => 'Audi'],
            ],
            [
                'name' => ['en' => 'Mercedes', 'ar' => 'Mercedes'],
            ],
            [
                'name' => ['en' => 'Nissan', 'ar' => 'Nissan'],
            ],
        ];

        $models = [
            'X1',
            '2024',
            '2023',
            '2022',
            'C300',
            'C400',
            'C500',
            '2018',
        ];
        foreach($brands as $brandData){
           $brand = Brand::create($brandData);
           foreach($models as $model){
            $brand->models()->create([
                'name' => $model,
            ]);
           }
        }
    }
}
