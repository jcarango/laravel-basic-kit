<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles, LogsActivity {
        HasRoles::hasPermissionTo as traitHasPermissionTo;
    }

    protected $fillable = [
        'name',
        'lastname',
        'phone',
        'address',
        'country_id',
        'state_id',
        'city_id',
        'avatar',
        'email',
        'password',
        'is_active',
        'monthly_goal',
        'habeas_data_accepted'
    ];

    protected string $guard_name = 'web';

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Avatar para Filament
    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar 
            ? asset('storage/' . $this->avatar) 
            : null;
    }

    // Relaciones geográficas
    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function suffragans()
    {
        return $this->hasMany(Suffragan::class);
    }

    public function resume()
    {
        return $this->hasOne(Resume::class);
    }

    // Control de acceso a Filament
    public function canAccessFilament(): bool
    {
        return $this->hasRole('super-admin') || $this->hasPermissionTo('dashboard.ver');
    }

    // Bypass para el superadmin (ID 1) y Blindaje para Testigos
    public function hasPermissionTo($permission, $guardName = null): bool
    {
        // 1. Superadmin absoluto
        if ($this->id === 1 || $this->hasRole('super-admin')) {
            return true;
        }

        // 2. Blindaje para Testigos (Solo dashboard y conteo)
        $witnessRoles = ['Testigo', 'Testigo Electoral', 'testigo-electoral'];
        if ($this->hasRole($witnessRoles)) {
            $allowedPrefixes = ['dashboard.', 'e14conteos.'];
            foreach ($allowedPrefixes as $prefix) {
                if (str_starts_with($permission, $prefix)) {
                    return $this->traitHasPermissionTo($permission, $guardName);
                }
            }
            return false; // Bloqueo total para el resto
        }

        return $this->traitHasPermissionTo($permission, $guardName);
    }

    // Configuración de logs de actividad
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('user')
            ->logOnly(['name', 'email', 'country_id', 'state_id', 'city_id'])
            ->logOnlyDirty();
    }
}
