<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;
use Database\Seeders\SecurityQuestionSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            SecurityQuestionSeeder::class,
            UserSeeder::class,
            TransactionCategorySeeder::class,
            TransactionSeeder::class,
        ]);
    }
}
