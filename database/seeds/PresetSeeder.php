<?php

use Illuminate\Database\Seeder;

class PresetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $preset_imgs = [
            'guinea_pig.jpg',
            'horses.jpg',
            'long_exposure_lights.jpg',
            'mountains.jpg',
            'paint.jpg',
            'pencils.jpg',
            'school_desk.jpg',
            'soccer_ball.jpg',
            'valley.jpg',
        ];


        foreach ($preset_imgs as $img_name) {
            DB::table('presets')->insert([
               'name' => $img_name,
           ]);
        }
    }
}
