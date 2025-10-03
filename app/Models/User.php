<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
    ];

    /**
     * Направления пользователя
     */
    public function directions(): BelongsToMany
    {
        return $this->belongsToMany(Direction::class, 'user_directions')
            ->withPivot('priority')
            ->withTimestamps();
    }

    /**
     * Профили пользователя
     */
    public function profiles(): BelongsToMany
    {
        return $this->belongsToMany(Profile::class, 'user_profiles')
            ->withPivot('priority')
            ->withTimestamps();
    }
}
