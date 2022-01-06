<?php

namespace App\Http\Controllers;

use App\Models\Abono;
use App\Models\Pago;
use App\Models\Alumno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Jenssegers\Date\Date;

class PuntoDeVentaController extends Controller
{
    public function index(Request $request)
    {
        $alumno_huella = null;
        if(isset($request->id)){
            $alumno_huella = Alumno::find($request->id);
        }
        // dd($alumno_huella);

        return view('punto_de_venta.index', compact('alumno_huella'));
    }

    public function recibir_abonos(Request $request)
    {
        $this->validate($request,[
            'monto'         => 'required|numeric|min:0.01|not_in:0',
            'forma_pago'    => 'required',
        ]);

        $id_sucursal = optional(session('sucursal'))->id;
        $id_recibio = auth()->id();
        $fecha_abono = now();

        if(isset($request->id_alumno)){
            $alumno = Alumno::find($request->input('id_alumno'));
            $pagos_alumno = $alumno->pagos()->pendientes()->where('id_grupo','=', $request->id_grupo)->orderBy('fecha_limite','asc')->get();
            $monto = $request->input('monto');

            $folio = Pago::query()->select('folio')->where('id_sucursal', $id_sucursal)->max('folio') ?? 0;
            $folio_fiscal = Pago::query()->select('folio_fiscal')->where('id_sucursal', $id_sucursal)->max('folio_fiscal') ?? 0;

            $venta_fiscal = ($request->input('forma_pago','') != 'Efectivo') ? true : $alumno->solicitud_factura;
        }

        if(isset($request->id_preregistro)){
            $alumno = Alumno::find($request->input('id_preregistro'));
            $monto = $request->input('monto');

            $folio = Pago::query()->select('folio')->where('id_sucursal', $id_sucursal)->max('folio') ?? 0;
            $folio_fiscal = Pago::query()->select('folio_fiscal')->where('id_sucursal', $id_sucursal)->max('folio_fiscal') ?? 0;

            $venta_fiscal = ($request->input('forma_pago','') != 'Efectivo') ? true : $alumno->solicitud_factura;
        }

        try {
            DB::beginTransaction();

            $pago = Pago::create([
                'folio'         => $folio + 1,
                'folio_fiscal'   => ($venta_fiscal)?$folio_fiscal + 1 : null,
                'id_sucursal'   => $id_sucursal,
                'id_alumno'     => $alumno->id,
                'monto'         => $monto,
                'fecha'         => $fecha_abono,
                'id_recibio'    => $id_recibio,
            ]);

            // dd($pagos_alumno);
            // Si es alumno se cobran sus pagos
            if(isset($request->id_alumno)){
                foreach ($pagos_alumno as $pa) {
                    if ($monto > 0) {
                        $saldo_alumno = abs( ($pa->saldo == 0) ? $pa->monto : $pa->saldo );

                        if($monto > $saldo_alumno){
                            $monto = $monto - $saldo_alumno;

                            $pa->update([
                                'saldo'     => 0,
                                'status'    => config('pagos.status.Pagado'),
                            ]);

                            $pago->abonos()->create([
                                'id_sucursal'       => $id_sucursal,
                                'id_alumno_pago'    => $pa->id,
                                'monto'             => $saldo_alumno,
                                'venta_fiscal'      => $venta_fiscal,
                            ]);

                        }else{
                            $nuevo_saldo =  $saldo_alumno - $monto;

                            $pa->update([
                                'saldo'     => $nuevo_saldo,
                                'status'    => ($nuevo_saldo == 0)?config('pagos.status.Pagado') : config('pagos.status.Pendiente'),
                            ]);

                            $pago->abonos()->create([
                                'id_sucursal'       => $id_sucursal,
                                'id_alumno_pago'    => $pa->id,
                                'monto'             => $monto,
                                'venta_fiscal'      => $venta_fiscal,
                            ]);

                            $monto = 0;
                        }
                    }
                }

            }
            // Si es prergistro se abona el monto a su saldo
            else{
                $alumno->saldo = $monto;
                $alumno->save();

                $pago->abonos()->create([
                    'id_sucursal'       => $id_sucursal,
                    'id_alumno_pago'    => null,
                    'monto'             => $monto,
                    'venta_fiscal'      => $venta_fiscal,
                ]);
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Abono Registrado correctamente',
                    'data'    => [
                        'pago' => $pago
                    ]
                ]);
            }
        } catch (\Throwable $th) {
            DB::rollBack();

            throw ValidationException::withMessages([
                "error" => 'Error al guardar en base de datos' . $th->getMessage(),
            ]);
        }

        return redirect()->back();
    }

    public function pago_manual(Request $request)
    {
        $request->validate([
            'id_alumno'     => 'required',
            'id_grupo'      => 'required',
            'monto'         => 'required',
            'concepto'      => 'required',
            // 'folio'         => 'required',
            'forma_pago'    => 'required',
            'fecha'         => 'required',
            'no_pago'       => 'required',
            'forma'         => 'required',
        ]);

        $id_sucursal = optional(session('sucursal'))->id;
        $id_recibio = auth()->id();
        $fecha_pago = $request->fecha;

        

        try {
            DB::beginTransaction();

            $alumno = Alumno::findOrFail($request->input('id_alumno'));

            # FOLIO Y VENTA FISCAL 😁
            $folio_fiscal = Pago::query()->select('folio_fiscal')->where('id_sucursal', $id_sucursal)->max('folio_fiscal') ?? 0;
            $venta_fiscal = ($request->input('forma_pago','') != 'Efectivo') ? true : $alumno->solicitud_factura;
            $folio = Pago::query()->select('folio')->where('id_sucursal', $id_sucursal)->max('folio') + 1 ?? 0;



            # CREO EL ABONO DEL ALUMNO 😊
            $pago_alumno = $alumno->pagos()->create([
                'id_grupo'      => $request->input('id_grupo'),
                'concepto'      => $request->input('concepto').' '.$request->input('forma').' '.$request->input('no_pago'),
                'monto'         => $request->input('monto'),
                'fecha_limite'  => $fecha_pago,
                'status'        => config('pagos.status.Pagado'),
            ]);

            # CREO EL PAGO DEL ALUMNO 😏
            $pago = Pago::create([
                'folio'         => $folio,
                'folio_fiscal'  => ($venta_fiscal)?$folio_fiscal + 1 : null,
                'id_sucursal'   => $id_sucursal,
                'id_alumno'     => $alumno->id,
                'monto'         => $request->input('monto'),
                'fecha'         => $fecha_pago,
                'id_recibio'    => $id_recibio,
            ]);

            # GENERO EL ABONO 🙄
            $pago->abonos()->create([
                'id_sucursal'       => $id_sucursal,
                'id_alumno_pago'    => $pago_alumno->id,
                'monto'             => $request->input('monto'),
                'venta_fiscal'      => $venta_fiscal,
            ]);

            # TERMINO TRANSACCION 😥
            DB::commit();

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Abono Registrado correctamente',
                    'data'    => [
                        'pago' => $pago
                    ]
                ]);
            }
        } catch (\Throwable $th) {
            DB::rollBack();
            dd($th);
            throw ValidationException::withMessages([
                "error" => 'Error al guardar en base de datos: ' . $th->getMessage(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message'   => 'Pago Registrado correctamente',
        ]);

    }

    public function ticket($id)
    {
        $pago = Pago::findOrFail($id);
        $sucursal = optional(session('sucursal'));

        $pago->load(['abonos.alumno_pago','abonos.pago','alumno','sucursal']);

        return view('punto_de_venta.ticket',compact('pago','sucursal'));
    }

    public function traer_grupos(Request $request){

        $grupos = Alumno::find($request->id)->grupos;

        return response()->json($grupos);

    }
}
