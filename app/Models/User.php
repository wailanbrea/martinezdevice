<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'firstname',
        'lastname',
        'email',
        'password',
        'address',
        'city',
        'country',
        'postal',
        'about'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Always encrypt the password when it is updated.
     *
     * @param $value
    * @return string
    */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = bcrypt($value);
    }

    /**
     * Relación muchos a muchos con roles
     */
    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'role_user', 'user_id', 'role_id');
    }

    /**
     * Verificar si el usuario tiene un rol específico
     */
    public function hasRole($role)
    {
        return $this->roles()->where('slug', $role)->exists();
    }

    /**
     * Reparaciones asignadas al usuario (técnico)
     */
    public function reparaciones()
    {
        return $this->hasMany(Reparacion::class, 'tecnico_id');
    }

    /**
     * Reparaciones completadas por el usuario (técnico que completó)
     */
    public function reparacionesCompletadas()
    {
        return $this->hasMany(Reparacion::class, 'tecnico_completo_id');
    }

    /**
     * Reparaciones recibidas por el usuario (recepcionista)
     */
    public function reparacionesRecibidas()
    {
        return $this->hasMany(Reparacion::class, 'recepcionista_id');
    }

    /**
     * Cambios de estado realizados por el usuario
     */
    public function cambiosEstado()
    {
        return $this->hasMany(EstadoReparacion::class, 'usuario_id');
    }

    /**
     * Notas agregadas por el usuario
     */
    public function notas()
    {
        return $this->hasMany(NotaReparacion::class, 'usuario_id');
    }
}
