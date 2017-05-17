<?php

use Illuminate\Database\Seeder;
use App\Quote;

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


        $new_quote = new Quote();
        $new_quote->short_id = substr(md5(uniqid(mt_rand(), true)), 0, 8);
        $new_quote->child_id = 1;
        $new_quote->quote = 'gfjhrtyjrgerdv ve erz vrev ergerg e ghrteg';
        $new_quote->story = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.';
        $new_quote->main_color = '#e2e2e2';
        $new_quote->img_original_url_id = 'azertyuiop';
        $new_quote->img_baked_url_id = 'poiuytreza';
        $new_quote->addMedia(storage_path() . '/quote-seeder-img/test1.png')->preservingOriginal()->toMediaLibrary('original');
        $new_quote->addMedia(storage_path() . '/quote-seeder-img/test1.png')->preservingOriginal()->toMediaLibrary('baked');
    }
}
