<?php

use Illuminate\Database\Seeder;
use App\User;
use App\News;
use Carbon\Carbon;

class NewsSeeder extends Seeder
{
    /**
    * Run the database seeds.
    *
    * @return void
    */
    public function run()
    {
        DB::table('news')->insert([
            'title' => 'Welcome To Scribblr',
            'message' => 'Hey there,
            We are happy to welcome you to Scribblr. You can start adding your children in the navigation bar on the left side. After adding them, adding quotes to them is as easy as pie.
            </br>
            Please let us know what you think about Scribblr and what we can improve on.
            </br>
            Suggestions for improvements of our services are more than welcome at: <a href="mailto:info@scribblr.be">info@scribblr.be</a>',
            'url' => strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', 'Welcome To Scribblr'), '-')),
            'img' => '/assets/news/news-welcome.png',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('news')->insert([
            'title' => 'Update: new book covers',
            'message' => 'Hey there again,
            </br>
            We added some new book covers to our collection. We asked three very talented digital artist if they could come up with
            something that fits our brand without losing their own unique touch.
            </br>
            We\'d be proud to see any of these new designs on your next Scribbl\' book.',
            'url' => strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', 'Update: new book covers'), '-')),
            'img' => '/assets/news/news-bookcovers.png',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('news')->insert([
            'title' => 'Update: added more fonts',
            'message' => 'More fonts
            </br>
            We know how much our users like to personalize their memories. This is why we added a few more fonts to make your quotes look even more beautiful.
            </br>
            Here\'s a list of the fonts we added:
            <ul>
                <li>Architects Daughter</li>
                <li>Barrio</li>
                <li>Satisfy</li>
            </ul>
            </br>
            Go ahead and try \'em out!',
            'url' => strtolower(trim(preg_replace('/[^a-zA-Z0-9]+/', '-', 'Update: added more fonts'), '-')),
            'img' => '/assets/news/news-fonts.png',
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);


    }
}
