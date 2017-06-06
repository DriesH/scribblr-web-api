<?php

use Illuminate\Database\Seeder;
use App\Post;

class QuoteSeeder extends Seeder
{
    /**
    * Run the database seeds.
    *
    * @return void
    */
    public function run()
    {
        $quotes = [
            "But granny, she already has two hands",
            "Fortunately, it's only leaking at the bottom",
            "There's clouds coming out of my mouth!",
            "Look miss, the snow is sunbathing!",
            "So granny used to be a monkey?",
            "Is that a grandpa in a wedding dress?",
            "Mom, I think the grass just peed",
            "Is that a witch with two flashlights?",
            "Grandpa, you look like a cash register!",
            "So, did you eat him first?",
            "Mom, there's no date on them!",
            "No, in our car you can not put the steering wheel on the other side"
        ];

        $counter = 0;
        foreach ($quotes as $quote) {
            $for_child = ($counter <= count($quotes) / 2) ? 1 : 2;
            $counter++;

            $new_quote = new Post();
            $new_quote->short_id = substr(md5(uniqid(mt_rand(), true)), 0, 8);
            $new_quote->child_id = $for_child;
            $new_quote->quote = $quote;
            $new_quote->lqip = self::getSmallSizeImage(storage_path() . '/quote-seeder-img/baked/img' . $counter . '.jpg');
            $new_quote->img_original_url_id = md5(uniqid(mt_rand(), true));
            $new_quote->img_baked_url_id = md5(uniqid(mt_rand(), true));
            $new_quote->is_memory = false;
            $new_quote->save();
            $new_quote->addMedia(storage_path() . '/quote-seeder-img/original/img' . $counter . '.jpg')->preservingOriginal()->toMediaLibrary('original');
            $new_quote->addMedia(storage_path() . '/quote-seeder-img/baked/img' . $counter . '.jpg')->preservingOriginal()->toMediaLibrary('baked');
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
