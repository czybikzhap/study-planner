<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'direction_id',
        'name',
    ];

    /**
     * Направление, к которому принадлежит профиль
     */
    public function direction(): BelongsTo
    {
        return $this->belongsTo(Direction::class);
    }

    /**
     * Пользователи, связанные с профилем
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_profiles')
            ->withPivot('priority')
            ->withTimestamps();
    }
}

