<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Request;


class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'nombres',
        'foto',
        'telefono',
        'celular',
        'apellido_paterno',
        'apellido_materno',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getFullnameAttribute()
    {
        return $this->nombres . ' ' . $this->apellido_paterno . ' ' . $this->apellido_materno;
    }

    public function setPasswordAttribute($valor)
    {
        if (!empty($valor)) {

            if (Request::url() != url('/') . "/password/reset") {
                $this->attributes['password'] = bcrypt($valor);
            } else {
                $this->attributes['password'] = $valor;
            }
        }
    }

    public function getNameRoleUserAttribute()
    {
        $roles = $this->roles();

        if (is_null($roles)) {
            return '(Sin Rol)';
        }

        return $roles->pluck('display_name')->implode(',');
    }

   

    public function sucursales()
    {
        return $this->belongsToMany(Sucursal::class, 'sucursales_usuarios', 'id_usuario', 'id_sucursal');
    }

    public function getSucursalesUserAttribute()
    {
        $sucursales = $this->sucursales();

        if (is_null($sucursales)) {
            return '(Sin Rol)';
        }

        return $sucursales->pluck('nombre')->implode(',');
    }
}
