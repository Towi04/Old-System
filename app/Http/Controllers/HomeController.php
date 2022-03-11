<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Jenssegers\Date\Date;

use App\Models\Documento; 
use App\Models\AbonoDocumento; 
use App\Models\Grupo;
use App\Services\PagoInscripcionDocumentosService;

use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }

    public function ver_archivo($modulo, $id, $archivo = null)
    {
        $storage_path = storage_path();
        $public_path = public_path();

        if ($id === 'no') { //Si no se manda id, se busca en la carpeta temporal el archivo

            $url = "{$storage_path}/app/temp/{$archivo}";

            # verificamos si el archivo existe y lo retornamos
            if (!Storage::exists("temp/{$archivo}")) {
                # Cambiar por una imagen generica
                return response()->download("{$public_path}/no_image/{$modulo}.png");
            }

            return response()->download($url);
        } else {
            $url = "{$storage_path}/app/{$modulo}/{$id}/{$archivo}";

            # verificamos si el archivo existe y lo retornamos
            if (Storage::exists("{$modulo}/{$id}/{$archivo}") && $archivo != '') {

                if (Str::endsWith($url, '.pdf')) {
                    return Response::make(
                        file_get_contents($url),
                        200,
                        [
                            'Content-Type' => 'application/pdf',
                            'Content-Disposition' => 'inline;',
                        ]
                    );
                }

                if (Str::endsWith($url, '.txt')) {
                    return Response::make(
                        file_get_contents($url),
                        200,
                        [
                            'Content-Type' => 'text/html',
                            'Content-Disposition' => 'inline;',
                        ]
                    );
                }

                if (Str::endsWith($url, '.jpg') || Str::endsWith($url, '.png') || Str::endsWith($url, '.gif') || Str::endsWith($url, '.JPG') || Str::endsWith($url, '.PNG') || Str::endsWith($url, '.jpeg') ) {
                    return Response::make(
                        file_get_contents($url),
                        200,
                        [
                            'Content-Type' => 'image/jpeg',
                            'Content-Disposition' => 'inline;',
                        ]
                    );
                }


                return response()->download($url);

            } else {
                return response()->download("{$public_path}/no_image/{$modulo}.png");
            }
        }
    }

    public function generar_documentos(PagoInscripcionDocumentosService $pids){
        

        #borramos todos los documentos
        Documento::query()->delete();

        #obtenemos todos los grupos
        $grupos = Grupo::with('alumnos')->whereIn('id',['168'])->get();
        foreach($grupos as $grupo){
            #OBTENEMOS TODOS LOS ALUMNOS DEL GRUPO
            $alumnos = $grupo->alumnos;
            // dd($alumnos);
            foreach($alumnos as $alumno){
                $pids->setAlumno($alumno);
                $pids->inscripcion($grupo, $grupo->precio_inscripcion);
                $fecha_inicio = $grupo->fecha_inicio;
                $forma_pago = ($alumno->forma_pago) ? $alumno->forma_pago :'semanal';
                

                if($forma_pago == 'mensual'){
                    $this->generar_mensuales($grupo, $alumno );
                    
                }
                if($forma_pago == 'semanal'){
                    $this->generar_semanales($grupo, $alumno);
                }

            }

        }

        #GENERAR ABONOS DE LA SUCURSAL DE CELAYA
        $this->generar_abonos(2);

    }

    public function generar_mensuales($grupo, $alumno ){

        
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
            $documento = $alumno->documentos()->create([
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

            echo "Fecha {$fecha_inicio->format('d-m-Y')} | {$alumno->fullname}<br>";
            $fecha_inicio->addMonth();
            

        }   
        


    }

    public function generar_semanales($grupo, $alumno ){
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
        $monto =  $grupo->precio_semanal ?? 0;
        #SE PREGUNTA SI EL MES ACTUAL MAS 1 ES IGUAL A LA FECHA DE INICIO PARA SALIR DEL CICLO
        #SI NO SE SIGUEN GENERANDO PAGOS MENSUALE
        while(!$today->copy()->addWeek()->isSameWeek($fecha_inicio) && $fecha_inicio->lte($today->copy()->addWeek())){
            $documento = $alumno->documentos()->create([
                'id_grupo'                  => $grupo->id,
                'concepto'                  => config('alumnos.concepto.colegiatura') .' de semana #'.$fecha_inicio->weekOfYear.' del '.$fecha_inicio->year,
                'monto'                     => $monto,
                'saldo'                     => $monto,
                'monto_apoyo_inscripcion'   => 0,
                'mes'                       => $fecha_inicio->weekOfYear,
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

    public function generar_abonos($id_sucursal){

        #borramos todos los documentos
        AbonoDocumento::query()->delete();
        // dd('hola');
        #obtenemos todos los grupos de la sucursal
        // $grupos = Grupo::with('alumnos')->where('id_sucursal','=',$id_sucursal)->get();
        $grupos = Grupo::with('alumnos')->where('id','=','168')->get();

        foreach($grupos as $grupo){

            foreach($grupo->alumnos as $alumno){
                $pagos = $alumno->pagos_caja;
                #Para cada pago realizado se van a crear los abnos a los documentos del mas antiguo al mas reciente
                foreach($pagos as $pago){

                    $monto_pago = $pago->monto;
                    $documentos = $alumno->documentos->where('saldo','>',0)->sortBy('fecha_limite');

                    foreach($documentos as $documento){
                        # GENERO EL ABONO
                        #SE VA A VALIDAR SI FUE COLEGIATURA POR PRONTO PAGO
                        if($documento->tipo == 'Colegiatura'){
                            // validar fecha limite de pronto pago
                            $fecha_limite_pronto = Carbon::createFromFormat('Y-m-d',$documento->anio.'-'.$documento->mes.'-06');

                            if($pago->fecha->lte($fecha_limite_pronto) &&  $documento->monto == $grupo->precio_mensualidad ){
                                $documento->monto = $grupo->precio_mensualidad_pronto_pago;
                                $documento->saldo = $grupo->precio_mensualidad_pronto_pago;
                                $documento->save();
                            }
                        }

                        if($documento->saldo >= $monto_pago){
                            $monto = $monto_pago;
                            $monto_pago = 0;
                        }else{
                            $monto = $documento->saldo;
                            $monto_pago = $monto_pago - $documento->saldo;
                        }

                        $pago->abonos_documentos()->create([
                            'id_sucursal'       => $id_sucursal,
                            'id_documento'    => $documento->id,
                            'monto'             => $monto,
                            'venta_fiscal'      => '0',
                        ]);

                        $documento->saldo = $documento->saldo - $monto;
                        if($documento->saldo == 0){
                            $documento->status = 'Pagado';
                        }
                        $documento->save();
                        
                        if($monto_pago <= 0){
                            break;
                        }
                        

                    }



                }
            }
            
        }
        


    }

}
