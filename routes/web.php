<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::resource('transaction', 'TransactionController');
Route::resource('budget', 'BudgetController');
Route::resource('account', 'AccountController');
Route::resource('category', 'CategoryController');
Route::resource('import', 'ImportController');

Route::get('transactions/{startDate?}', 'AccountingPeriodController@index')->name('period');
Route::get('income-statement/{startDate?}', 'IncomeStatementController@index')->name('income-statement');
Route::get('cashflow', 'CashflowController@cashflow')->name('cashflow');

Route::get('payee/{payee}','TransactionController@payee')->name('payee');
Route::get('search/{payee}','TransactionController@search')->name('payee');

Route::get('settings',function(){return view('pages.settings');})->name('settings');
