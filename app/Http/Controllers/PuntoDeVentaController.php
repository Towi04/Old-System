<?php

namespace App\Http\Controllers;

use App\Models\Abono;
use App\Models\Pago;
use App\Models\Alumno;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PuntoDeVentaController extends Controller
{
    public function index(){
        return view('punto_de_venta.index');
    }

    public function recibir_abonos(Request $request)
    {
        $this->validate($request,[
            'id_alumno' => 'required',
            'monto'     => 'required'
        ]);

        $id_sucursal = optional(session('sucursal'))->id;
        $id_recibio = auth()->id();
        $fecha_abono = now();

        $alumno = Alumno::find($request->input('id_alumno'));
        $pagos_alumno = $alumno->pagos()->pendientes()->orderBy('fecha_limite','asc')->get();
        $monto = $request->input('monto');

        $folio = Pago::query()->select('folio')->where('id_sucursal', $id_sucursal)->max('folio') ?? 0;

        try {
            DB::beginTransaction();

            $pago = Pago::create([
                'folio'         => $folio + 1,
                'id_sucursal'   => $id_sucursal,
                'id_alumno'     => $alumno->id,
                'monto'         => $monto,
                'fecha'         => $fecha_abono,
                'id_recibio'    => $id_recibio,
            ]);

            foreach ($pagos_alumno as $pa) {
                if ($monto > 0) {
                    $saldo_alumno = abs( ($pa->saldo == 0) ? $pa->monto : $pa->saldo );

                    if($monto > $saldo_alumno){
                        $monto = $monto - $saldo_alumno;

                        $pa->update([
                            'saldo'     => 0,
                            'status'    => config('pagos.status.pagado'),
                        ]);

                        $pago->abonos()->create([
                            'id_sucursal'       => $id_sucursal,
                            'id_alumno_pago'    => $pa->id,
                            'monto'             => $monto,
                        ]);

                    }else{
                        $nuevo_saldo =  $saldo_alumno - $monto;

                        $pa->update([
                            'saldo'     => $nuevo_saldo,
                            'status'    => config('pagos.status.pendiente'),
                        ]);

                        $pago->abonos()->create([
                            'id_sucursal'       => $id_sucursal,
                            'id_alumno_pago'    => $pa->id,
                            'monto'             => $monto,
                        ]);

                        $monto = 0;
                    }
                }
            }

            DB::commit();

            if ($request->ajax()) {
                return response()->json([
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

    public function ticket($id)
    {
        $pago = Pago::findOrFail($id);

        $pago->load(['abonos.alumno_pago','abonos.pago','alumno','sucursal']);

        return view('punto_de_venta.ticket',compact('pago'));
    }
}
