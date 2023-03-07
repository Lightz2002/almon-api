<?php

namespace Database\Seeders;

use App\Models\ExpenseCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $category1 = new ExpenseCategory();
        $category1->name = 'Sedekah';
        $category1->icon = public_path('images/love.png');
        $category1->save();

        $category2 = new ExpenseCategory();
        $category2->name = 'Dana Darurat';
        $category2->icon = public_path('images/emergency.png');
        $category2->save();

        $category3 = new ExpenseCategory();
        $category3->name = 'Gaya Hidup';
        $category3->icon = public_path('images/lifestyle.png');
        $category3->save();

        $category4 = new ExpenseCategory();
        $category4->name = 'Tabungan';
        $category4->icon = public_path('images/salary.png');
        $category4->save();

        $category5 = new ExpenseCategory();
        $category5->name = 'Kebutuhan';
        $category5->icon = public_path('images/basic-needs 1.png');
        $category5->save();
    }
}
