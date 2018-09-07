<?php

namespace App\Jobs;

use Illuminate\Foundation\Bus\Dispatchable;

class Export
{
    use Dispatchable;

    public function handle()
    {
        // Enable user error handling
        libxml_use_internal_errors(true);
        $dom = new \DomDocument;
        $accounts = \App\Account::get();
        $view = \View::make('ofx', ['accounts' => $accounts]);
        $contents = $view->render();
        $dom->loadXML($contents);
        $dom->formatOutput = true;
        echo $dom->saveXML();
    }
}
