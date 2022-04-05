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

    public function setFechaActual($fecha_actual)
    {
        $this->fecha_actual = $fecha_actual;

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

        

        $this->fecha_actual = $fecha_inicio;

        $fecha_inicio = new Date($fecha_inicio);
        $today = Carbon::today();

        #VALIDAMOS SI SE VAN A GENERAR PRONTO PAGO O NORMAL

        $dia = $today->day;

        
       
        #SE PREGUNTA SI EL MES ACTUAL MAS 1 ES IGUAL A LA FECHA DE INICIO PARA SALIR DEL CICLO
        #SI NO SE SIGUEN GENERANDO PAGOS MENSUALE
        while(!$today->copy()->addMonth()->isSameMonth($fecha_inicio) && $fecha_inicio->lte($today->copy()->addMonth())){

            
            $result =  $this->calcular_precio_mensual($grupo, $alumno);
                
                


            $documento = $alumno->documentos()->create([
                'id_grupo'                  => $grupo->id,
                'concepto'                  => config('alumnos.concepto.colegiatura') .' de '.$fecha_inicio->format('F').' del '.$fecha_inicio->year,
                'monto'                     => $result['monto'],
                'saldo'                     => $result['monto'],
                'monto_apoyo_inscripcion'   => 0,
                'mes'                       => $fecha_inicio->month,
                'anio'                      => $fecha_inicio->year,
                'fecha_limite'              => $fecha_inicio->copy()->endOfMonth(),
                'modalidad'                 => 'mensual',
                'tipo'                      => config('alumnos.concepto.colegiatura'),
                'status'                    => config('pagos.status.Pendiente'),
                'especial'                  => $result['especial'],
            ]);

            echo "Fecha {$fecha_inicio->format('d-m-Y')} | {$alumno->fullname}<br>";
            // dd($fecha_inicio->addMonthNoOverflow());
            $fecha_inicio->addMonthNoOverflow()->startOfMonth();
            $this->fecha_actual = $fecha_inicio;
        }   
        
            
        }
    }

    private function calcular_precio_mensual($grupo, $alumno)
    {
        $fecha_inicio = $this->fecha_actual;
        $apoyo_especial = $alumno ->apoyos_especiales->where('id_grupo', $grupo->id)->filter(function($apoyo)use($fecha_inicio){
            return $apoyo->fecha_inicio->lte($fecha_inicio) && $apoyo->fecha_final->gte($fecha_inicio);
        })->first();


        if (empty($apoyo_especial)) {

            $dia = $this->fecha_actual->day;

            # SI EL DIA ACTUAL ES ENTRE 1-6, ENTONCES SE ASIGNA EL PRECIO DE PRONTO PAGO
            if (in_array($dia, range(1, 6))) {
                return [
                    'monto'=> $grupo->precio_mensualidad_pronto_pago ?? 0,
                    'especial'=>0,
                ];
            }else{
                #SE CALCULA DE ACUERDO A LAS CLASES RESTANTES QUE TENGA EN EL MES
                    
                    $precio_mensualidad = $grupo->precio_mensualidad;
            
                    $dia_actual = $fecha_inicio;
                    $mes_actual = $dia_actual->month;
                    

                    $lista_numero_dias = [
                        'lunes'     => 1,
                        'martes'    => 2,
                        'miercoles' => 3,
                        'jueves'    => 4,
                        'viernes'   => 5,
                        'sabado'    => 6,
                        'domingo'   => 7,
                    ];
            
                    $total_dias = 0;
                    $dias_pendientes = 0;
            
                    $days = $grupo->days;
                    
                    foreach ($days as $grupodia) {
                        $numero_dia =  $lista_numero_dias[$grupodia->dia] ?? 0;

                        $total_dias += countDaysInMonth($mes_actual,$numero_dia);
                        $dias_pendientes += countDaysInMonth($dia_actual,$numero_dia);

                    }
                    $precio = ($total_dias == 0) ? 0 : $dias_pendientes * $grupo->precio_mensualidad / $total_dias;
                    
                    return [
                        'monto'=>$precio ?? 0,
                        'especial'=>1,
                    ];

            }
            # SE AGREGA EL PRECIO NORMAL DE LA MENSUALIDAD

            return [
                'monto'=> $grupo->precio_mensualidad ?? 0,
                'especial'=>0,
            ];
        }

        return [
            'monto'=>$apoyo_especial->precio ?? 0,
            'especial'=>1,
        ];
    }

    public function semanal()
    {
        $alumno = $this->alumno;

        Carbon::setWeekStartsAt(Carbon::SUNDAY);
        Carbon::setWeekEndsAt(Carbon::SATURDAY);

        $today = Carbon::today();

        $grupos = $this->alumno->grupos->filter(function($grupo)use($today){
            return $grupo->fecha_inicio->lte($today);
        });

        foreach ($grupos as $grupo) {
            Carbon::setWeekStartsAt(Carbon::SUNDAY);
            Carbon::setWeekEndsAt(Carbon::SATURDAY);
    
            if(optional(optional($grupo->alumnos->where('id',$alumno->id)->first())->pivot)->fecha_inicio){
                $fecha_inicio = optional(optional($grupo->alumnos->where('id',$alumno->id)->first())->pivot)->fecha_inicio;
            }else{
                $fecha_inicio = new Date($grupo->fecha_inicio);
            }
    
    
            if($fecha_inicio->isSunday()){
                $fecha_inicio->addDay();
            }
            $today = Carbon::today();
    
            # SE AGREGA EL PRECIO NORMAL DE LA MENSUALIDAD
    
           
    
            #SE PREGUNTA SI EL MES ACTUAL MAS 1 ES IGUAL A LA FECHA DE INICIO PARA SALIR DEL CICLO
            #SI NO SE SIGUEN GENERANDO PAGOS MENSUALE
            while(!$today->copy()->addWeek()->isSameWeek($fecha_inicio) && $fecha_inicio->lte($today->copy()->addWeek())){
    
                 #BUSCA UN APOYO SI EXISTE EN ESA SEMANA 
                $apoyos = $alumno ->apoyos_especiales->where('id_grupo', $grupo->id)->filter(function($apoyo)use($fecha_inicio){
                    return $apoyo->fecha_inicio->lte($fecha_inicio) && $apoyo->fecha_final->gte($fecha_inicio);
                });
    
                if($apoyos->first()){
                    $monto =  $apoyos->first()->precio ?? 0;
                }else{
                    $monto =  $grupo->precio_semanal ?? 0;
                }
                
                
                $documento = $alumno->documentos()->create([
                    'id_grupo'                  => $grupo->id,
                    'concepto'                  => config('alumnos.concepto.colegiatura') .' de semana #'.$fecha_inicio->weekOfYear.' del '.$fecha_inicio->year,
                    'monto'                     => $monto,
                    'saldo'                     => $monto,
                    'monto_apoyo_inscripcion'   => 0,
                    'semana'                    => $fecha_inicio->weekOfYear,
                    'anio'                      => $fecha_inicio->year,
                    'fecha_limite'              => $fecha_inicio->copy()->endOfWeek(),
                    'modalidad'                 => 'mensual',
                    'tipo'                      => config('alumnos.concepto.colegiatura'),
                    'status'                    => config('pagos.status.Pendiente'),
                ]);
    
                echo "Semana {$fecha_inicio->weekOfYear} fin_semana {$fecha_inicio->copy()->endOfWeek()}| Fecha {$fecha_inicio->format('d-m-Y')} | {$alumno->fullname}<br>";
                $fecha_inicio->addWeek();
                
    
            }   
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

    

    private function apoyo_especial($grupo, $alumno)
    {
        $apoyo_especial = ApoyoEspecial::toBase()
            ->where('id_alumno', $alumno->id)
            ->where('id_grupo', $grupo->id)
            ->whereRaw('CAST(fecha_final AS date) > cast( NOW() AS date) AND ')
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
