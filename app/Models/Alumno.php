<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Model;
use Collective\Html\Eloquent\FormAccessible;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Alumno extends Model
{
    use HasFactory,FormAccessible;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'alumnos';


    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'solicitud_factura' => 'boolean',
        'grado_estudios'    => 'array',
        'especialidad'      => 'array',
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = ['created_at', 'updated_at', 'fecha_nacimiento'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id_sucursal',

        'foto',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'edad',
        'fecha_nacimiento',
        'domicilio',
        'colonia',
        'municipio',
        'telefono',
        'celular',
        'email',
        'codigo_postal',
        'ocupacion',
        'grado_estudios',
        'otro_grado_estudios',
        'tutor',
        'especialidad',
        'otra_especialidad',
        'escuela_procedencia',
        'objetivo_inscripcion',
        'enfermedad_cronica',
        'solicitud_factura',
        'id_asesor_educativo',

        # DATOS DE FACTURACION
        'razon_social',
        'rfc',
        'cfdi',
        'curp',
        'telefono_general',
        'correo_general',
        'domicilio_fiscal',

        'observaciones',
        'forma_pago',
    ];

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class,'id_sucursal','id')->withDefault();
    }

    public function asesor_educativo()
    {
        return $this->belongsTo(User::class,'id_asesor_educativo','id')->withDefault();
    }

    public function grupos()
    {
        return $this->belongsToMany(Grupo::class, 'alumnos_grupos', 'id_alumno','id_grupo')
            ->withPivot('id','id_grupo')->using(AlumnoGrupo::class);
    }

    public function pagos()
    {
        return $this->hasMany(AlumnoPago::class,'id_alumno','id');
    }

    public function getFullnameAttribute()
    {
        return $this->nombres . ' ' . $this->apellido_paterno . ' ' . $this->apellido_materno;
    }

    # NOTE: Form Model Accessors (Laravel Collective) https://laravelcollective.com/docs/5.4/html

    public function formFechaNacimientoAttribute($value)
    {
        if (empty($value)) {
            return null;
        }

        return Carbon::parse($value)->format('Y-m-d');
    }
}
