<?php

use Illuminate\Database\Seeder;

class QuoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('quotes')->insert([
           'short_id' => 'aaaaaaaa',
           'child_id' => 1,
       ]);
        DB::table('quotes')->insert([
           'short_id' => 'bbbbbbbb',
           'child_id' => 1,
       ]);
    }
}
