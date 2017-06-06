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
            'Boogaloo' => 'https://fonts.googleapis.com/css?family=Boogaloo',
            'Special Elite' => 'https://fonts.googleapis.com/css?family=Special+Elite',
            'Montserrat' => 'https://fonts.googleapis.com/css?family=Montserrat',
            'Dosis' => 'https://fonts.googleapis.com/css?family=Dosis',
            'Architects Daughter' => 'https://fonts.googleapis.com/css?family=Architects+Daughter',
            'Barrio' => 'https://fonts.googleapis.com/css?family=Barrio',
            'Satisfy' => 'https://fonts.googleapis.com/css?family=Satisfy',
        ];


        foreach ($fonts as $font_name => $url) {
            DB::table('fonts')->insert([
               'name' => $font_name,
               'url' => $url
           ]);
        }
    }
}
