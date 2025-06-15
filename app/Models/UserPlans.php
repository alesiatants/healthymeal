<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserPlans extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'weight', 'goal', 'activity_level', 'meals_per_day'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function scopeLatestForUser($query, $userId)
    {
        return $query->where('user_id', $userId)->latest()->first();
    }
}
