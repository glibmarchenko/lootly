<?php

use App\Models\Language;
use Illuminate\Database\Seeder;

class LanguageTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $langauge_arr = [
            [
                'name' => 'English',
            ],
            // [
            //     'name' => 'Spanish',
            // ],
            // [
            //     'name' => 'Italian',
            // ],
            // [
            //     'name' => 'French',
            // ],
            // [
            //     'name' => 'Armenian',
            // ]
        ];
        Language::query()->delete();
        Language::query()->insert($langauge_arr);
    }
}
