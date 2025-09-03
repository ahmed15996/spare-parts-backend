<?php

namespace Database\Seeders;

use App\Models\Day;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $days = [
            [
                'name' => [
                    'ar' => 'السبت',
                    'en' => 'Saturday',
                ],
            ],
            [
                'name' => [
                    'ar' => 'الأحد',
                    'en' => 'Sunday',
                ],
            ],[
                'name' => [
                    'ar' => 'الاثنين',
                    'en' => 'Monday',
                ],
            ],
            [
                'name' => [
                    'ar' => 'الثلاثاء',
                    'en' => 'Tuesday',
                ],
            ],
            [
                'name' => [
                    'ar' => 'الأربعاء',
                    'en' => 'Wednesday',
                ],
            ],
            [
                'name' => [
                    'ar' => 'الخميس',
                    'en' => 'Thursday',
                ],
            ],
            [
                'name' => [
                    'ar' => 'الجمعة',
                    'en' => 'Friday',
                ],
            ]
        ];
        foreach ($days as $dayData) {
            Day::create($dayData);
        }
    }
}
