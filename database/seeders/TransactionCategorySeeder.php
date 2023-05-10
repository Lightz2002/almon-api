<?php

namespace Database\Seeders;

use App\Models\TransactionCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category1 = new TransactionCategory();
        $category1->name = 'Sedekah';
        $category1->icon = env("APP_URL", "https://almon.ryankenidy.site") . '/images/love.png';
        $category1->save();

        $category2 = new TransactionCategory();
        $category2->name = 'Dana Darurat';
        $category2->icon = env("APP_URL", "https://almon.ryankenidy.site") . ('/images/emergency.png');
        $category2->save();

        $category3 = new TransactionCategory();
        $category3->name = 'Gaya Hidup';
        $category3->icon = env("APP_URL", "https://almon.ryankenidy.site") . '/images/lifestyle.png';
        $category3->save();

        $category4 = new TransactionCategory();
        $category4->name = 'Tabungan';
        $category4->icon = env("APP_URL", "https://almon.ryankenidy.site") . '/images/salary.png';
        $category4->save();

        $category5 = new TransactionCategory();
        $category5->name = 'Kebutuhan';
        $category5->icon = env("APP_URL", "https://almon.ryankenidy.site") . '/images/basic-needs 1.png';
        $category5->save();

        $category6 = new TransactionCategory();
        $category6->name = 'Cicilan';
        $category6->icon = env("APP_URL", "https://almon.ryankenidy.site") . '/images/installment.png';
        $category6->save();
    }
}
