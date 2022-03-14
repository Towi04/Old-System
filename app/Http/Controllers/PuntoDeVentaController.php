<?php

namespace App\Http\Controllers;

use App\Models\Abono;
use App\Models\Pago;
use App\Models\Alumno;
use App\Models\AlumnoPago;
use App\Models\Grupo;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Jenssegers\Date\Date;

class PuntoDeVentaController extends Controller
{
    public function index(Request $request)
    {
        $alumno_huella = null;

        if (isset($request->id)) {
            $alumno_huella = Alumno::find($request->id);
        }

        return view('punto_de_venta.index', compact('alumno_huella'));
    }

    public function recibir_abonos(Request $request)
    {
        $this->validate($request, [
            'monto'         => 'required|numeric|min:0.01|not_in:0',
            'forma_pago'    => 'required',
        ]);

        $id_sucursal = optional(session('sucursal'))->id;
        $id_recibio = auth()->id();
        $fecha_abono = now();

        if (isset($request->id_alumno)) {
            $alumno = Alumno::find($request->input('id_alumno'));
            $pagos_alumno = $alumno->documentos()->pendientes()->where('id_grupo', '=', $request->id_grupo)->orderBy('fecha_limite', 'asc')->get();
            $monto = $request->input('monto');

            $folio = Pago::query()->select('folio')->where('id_sucursal', $id_sucursal)->max('folio') ?? 0;
            $folio_fiscal = Pago::query()->select('folio_fiscal')->where('id_sucursal', $id_sucursal)->max('folio_fiscal') ?? 0;

            $venta_fiscal = ($request->input('forma_pago', '') != 'Efectivo') ? true : $alumno->solicitud_factura;
        }

        if (isset($request->id_preregistro)) {
            $alumno = Alumno::find($request->input('id_preregistro'));
            $monto = $request->input('monto');

            $folio = Pago::query()->select('folio')->where('id_sucursal', $id_sucursal)->max('folio') ?? 0;
            $folio_fiscal = Pago::query()->select('folio_fiscal')->where('id_sucursal', $id_sucursal)->max('folio_fiscal') ?? 0;

            $venta_fiscal = ($request->input('forma_pago', '') != 'Efectivo') ? true : $alumno->solicitud_factura;
        }

        try {
            DB::beginTransaction();

            $pago = Pago::create([
                'folio'         => ($venta_fiscal) ? null : ($folio + 1),  # 👉 SI NO ES UNA VENTA FISCAL, PONER EL FOLIO EN NULL
                'folio_fiscal'  => ($venta_fiscal) ? $folio_fiscal + 1 : null,
                'forma_pago'    => $request->input('forma_pago'),
                'id_sucursal'   => $id_sucursal,
                'id_alumno'     => $alumno->id,
                'monto'         => $monto,
                'fecha'         => $fecha_abono,
                'id_recibio'    => $id_recibio,
            ]);

            # 👉 SI ES ALUMNO SE COBRAN SUS PAGOS
            if (isset($request->id_alumno)) {

                # SI NO TIENE NINGUN PAGO PENDIENTE, SE ADELANTA SU PROXIMO PAGO
                if ($pagos_alumno->isEmpty()) {

                    $grupo = Grupo::find($request->id_grupo);

                    # SE VERIFICA SEGUN LA MODALIDAD EN LA QUE SE ENCUENTRE EL ALUMNO
                    if ($alumno->forma_pago == 'semanal') {

                        $ultimo_pago = $alumno->pagos()
                            ->where('id_grupo', '=', $request->id_grupo)
                            ->where('tipo', config('alumnos.concepto.colegiatura'))
                            ->where('status',config('pagos.status.Pagado'))
                            ->where('anio', now()->year)
                            ->where('modalidad', 'semanal')
                            ->orderBy('semana', 'desc')
                            ->first();

                        # SI NO HAY UN PAGO PREVIO, ENTONCES AGREGO EL SIGUIENTE MES
                        if (empty($ultimo_pago)) {
                            $fecha = now();
                        } else {
                            # SE OBTIENE EL ULTIMO REGISTRO Y SE AGREGA EL SIGUIENTE MES CON RESPECTO AL ULTIMO RECIB
                            $fecha = now()->week($ultimo_pago->semana)->addWeek();
                        }

                        $precio_semanal = $grupo->precio_semanal ?? 0;
                        $saldo = ($monto > $precio_semanal) ? 0:  $precio_semanal - $monto;

                        $data = [
                            'modalidad'     => 'semanal',
                            'semana'        => $fecha->week,
                            'anio'          => $fecha->year,
                            'concepto'      => config('alumnos.concepto.colegiatura'),
                            'fecha_limite'  => $fecha->clone()->endOfWeek(),
                            'monto'         => $monto,
                            'saldo'         => $saldo,
                            'status'        => ($saldo == 0) ? config('pagos.status.Pagado') : config('pagos.status.Pendiente'),
                        ];
                    } else {
                        $ultimo_pago = $alumno->pagos()
                            ->where('status',config('pagos.status.Pagado'))
                            ->where('id_grupo', '=', $request->id_grupo)
                            ->where('tipo', config('alumnos.concepto.colegiatura'))
                            ->where('modalidad', 'mensual')
                            ->where('anio', now()->year)
                            ->orderBy('mes', 'desc')
                            ->first();

                        # SI NO HAY PAGOS REGISTRADOS ,ENTONCES AGREGO EL MES ACTUAL
                        if (empty($ultimo_pago)) {
                             $fecha = now();
                        } else {
                            # SE OBTIENE EL ULTIMO REGISTRO Y SE AGREGA EL SIGUIENTE MES CON RESPECTO AL ULTIMO RECIBO
                            $fecha = now()->setMonth($ultimo_pago->mes)->addMonth();
                        }

                        # VERIFICAR SI SE PAGA COMPLETAMENTE
                        $mensualidad_pronto_pago = $grupo->precio_mensualidad_pronto_pago ?? 0;
                        $saldo = ($monto > $mensualidad_pronto_pago) ? 0:  $mensualidad_pronto_pago - $monto;

                        $data = [
                            'modalidad'     => 'mensual',
                            'mes'           => $fecha->month,
                            'anio'          => $fecha->year,
                            'fecha_limite'  => $fecha->clone()->endOfMonth(),
                            'concepto'      => config('alumnos.concepto.colegiatura') . ' ' . now()->addMonth()->format('F \d\e\l Y'),
                            'monto'         => $monto,
                            'saldo'         => $saldo,
                            'status'        => ($saldo == 0) ? config('pagos.status.Pagado') : config('pagos.status.Pendiente'),
                        ];
                    }

                    $fields = [
                        'id_alumno'     => $alumno->id,
                        'id_grupo'      => $request->id_grupo,
                        'tipo'          => config('alumnos.concepto.colegiatura'),
                    ];

                    # CREAR DOCUMENTO DE PAGO
                    $alumno_pago = AlumnoPago::create(array_merge($data, $fields));

                    $pago->abonos()->create([
                        'id_sucursal'       => $id_sucursal,
                        'id_alumno_pago'    => $alumno_pago->id,
                        'monto'             => $monto,
                        'venta_fiscal'      => $venta_fiscal,
                    ]);
                } else {

                    # SI TIENE PAGOS ACTUALIZAR PAGOS
                    foreach ($pagos_alumno as $pa) {
                        if ($monto > 0) {
                            $saldo_alumno = abs(($pa->saldo == 0) ? $pa->monto : $pa->saldo);

                            if ($monto > $saldo_alumno) {
                                $monto = $monto - $saldo_alumno;

                                $pa->update([
                                    'saldo'     => 0,
                                    'status'    => config('pagos.status.Pagado'),
                                ]);

                                $pago->abonos_documentos()->create([
                                    'id_sucursal'       => $id_sucursal,
                                    'id_documento'    => $pa->id,
                                    'monto'             => $saldo_alumno,
                                    'venta_fiscal'      => $venta_fiscal,
                                ]);
                            } else {
                                $nuevo_saldo =  $saldo_alumno - $monto;

                                $pa->update([
                                    'saldo'     => $nuevo_saldo,
                                    'status'    => ($nuevo_saldo == 0) ? config('pagos.status.Pagado') : config('pagos.status.Pendiente'),
                                ]);

                                $pago->abonos_documentos()->create([
                                    'id_sucursal'       => $id_sucursal,
                                    'id_documento'    => $pa->id,
                                    'monto'             => $monto,
                                    'venta_fiscal'      => $venta_fiscal,
                                ]);

                                $monto = 0;
                            }
                        }
                    }
                }
            } else {
                # 👉 SI ES PREREGISTRO SE ABONA EL MONTO A SU SALDO
                $alumno->saldo = $monto;
                $alumno->save();

                # 👉 SE GENERA EL CONCEPTO
                $alumno_pago = AlumnoPago::create([
                    'id_alumno'     => $alumno->id,
                    'tipo'          => 'Apartado',
                    'concepto'      => 'Apartado',
                    'monto'         => $monto,
                    'fecha_limite'  => now(),
                    'status'        => config('pagos.status.Pagado'),
                ]);

                # 👉 SE GENERA EL PAGO
                $pago->abonos()->create([
                    'id_sucursal'       => $id_sucursal,
                    'id_alumno_pago'    => $alumno_pago->id,
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

            # INDENTIFICAR EL FOLIO MENSUAL 😁
            $venta_fiscal = ($request->input('forma_pago', '') != 'Efectivo') ? true : $alumno->solicitud_factura;

            # MODALIDAD DE PAGO
            $modalidades = [
                'Mes'       => 'mensual',
                'Semana'    => 'semanal'
            ];

            $modalidad = $modalidades[$request->input('forma')] ?? '';

            if ($modalidad == 'semanal') {
                $data = [
                    'modalidad' => $modalidad,
                    'semana'    => $request->input('no_pago'),
                ];
            } else {
                $data = [
                    'modalidad' => $modalidad,
                    'mes'       => $request->input('no_pago'),
                ];
            }

            $fields = array_merge([
                'id_grupo'      => $request->input('id_grupo'),
                'concepto'      => $request->input('concepto') . ' ' . $request->input('forma') . ' ' . $request->input('no_pago'),
                'monto'         => $request->input('monto'),
                'tipo'          => $request->input('concepto'),
                'status'        => config('pagos.status.Pagado'),
                'fecha_limite'  => $fecha_pago,
                'anio'          =>  Carbon::parse($fecha_pago)->year
            ], $data);


            # CREO EL ABONO DEL ALUMNO 😊
            $pago_alumno = $alumno->pagos()->create($fields);

            # CREO EL PAGO DEL ALUMNO 😏
            $pago = Pago::create([
                'folio'         => ($venta_fiscal) ? null : $request->input('folio'),  # 👉 SI NO ES UNA VENTA FISCAL, PONER EL FOLIO EN NULL,
                'folio_fiscal'  => ($venta_fiscal) ? $request->input('folio') : null,
                'id_sucursal'   => $id_sucursal,
                'id_alumno'     => $alumno->id,
                'monto'         => $request->input('monto'),
                'forma_pago'    => $request->input('forma_pago'),
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

        $pago->load(['abonos.alumno_pago', 'abonos.pago', 'alumno', 'sucursal']);

        return view('punto_de_venta.ticket', compact('pago', 'sucursal'));
    }

    public function traer_grupos(Request $request)
    {

        $grupos = Alumno::find($request->id)->grupos;

        return response()->json($grupos);
    }
}
