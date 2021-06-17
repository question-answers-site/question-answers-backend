<?php

use Illuminate\Database\Seeder;

class AnswersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \App\Answer::create([
            'user_id'=>1,
            'question_id'=>1,
            'body'=>'First Answer From Seeder',
        ]);
        \App\Answer::create([
            'user_id'=>1,
            'question_id'=>2,
            'body'=>'Second Answer From Seeder',
        ]);
        \App\Answer::create([
            'user_id'=>1,
            'question_id'=>3,
            'body'=>'Third Answer From Seeder',
        ]);

        \App\Answer::create([
            'user_id'=>1,
            'question_id'=>4,
            'body'=>'First Answer From Seeder',
        ]);
        \App\Answer::create([
            'user_id'=>1,
            'question_id'=>5,
            'body'=>'Second Answer From Seeder',
        ]);
        \App\Answer::create([
            'user_id'=>1,
            'question_id'=>6,
            'body'=>'Third Answer From Seeder',
        ]);

        \App\Answer::create([
            'user_id'=>1,
            'question_id'=>7,
            'body'=>'First Answer From Seeder',
        ]);
        \App\Answer::create([
            'user_id'=>1,
            'question_id'=>8,
            'body'=>'Second Answer From Seeder',
        ]);
        \App\Answer::create([
            'user_id'=>1,
            'question_id'=>9,
            'body'=>'Third Answer From Seeder',
        ]);
    }
}
