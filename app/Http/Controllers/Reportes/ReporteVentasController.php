<?php
namespace App\Http\Controllers\Reportes;

use Jenssegers\Date\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Abono;
use App\Models\Pago;
use App\Models\Alumno;
use App\Models\User;
use App\Models\Venta;
use App\Models\Configuracion;

class ReporteVentasController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('tipo')) {
            $tipo = $request->input('tipo');
        } else {
            $tipo = 'dia';
        }
        
        $sucursal = optional(session('sucursal'));

        if ($tipo == 'dia') {
            $tipo = 'dia';

            if (isset($request->fecha)) {
                $fecha = Carbon::createFromFormat('d-m-Y', $request->input('fecha'));
            } else {
                $fecha = Carbon::today();
            }

            $fecha =  new Date($fecha);
            $fecha_antes = Carbon::createFromFormat('Y-m-d', $fecha->format('Y-m-d'))->subDay();
            $fecha_despues = Carbon::createFromFormat('Y-m-d', $fecha->format('Y-m-d'))->addDay();

            
            $abonos = Abono::query()
                ->where('id_sucursal','=',$sucursal->id)
                ->whereBetween('created_at', [$fecha->startOfDay()->format('Y-m-d H:i:s'), $fecha->endOfDay()->format('Y-m-d H:i:s')])
                ->orderBy('created_at', 'desc');

            $fecha_antes = new Date($fecha_antes);
            $fecha_despues = new Date($fecha_despues);
        }

        if ($tipo == 'mes') {
            if (isset($request->fecha)) {
                $fecha = Carbon::createFromFormat('d-m-Y', $request->fecha);
            } else {
                $fecha = Carbon::today();
            }
            $fecha =  new Date($fecha);
            $fecha_antes = Carbon::createFromFormat('Y-m-d', $fecha->format('Y-m-d'))->subMonth();
            $fecha_despues = Carbon::createFromFormat('Y-m-d', $fecha->format('Y-m-d'))->addMonth();

            $abonos = Abono::query()
                ->where('id_sucursal','=',$sucursal->id)
                ->whereBetween('created_at', [$fecha->startOfMonth()->format('Y-m-d H:i:s'), $fecha->endOfMonth()->format('Y-m-d H:i:s')])
                ->orderBy('created_at', 'desc');

            $fecha_antes = new Date($fecha_antes);
            $fecha_despues = new Date($fecha_despues);
        }

        if ($tipo == 'semanal') {
            if (isset($request->fecha)) {
                $fecha = Carbon::createFromFormat('d-m-Y', $request->fecha);
            } else {
                $fecha = Carbon::today();
            }
            $fecha =  new Date($fecha);
            $fecha_antes = Carbon::createFromFormat('Y-m-d', $fecha->format('Y-m-d'))->subDays(7);
            $fecha_despues = Carbon::createFromFormat('Y-m-d', $fecha->format('Y-m-d'))->addDays(7);

            $abonos = Abono::query()
                ->where('id_sucursal','=',$sucursal->id)
                ->whereBetween('created_at', [$fecha->startOfWeek()->format('Y-m-d H:i:s'), $fecha->endOfWeek()->format('Y-m-d H:i:s')])
                ->orderBy('created_at', 'desc');

            $fecha_antes = new Date($fecha_antes);
            $fecha_despues = new Date($fecha_despues);
        }
        if ($tipo == 'anual') {
            if (isset($request->fecha)) {
                $fecha = Carbon::createFromFormat('d-m-Y', $request->fecha);
            } else {
                $fecha = Carbon::today();
            }
            $fecha =  new Date($fecha);
            $fecha_antes = Carbon::createFromFormat('Y-m-d', $fecha->format('Y-m-d'))->subYear();
            $fecha_despues = Carbon::createFromFormat('Y-m-d', $fecha->format('Y-m-d'))->addYear();

            $abonos = Abono::query()
                ->where('id_sucursal','=',$sucursal->id)
                ->whereBetween('created_at', [$fecha->startOfYear()->format('Y-m-d H:i:s'), $fecha->endOfYear()->format('Y-m-d H:i:s')])
                ->orderBy('created_at', 'desc');

            $fecha_antes = new Date($fecha_antes);
            $fecha_despues = new Date($fecha_despues);
        }

        // dd(Configuracion::where('nombre','=','mostrar_solo_fiscales')->first()->valor);

        if(Configuracion::where('nombre','=','mostrar_solo_fiscales')->first()->valor == 'Si'){
           $abonos =  $abonos->where('venta_fiscal','=',1);
        }

        $abonos =  $abonos->with(['pago.alumno','alumno_pago'])->get();

        return view('reportes.reporte_ventas.index',compact('abonos', 'fecha', 'fecha_antes', 'fecha_despues', 'tipo'));
    }

    public function vencimientos()
    {
       return view('reportes.reporte_ventas.vencimientos');
    }

    public function datatables_vencimientos(Request $request)
    {
        $query = Alumno::with(['pagos'])
            ->where('status',config('alumnos.status.Alumno'))
            ->when($request->input('id_sucursal'),function($q,$id_sucursal){
                $q->where('id_sucursal',$id_sucursal);
            })->whereHas('pagos', function($q){
                return $q->where('status','=','pendiente')->where('fecha_limite','<',date('Y-m-d'));
            });

        return DataTables::eloquent($query)
            ->addColumn('nombre_alumno',function($model){
                return "<a href=".route('alumnos.show', $model->id).">{$model->nombres} {$model->apellido_paterno} {$model->apellido_materno}</a>";
            })
            ->editColumn('pagos_vencidos',function($model){
                return $model->pagos_vencidos->count();
            })
            ->editColumn('monto_vencido',function($model){
                return "$ ". number_format($model->monto_vencido,2,'.',',');
            })
            // ->addColumn('buttons', 'alumnos.datatables._buttons')

            ->rawColumns(['buttons','nombre_alumno'])
            ->make(true);
    }

    public function proyeccion()
    {
       return view('reportes.reporte_ventas.proyeccion');
    }

    public function datatables_proyeccion(Request $request)
    {
        $today = \Carbon\Carbon::today();
        $fin_de_mes = $today->endOfMonth();

        $query = Alumno::with(['pagos'])
            ->where('status',config('alumnos.status.Alumno'))
            ->when($request->input('id_sucursal'),function($q,$id_sucursal){
                $q->where('id_sucursal',$id_sucursal);
            })->whereHas('pagos', function($q) use($fin_de_mes){
                return $q->where('status','=','pendiente')->where('fecha_limite','<',$fin_de_mes->format('Y-m-d'));
            });

        return DataTables::eloquent($query)
            ->addColumn('nombre_alumno',function($model){
                return "<a href=".route('alumnos.show', $model->id).">{$model->nombres} {$model->apellido_paterno} {$model->apellido_materno}</a>";
            })
            ->editColumn('pagos_por_cobrar',function($model){
                return $model->pagos_por_cobrar->count();
            })
            ->editColumn('monto_por_cobrar',function($model){
                return "$ ". number_format($model->monto_por_cobrar,2,'.',',');
            })
            // ->addColumn('buttons', 'alumnos.datatables._buttons')

            ->rawColumns(['buttons','nombre_alumno'])
            ->make(true);
    }


    public function asesores(Request $request)
    {
        if ($request->has('tipo')) {
            $tipo = $request->input('tipo');
        } else {
            $tipo = 'dia';
        }
        
        $sucursal = optional(session('sucursal'));

        if ($tipo == 'dia') {
            $tipo = 'dia';

            if (isset($request->fecha)) {
                $fecha = Carbon::createFromFormat('d-m-Y', $request->input('fecha'));
            } else {
                $fecha = Carbon::today();
            }

            $fecha =  new Date($fecha);
            $fecha_antes = Carbon::createFromFormat('Y-m-d', $fecha->format('Y-m-d'))->subDay();
            $fecha_despues = Carbon::createFromFormat('Y-m-d', $fecha->format('Y-m-d'))->addDay();

            
            $alumnos = Alumno::with('asesor_educativo')
                ->where('id_sucursal','=',$sucursal->id)
                ->whereBetween('created_at', [$fecha->startOfDay()->format('Y-m-d H:i:s'), $fecha->endOfDay()->format('Y-m-d H:i:s')])
                ->orderBy('created_at', 'desc');

            $fecha_antes = new Date($fecha_antes);
            $fecha_despues = new Date($fecha_despues);
        }

        if ($tipo == 'mes') {
            if (isset($request->fecha)) {
                $fecha = Carbon::createFromFormat('d-m-Y', $request->fecha);
            } else {
                $fecha = Carbon::today();
            }
            $fecha =  new Date($fecha);
            $fecha_antes = Carbon::createFromFormat('Y-m-d', $fecha->format('Y-m-d'))->subMonth();
            $fecha_despues = Carbon::createFromFormat('Y-m-d', $fecha->format('Y-m-d'))->addMonth();

            $alumnos = Alumno::with('asesor_educativo')
                ->where('id_sucursal','=',$sucursal->id)
                ->whereBetween('created_at', [$fecha->startOfMonth()->format('Y-m-d H:i:s'), $fecha->endOfMonth()->format('Y-m-d H:i:s')])
                ->orderBy('created_at', 'desc');

            $fecha_antes = new Date($fecha_antes);
            $fecha_despues = new Date($fecha_despues);
        }

        if ($tipo == 'semanal') {
            if (isset($request->fecha)) {
                $fecha = Carbon::createFromFormat('d-m-Y', $request->fecha);
            } else {
                $fecha = Carbon::today();
            }
            $fecha =  new Date($fecha);
            $fecha_antes = Carbon::createFromFormat('Y-m-d', $fecha->format('Y-m-d'))->subDays(7);
            $fecha_despues = Carbon::createFromFormat('Y-m-d', $fecha->format('Y-m-d'))->addDays(7);

            $alumnos = Alumno::with('asesor_educativo')
                ->where('id_sucursal','=',$sucursal->id)
                ->whereBetween('created_at', [$fecha->startOfWeek()->format('Y-m-d H:i:s'), $fecha->endOfWeek()->format('Y-m-d H:i:s')])
                ->orderBy('created_at', 'desc');

            $fecha_antes = new Date($fecha_antes);
            $fecha_despues = new Date($fecha_despues);
        }
        if ($tipo == 'anual') {
            if (isset($request->fecha)) {
                $fecha = Carbon::createFromFormat('d-m-Y', $request->fecha);
            } else {
                $fecha = Carbon::today();
            }
            $fecha =  new Date($fecha);
            $fecha_antes = Carbon::createFromFormat('Y-m-d', $fecha->format('Y-m-d'))->subYear();
            $fecha_despues = Carbon::createFromFormat('Y-m-d', $fecha->format('Y-m-d'))->addYear();

            $alumnos = Alumno::with('asesor_educativo')
                ->where('id_sucursal','=',$sucursal->id)
                ->whereBetween('created_at', [$fecha->startOfYear()->format('Y-m-d H:i:s'), $fecha->endOfYear()->format('Y-m-d H:i:s')])
                ->orderBy('created_at', 'desc');

            $fecha_antes = new Date($fecha_antes);
            $fecha_despues = new Date($fecha_despues);
        }

        // dd($alumnos->get());        

        $asesores =  User::whereHas('registros', function($q) use($alumnos){
            return $q->whereIn('id', $alumnos->pluck('id'));
        })->get();

        $alumnos = $alumnos->get()->groupBy(function($alumno){
            return $alumno->asesor_educativo->id;
        });

        // dd($alumnos);

       

        return view('reportes.reporte_ventas.asesores',compact('alumnos', 'fecha', 'fecha_antes', 'fecha_despues','asesores','tipo'));
    }

    public function convertir_ventas_fiscales(Request $request){
        $tipo = $request->tipo;

        $sucursal = optional(session('sucursal'));

        if ($tipo == 'mes') {
            if (isset($request->fecha)) {
                $fecha = Carbon::createFromFormat('d-m-Y', $request->fecha);
            } else {
                $fecha = Carbon::today();
            }
            $fecha =  new Date($fecha);
            $fecha_antes = Carbon::createFromFormat('Y-m-d', $fecha->format('Y-m-d'))->subMonth();
            $fecha_despues = Carbon::createFromFormat('Y-m-d', $fecha->format('Y-m-d'))->addMonth();

            $pagos = Pago::query()
                ->where('id_sucursal','=',$sucursal->id)
                ->whereBetween('fecha', [$fecha->copy()->startOfMonth()->format('Y-m-d H:i:s'), $fecha->copy()->endOfMonth()->format('Y-m-d H:i:s')])
                ->orderBy('folio', 'asc')->get();

            $fecha_antes = new Date($fecha_antes);
            $fecha_despues = new Date($fecha_despues);
        }

        if ($tipo == 'semanal') {
            if (isset($request->fecha)) {
                $fecha = Carbon::createFromFormat('d-m-Y', $request->fecha);
            } else {
                $fecha = Carbon::today();
            }
            $fecha =  new Date($fecha);
            $fecha_antes = Carbon::createFromFormat('Y-m-d', $fecha->format('Y-m-d'))->subDays(7);
            $fecha_despues = Carbon::createFromFormat('Y-m-d', $fecha->format('Y-m-d'))->addDays(7);

            $pagos = Pago::query()
                ->where('id_sucursal','=',$sucursal->id)
                ->whereBetween('created_at', [$fecha->copy()->startOfWeek()->format('Y-m-d H:i:s'), $fecha->copy()->endOfWeek()->format('Y-m-d H:i:s')])
                ->orderBy('folio', 'asc')->get();

            $fecha_antes = new Date($fecha_antes);
            $fecha_despues = new Date($fecha_despues);
        }

        $porcentaje_configuracion = Configuracion::where('nombre','=','porcentaje_fiscal')->first();

        // SI NO ESTA CONFIGURADO EL PORCENTAJE FISCAL
        if(!$porcentaje_configuracion){
            Session::flash('error','Se debe configurar un porcentaje en la configuración global de la plataforma');
            return redirect()->back();
        }

        $porcentaje_esperado = $porcentaje_configuracion->valor;
        // dd($pagos);
        // SE COMIENZA CON LA CONVERSION

        $porcentaje_fiscal_real = $pagos->whereNotNull('folio_fiscal')->sum('monto') / $pagos->sum('monto') * 100; 

        // dd($porcentaje_fiscal_real);
        if($porcentaje_fiscal_real <= $porcentaje_esperado){
            foreach($pagos->whereNull('folio_fiscal') as $pago){
           
                $max_folio_fiscal = Pago::where('id_sucursal','=', $sucursal->id)->max('folio_fiscal');
    
                $max_folio_fiscal = $max_folio_fiscal + 1;
    
                $pago->update(['folio_fiscal'=>$max_folio_fiscal]);
                // Abono::where('id_pago','=',$pago->id)->update(['venta_fiscal'=>1]);
                $pago->abonos()->update(['venta_fiscal'=>1]);
                // break;
                if ($tipo == 'mes') {
                $pas = Pago::query()
                ->where('id_sucursal','=',$sucursal->id)
                ->whereBetween('fecha', [$fecha->copy()->startOfMonth()->format('Y-m-d H:i:s'), $fecha->copy()->endOfMonth()->format('Y-m-d H:i:s')])
                ->orderBy('folio', 'asc')->get();
                }

                if ($tipo == 'semanal') {
                    $pas = Pago::query()
                    ->where('id_sucursal','=',$sucursal->id)
                    ->whereBetween('created_at', [$fecha->copy()->startOfWeek()->format('Y-m-d H:i:s'), $fecha->endOfWeek()->format('Y-m-d H:i:s')])
                    ->orderBy('folio', 'asc')->get();
                }

                // dd($pas);

                $porcentaje_fiscal_real = $pas->whereNotNull('folio_fiscal')->sum('monto') / $pas->sum('monto') * 100; 
                
                if($porcentaje_fiscal_real >= $porcentaje_esperado){
                    break;
                }
            }
        }

        

        return response()->json([
            
        ]);
        // Session::flash('message','Se ajustaron las ventas no fiscales a fiscales de acuerdo al porcentaje correctamente');
        // return redirect()->back();

    }

    public function index_productos(Request $request)
    {
        if ($request->has('tipo')) {
            $tipo = $request->input('tipo');
        } else {
            $tipo = 'dia';
        }
        
        $sucursal = optional(session('sucursal'));

        if ($tipo == 'dia') {
            $tipo = 'dia';

            if (isset($request->fecha)) {
                $fecha = Carbon::createFromFormat('d-m-Y', $request->input('fecha'));
            } else {
                $fecha = Carbon::today();
            }

            $fecha =  new Date($fecha);
            $fecha_antes = Carbon::createFromFormat('Y-m-d', $fecha->format('Y-m-d'))->subDay();
            $fecha_despues = Carbon::createFromFormat('Y-m-d', $fecha->format('Y-m-d'))->addDay();

            
            $ventas = Venta::query()->where('status','=','Cerrada')
                ->where('id_sucursal','=',$sucursal->id)
                ->whereBetween('created_at', [$fecha->startOfDay()->format('Y-m-d H:i:s'), $fecha->endOfDay()->format('Y-m-d H:i:s')])
                ->orderBy('created_at', 'desc');

            $fecha_antes = new Date($fecha_antes);
            $fecha_despues = new Date($fecha_despues);
        }

        if ($tipo == 'mes') {
            if (isset($request->fecha)) {
                $fecha = Carbon::createFromFormat('d-m-Y', $request->fecha);
            } else {
                $fecha = Carbon::today();
            }
            $fecha =  new Date($fecha);
            $fecha_antes = Carbon::createFromFormat('Y-m-d', $fecha->format('Y-m-d'))->subMonth();
            $fecha_despues = Carbon::createFromFormat('Y-m-d', $fecha->format('Y-m-d'))->addMonth();

            $ventas = Venta::query()->where('status','=','Cerrada')
                ->where('id_sucursal','=',$sucursal->id)
                ->whereBetween('created_at', [$fecha->startOfMonth()->format('Y-m-d H:i:s'), $fecha->endOfMonth()->format('Y-m-d H:i:s')])
                ->orderBy('created_at', 'desc');

            $fecha_antes = new Date($fecha_antes);
            $fecha_despues = new Date($fecha_despues);
        }

        if ($tipo == 'semanal') {
            if (isset($request->fecha)) {
                $fecha = Carbon::createFromFormat('d-m-Y', $request->fecha);
            } else {
                $fecha = Carbon::today();
            }
            $fecha =  new Date($fecha);
            $fecha_antes = Carbon::createFromFormat('Y-m-d', $fecha->format('Y-m-d'))->subDays(7);
            $fecha_despues = Carbon::createFromFormat('Y-m-d', $fecha->format('Y-m-d'))->addDays(7);

            $ventas = Venta::query()->where('status','=','Cerrada')
                ->where('id_sucursal','=',$sucursal->id)
                ->whereBetween('created_at', [$fecha->startOfWeek()->format('Y-m-d H:i:s'), $fecha->endOfWeek()->format('Y-m-d H:i:s')])
                ->orderBy('created_at', 'desc');

            $fecha_antes = new Date($fecha_antes);
            $fecha_despues = new Date($fecha_despues);
        }
        if ($tipo == 'anual') {
            if (isset($request->fecha)) {
                $fecha = Carbon::createFromFormat('d-m-Y', $request->fecha);
            } else {
                $fecha = Carbon::today();
            }
            $fecha =  new Date($fecha);
            $fecha_antes = Carbon::createFromFormat('Y-m-d', $fecha->format('Y-m-d'))->subYear();
            $fecha_despues = Carbon::createFromFormat('Y-m-d', $fecha->format('Y-m-d'))->addYear();

            $ventas = Venta::query()->where('status','=','Cerrada')
                ->where('id_sucursal','=',$sucursal->id)
                ->whereBetween('created_at', [$fecha->startOfYear()->format('Y-m-d H:i:s'), $fecha->endOfYear()->format('Y-m-d H:i:s')])
                ->orderBy('created_at', 'desc');

            $fecha_antes = new Date($fecha_antes);
            $fecha_despues = new Date($fecha_despues);
        }

        // dd(Configuracion::where('nombre','=','mostrar_solo_fiscales')->first()->valor);

        // if(Configuracion::where('nombre','=','mostrar_solo_fiscales')->first()->valor == 'Si'){
        //    $abonos =  $abonos->where('venta_fiscal','=',1);
        // }

        $ventas =  $ventas->get();

        return view('reportes.reporte_ventas.index_productos',compact('ventas', 'fecha', 'fecha_antes', 'fecha_despues', 'tipo'));
    }

}
