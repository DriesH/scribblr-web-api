<?php

use Illuminate\Database\Seeder;

class ChildSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('children')->insert([
           'short_id' => 'aaaaaaaa',
           'user_id' => 1,
           'gender' => 'Male',
           'first_name' => 'Druis',
           'last_name' => 'Tablet',
           'date_of_birth' => '2015-02-03',
       ]);

        DB::table('children')->insert([
           'short_id' => 'bbbbbbbb',
           'user_id' => 1,
           'gender' => 'Female',
           'first_name' => 'Ons',
           'last_name' => 'Miranda',
           'date_of_birth' => '2014-07-15',
       ]);

       for ($a=0; $a < 50; $a++) {
           DB::table('children')->insert([
              'short_id' => substr(md5(uniqid(mt_rand(), true)), 0, 8),
              'user_id' => 1,
              'gender' => 'Female',
              'first_name' => 'Ons',
              'last_name' => 'Miranda',
              'date_of_birth' => '2014-07-15',
          ]);
       }
    }
}
