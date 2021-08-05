<?php

namespace App\Services;

use App\Models\Alumno;
use Illuminate\Support\Carbon;

use Jenssegers\Date\Date;

class PagoColegiaturaService
{
    protected $alumno;

    protected $fecha_actual;

    public function setAlumno(Alumno $alumno)
    {
        $this->alumno = $alumno;

        $this->fecha_actual = today();

        return $this;
    }

    public function mensual()
    {
        $grupos = $this->alumno->grupos;

        foreach ($grupos as $grupo) {

            $precio_mensualidad = $grupo->precio_mensualidad ?? 0;
            $dias_del_mes = $this->fecha_actual->copy()->daysInMonth;
            $fecha_inicio = $this->fecha_actual->copy()->firstOfMonth();
            $fecha_final = $this->fecha_actual->copy();
            $dias_transcurridos = $fecha_inicio->diffInDays($fecha_final);
            $dias_faltan = $dias_del_mes - $dias_transcurridos;
            $mensualidad = ($dias_faltan * $precio_mensualidad) / $dias_del_mes;
            $fecha_mes = new Date($fecha_inicio);
            
            $this->alumno->pagos()->create([
                'id_grupo'      => $grupo->id,
                'concepto'      => config('alumnos.concepto.colegiatura').' '.$fecha_mes->format('F \d\e\l Y'),
                'monto'         => $mensualidad,
                'fecha_limite'  => $fecha_inicio->endOfMonth(),
            ]);
        }
    }

    public function semanal()
    {
        $grupos = $this->alumno->grupos;

        foreach ($grupos as $grupo) {
            $precio_semanal = $grupo->precio_semanal ?? 0;

            $dias_de_la_semana = 7;
            $fecha_inicio = $this->fecha_actual->copy()->startOfWeek(Carbon::MONDAY);
            $fecha_final = $this->fecha_actual->copy();
            $dias_transcurridos = $fecha_inicio->diffInDays($fecha_final);
            $dias_faltan = $dias_de_la_semana - $dias_transcurridos;
            $semanal = ($dias_transcurridos * $precio_semanal) / $dias_de_la_semana;

            $this->alumno->pagos()->create([
                'id_grupo'      => $grupo->id,
                'concepto'      => config('alumnos.concepto.colegiatura'),
                'monto'         => $semanal,
                'fecha_limite'  => optional($grupo->fecha_inicio)->copy(),
            ]);
        }
    }
}
