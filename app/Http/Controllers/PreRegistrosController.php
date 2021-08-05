<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Alumno;
use App\Models\Especialidad;
use Illuminate\Http\Request;
use App\Services\FacturacionService;
use App\Services\PagoInscripcionService;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Symfony\Component\HttpFoundation\Response as HTTPMessages;

class PreRegistrosController extends Controller
{
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        abort_unless(Auth::user()->canAny(['realizar_pre_registro','convertir_pre_registro_alumno']), HTTPMessages::HTTP_FORBIDDEN, __('Forbidden'));

        return view('alumnos.pre_registro.index');
    }

    public function datatables(Request $request)
    {
        $query = Alumno::query()
            ->when($request->input('id_sucursal'),function($q,$id_sucursal){
                $q->where('id_sucursal',$id_sucursal);
            })->where(function($q){
                $q->whereNull('status');
                $q->orWhere('status',config('alumnos.status.Pre-Registro'));
            })
            ->when($request->input('id_asesor_educativo'),function($q,$id){
                $q->where('id_asesor_educativo',$id);
            })
            ->with(['asesor_educativo']);

        return DataTables::eloquent($query)
            ->addColumn('nombre_asesor',function($model){
                return $model->asesor_educativo->fullname;
            })
            ->addColumn('nombre_alumno',function($model){
                return "{$model->nombres} {$model->apellido_paterno} {$model->apellido_materno}";
            })
            ->addColumn('fecha_nacimiento',function($model){
                return optional($model->fecha_nacimiento)->format('d/m/Y');
            })
            ->addColumn('buttons', 'alumnos.pre_registro.datatables._buttons')
            ->editColumn('created_at', function($model){
                return optional($model->created_at)->format('d/m/Y');
            })
            ->rawColumns(['buttons'])
            ->make(true);
    }

    public function show(Alumno $alumno)
    {
        #abort_unless(Auth::user()->can('consultar_alumno'), HTTPMessages::HTTP_FORBIDDEN, __('Forbidden'));

        return view('alumnos.pre_registro.show',compact('alumno'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(FacturacionService $facturacionService)
    {
        return view('alumnos.pre_registro.create',[
            'alumno'            => new Alumno,
            'especialidades'    => Especialidad::query()->pluck('nombre','id')->sort()->prepend('Selecciona una especialidad',''),
            'cfdis'             => $facturacionService->usosCfdi()->prepend('Selecciona un cfdi','')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
            'id_sucursal'           => 'required',
            'foto'                  => 'nullable',
            'nombres'               => 'required',
            'apellido_paterno'      => 'required',
            'apellido_materno'      => 'required',
            'edad'                  => 'nullable',
            'fecha_nacimiento'      => 'nullable',
            'domicilio'             => 'nullable',
            'colonia'               => 'nullable',
            'municipio'             => 'nullable',
            'telefono'              => 'nullable',
            'celular'               => 'nullable',
            'email'                 => 'nullable',
            'codigo_postal'         => 'nullable',
            'ocupacion'             => 'nullable',
            'grado_estudios'        => 'nullable',
            'otro_grado_estudios'   => 'nullable',
            'tutor'                 => 'nullable',
            'id_especialidad'       => 'nullable',
            'otra_especialidad'     => 'nullable',
            'escuela_procedencia'   => 'nullable',
            'objetivo_inscripcion'  => 'nullable',
            'enfermedad_cronica'    => 'nullable',
            'solicitud_factura'     => 'nullable',
            'id_asesor_educativo'   => 'required',

            # DATOS DE FACTURACION
            'razon_social'          => 'nullable',
            'rfc'                   => 'nullable',
            'cfdi'                  => 'nullable',
            'curp'                  => 'nullable',
            'telefono_general'      => 'nullable',
            'correo_general'        => 'nullable',
            'domicilio_fiscal'      => 'nullable',

            'observaciones'         => 'nullable',
            'id_grupo'              => 'nullable',
            'status'                => 'required',
        ];

        $sucursal = optional(session('sucursal'));

        $request->request->add([
            'id_sucursal'         => $sucursal->id,
            'id_asesor_educativo' => auth()->id(),
            'solicitud_factura'   => $request->has('solicitud_factura'),
            'status'              => config('alumnos.status.Pre-Registro'),
        ]);

        $data = $request->validate($rules);

        $alumno = Alumno::create($data);

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');

            $image = Image::make($file);

            $nombre_foto = $file->getClientOriginalName();

            if (!Storage::exists('alumnos_foto')) {
                Storage::makeDirectory('usuarios_foto');
            }

            if (!Storage::exists("alumnos_foto/{$alumno->id}")) {
                Storage::makeDirectory("alumnos_foto/{$alumno->id}");
            }

            $path = storage_path() . "/app/alumnos_foto/{$alumno->id}/";
            $image->save($path . $nombre_foto);

            $alumno->foto = $nombre_foto;
            $alumno->save();
        }

        return redirect()->route('pre-registro-alumnos.index')->with([
            'message' => 'Se agregó el alumno con éxito'
        ]);
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Alumno  $alumno
     * @return \Illuminate\Http\Response
     */
    public function edit(Alumno $alumno,FacturacionService $facturacionService)
    {
        return view('alumnos.pre_registro.edit', [
            'alumno'            => $alumno,
            'especialidades'    => Especialidad::query()->pluck('nombre','id')->sort()->prepend('Selecciona una especialidad',''),
            'cfdis'             => $facturacionService->usosCfdi()->prepend('Selecciona un cfdi','')
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Alumno  $alumno
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Alumno $alumno,PagoInscripcionService $pis)
    {
        $rules = [
            'id_sucursal'           => 'required',
            'numero_control'        => 'required',
            'foto'                  => 'nullable',
            'nombres'               => 'required',
            'apellido_paterno'      => 'required',
            'apellido_materno'      => 'required',
            'edad'                  => 'nullable',
            'fecha_nacimiento'      => 'nullable',
            'domicilio'             => 'nullable',
            'colonia'               => 'nullable',
            'municipio'             => 'nullable',
            'telefono'              => 'nullable',
            'celular'               => 'nullable',
            'email'                 => 'nullable',
            'codigo_postal'         => 'nullable',
            'ocupacion'             => 'nullable',
            'grado_estudios'        => 'nullable',
            'otro_grado_estudios'   => 'nullable',
            'tutor'                 => 'nullable',
            'id_especialidad'       => 'nullable',
            'otra_especialidad'     => 'nullable',
            'escuela_procedencia'   => 'nullable',
            'objetivo_inscripcion'  => 'nullable',
            'enfermedad_cronica'    => 'nullable',
            'solicitud_factura'     => 'nullable',

            # DATOS DE FACTURACION
            'razon_social'          => 'nullable',
            'rfc'                   => 'nullable',
            'cfdi'                  => 'nullable',
            'curp'                  => 'nullable',
            'telefono_general'      => 'nullable',
            'correo_general'        => 'nullable',
            'domicilio_fiscal'      => 'nullable',

            'observaciones'         => 'nullable',
            'forma_pago'            => 'required',
            'status'                => 'required',
        ];

        $sucursal = optional(session('sucursal'));

        $max_alumno = Alumno::query()
            ->where('status',config('alumnos.status.Alumno'))
            ->where('id_sucursal', $sucursal->id)
            ->max('numero_control') ?? 0;

        $request->request->add([
            'id_sucursal'         => $sucursal->id,
            'solicitud_factura'   => $request->has('solicitud_factura'),
            'status'              => config('alumnos.status.Alumno'),
            'numero_control'      => $max_alumno + 1,
        ]);

        $data = $this->validate($request, $rules);
        $alumno->fill($data);

        if ($request->hasFile('foto')) {
            $file = $request->file('foto');

            $image = Image::make($file);

            $nombre_foto = $file->getClientOriginalName();

            if (!Storage::exists('alumnos_foto')) {
                Storage::makeDirectory('usuarios_foto');
            }

            if (!Storage::exists("alumnos_foto/{$alumno->id}")) {
                Storage::makeDirectory("alumnos_foto/{$alumno->id}");
            }

            $path = storage_path() . "/app/alumnos_foto/{$alumno->id}/";
            $image->save($path . $nombre_foto);

            $alumno->foto = $nombre_foto;
            $alumno->save();
        }

        $alumno->save();

        if ($request->has('id_grupo')) {
            $alumno->grupos()->attach($request->input('id_grupo'));
            $alumno->load('grupos');

            $pis->setAlumno($alumno);

            switch ($request->input('forma_pago')) {
                case config('alumnos.forma_pago.mensual','mensual'):
                    $pis->mensual();
                break;
                case config('alumnos.forma_pago.semanal','semanal'):
                    $pis->semanal();
                break;
            }
        }

        return redirect()->route('pre-registro-alumnos.index')->with([
            'message' => 'El alumno se inscribio con éxito'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Alumno  $alumno
     * @return \Illuminate\Http\Response
     */
    public function destroy(Alumno $alumno, Request $request)
    {
        $alumno->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'El alumno fue eliminado con éxito',
            ]);
        }

        return redirect()->route('pre-registro-alumnos.index')->with([
            'message' => 'El alumno fue eliminado con éxito'
        ]);
    }
}
