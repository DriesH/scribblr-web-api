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
        /*          Account     */
        DB::table('achievements')->insert([
            'title' => 'Create a Scribblr account',
            'description' => 'We\'ll give you this one.',
            'points' => 10,
            'image' => '/assets/achievements/account.svg',
            'scope_name' => 'register_account',
            'category' => 'Account',
        ]);
        DB::table('achievements')->insert([
            'title' => 'Confirm your email',
            'description' => 'Just to be sure.',
            'points' => 10,
            'image' => '/assets/achievements/email.svg',
            'scope_name' => 'confirm_email',
            'category' => 'Account',
        ]);
        DB::table('achievements')->insert([
            'title' => 'Complete your account information',
            'description' => 'We want to get to know you better.',
            'points' => 10,
            'image' => '/assets/achievements/info.svg',
            'scope_name' => 'complete_account_info',
            'category' => 'Account',
        ]);

        /*       Children    */
        DB::table('achievements')->insert([
            'title' => 'Add your first child',
            'description' => 'Add your first child on Scribblr.',
            'points' => 20,
            'image' => '/assets/achievements/child.svg',
            'scope_name' => 'add_child',
            'category' => 'Children',
        ]);

        /*       Scribbles       */
        DB::table('achievements')->insert([
            'title' => 'Create your first memory',
            'description' => 'add their first words.',
            'points' => 10,
            'image' => '/assets/achievements/memory_1.svg',
            'scope_name' => 'add_memory',
            'amount_to_complete' => 1,
            'category' => 'Memories',
        ]);
        DB::table('achievements')->insert([
            'title' => 'Create 5 memories',
            'description' => 'High five!',
            'points' => 10,
            'image' => '/assets/achievements/memory_5.svg',
            'scope_name' => 'add_memory',
            'amount_to_complete' => 5,
            'category' => 'Memories',
        ]);
        DB::table('achievements')->insert([
            'title' => 'Create 20 memories',
            'description' => 'You can fill a book with these!',
            'points' => 15,
            'image' => '/assets/achievements/memory_20.svg',
            'scope_name' => 'add_memory',
            'amount_to_complete' => 20,
            'category' => 'Memories',
        ]);
        DB::table('achievements')->insert([
            'title' => 'Share a memory on social media.',
            'description' => 'Your friends will love it.',
            'points' => 15,
            'image' => '/assets/achievements/share_1.svg',
            'scope_name' => 'share_memory',
            'amount_to_complete' => 1,
            'category' => 'Memories',
        ]);
        DB::table('achievements')->insert([
            'title' => 'Share 5 memories on social media.',
            'description' => 'Sharing is caring!',
            'points' => 15,
            'image' => '/assets/achievements/share_5.svg',
            'scope_name' => 'share_memory',
            'amount_to_complete' => 5,
            'category' => 'Memories',
        ]);
        DB::table('achievements')->insert([
            'title' => 'Share 20 memories on social media.',
            'description' => 'You show-off!',
            'points' => 20,
            'image' => '/assets/achievements/share_20.svg',
            'scope_name' => 'share_memory',
            'amount_to_complete' => 20,
            'category' => 'Memories',
        ]);

        /*      ScribbleBook        */
        DB::table('achievements')->insert([
            'title' => 'Create your first book',
            'description' => 'Never lose a single memory.',
            'points' => 15,
            'image' => '/assets/achievements/book.svg',
            'scope_name' => 'add_book',
            'category' => 'Books',
        ]);
        DB::table('achievements')->insert([
            'title' => 'Order your first book',
            'description' => 'We\'re shipping it to you as we speak!',
            'points' => 25,
            'image' => '/assets/achievements/book_order_1.svg',
            'scope_name' => 'order_book',
            'amount_to_complete' => 1,
            'category' => 'Books',
        ]);
        DB::table('achievements')->insert([
            'title' => 'Order your third book',
            'description' => 'Third time\'s the charm!',
            'points' => 50,
            'image' => '/assets/achievements/book_order_3.svg',
            'scope_name' => 'order_book',
            'amount_to_complete' => 3,
            'category' => 'Books',
        ]);

    }
}
