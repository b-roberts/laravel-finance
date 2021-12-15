<?php

namespace App\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;

class RunRules
{
    use Dispatchable;

    /**
     * Create a new job instance.
     */
    public function __construct(\App\Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $rules = \App\Rule::get();

        foreach ($rules as $rule) {
            if (preg_match($rule->pattern, $this->transaction->location)) {
                if (null != $rule->minValue && $this->transaction->value < $rule->minValue) {
                    continue;
                }
                if (null != $rule->maxValue && $this->transaction->value > $rule->maxValue) {
                    continue;
                }
                //Check Transaction type
                if ('transfer' == $rule->type) {
                  $this->transaction->type='transfer';
                }
                if ('payment' == $rule->type) {
                    try {
                        $this->transaction->categories()->attach($rule->category_id, [
                    'value' => $this->transaction->value * $rule->percentage,
                    'file_date'=>$this->transaction->date]);
                    } catch (\Illuminate\Database\QueryException $e) {
                        continue;
                    }
                }
                $this->transaction->allocation_type = 1;
            }
        }
        $this->transaction->save();
    }
}
