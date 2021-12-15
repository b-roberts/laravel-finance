<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $table = 'notes';
    protected $primaryKey = 'id';
    public const UPDATED_AT = null;
    protected $fillable = [
        'body'
    ];
}
