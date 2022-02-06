<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Date\Date;

class Asistencia extends Model
{
    use HasFactory;

    protected $table = 'asistencias';

    protected $dates = [
        'fecha'
    ];

    public function getFechaAttribute($value){
        return new Date($value);
    }

    /**
     * Get the personal t
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function personal()
    {
        return $this->belongsTo(User::class, 'id_usuario', 'id');
    }
}
