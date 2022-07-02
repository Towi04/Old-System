<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Especialidad extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'especialidades';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_sucursal',
        'nombre',
        'descripcion',
        'precio_inscripcion',
        'precio_mensualidad',
        'precio_mensualidad_pronto_pago',
        'precio_semanal',
    ];

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'id_especialidad', 'id');
    }

    public function grupos_activos()
    {
        return $this->hasMany(Grupo::class,'id_especialidad', 'id')
            ->where('status',config('grupos.status.values.Activo'));
    }

    public function cordinadores()
    {
        return $this->belongsToMany(User::class, 'especialidades_users', 'id_especialidad', 'id_usuario')
            ->withPivot('id_usuario')
            ->using(EspecialidadUser::class);
    }

    /**
     * Get all of the precios f
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function precios()
    {
        return $this->hasMany(Precio::class, 'id_especialidad', 'id');
    }
}
