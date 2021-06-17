<?php

use Illuminate\Database\Seeder;
use App\User;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        User::create(['first_name'=>'gobran','last_name'=>'Fahd','email'=>'gobran.fd@gmail.com','password'=>bcrypt(1234)]);
    }
}
