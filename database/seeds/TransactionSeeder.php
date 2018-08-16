<?php

use Illuminate\Database\Seeder;

class TransactionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    private $carbon;
    private $faker;

    public function insert($location, $accountID, $value, $dateRandomness = 0)
    {
        DB::table('transactions')->insert([
         'location' => $location,
         'account_id' => $accountID,
         'date' => $this->carbon->copy()->addDay(mt_rand(0, $dateRandomness))->format('Y-m-d'),
         'value' => $value,
       ]);
    }

    public function daily()
    {
        $biased = $this->faker->biasedNumberBetween($min = 0, $max = 100, $function = 'sqrt');
        $fair = mt_rand(0, 100);

        if (false == $this->carbon->isWeekend()) {
            if ($biased > 40) {
              $this->insert('Starbucks',3,5);
            }
            if ($biased > 70) {
              $this->insert('McDonalds',3,5);
            }
        }
        if (true == $this->carbon->isWeekend()) {
            if ($biased > 40) {
              $this->insert("Joe's Tavern",3,$this->faker->randomFloat(2, $min = 35, $max = 50));
            }
            if ($biased > 80) {
              $this->insert("Theater 24",3,$this->faker->randomFloat(2, $min = 20, $max = 30));
            }
        }


    }

    public function weekly()
    {
      $this->insert("Amazon",3,$this->faker->randomFloat(2, $min = 5, $max = 70),4);
      $this->insert("Walmart",3,$this->faker->randomFloat(2, $min = 5, $max = 70),4);
    }

    public function biMonthly()
    {
        //Add Payments
        $grossIncome = -72641;
        $earnerRatio = .65;
        $this->insert('Income for earner 1', 1, $grossIncome * $earnerRatio / 24);
        $this->insert('Income for earner 2', 1, $grossIncome * (1 - $earnerRatio) / 24);
        $this->insert('The Corner Market', 1, $this->faker->randomFloat(2, $min = 80, $max = 120), 4);
        $this->insert('BP Gasoline', 3, $this->faker->randomFloat(2, $min = 10, $max = 40), 4);
        $this->insert("Petco",3,$this->faker->randomFloat(2, $min = 5, $max = 25),4);
        $this->insert("Car Wash",3,8,7);
    }

    public function monthly()
    {
        $biased = $this->faker->biasedNumberBetween($min = 0, $max = 100, $function = 'sqrt');
        $fair = mt_rand(0, 100);


        $this->insert('Gas Bill', 1, $this->faker->randomFloat(2, $min = 80, $max = 120), 4);
        $this->insert('Electric Bill', 1, $this->faker->randomFloat(2, $min = 80, $max = 120), 4);
        $this->insert('Credit Card Bill', 1, $this->faker->randomFloat(2, $min = 600, $max = 1200), 4);
        $this->insert('Rent', 1, 750);
        $this->insert('Insurance', 1, 200);

if($this->carbon->month==3)
{
  $this->insert('Tax Software', 1, 40,5);
}

if($this->carbon->month==4)
{
  $this->insert('Income Tax', 1, 500,5);
}

        if($this->carbon->month%4==0)
        {
          $this->insert('Doctor Visit', 4, $this->faker->randomFloat(2, $min = 80, $max = 120), 4);
          $this->insert('Perscription Refill', 4, $this->faker->randomFloat(2, $min = 80, $max = 120), 4);
        }
        if($this->carbon->month%6==0)
        {
          $this->insert('Oil Change', 4, $this->faker->randomFloat(2, $min = 40, $max = 45), 4);
        }
    }


    public function run()
    {
        $this->faker = \Faker\Factory::create();
        DB::table('transactions')->truncate();

        $this->carbon = new \Carbon\Carbon();
        for ($i = 0; $i < 5 * 365; ++$i) {

            if (1 == $this->carbon->day || 15 == $this->carbon->day) {
                $this->biMonthly();
            }

            if (1 == $this->carbon->day) {
                $this->monthly();
            }

            if (1 == $this->carbon->dayOfWeek) {
                $this->weekly();
            }

            $this->daily();
            $this->carbon->subDay();
        }
    }
}
