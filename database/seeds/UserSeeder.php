<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

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
            'short_id' => '25GkkDi1',
            'first_name' => 'Thomas',
            'last_name' => 'Harris',
            'email' => 'thomas.harris@gmail.com',
            'street_name' => 'Fulton Street',
            'house_number' => '1550',
            'city' => 'Parkersburg',
            'postal_code' => '26101',
            'country' => 'United States',
            'password' => bcrypt('testtest123'),
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
    }
}
