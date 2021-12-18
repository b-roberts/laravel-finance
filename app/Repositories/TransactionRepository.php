<?php

namespace App\Repositories;

use App\Payee;
use \App\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Traits\ForwardsCalls;

class TransactionRepository
{
    use ForwardsCalls;
    protected $query;
    public function __construct()
    {
        $this->query();
    }
    public function query()
    {
        $this->query = Transaction::orderBy('date')->orderBy('value')
        ->with('categories')
        ->with('account')
        ->with('note');
        return $this;
    }

    public function mode($column)
    {
        return $this->query->groupBy($column)->orderBy(\DB::raw('COUNT(*)'), 'DESC')->limit(1)->pluck($column)->first();
    }

    public function during(Carbon $startDate, Carbon $endDate)
    {
        $this->query = $this->query
            ->where('date', '>=', $startDate->toDateString())
            ->where('date', '<', $endDate->toDateString());
        return $this;
    }
    public function onlyPayments()
    {
        $this->query = $this->query
            ->where('type', 'payment');
        return $this;
    }
    public function onlyUnallocated()
    {
        $this->query = $this->query
            ->doesntHave('categories');
        return $this;
    }
    public function madeTo(Payee $payee)
    {
        $this->query = $this->query
            ->where('payee_id', $payee->id);
        return $this;
    }

    public function get()
    {
        return $this->query->get();
    }

    public static function make()
    {
        return new static;
    }

    public static function byDate(Carbon $startDate, Carbon $endDate)
    {
        return static::make()->during($startDate, $endDate)->get();
    }

    public static function payments()
    {
        return static::make()->onlyPayments()->get();
    }

    public static function paymentsByDate(Carbon $startDate, Carbon $endDate)
    {
        return static::make()->during($startDate, $endDate)->onlyPayments()->get();
    }

    public static function unallocatedByDate(Carbon $startDate, Carbon $endDate)
    {
        return static::make()->during($startDate, $endDate)->onlyPayments()->onlyUnallocated()->get();
    }
    public function __call($method, $parameters)
    {
        return $this->forwardCallTo($this->query, $method, $parameters);
    }
}
