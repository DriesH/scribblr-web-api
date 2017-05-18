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
        $this->call(ChildSeeder::class);
        $this->call(QuoteSeeder::class);
        $this->call(AchievementSeeder::class);
        $this->call(PresetSeeder::class);
    }
}
