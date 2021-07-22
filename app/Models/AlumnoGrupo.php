<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AlumnoGrupo extends Pivot
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'alumnos_grupos';

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    public function alumno()
    {
        return $this->belongsTo(Alumno::class,'id_alumno','id')->withDefault();
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class,'id_grupo','id')->withDefault();
    }
}
