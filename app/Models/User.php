<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'birth_date',
		'gender',
        'phone',
        'password',
        'active'
    ];
    /**
     * Заявки на диетолога
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function dietitianApplications()
    {
        return $this->hasMany(Application::class);
    }


    /**
     * Логи активации пользователем других пользователей
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function activationLogs()
    {
        return $this->hasMany(UserActivationLog::class);
    }
    public function plans()
    {
        return $this->hasMany(UserPlans::class)->latest();
    }
    public function currentPlan()
    {
        return $this->plans()->latest()->first();
    }
    /**
     * Рецепты, которые пользователь добавил в избранное
     * 
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function recipes(): BelongsToMany
    {
      return $this->belongsToMany(Recipes::class, 'recipes_favorites');
    }
    /**
     * Рецепты, которые пользовател создал
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ownRecipes(): HasMany
    {
        return $this->hasMany(Recipes::class);
    }
    /**
     * Оценки пользователя
     * 
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(RecipesScore::class);
    }
    /**
     * Роли пользователя
     * 
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function scopeWhereHasRoles($query, $roles)
    {
        if (empty($roles)) {
            return $query;
        }

        // Преобразуем в массив, если передана строка
        $roles = is_array($roles) ? $roles : [$roles];

        return $query->whereHas('roles', function($q) use ($roles) {
            $q->whereIn('name', $roles);
        });
    }

    /**
     * Получить всех пользователей с определенной ролью
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string|array $roles
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWhereHasAnyRoles($query, $roles)
    {
        if (empty($roles)) {
            return $query;
        }

        $roles = is_array($roles) ? $roles : [$roles];

        return $query->where(function($query) use ($roles) {
            foreach ($roles as $role) {
                $query->orWhereHas('roles', function($q) use ($role) {
                    $q->where('name', $role);
                });
            }
        });
    }

    /**
     * Получить всех пользователейпо имени или email или телефону
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $search
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeSearchByNameOrEmail($query, $search)
    {
        if (empty($search)) return;
        
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
    
}
