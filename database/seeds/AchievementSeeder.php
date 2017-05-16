<?php

use Illuminate\Database\Seeder;

class AchievementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('achievements')->insert([
           'title' => 'Create a Scribblr account',
           'description' => 'We\'ll give you this one.',
           'points' => 10,
           'image' => '/assets/achievements/account.svg',
       ]);
        DB::table('achievements')->insert([
           'title' => 'Confirm your email',
           'description' => 'Just to be sure.',
           'points' => 15,
           'image' => '/assets/achievements/email.svg',
       ]);
        DB::table('achievements')->insert([
           'title' => 'Add your first child',
           'description' => 'Add your first child on Scribblr.',
           'points' => 20,
           'image' => '/assets/achievements/email.svg',
       ]);
    }
}
