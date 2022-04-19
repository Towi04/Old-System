<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Date\Date;

use App\Models\Documento; 
use App\Models\AbonoDocumento; 
use App\Models\Grupo;
use App\Models\Alumno;
use App\Models\AlumnoGrupo;
use App\Models\AlumnoPago;
use App\Models\ApoyoInscripcion;
use App\Services\PagoInscripcionDocumentosService;
use App\Services\PagoColegiaturaDocumentosService;


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
        $grupos = Grupo::with('alumnos')->get();
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


        #borramos todos los documentos
        AbonoDocumento::query()->delete();
        #GENERAR ABONOS DE LA SUCURSAL DE CELAYA
        #2 CELAYA,3 IRAPUATO 4 SALAMANCA
        $this->generar_abonos(2);
        $this->generar_abonos(3);
        $this->generar_abonos(4);

    }

    public function generar_documentos_sucursal($id, PagoInscripcionDocumentosService $pids){
        

        #borramos todos los documentos
        Documento::with('alumno')->whereHas('alumno', function()use($id){
            return $q->where('id_sucursal',$id);
        })->delete();

        #obtenemos todos los grupos
        $grupos = Grupo::with('alumnos')->get();
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


        #borramos todos los documentos
        AbonoDocumento::query()->delete();
        #GENERAR ABONOS DE LA SUCURSAL DE CELAYA
        #2 CELAYA,3 IRAPUATO 4 SALAMANCA
        $this->generar_abonos(2);
        $this->generar_abonos(3);
        $this->generar_abonos(4);

    }

    public function generar_documentos_alumno($id, PagoInscripcionDocumentosService $pids, PagoColegiaturaDocumentosService $pcds){
        #borramos todos los documentos
        Documento::with('alumno')->whereHas('alumno', function($q)use($id){
            return $q->where('id_alumno',$id);
        })->delete();

        $alumno = Alumno::find($id);

        $grupos = $alumno->grupos;

        foreach($grupos as $grupo){
            $pids->setAlumno($alumno);
            $pcds->setAlumno($alumno);
            $pids->inscripcion($grupo, $grupo->precio_inscripcion);
            $fecha_inicio = $grupo->fecha_inicio;
            $forma_pago = ($alumno->forma_pago) ? $alumno->forma_pago :'semanal';
            

            if($forma_pago == 'mensual'){
                
                $pcds->mensual($grupo, $alumno );
                
            }
            if($forma_pago == 'semanal'){
                $pcds->semanal($grupo, $alumno);
            }
        }
        
        return redirect()->back();

    }


    public function generar_abonos($id_sucursal){

       
        // dd('hola');
        #obtenemos todos los grupos de la sucursal
        $grupos = Grupo::with('alumnos')->where('id_sucursal','=',$id_sucursal)->get();
        // $grupos = Grupo::with('alumnos')->get();

        foreach($grupos as $grupo){

            foreach($grupo->alumnos as $alumno){
                $pagos = $alumno->pagos_caja;
                #Para cada pago realizado se van a crear los abnos a los documentos del mas antiguo al mas reciente
                foreach($pagos->sortBy(function($pa){
                    return $pa->fecha->format('Ymd');
                }) as $pago){

                    $monto_pago = $pago->monto;
                    $documentos = $alumno->documentos->where('saldo','>',0)->sortBy('fecha_limite')->values();

                    foreach($documentos as $documento){
                        # GENERO EL ABONO
                        #SE VA A VALIDAR SI FUE COLEGIATURA POR PRONTO PAGO
                        if($documento->tipo == 'Colegiatura'){
                            // validar fecha limite de pronto pago
                            if($alumno->forma_pago == 'mensual'){
                                $fecha_limite_pronto = Carbon::createFromFormat('Y-m-d',$documento->anio.'-'.$documento->mes.'-06');

                                if($pago->fecha->gte($fecha_limite_pronto) &&  $documento->monto == $grupo->precio_mensualidad_pronto_pago ){
                                    $documento->monto = $grupo->precio_mensualidad;
                                    $documento->saldo = $grupo->precio_mensualidad;
                                    $documento->save();
                                }
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


    public function generar_abonos_alumno($id_alumno){
   
        $alumno = Alumno::find($id_alumno);
        
                $pagos = $alumno->pagos_caja;
                #Para cada pago realizado se van a crear los abnos a los documentos del mas antiguo al mas reciente
                foreach($pagos->sortBy(function($pago){
                    return $pago->fecha->format('Ymd');
                }) as $pago){

                    $monto_pago = $pago->monto;
                    $documentos = $alumno->documentos->where('saldo','>',0)->sortBy('fecha_limite')->values();
                
                    foreach($documentos as $documento){
                        # GENERO EL ABONO
                        #SE VA A VALIDAR SI FUE COLEGIATURA POR PRONTO PAGO
                        if($documento->tipo == 'Colegiatura'){
                            if($alumno->forma_pago == 'mensual'){
                                // validar fecha limite de pronto pago
                                $fecha_limite_pronto = Carbon::createFromFormat('Y-m-d',$documento->anio.'-'.$documento->mes.'-06');
                                
                                // dd($pago->fecha);
                                if($pago->fecha->gte($fecha_limite_pronto) && $documento->especial == 0 && $documento->saldo == $documento->monto){
                                    $documento->monto = $documento->grupo->precio_mensualidad;
                                    $documento->saldo = $documento->grupo->precio_mensualidad;
                                    $documento->save();
                                }
                            }
                        }

                        if($documento->saldo >= $monto_pago){
                            $monto = $monto_pago;
                            $monto_pago = 0;
                        }else{
                            $monto = $documento->saldo;
                            $monto_pago = $monto_pago - $documento->saldo;
                        }

                        $abono = $pago->abonos_documentos()->create([
                            'id_sucursal'       => $alumno->id_sucursal,
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
            
            

        
        return redirect()->back();

    }

    public function info(){

        #ALUMNOS CON MAS DE 2 GRUPOS

        $alumnos = Alumno::with('grupos.especialidad')->where('id_sucursal','=',2)->has('grupos','>',1)->get();

        
        $table = '<h3>Sucursal Celaya</h3>';
        $table .= '<table>';
        foreach($alumnos as $alumno){
            $table .= '<tr>';
            $table .= "  <td style='background: #030050; color:white' >";
            $table .= $alumno->nuevo_numero_control.' '.$alumno->fullname;
            $table .= '  </td>';
            $table .= '</tr>';
            foreach($alumno->grupos as $grupo){
                $table .= '<tr>';
                $table .= "  <td >";
                $table .= $grupo->clave.' - '.$grupo->especialidad->nombre;
                $table .= '  </td>';
                $table .= '</tr>';
            }

        }
        $table .= '</table>';

        echo $table;

    }

    public function alumnos_grupos(){
        $sucursal  = Session::get('sucursal');

        $alumnos = Alumno::with('pagos_caja')->has('pagos_caja')->where('id_sucursal','=',$sucursal->id)->get();

        // echo "ALUMNOS CON GRUPOS DESFADASO DE SUCURSAL: {$sucursal->nombre}<br>";
        $table = '<table class="table table-bordered"  ><thead class="bg-primary text-white"><tr><th>ALUMNO</th><th>FECHA PRIMER PAGO</th><th>INICIO GRUPO</th><th>INICIO DEL ALUMNO EN EL GRUPO</th></tr></thead>';
        foreach($alumnos as $alumno){
            
            $fecha_primer_pago = $alumno->pagos_caja->sortBy('created_at')->first()->fecha;
            
            foreach($alumno->grupos as $grupo){
                if($grupo->fecha_inicio->diffInDays($fecha_primer_pago, false) > 30){
                    $table .= '<tr>';
                    $table .= ' <td>';
                    $table .= "     <a target='_blank' class='text-primary' href='".route('alumnos.show', $alumno->id)."'>{$alumno->fullname}</a>";
                    $table .= ' </td>';
                    $table .= ' <td>';
                    $table .= "     {$fecha_primer_pago->format('d-m-Y')}";
                    $table .= ' </td>';
                    $table .= ' <td>';
                    $table .= "     {$grupo->clave}: ".optional($grupo->fecha_inicio)->format('d-m-Y');
                    $table .= ' </td>';
                    $table .= " <td><a class='editable_fecha_inicio_grupo' data-pk='{$grupo->pivot->id}' data-name='fecha_inicio' data-url='".route('especiales.actualizar_informacion_grupos_alumnos')."' data-type='date' >";
                    $table .=  optional($grupo->pivot->fecha_inicio)->format('d-m-Y');
                    $table .= ' </a></td>';
                    $table .= '</tr>';
                } 
            }
            

        }
        $table .= '</table>';

         // echo "ALUMNOS CON GRUPOS DESFADASO DE SUCURSAL: {$sucursal->nombre}<br>";
         $table2 = '<table class="table table-bordered"  ><thead class="bg-primary text-white"><tr><th>ALUMNO</th><th>FECHA PRIMER PAGO</th><th>INICIO GRUPO</th><th>INICIO DEL ALUMNO EN EL GRUPO</th></tr></thead>';
         foreach($alumnos as $alumno){
             
             $fecha_primer_pago = $alumno->pagos_caja->sortBy('created_at')->first()->fecha;
             
             foreach($alumno->grupos as $grupo){
                 if($fecha_primer_pago->diffInDays($grupo->fecha_inicio, false) > 30){
                     $table2 .= '<tr>';
                     $table2 .= ' <td>';
                     $table2 .= "     <a target='_blank' class='text-primary' href='".route('alumnos.show', $alumno->id)."'>{$alumno->fullname}</a>";
                     $table2 .= ' </td>';
                     $table2 .= ' <td>';
                     $table2 .= "     {$fecha_primer_pago->format('d-m-Y')}";
                     $table2 .= ' </td>';
                     $table2 .= ' <td>';
                     $table2 .= "     {$grupo->clave}: ".optional($grupo->fecha_inicio)->format('d-m-Y');
                     $table2 .= ' </td>';
                     $table2 .= " <td><a class='editable_fecha_inicio_grupo' data-pk='{$grupo->pivot->id}' data-name='fecha_inicio' data-url='".route('especiales.actualizar_informacion_grupos_alumnos')."' data-type='date' >";
                     $table2 .=  optional($grupo->pivot->fecha_inicio)->format('d-m-Y');
                     $table2 .= ' </a></td>';
                     $table2 .= '</tr>';
                 } 
             }
             
 
         }
         $table2 .= '</table>';
 

        return view('especiales.alumnos_grupos', compact('table','table2'));

    }

    public function actualizar_informacion_grupos_alumnos(Request $request){
        $alumno_grupo = AlumnoGrupo::findOrFail($request->pk);
        $alumno_grupo[$request->name] = $request->value;
        $alumno_grupo->save();

    }

    public function crear_apoyos_inscripcion(){
        $pagos = AlumnoPago::where('monto_apoyo_inscripcion','>',0)->get();

        foreach($pagos as $pago){
            $apoyo = ApoyoInscripcion::create([
                'id_alumno' => $pago->id_alumno,
                'id_grupo'  => $pago->id_grupo,
                'apoyo'     => $pago->monto_apoyo_inscripcion,
                'id_usuario'=> Auth::id(),
            ]);

        }

        $documentos = Documento::where('monto_apoyo_inscripcion','>',0)->get();

        foreach($documentos as $documento){
            $apoyo = ApoyoInscripcion::create([
                'id_alumno' => $documento->id_alumno,
                'id_grupo'  => $documento->id_grupo,
                'apoyo'     => $documento->monto_apoyo_inscripcion,
                'id_usuario'=> Auth::id(),
            ]);
        }



    }

}
