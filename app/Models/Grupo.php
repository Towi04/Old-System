<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Collective\Html\Eloquent\FormAccessible;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Grupo extends Model
{
    use HasFactory,FormAccessible;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'grupos';

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'infantil' => 'bool',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'fecha_inicio'];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'tipo_grupo',
        'fecha_inicio_format'
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_sucursal',
        'id_especialidad',
        'horario',
        'dias',
        'infantil',
        'fecha_inicio',
        'inscripcion',
        'colegiatura',
    ];

    public function especialidad()
    {
        return $this->belongsTo(Especialidad::class,'id_especialidad','id')->withDefault([
            'nombre'        => '',
            'descripcion'   => '',
        ]);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class,'id_sucursal','id')->withDefault();
    }

    public function materias()
    {
        return $this->belongsToMany(Materia::class, 'grupos_materias', 'id_grupo', 'id_materia')
            ->withPivot('id','id_profesor','horas_semana')->using(GrupoMateria::class);
    }

    public function alumnos()
    {
        return $this->belongsToMany(Alumno::class, 'alumnos_grupos', 'id_grupo', 'id_alumno')
            ->withPivot('id','id_alumno')->using(AlumnoGrupo::class);
    }


    public function getTipoGrupoAttribute()
    {
        $tipo_grupo = ($this->infantil)? 'Infantil':'Adulto';

        return $tipo_grupo;

    }

    public function getFechaInicioFormatAttribute()
    {
        if (empty($this->fecha_inicio)) {
            return null;
        }

        return Carbon::parse($this->fecha_inicio)->format('Y-m-d');

    }

     # NOTE: Form Model Accessors (Laravel Collective) https://laravelcollective.com/docs/5.4/html

     public function formFechaInicioAttribute($value)
     {
         if (empty($value)) {
             return null;
         }

         return Carbon::parse($value)->format('Y-m-d');
     }
}
