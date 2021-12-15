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
Route::post('transaction/{id}/prediction', 'TransactionController@usePrediction')->name('transaction.usePrediction');

Route::resource('budget', 'BudgetController');
Route::resource('account', 'AccountController');
Route::resource('category', 'CategoryController');
Route::resource('import', 'ImportController');
Route::resource('rule', 'RuleController');
Route::resource('payee', 'PayeeController');
Route::resource('note', 'NoteController');

Route::get('transactions/{startDate?}', 'AccountingPeriodController@index')->name('period');
Route::get('income-statement/{startDate?}', 'IncomeStatementController@index')->name('income-statement');
Route::get('balance/{startDate?}', 'IncomeStatementController@balance')->name('balance');
Route::get('cashflow', 'CashflowController@cashflow')->name('cashflow');

Route::get('payee/{payee}','TransactionController@payee')->name('payee');
Route::get('search/{payee}','TransactionController@search')->name('payee');

Route::get('settings',function(){return view('pages.settings');})->name('settings');

Route::any('vue/transactions','TransactionController@indexVue')->name('vue.transactions');
