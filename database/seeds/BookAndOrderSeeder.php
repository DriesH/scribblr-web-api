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
           'cover_preset' => 'covers-01.png',
           'created_at' => Carbon::now()->format('Y-m-d H:i:s')
       ]);

        DB::table('orders')->insert([
           'short_id' => 'azertyui',
           'user_id' => 1,
           'price' => 14.99,
           'created_at' => Carbon::now()->format('Y-m-d H:i:s')
       ]);

        DB::table('book__orders')->insert([
           'book_id' => 1,
           'order_id' => 1,
           'amount' => 1,
           'created_at' => Carbon::now()->format('Y-m-d H:i:s')
       ]);
    }
}
