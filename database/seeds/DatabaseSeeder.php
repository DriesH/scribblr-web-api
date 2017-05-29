<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(UserSeeder::class);
        $this->call(PresetSeeder::class);
        $this->call(FontSeeder::class);
        $this->call(ChildSeeder::class);
        $this->call(QuoteSeeder::class);
        $this->call(MemorySeeder::class);
        $this->call(AchievementSeeder::class);
        $this->call(NewsSeeder::class);
        $this->call(ColorSeeder::class);
        $this->call(CountriesSeeder::class);
    }
}
