<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class YearSequence extends Model
{
    use HasFactory;

    protected $table = "year_sequence";

    protected $guarded = [];
}
