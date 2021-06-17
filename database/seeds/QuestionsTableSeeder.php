<?php

use Illuminate\Database\Seeder;

class QuestionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Question::create([
            'user_id'=>1,
            'body'=>'First Question From Seeder',
            ]);
        \App\Question::create([
            'user_id'=>1,
            'body'=>'Second Question From Seeder',
        ]);
        \App\Question::create([
            'user_id'=>1,
            'body'=>'Third Question From Seeder',
        ]);
        \App\Question::create([
            'user_id'=>1,
            'body'=>'First Question From Seeder',
        ]);
        \App\Question::create([
            'user_id'=>1,
            'body'=>'Second Question From Seeder',
        ]);
        \App\Question::create([
            'user_id'=>1,
            'body'=>'Third Question From Seeder',
        ]);
        \App\Question::create([
            'user_id'=>1,
            'body'=>'First Question From Seeder',
        ]);
        \App\Question::create([
            'user_id'=>1,
            'body'=>'Second Question From Seeder',
        ]);
        \App\Question::create([
            'user_id'=>1,
            'body'=>'Third Question From Seeder',
        ]);
        \App\Question::create([
            'user_id'=>1,
            'body'=>'First Question From Seeder',
        ]);
        \App\Question::create([
            'user_id'=>1,
            'body'=>'Second Question From Seeder',
        ]);
        \App\Question::create([
            'user_id'=>1,
            'body'=>'Third Question From Seeder',
        ]);
    }
}
