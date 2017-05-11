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
           'quote' => 'lalalalalaaa',
           'story' => 'vandaag was een zware dag voor kleine Timmy. Hij vulde zijn eerste belastingsbrief in.',
       ]);
        DB::table('quotes')->insert([
           'short_id' => 'bbbbbbbb',
           'child_id' => 1,
           'quote' => 'lalalalalaaa',
           'story' => 'Timmy heeft vandaag voor de eerste keer met de auto gereden. Er vielen 10 licht- en  2 zwaargewonden.',
       ]);
    }
}
