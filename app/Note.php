<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    //
    protected $table = 'transaction_notes';
    protected $primaryKey = 'transaction_id';
}
