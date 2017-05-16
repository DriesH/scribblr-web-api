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
           'short_id' => 'kgjszzsx',
           'child_id' => 1,
           'quote' => 'lalalalalaaa',
           'story' => 'vandaag was een zware dag voor kleine Timmy. Hij vulde zijn eerste belastingsbrief in.',
       ]);
        DB::table('quotes')->insert([
           'short_id' => 'xxhgoldm',
           'child_id' => 1,
           'quote' => 'lalalalalaaa',
           'story' => 'Timmy heeft vandaag voor de eerste keer met de auto gereden. Er vielen 10 licht- en  2 zwaargewonden.',
       ]);

       for ($a=0; $a < 10; $a++) {
           DB::table('quotes')->insert([
              'short_id' => substr(md5(uniqid(mt_rand(), true)), 0, 8),
              'child_id' => 1,
              'quote' => 'sgergergerg',
              'story' => 'Timmy heeft vandaag voor de eerste keer met de auto gerergergergregden. Er vielen 10 licht- en  2 zwaargewonden.',
          ]);
       }
    }
}
