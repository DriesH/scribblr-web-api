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
           'title' => 'Welcome To Scribblr!',
           'message' => 'Hey there
           We are happy to welcome you to Scribblr! You can start adding your children in the navigation bar on the left side. After adding them, adding quotes to them is as easy as pie.

           Please let us know what you think about Scribblr and what we can improve on.

           Suggestions for improvements of our services are more than welcome at: <a href="mailto:info@scribblr.be">info@scribblr.be</a>',
           'created_at' => Carbon::now()->format('Y-m-d H:i:s')
       ]);

        DB::table('news')->insert([
           'title' => 'New update! Added more preset images',
           'message' => 'Hey there
           We just wanted to let you know that we added some extra preset images for your Scribbles. They look amazing, don\'t they?',
           'created_at' => Carbon::now()->format('Y-m-d H:i:s')
       ]);


    }
}
