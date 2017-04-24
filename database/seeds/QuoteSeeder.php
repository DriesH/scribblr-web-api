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
           'quote' => 'En na gon we schelle',
           'font_size' => '20',
           'font_type' => 'Arial',
       ]);
        DB::table('quotes')->insert([
           'short_id' => 'bbbbbbbb',
           'child_id' => 1,
           'quote' => 'Ik ben ni versloafd, ik heb het gewoon nodig...',
           'font_size' => '15',
           'font_type' => 'Arial',
       ]);
    }
}
