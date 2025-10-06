<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Direction extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'number',
    ];

    /**
     * Профили, принадлежащие направлению
     */
    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }

    /**
     * Пользователи, связанные с направлением
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_directions')
            ->withPivot(['priority', 'created_at', 'updated_at']);
    }
}
