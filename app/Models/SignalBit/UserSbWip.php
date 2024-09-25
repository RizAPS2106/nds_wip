<?php

namespace App\Models\SignalBit;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSbWip extends Model
{
    use HasFactory;

    protected $connection = 'mysql_sb';

    protected $table = 'user_sb_wip';

    protected $guarded = [];

    public function userPassword()
    {
        return $this->belongsTo(UserLine::class, 'line_id', 'line_id');
    }
}
