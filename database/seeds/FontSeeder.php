<?php

use Illuminate\Database\Seeder;

class FontSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fonts = [
            'Calibri',
            'Impact',
        ];


        foreach ($fonts as $font_name) {
            DB::table('fonts')->insert([
               'name' => $font_name,
           ]);
        }
    }
}
