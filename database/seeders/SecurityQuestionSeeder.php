<?php

namespace Database\Seeders;

use App\Models\SecurityQuestion;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SecurityQuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sq1 = new SecurityQuestion();
        $sq1->name = "E-Commerce favorit Anda ?";
        $sq1->save();

        $sq2 = new SecurityQuestion();
        $sq2->name = "Makanan kesukaan Anda ?";
        $sq2->save();

        $sq3 = new SecurityQuestion();
        $sq3->name = "Smartphone pertama Anda ?";
        $sq3->save();

        $sq4 = new SecurityQuestion();
        $sq4->name = "Nama teman baik anda ?";
        $sq4->save();

        $sq5 = new SecurityQuestion();
        $sq5->name = "Nama guru favorit anda ?";
        $sq5->save();
    }
}
