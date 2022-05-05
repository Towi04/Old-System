<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApoyoInscripcion extends Model
{
    use HasFactory;

    protected $table = 'apoyos_inscripciones';

    protected $fillable = [
        'id_alumno',
        'id_grupo',
        'apoyo',
        'id_usuario',
        'id_usuario_autoriza'
    ];

    /**
     * Get the alumno
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */

    public function alumno()
    {
        return $this->belongsTo(Alumno::class, 'id_alumno', 'id');
    }

    /**
     * Get the alumno t
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'id_grupo', 'id');
    }

    /**
     * Get the alumno t
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }
}
