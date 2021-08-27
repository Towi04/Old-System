<?php

namespace App\Services;

use App\Models\Alumno;
use App\Models\Grupo;
use Illuminate\Support\Carbon;
use Jenssegers\Date\Date;

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
           $this->mensualPorGrupo($grupo);
        }
    }

    public function semanal()
    {
        $grupos = $this->alumno->grupos;

        foreach ($grupos as $grupo) {
            $this->semanalPorGrupo($grupo);
        }
    }

    public function mensualPorGrupo(Grupo $grupo)
    {
       $this->inscripcion($grupo);

        $precio_semanal = $grupo->precio_semanal ?? 0;

        if ($grupo->fecha_inicio->greaterThan($this->fecha_actual)) {
              # EL GRUPO YA COMENZO
              $fecha_inicio = $grupo->fecha_inicio->copy();
              $fecha_mes = $grupo->fecha_inicio->copy();
              $fecha_final = $grupo->fecha_inicio->copy()->lastOfMonth();
        }else{
            # EL GRUPO NO HA COMENZADO
            $fecha_inicio = $this->fecha_actual->copy();
            $fecha_mes =  $this->fecha_actual->copy();
            $fecha_final = $this->fecha_actual->copy()->lastOfMonth();

        }

        

        while($fecha_inicio->next('Saturday') &&  $fecha_inicio->isSameMonth($fecha_mes, true) )
        {
            $formato_fecha = new Date($fecha_inicio);
            $concepto = config('alumnos.concepto.colegiatura').' de la semana '.$formato_fecha->week.' de  '.$formato_fecha->format('F \d\e\l Y');

            $this->alumno->pagos()->create([
                'id_grupo'      => $grupo->id,
                'concepto'      => $concepto,
                'monto'         => $precio_semanal,
                'saldo'         => $precio_semanal,
                'fecha_limite'  => $fecha_inicio,
                'status'        => config('pagos.status.Pendiente'),
            ]);
        }
    }

    public function semanalPorGrupo(Grupo $grupo)
    {
       $this->inscripcion($grupo);

        $precio_semanal = $grupo->precio_semanal ?? 0;

        if ($grupo->fecha_inicio->greaterThan($this->fecha_actual)) {
            $fecha_inicio = $grupo->fecha_inicio->copy();
        }else{
            $fecha_inicio = $this->fecha_actual->copy();
        }

        $this->alumno->pagos()->create([
            'id_grupo'      => $grupo->id,
            'concepto'      => config('alumnos.concepto.colegiatura').'Semana: '.$grupo->fecha_inicio->week .' del '.$grupo->fecha_inicio->year,
            'monto'         => $precio_semanal,
            'fecha_limite'  => $fecha_inicio->endOfWeek(Carbon::SATURDAY),
            'status'        => config('pagos.status.Pendiente'),
        ]);
    }

    private function inscripcion(Grupo $grupo)
    {
        $this->alumno->pagos()->create([
            'id_grupo'      => $grupo->id,
            'concepto'      => config('alumnos.concepto.inscripcion').' del '.$grupo->fecha_inicio->year,
            'monto'         => $grupo->precio_inscripcion ?? 0,
            'fecha_limite'  => $grupo->fecha_inicio,
            'status'        => config('pagos.status.Pagado'),
        ]);
    }
}
