<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Meta extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];
    protected  $table = 'meta';
    public $timestamps = false;


}
