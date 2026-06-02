<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Leave extends Model
{
    protected $fillable = [
        'user',
        'leave_type',
        'start_date',
        'end_date',
        'is_half_day',
        'total_days',
        'status',
    ];
}
