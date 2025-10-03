<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class UserDirection extends Pivot
{
    use HasFactory;

    protected $table = 'user_directions';

    protected $fillable = [
        'user_id',
        'direction_id',
        'priority',
    ];

}
