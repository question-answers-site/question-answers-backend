<?php

use Illuminate\Database\Seeder;

class TopicsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Topic::create([
            'title'=>'sport',
            'description'=>'sports related questions like football,..',
        ]);
        \App\Topic::create([
            'title'=>'politics',
            'description'=>'',
        ]);
        \App\Topic::create([
            'title'=>'entertainment',
            'description'=>'entertainment stuff like music,books,movies',
        ]);
        \App\Topic::create([
            'title'=>'tech',
            'description'=>'technology issues',
        ]);
        \App\Topic::create([
            'title'=>'business',
            'description'=>'business and finance consulting',
        ]);
		\App\Topic::create([
            'title'=>'math',
            'description'=>'linear algebra,Geomtric,Probability theory',
        ]);
		\App\Topic::create([
            'title'=>'IT',
            'description'=>'all about Information Theory',
        ]);
		\App\Topic::create([
            'title'=>'Health',
            'description'=>'Health Care',
        ]);
    }
}
