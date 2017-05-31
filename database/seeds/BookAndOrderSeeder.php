<?php

use Illuminate\Database\Seeder;

class BookAndOrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('books')->insert([
           'short_id' => 'gkhjtubh',
           'user_id' => 1,
           'title' => 'Test title lalala',
           'is_flip_over' => false,
           'cover_preset' => 'covers-01.jpg',
       ]);

        DB::table('orders')->insert([
           'short_id' => 'azertyui',
           'user_id' => 1,
           'price' => 14.99,
       ]);

        DB::table('book__orders')->insert([
           'book_id' => 1,
           'order_id' => 1,
           'amount' => 1,
       ]);
    }
}
