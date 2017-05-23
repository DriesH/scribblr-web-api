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
            "But granny, she already has two hands" => "When my two grandchildren were crossing the road, I asked my granddaughter to give her little brother a hand. She answered: 'But granny, she already has two hands'",
            "Fortunately, it's only leaking at the bottom" => "On the way home we got a flat tyre because we drove into a nail which was lying in the middle of the road. The next morning my son said: 'Fortunately, it's only leaking at the bottom'",
            "There's clouds coming out of my mouth!" => "'Mom, there's a storm inside my mouth!', she said on a cold winter morning. I asked what she meant. She said: 'There's clouds coming out of my mouth!'",
            "Look miss, the snow is sunbathing!" => "I used to be a first grade teacher. On a sunny winter day one of my pupils shouted: 'Look miss, the snow is sunbathing!'",
            "So granny used to be a monkey?" => "During breakfast I explained to my daughter that our ancestors were monkeys. She replied 'So granny used to be a monkey?'",
            "Is that a grandpa in a wedding dress?" => "Today our daughter saw the pope on the news. She'd never seen him before and said: 'Is that a grandpa in a wedding dress?'",
            "Mom, I think the grass just peed" => "This morning she walked on the wet gras on her bare feet. She looked confused and said: 'Mom, I think the grass just peed'",
            "Is that a witch with two flashlights?" => "A plane flew over our house today. It was flying quite low on which she asked: 'Is that a witch with two flashlights?'",
            "Grandpa, you look like a cash register!" => "Grandpa wanted to scare her. He pulled out his fake dentures and she said: 'Grandpa, you look like a cash register!'",
            "So, did you eat him first?" => "Today I told our daughter I was pregnant and that she'd soon have a little brother. We also told her that children come from a mommy's belly. She looked a bit shocked and asked me: 'So, did you eat him first?'",
            "Mom, there's no date on them!" => "This morning she went to search for eggs in our henhouse. When she had found one, she brought it to me and said: 'Mom, there's no date on them!'",
            "No, in our car you can not put the steering wheel on the other side" => "A friend of our daughter asked her if I ever take over the wheel whenever dad gets too tired. She replied: 'No, in our car you can not put the steering wheel on the other side'",

        ];

        $counter = 0;
        foreach ($quotes as $quote => $story) {
            $counter++;

            $new_quote = new Post();
            $new_quote->short_id = substr(md5(uniqid(mt_rand(), true)), 0, 8);
            $new_quote->child_id = 1;
            $new_quote->quote = $quote;
            $new_quote->story = $story;
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
