<?php

use Illuminate\Database\Seeder;

class ColorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $colors = [
            'Blue' => '#4E84D5',
            'Purple' => '#CA60ED',
            'Pink' => '#fD5FE9',
            'Red' => '#F8315B',
            'Orange' => '#F7AB48',
            'Yellow' => '#FFD43E',
            'Green' => '#6DD472',
        ];


        foreach ($colors as $name => $color) {
            DB::table('colors')->insert([
               'name' => $name,
               'hex_code' => $color,
           ]);
        }
    }
}
