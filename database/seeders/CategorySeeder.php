<?php

namespace Database\Seeders;

use App\Models\Category;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $categories = [

            [
                'name'=>[
                    'ar'=>'الكل',
                    'en'=>'All'
                ]
            ],
                [
                    'name'=>[
                        'ar'=>'تشاليح',
                        'en'=>'Garage'
                    ]
                ],
                    [
                        'name'=>[
                            'ar'=>'قطع غيار',
                            'en'=>'Spare Parts'
                        ]
                ],
                        
         ];


         foreach ($categories as $categoryData) {
          $category = Category::create($categoryData);
          
          // Skip media upload for now - you can add icons later manually
          // or use a local image file if needed
         }
    }
}
