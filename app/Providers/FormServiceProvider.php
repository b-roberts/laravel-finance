<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use \Form;

class FormServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot()
    {
        Form::component('bsInput', 'components.input', ['type','name', 'value' => null, 'attributes' => [],'helpText'=>null]);
        Form::component('bsTextarea', 'components.textarea', ['name', 'value' => null, 'attributes' => [],'helpText'=>null]);
        Form::component('bsSelect', 'components.select', ['name', 'list'=>[], 'selected' => null, 'selectAttributes' => [], 'optionsAttributes' => []]);
    }


    /**
     * Register any application services.
     */
    public function register()
    {
    }
}
