<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Venta extends Model
{
    use HasFactory;

    protected $table = 'ventas';

    protected $dates = [
        'fecha'
    ];

    /**
     * Get all of the partidas f
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function partidas()
    {
        return $this->hasMany(PartidaVenta::class, 'id_venta', 'id');
    }


    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno', 'id')->withDefault([
            'nombres'               => '',
            'apellido_paterno'      => '',
            'apellido_materno'      => '',
            'numero_control'        => '',
            'nuevo_numero_control'  => '',
        ]);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal', 'id');
    }

    public function recibio()
    {
        return $this->belongsTo(User::class, 'id_recibio', 'id');
    }


}
