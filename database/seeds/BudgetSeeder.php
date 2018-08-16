<?php

use Illuminate\Database\Seeder;

class BudgetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      $faker = \Faker\Factory::create();
      $carbon = new \Carbon\Carbon();
      DB::statement('SET FOREIGN_KEY_CHECKS=0;');
      DB::table('budget')->truncate();
      DB::table('budget')->insert([
          'start_date'=>$carbon->copy()->subYear(6),
          'end_Date'=>$carbon->copy()->addYear(6),

          'monthly_income'=>$faker->randomNumber
      ]);

DB::table('designations')->truncate();
      DB::table('designations')->insert(['id'=>'0', 'name'=>'OTHER']);
      DB::table('designations')->where('name','OTHER')->update(['id'=>0]);
      DB::table('designations')->insert(['id'=>'1', 'name'=>'Shelter']);
      DB::table('designations')->insert(['id'=>'2', 'name'=>'Transportation']);
      DB::table('designations')->insert(['id'=>'3', 'name'=>'Food']);
      DB::table('designations')->insert(['id'=>'4', 'name'=>'Vice']);
      DB::table('designations')->insert(['id'=>'5', 'name'=>'Insurance']);
      DB::table('designations')->insert(['id'=>'6', 'name'=>'Personal']);
      DB::table('designations')->insert(['id'=>'7', 'name'=>'Medical']);
DB::table('categories')->truncate();
    DB::table('categories')->insert(['name'=>'Rent', 'designation_id'=>'1', 'fixed'=>'1', 'color'=>'5510024']);
    DB::table('categories')->insert(['name'=>'Utilities', 'designation_id'=>'1', 'fixed'=>'0', 'color'=>'14222184']);
    DB::table('categories')->insert(['name'=>'Resturants', 'designation_id'=>'3', 'fixed'=>'0', 'color'=>'2539317']);
    DB::table('categories')->insert(['name'=>'Groceries', 'designation_id'=>'3', 'fixed'=>'0', 'color'=>'2539317']);
    DB::table('categories')->insert(['name'=>'Gas', 'designation_id'=>'2', 'fixed'=>'0', 'color'=>'1222862']);
    DB::table('categories')->insert(['name'=>'Booze', 'designation_id'=>'4', 'fixed'=>'0', 'color'=>'1048469']);
    DB::table('categories')->insert(['name'=>'Cigarettes', 'designation_id'=>'4', 'fixed'=>'0', 'color'=>'6258060']);
    DB::table('categories')->insert(['name'=> 'Auto Expenses', 'designation_id'=>'2', 'fixed'=>'0', 'color'=>'43767']);
    DB::table('categories')->insert(['name'=> 'Entertainment', 'designation_id'=>'0', 'fixed'=>'0', 'color'=>'15928433']);
    DB::table('categories')->insert(['name'=> 'Personal Care', 'designation_id'=>'6', 'fixed'=>'0', 'color'=>'6954940']);
    DB::table('categories')->insert(['name'=> 'Miscellaneous', 'designation_id'=>'0', 'fixed'=>'0', 'color'=>'13679541']);
    DB::table('categories')->insert(['name'=> 'Home Products', 'designation_id'=>'0', 'fixed'=>'0', 'color'=>'9985945']);
    DB::table('categories')->insert(['name'=> 'Home Improvement', 'designation_id'=>'1', 'fixed'=>'0', 'color'=>'8507532']);
    DB::table('categories')->insert(['name'=> 'Car Payment', 'designation_id'=>'2', 'fixed'=>'1', 'color'=>'1499038']);
    DB::table('categories')->insert(['name'=> 'Savings', 'designation_id'=>'0', 'fixed'=>'0', 'color'=>'15527023']);
    DB::table('categories')->insert(['name'=> 'Gifts', 'designation_id'=>'0', 'fixed'=>'0', 'color'=>'6029142']);
    DB::table('categories')->insert(['name'=> 'ATM', 'designation_id'=>'0', 'fixed'=>'0', 'color'=>'399077']);
    DB::table('categories')->insert(['name'=> 'Clothing', 'designation_id'=>'6', 'fixed'=>'0', 'color'=>'969813']);
    DB::table('categories')->insert(['name'=> 'Taxes', 'designation_id'=>'0', 'fixed'=>'0', 'color'=>'3651836']);
    DB::table('categories')->insert(['name'=> 'Moving Expenses', 'designation_id'=>'1', 'fixed'=>'0', 'color'=>'15461550']);
    DB::table('categories')->insert(['name'=> 'Pets', 'designation_id'=>'0', 'fixed'=>'0', 'color'=>'0']);
    DB::table('categories')->insert(['name'=> 'Insurance', 'designation_id'=>'5', 'fixed'=>'0', 'color'=>'0']);
    DB::table('categories')->insert(['name'=> 'Phone Expense', 'designation_id'=>'8', 'fixed'=>'0', 'color'=>'0']);
    DB::table('categories')->insert(['name'=> 'Charity', 'designation_id'=>'0', 'fixed'=>'0', 'color'=>'0']);
    DB::table('categories')->insert(['name'=> 'Medical Visits', 'designation_id'=>'7', 'fixed'=>'0', 'color'=>'0']);
    DB::table('categories')->insert(['name'=> 'Medicine', 'designation_id'=>'7', 'fixed'=>'0', 'color'=>'0']);


DB::table('budget_category')->truncate();
foreach(\App\Category::get() as $category)
{
  DB::table('budget_category')->insert(['budget_id'=>'1', 'category_id'=>$category->id,'value'=>$faker->randomNumber]);
}
DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
