<?php

use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
           'short_id' => 'aaaaaaaa',
           'first_name' => 'admin',
           'Last_name' => 'admin',
           'email' => 'admin@scribblr.com',
           'street_name' => 'Scribblestreet',
           'house_number' => '25',
           'city' => 'Antwerp',
           'postal_code' => '2000',
           'country' => 'Belgium',
           'password' => bcrypt('admin123')
       ]);
    }
}
