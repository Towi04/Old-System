<?php

namespace App\Services;

use App\Models\Alumno;
use App\Models\ApoyoEspecial;
use Illuminate\Support\Carbon;

use Jenssegers\Date\Date;

class PagoColegiaturaDocumentosService
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
        $alumno = $this->alumno;

        $today = Carbon::today();

        $grupos = $this->alumno->grupos->filter(function($grupo)use($today){
            return $grupo->fecha_inicio->lte($today);
        });

        foreach ($grupos as $grupo) {

            if(optional(optional($grupo->alumnos->where('id',$alumno->id)->first())->pivot)->fecha_inicio){
                $fecha_inicio = optional(optional($grupo->alumnos->where('id',$alumno->id)->first())->pivot)->fecha_inicio;
            }else{
                $fecha_inicio = new Date($grupo->fecha_inicio);
            }

            $fecha_inicio = new Date($fecha_inicio);
            $today = Carbon::today();

            #VALIDAMOS SI SE VAN A GENERAR PRONTO PAGO O NORMAL
            $dia = $today->day;
            # SI EL DIA ACTUAL ES ENTRE 1-6, ENTONCES SE ASIGNA EL PRECIO DE PRONTO PAGO
            if (in_array($dia, range(1, 6))) {
                $monto =  $grupo->precio_mensualidad_pronto_pago ?? 0;
            }
            # SE AGREGA EL PRECIO NORMAL DE LA MENSUALIDAD
            $monto =  $grupo->precio_mensualidad ?? 0;
            #SE PREGUNTA SI EL MES ACTUAL MAS 1 ES IGUAL A LA FECHA DE INICIO PARA SALIR DEL CICLO
            #SI NO SE SIGUEN GENERANDO PAGOS MENSUALE
            while(!$today->copy()->addMonth()->isSameMonth($fecha_inicio) && $fecha_inicio->lte($today->copy()->addMonth())){
                $documento = $this->crear_documento([
                    'id_grupo'                  => $grupo->id,
                    'concepto'                  => config('alumnos.concepto.colegiatura') .' de '.$fecha_inicio->format('F').' del '.$fecha_inicio->year,
                    'monto'                     => $monto,
                    'saldo'                     => $monto,
                    'monto_apoyo_inscripcion'   => 0,
                    'mes'                       => $fecha_inicio->month,
                    'anio'                      => $fecha_inicio->year,
                    'fecha_limite'              => $fecha_inicio->copy()->endOfMonth(),
                    'modalidad'                 => 'mensual',
                    'tipo'                      => config('alumnos.concepto.colegiatura'),
                    'status'                    => config('pagos.status.Pendiente'),
                ]);

                // echo "Fecha {$fecha_inicio->format('d-m-Y')} | {$alumno->fullname}<br>";
                $fecha_inicio->addMonth();
                

            }   
            
        }
    }

    public function semanal()
    {
        Carbon::setWeekStartsAt(Carbon::SUNDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);

        $today = Carbon::today();

        $grupos = $this->alumno->grupos->filter(function($grupo)use($today){
            return $grupo->fecha_inicio->lte($today);
        });

        foreach ($grupos as $grupo) {
            $precio_semanal = $this->calcular_precio_semanal($grupo, $this->alumno);

            $dias_de_la_semana = 7;
            $fecha_inicio = $this->fecha_actual->copy()->startOfWeek(Carbon::SUNDAY);
            $fecha_final = $this->fecha_actual->copy()->endOfWeek(Carbon::SATURDAY);;
            
            if($fecha_inicio->isSunday()){
                $fecha_inicio->addDay();
            }
            // dd($fecha_inicio);
            // $dias_transcurridos = $fecha_inicio->diffInDays($fecha_final);
            // $semanal = ($dias_transcurridos * $precio_semanal) / $dias_de_la_semana;
            $semanal =  $precio_semanal;
            $this->crear_documento([
                'id_grupo'      => $grupo->id,
                'concepto'      => config('alumnos.concepto.colegiatura') .' de semana #'.$fecha_inicio->weekOfYear.' del '.$fecha_inicio->year,
                'monto'         => $semanal,
                'saldo'         => $semanal,
                'tipo'          => config('alumnos.concepto.colegiatura'),
                'fecha_limite'  => $fecha_final,
                'semana'        => $fecha_inicio->week,
                'anio'          => $fecha_inicio->year,
                'modalidad'     => 'semanal',
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
            if (in_array($dia, range(1, 6))) {
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

    private function crear_documento(array $atributos)
    {
        $fields = [
            'semanal' => 'semana',
            'mensual' => 'mes'
        ];

        $field = $fields[$atributos['modalidad']];

        # SE DEBE VERIFICAR SI EXISTEN DOCUMENTOS QUE FUERON CREADOS POR ADELANTADO
        $existe_documento = $this->alumno->documentos()
            ->where('id_grupo', $atributos['id_grupo'])
            ->where('modalidad', $atributos['modalidad'])
            ->where('anio', $atributos['anio'])
            ->where($field, $atributos[$field] )
            ->whereYear('created_at', $this->fecha_actual->year)
            ->exists();

        // dd( $atributos[$field]);
        

        # SI NO EXISTE DOCUMENTO , SE DEBE GENERAR
        if (!$existe_documento) {
            $this->alumno->documentos()->create($atributos);
        }
    }
}
