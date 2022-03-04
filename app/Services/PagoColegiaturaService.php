<?php

namespace App\Services;

use App\Models\Alumno;
use App\Models\ApoyoEspecial;
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

            $precio_mensualidad = $this->calcular_precio_mensual($grupo, $this->alumno);

            $dias_del_mes = $this->fecha_actual->copy()->daysInMonth;
            $fecha_inicio = $this->fecha_actual->copy()->firstOfMonth();
            $fecha_final = $this->fecha_actual->copy();
            $dias_transcurridos = $fecha_inicio->diffInDays($fecha_final);
            $dias_faltan = $dias_del_mes - $dias_transcurridos;
            $mensualidad = ($dias_faltan * $precio_mensualidad) / $dias_del_mes;
            $fecha_mes = new Date($fecha_inicio);

            $this->alumno->pagos()->create([
                'id_grupo'      => $grupo->id,
                'concepto'      => config('alumnos.concepto.colegiatura') . ' ' . $fecha_mes->format('F \d\e\l Y'),
                'tipo'          => config('alumnos.concepto.colegiatura'),
                'monto'         => $mensualidad,
                'saldo'         => $mensualidad,
                'fecha_limite'  => $fecha_inicio->endOfMonth(),
            ]);
        }
    }

    public function semanal()
    {
        $grupos = $this->alumno->grupos;

        foreach ($grupos as $grupo) {
            $precio_semanal = $this->calcular_precio_semanal($grupo, $this->alumno);

            $dias_de_la_semana = 7;
            $fecha_inicio = $this->fecha_actual->copy()->startOfWeek(Carbon::SUNDAY);
            $fecha_final = $this->fecha_actual->copy();
            $dias_transcurridos = $fecha_inicio->diffInDays($fecha_final);
            $semanal = ($dias_transcurridos * $precio_semanal) / $dias_de_la_semana;

            $this->alumno->pagos()->create([
                'id_grupo'      => $grupo->id,
                'concepto'      => config('alumnos.concepto.colegiatura'),
                'monto'         => $semanal,
                'saldo'         => $semanal,
                'tipo'          => config('alumnos.concepto.colegiatura'),
                'fecha_limite'  => optional($grupo->fecha_inicio)->copy(),
            ]);
        }
    }

    private function calcular_precio_semanal($grupo, $alumno)
    {
        $apoyo_especial = $this->apoyo_especial($grupo, $alumno);

        if (empty($apoyo_especial)) {
            return  $grupo->precio_semanal ?? 0;
        }

        return $apoyo_especial->precio ?? 0;
    }

    private function calcular_precio_mensual($grupo, $alumno)
    {
        $apoyo_especial = $this->apoyo_especial($grupo, $alumno);

        if (empty($apoyo_especial)) {

            $dia = $this->fecha_actual->day;

            # SI EL DIA ACTUAL ES ENTRE 1-6, ENTONCES SE ASIGNA EL PRECIO DE PRONTO PAGO
            if (in_array($dia,range(1,6))) {
                return  $grupo->precio_mensualidad_pronto_pago ?? 0;
            }

            # SE AGREGA EL PRECIO NORMAL DE LA MENSUALIDAD
            return  $grupo->precio_mensualidad ?? 0;
        }

        return $apoyo_especial->precio ?? 0;
    }


    private function apoyo_especial($grupo, $alumno)
    {
        $apoyo_especial = ApoyoEspecial::toBase()
            ->where('id_alumno', $alumno->id)
            ->where('id_grupo', $grupo->id)
            ->whereRaw('CAST(fecha_final AS date) > cast( NOW() AS date)')
            ->orderBy('fecha_final')
            ->first();

        return $apoyo_especial;
    }
}
