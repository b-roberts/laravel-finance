<?php

use Illuminate\Database\Seeder;

class AccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $faker = \Faker\Factory::create();
DB::table('accounts')->truncate();
      DB::table('accounts')->insert([
          'name' => 'Checking Account',
          'description'=>'',
          'account_number'=>$faker->randomNumber
      ]);
      DB::table('accounts')->insert([
          'name' => 'Savings Account',
          'description'=>'',
          'account_number'=>$faker->randomNumber
      ]);

      DB::table('accounts')->insert([
          'name' => 'Credit Card',
          'description'=>'',
          'account_number'=>$faker->randomNumber
      ]);
      DB::table('accounts')->insert([
          'name' => 'Health Savings Account',
          'description'=>'',
          'account_number'=>$faker->randomNumber
      ]);
    }
}
