<?php
namespace App\Http\Controllers\Reportes;

use Jenssegers\Date\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Models\Abono;
use App\Models\Alumno;

class ReporteVentasController extends Controller
{
    public function index(Request $request)
    {
        if ($request->has('tipo')) {
            $tipo = $request->input('tipo');
        } else {
            $tipo = 'dia';
        }

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
                ->whereBetween('created_at', [$fecha->startOfYear()->format('Y-m-d H:i:s'), $fecha->endOfYear()->format('Y-m-d H:i:s')])
                ->orderBy('created_at', 'desc');

            $fecha_antes = new Date($fecha_antes);
            $fecha_despues = new Date($fecha_despues);
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

}
