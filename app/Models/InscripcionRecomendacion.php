<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InscripcionRecomendacion extends Model
{
    use HasFactory;

    protected $table = 'inscripciones_recomendaciones';

    protected $fillable = [
        'id_alumno_recomendado',
        'id_alumno_recomendo',
        'id_especialidad',
        'id_documento_aplicado',
        'id_autorizo',
        'fecha',
        'id_sucursal',
    ];

    /**
     * Get the alumno_recomendado that owns the InscripcionRecomendacion
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function alumno_recomendado(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'id_alumno_recomendado', 'id');
    }


    /**
     * Get the alumno_recomendado that owns the InscripcionRecomendacion
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function alumno_recomendo(): BelongsTo
    {
        return $this->belongsTo(Alumno::class, 'id_alumno_recomendo', 'id');
    }


    /**
     * Get the alumno_recomendado that owns the InscripcionRecomendacion
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function especialidad(): BelongsTo
    {
        return $this->belongsTo(Especialidad::class, 'id_especialidad', 'id');
    }

    /**
     * Get the alumno_recomendado that owns the InscripcionRecomendacion
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function documento_aplicado(): BelongsTo
    {
        return $this->belongsTo(Especialidad::class, 'id_documento_aplicado', 'id');
    }

    /**
     * Get the usuario_autorizo t
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function usuario_autorizo()
    {
        return $this->belongsTo(User::class, 'id_usuario_autorizo', 'id');
    }

    /**
     * Get the usuario_autorizo t
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class, 'id_sucursal', 'id');
    }
}
