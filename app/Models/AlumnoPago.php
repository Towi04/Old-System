<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class AlumnoPago extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'alumnos_pagos';

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at','updated_at','fecha_limite'];


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_alumno',
        'id_grupo',
        'concepto',
        'monto',
        'saldo',
        'fecha_limite',
        'status',
    ];

    protected $attributes = [
        'status' => 'pendiente'
    ];

    public function alumno()
    {
        return $this->belongsTo(Alumno::class,'id_alumno','id')->withDefault();
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class,'id_grupo','id')->withDefault();
    }

    public function abonos()
    {
        return $this->hasMany(Abono::class,'id_alumno_pago','id');
    }

    # NOTE: SCOPES

    public function scopePendientes($query)
    {
        return $query->where('status',config('pagos.status.pendiente'));
    }
}
