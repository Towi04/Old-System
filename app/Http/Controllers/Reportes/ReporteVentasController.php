<?php
namespace App\Http\Controllers\Reportes;

use Jenssegers\Date\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use App\Models\Abono;

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
}
