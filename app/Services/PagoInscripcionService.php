<?php

namespace App\Services;

use App\Models\Pago;
use App\Models\Grupo;
use App\Models\Alumno;
use Jenssegers\Date\Date;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PagoInscripcionService
{
    protected $alumno;

    protected $fecha_actual;

    protected $request;

    public function __construct()
    {
        $this->alumno = new Alumno();

        $this->fecha_actual = today();
    }


    public function setAlumno(Alumno $alumno)
    {
        $this->alumno = $alumno;

        return $this;
    }

    public function setRequest(Request $request)
    {
        $this->request = $request;

        return $this;
    }

    public function mensual()
    {
        $grupos = $this->alumno->grupos;

        foreach ($grupos as $grupo) {
            $this->mensualPorGrupo($grupo);
        }
    }

    public function semanal()
    {
        $grupos = $this->alumno->grupos;

        foreach ($grupos as $grupo) {
            $this->semanalPorGrupo($grupo);
        }
    }

    public function mensualPorGrupo(Grupo $grupo)
    {
        $this->inscripcion($grupo);

        $precio_semanal = $grupo->precio_semanal ?? 0;

        if ($grupo->fecha_inicio->greaterThan($this->fecha_actual)) {
            # EL GRUPO YA COMENZO
            $fecha_inicio = $grupo->fecha_inicio->copy();
            $fecha_mes = $grupo->fecha_inicio->copy();
            $fecha_final = $grupo->fecha_inicio->copy()->lastOfMonth();
        } else {
            # EL GRUPO NO HA COMENZADO
            $fecha_inicio = $this->fecha_actual->copy();
            $fecha_mes =  $this->fecha_actual->copy();
            $fecha_final = $this->fecha_actual->copy()->lastOfMonth();
        }

        while ($fecha_inicio->next('Saturday') &&  $fecha_inicio->isSameMonth($fecha_mes, true)) {
            $formato_fecha = new Date($fecha_inicio);
            $concepto = config('alumnos.concepto.colegiatura') . ' de la semana ' . $formato_fecha->week . ' de  ' . $formato_fecha->format('F \d\e\l Y');

            $this->alumno->pagos()->create([
                'id_grupo'      => $grupo->id,
                'concepto'      => $concepto,
                'monto'         => $precio_semanal,
                'saldo'         => $precio_semanal,
                'fecha_limite'  => $fecha_inicio,
                'status'        => config('pagos.status.Pendiente'),
            ]);
        }
    }

    public function semanalPorGrupo(Grupo $grupo)
    {
        $this->inscripcion($grupo);

        $precio_semanal = $grupo->precio_semanal ?? 0;

        if ($grupo->fecha_inicio->greaterThan($this->fecha_actual)) {
            $fecha_inicio = $grupo->fecha_inicio->copy();
        } else {
            $fecha_inicio = $this->fecha_actual->copy();
        }

        $this->alumno->pagos()->create([
            'id_grupo'      => $grupo->id,
            'concepto'      => config('alumnos.concepto.colegiatura') . 'Semana: ' . $grupo->fecha_inicio->week . ' del ' . $grupo->fecha_inicio->year,
            'monto'         => $precio_semanal,
            'fecha_limite'  => $fecha_inicio->endOfWeek(Carbon::SATURDAY),
            'status'        => config('pagos.status.Pendiente'),
        ]);
    }

    private function inscripcion(Grupo $grupo)
    {
        $precio_inscripcion = $this->request->input('precio_inscripcion') ?? $grupo->precio_inscripcion ?? 0;

        $pago_alumno = $this->alumno->pagos()->create([
            'id_grupo'      => $grupo->id,
            'concepto'      => config('alumnos.concepto.inscripcion') . ' del ' . $grupo->fecha_inicio->year,
            'monto'         => $precio_inscripcion,
            'fecha_limite'  => $grupo->fecha_inicio,
            'status'        => config('pagos.status.Pagado'),
        ]);

        if (!empty($this->request)) {

            # FOLIO Y VENTA FISCAL 😁
            # DETECTAR SI ES FISCAL O NO FISCAL:
            $venta_fiscal = ($this->request->input('tipo_pago', '') != 'Efectivo') ? true : $this->alumno->solicitud_factura;
            $folio_fiscal = Pago::query()->select('folio_fiscal')->where('id_sucursal', $this->request->input('id_sucursal'))->max('folio_fiscal') ?? 0;
            $folio = Pago::query()->select('folio')->where('id_sucursal', $this->request->input('id_sucursal'))->max('folio') + 1 ?? 0;

            # CREO EL PAGO DEL ALUMNO 😏
            $pago = Pago::create([
                'folio'         => $folio,
                'folio_fiscal'  => ($venta_fiscal) ? $folio_fiscal + 1 : null,
                'id_sucursal'   => $this->request->input('id_sucursal'),
                'id_alumno'     => $this->alumno->id,
                'monto'         => $precio_inscripcion,
                'fecha'         => now(),
                'id_recibio'    => auth()->id(),
            ]);

            # GENERO EL ABONO 🙄
            $pago->abonos()->create([
                'id_sucursal'       => $this->request->input('id_sucursal'),
                'id_alumno_pago'    => $pago_alumno->id,
                'monto'             => $precio_inscripcion,
                'venta_fiscal'      => $venta_fiscal,
            ]);
        }
    }
}
