<?php

namespace Database\Seeders;

use App\Models\SecurityQuestion;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
  /**
   * Run the database seeds.
   *
   * @return void
   */
  public function run()
  {
    User::factory()
      ->count(5)
      ->create();

    // user Ryan
    $user = new User();
    $user->username = 'ryan';
    $user->email = 'kenidyryan@gmail.com';
    $user->email_verified_at = now();
    $user->password = Hash::make('123');
    $user->security_question_id = SecurityQuestion::firstWhere('name', 'Smartphone pertama Anda ?')->id;
    $user->security_question_answer = 'Samsung';
    $user->monthly_salary = 4500000;
    $user->save();
  }
}
