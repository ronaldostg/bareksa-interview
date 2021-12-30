<?php

namespace Database\Seeders;

use App\Models\News;
use App\Models\Tags;
use App\Models\Topic;
use Illuminate\Database\Seeder;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();

        News::create([
            'title'=>'Investasi minim risiko ? solusinya beli reksadana',
            'tags'=>'["investasi murah", "minim risiko","mutual funds"]',
            'news_text'=>'Investasi minim risiko ? solusinya beli reksadana',
            
            'status'=>'draft',

        ]);

        News::create([
            'title'=>'Jangan mau tertipu dengan titip dana',
            'tags'=>'["hati-hati", "titipdana", "penipuan"]',
            'news_text'=>'Jika anda ingin berinvestasi, teliti sebelum anda kena tipu',
            'status'=>'deleted',

        ]);


        Tags::create([
            'tags_name'=>'Titip dana',
            'slug'=>'titip-dana'
        ]);
        
        Tags::create([
            'tags_name'=>'Trading Saham',
            'slug'=>'trading-saham'
        ]);

        Tags::create([
            'tags_name'=>'Investasi',
            'slug'=>'investasi'
        ]);


    }
}
