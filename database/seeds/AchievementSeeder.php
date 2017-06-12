<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AchievementSeeder extends Seeder
{
    /**
    * Run the database seeds.
    *
    * @return void
    */
    public function run()
    {
        /*          Account     */
        DB::table('achievements')->insert([
            'title' => 'Create a Scribblr account.',
            'points' => 10,
            'image' => '/assets/achievements/account.svg',
            'scope_name' => 'register_account',
            'category' => 'Account',
        ]);
        DB::table('achievements')->insert([
            'title' => 'Confirm your email.',
            'points' => 10,
            'image' => '/assets/achievements/email.svg',
            'scope_name' => 'confirm_email',
            'category' => 'Account',
        ]);
        DB::table('achievements')->insert([
            'title' => 'Complete your account information.',
            'points' => 10,
            'image' => '/assets/achievements/info.svg',
            'scope_name' => 'complete_account_info',
            'category' => 'Account',
        ]);

        /*       Children    */
        DB::table('achievements')->insert([
            'title' => 'Add your first child.',
            'points' => 20,
            'image' => '/assets/achievements/children.svg',
            'scope_name' => 'add_child',
            'category' => 'Children',
        ]);

        /*       Scribbles       */
        DB::table('achievements')->insert([
            'title' => 'Create your first memory.',
            'points' => 10,
            'image' => '/assets/achievements/memory.svg',
            'scope_name' => 'add_memory',
            'amount_to_complete' => 1,
            'category' => 'Memories',
        ]);
        DB::table('achievements')->insert([
            'title' => 'Create 5 memories.',
            'points' => 10,
            'image' => '/assets/achievements/memory.svg',
            'scope_name' => 'add_memory',
            'amount_to_complete' => 5,
            'category' => 'Memories',
        ]);
        DB::table('achievements')->insert([
            'title' => 'Create 20 memories.',
            'points' => 15,
            'image' => '/assets/achievements/memory.svg',
            'scope_name' => 'add_memory',
            'amount_to_complete' => 20,
            'category' => 'Memories',
        ]);
        DB::table('achievements')->insert([
            'title' => 'Share a memory on social media.',
            'points' => 15,
            'image' => '/assets/achievements/share.svg',
            'scope_name' => 'share_memory',
            'amount_to_complete' => 1,
            'category' => 'Memories',
        ]);
        DB::table('achievements')->insert([
            'title' => 'Share 5 memories on social media.',
            'points' => 15,
            'image' => '/assets/achievements/share.svg',
            'scope_name' => 'share_memory',
            'amount_to_complete' => 5,
            'category' => 'Memories',
        ]);
        DB::table('achievements')->insert([
            'title' => 'Share 20 memories on social media.',
            'points' => 20,
            'image' => '/assets/achievements/share.svg',
            'scope_name' => 'share_memory',
            'amount_to_complete' => 20,
            'category' => 'Memories',
        ]);

        /*      ScribbleBook        */
        DB::table('achievements')->insert([
            'title' => 'Create your first Scribbl\' book.',
            'points' => 15,
            'image' => '/assets/achievements/book.svg',
            'scope_name' => 'add_book',
            'category' => 'Books',
        ]);
        DB::table('achievements')->insert([
            'title' => 'Order your first Scribbl\' book.',
            'points' => 25,
            'image' => '/assets/achievements/cart.svg',
            'scope_name' => 'order_book',
            'amount_to_complete' => 1,
            'category' => 'Books',
        ]);
        DB::table('achievements')->insert([
            'title' => 'Order your third Scribbl\' book.',
            'points' => 50,
            'image' => '/assets/achievements/cart.svg',
            'scope_name' => 'order_book',
            'amount_to_complete' => 3,
            'category' => 'Books',
        ]);


        //for main user
        DB::table('achievement__users')->insert([
            'user_id' => 1,
            'achievement_id' => 1,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('achievement__users')->insert([
            'user_id' => 1,
            'achievement_id' => 2,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('achievement__users')->insert([
            'user_id' => 1,
            'achievement_id' => 3,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('achievement__users')->insert([
            'user_id' => 1,
            'achievement_id' => 4,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('achievement__users')->insert([
            'user_id' => 1,
            'achievement_id' => 5,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);
        DB::table('achievement__users')->insert([
            'user_id' => 1,
            'achievement_id' => 6,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

    }
}
