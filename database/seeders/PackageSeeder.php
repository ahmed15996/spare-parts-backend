<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Enums\BannerType;
use App\Models\Package;

class PackageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $packages = [
            [
                'name'=>[
                    'ar' =>'باقة العرض',
                    'en' =>'basic package'
                ],
                'description'=>[
                    'ar' =>'باقة العرض للمكان الوجود في القسم الرئيسي',
                    'en' =>'basic package for the main section'
                ],
                'banner_type'=>BannerType::Home,
                'price'=>1000,
                'duration'=>1,
            ],[
                'name'=>[
                    'ar' =>'باقة العرض للمكان الوجود في القسم الرئيسي',
                    'en' =>'basic package for the main section'
                ],
                'description'=>[
                    'ar' =>'باقة العرض للمكان الوجود في القسم الشخصي',
                    'en' =>'basic package for the profile section'
                ],
                'banner_type'=>BannerType::Profile,
                'price'=>1000,
                'duration'=>1,
            ],
            [
                'name'=>[
                    'ar' =>'باقة العرض للمكان الوجود في القسم الرئيسي والشخصي',
                    'en' =>'basic package for the main and profile section'
                ],
                'description'=>[
                    'ar' =>'باقة العرض للمكان الوجود في القسم الرئيسي والشخصي',
                    'en' =>'basic package for the main and profile section'
                ],
                'banner_type'=>BannerType::Both,
                'price'=>2000,
                'duration'=>1,
            ]
        ];
        foreach ($packages as $package) {
            Package::create($package);
        }
    }
}
