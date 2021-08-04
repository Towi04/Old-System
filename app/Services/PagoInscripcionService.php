<?php

namespace App\Services;

use App\Models\Alumno;
use App\Models\Grupo;
use Illuminate\Support\Carbon;

class PagoInscripcionService
{
    protected $alumno;

    protected $fecha_actual;

    public function __construct()
    {
        $this->alumno = new Alumno();

        $this->fecha_actual = today();
    }


    public function setAlumno(Alumno $alumno)
    {
        $this->alumno = $alumno;

        return $this;
    }

    public function mensual()
    {
        $grupos = $this->alumno->grupos;

        foreach ($grupos as $grupo) {
            $this->alumno->pagos()->create([
                'id_grupo'      => $grupo->id,
                'concepto'      => config('alumnos.concepto.inscripcion'),
                'monto'         => $grupo->precio_inscripcion ?? 0,
                'fecha_limite'  => $grupo->fecha_inicio,
            ]);

            $precio_mensualidad = $grupo->precio_mensualidad ?? 0;

            $dias_del_mes = $this->fecha_actual->copy()->daysInMonth;
            $fecha_inicio = $this->fecha_actual->copy()->firstOfMonth();
            $fecha_final = $this->fecha_actual->copy();
            $dias_transcurridos = $fecha_inicio->diffInDays($fecha_final);

            $mensualidad = ($dias_transcurridos * $precio_mensualidad ) / $dias_del_mes;


            $this->alumno->pagos()->create([
                'id_grupo'      => $grupo->id,
                'concepto'      => config('alumnos.concepto.colegiatura'),
                'monto'         => $mensualidad,
                'fecha_limite'  => optional($grupo->fecha_inicio)->copy(),
            ]);
        }
    }

    public function semanal()
    {
        $grupos = $this->alumno->grupos;

        foreach ($grupos as $grupo) {
            $this->alumno->pagos()->create([
                'id_grupo'      => $grupo->id,
                'concepto'      => config('alumnos.concepto.inscripcion'),
                'monto'         => $grupo->precio_inscripcion ?? 0,
                'fecha_limite'  => $grupo->fecha_inicio,
            ]);


            $precio_semanal = $grupo->precio_semanal ?? 0;

            $dias_de_la_semana = 7;
            $fecha_inicio = $this->fecha_actual->copy()->startOfWeek(Carbon::MONDAY);
            $fecha_final = $this->fecha_actual->copy();
            $dias_transcurridos = $fecha_inicio->diffInDays($fecha_final);

            $semanal = ($dias_transcurridos * $precio_semanal ) / $dias_de_la_semana;

            $this->alumno->pagos()->create([
                'id_grupo'      => $grupo->id,
                'concepto'      => config('alumnos.concepto.colegiatura'),
                'monto'         => $semanal,
                'fecha_limite'  => optional($grupo->fecha_inicio)->copy(),
            ]);

        }
    }
}
