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
        // DB::table('quotes')->insert([
        //     'short_id' => 'kgjszzsx',
        //     'child_id' => 1,
        //     'quote' => 'lalalalalaaa',
        //     'story' => 'vandaag was een zware dag voor kleine Timmy. Hij vulde zijn eerste belastingsbrief in.',
        // ]);
        // DB::table('quotes')->insert([
        //     'short_id' => 'xxhgoldm',
        //     'child_id' => 1,
        //     'quote' => 'lalalalalaaa',
        //     'story' => 'Timmy heeft vandaag voor de eerste keer met de auto gereden. Er vielen 10 licht- en  2 zwaargewonden.',
        // ]);

        for ($i=0; $i < 10; $i++) {
            $new_quote = new Quote();
            $new_quote->short_id = substr(md5(uniqid(mt_rand(), true)), 0, 8);
            $new_quote->child_id = 1;
            $new_quote->quote = 'gfjhrtyjrgerdv ve erz vrev ergerg e ghrteg';
            $new_quote->story = 'Lorem ipsum dolor sit amet, conseg re eer ger gere ctetur adipt anim id est laborum.';
            $new_quote->lqip = self::getSmallSizeImage(storage_path() . '/quote-seeder-img/test1.png');
            $new_quote->img_original_url_id = md5(uniqid(mt_rand(), true));
            $new_quote->img_baked_url_id = md5(uniqid(mt_rand(), true));
            $new_quote->save();
            $new_quote->addMedia(storage_path() . '/quote-seeder-img/test1.png')->preservingOriginal()->toMediaLibrary('original');
            $new_quote->addMedia(storage_path() . '/quote-seeder-img/test1.png')->preservingOriginal()->toMediaLibrary('baked');

            $new_quote2 = new Quote();
            $new_quote2->short_id = substr(md5(uniqid(mt_rand(), true)), 0, 8);
            $new_quote2->child_id = 1;
            $new_quote2->quote = 'kjhzkjejk ovjelk ozep ^ppek ze';
            $new_quote2->story = 'Lorem ipsum dolor sit fre fz fzefzeflkj clkjezlkj ivjiorzjirv hjkz ha deserunt mollit anim id est laborum.';
            $new_quote2->lqip = self::getSmallSizeImage(storage_path() . '/quote-seeder-img/test2.jpg');
            $new_quote2->img_original_url_id = md5(uniqid(mt_rand(), true));
            $new_quote2->img_baked_url_id = md5(uniqid(mt_rand(), true));
            $new_quote2->save();
            $new_quote2->addMedia(storage_path() . '/quote-seeder-img/test2.jpg')->preservingOriginal()->toMediaLibrary('original');
            $new_quote2->addMedia(storage_path() . '/quote-seeder-img/test2.jpg')->preservingOriginal()->toMediaLibrary('baked');

            $new_quote3 = new Quote();
            $new_quote3->short_id = substr(md5(uniqid(mt_rand(), true)), 0, 8);
            $new_quote3->child_id = 1;
            $new_quote3->quote = 'kjhzkjejk ovjelk ozep ^ppek ze';
            $new_quote3->story = 'Lorem ipsum dolor sit fre fz fzefzeflkj clkjezlkj ivjiorzjirv hjkz ha deserunt mollit anim id est laborum.';
            $new_quote3->lqip = self::getSmallSizeImage(storage_path() . '/quote-seeder-img/test3.jpg');
            $new_quote3->img_baked_url_id = md5(uniqid(mt_rand(), true));
            $new_quote3->preset_id = random_int(1, 9);
            $new_quote3->save();
            $new_quote3->addMedia(storage_path() . '/quote-seeder-img/test3.jpg')->preservingOriginal()->toMediaLibrary('original');
            $new_quote3->addMedia(storage_path() . '/quote-seeder-img/test3.jpg')->preservingOriginal()->toMediaLibrary('baked');

        }
    }

    private function getSmallSizeImage($image) {
        return Image::make($image)
        ->resize(5, null, function ($constraint) {
            $constraint->aspectRatio();
        })
        ->encode('data-url')
        ->encoded;
    }
}
