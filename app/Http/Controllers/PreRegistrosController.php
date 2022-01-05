<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Alumno;
use App\Models\User;
use App\Models\Especialidad;
use Illuminate\Http\Request;
use App\Services\FacturacionService;
use App\Services\PagoInscripcionService;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class PreRegistrosController extends Controller
{
    public function index()
    {
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
                if(!Auth::user()->canAny(['convertir_pre_registro_alumno'])){
                    $q->where('id_asesor_educativo',$id);
                }
            })
            ->with(['asesor_educativo','especialidad']);

        return DataTables::eloquent($query)
            ->addColumn('nombre_asesor',function($model){
                return $model->asesor_educativo->fullname;
            })
            ->addColumn('nombre_alumno',function($model){
                return "<a href=".route('pre-registro-alumnos.show', $model->id).">{$model->nombres} {$model->apellido_paterno} {$model->apellido_materno}</a>";
            })
            ->addColumn('fecha_nacimiento',function($model){
                return optional($model->fecha_nacimiento)->format('d/m/Y');
            })
            ->addColumn('buttons', 'alumnos.pre_registro.datatables._buttons')
            ->editColumn('created_at', function($model){
                return optional($model->created_at)->format('d/m/Y');
            })
            ->rawColumns(['buttons','nombre_alumno'])
            ->make(true);
    }

    public function show(Alumno $alumno)
    {
        return view('alumnos.pre_registro.show',compact('alumno'));
    }

    public function create(FacturacionService $facturacionService)
    {
        return view('alumnos.pre_registro.create',[
            'alumno'            => new Alumno,
            'especialidades'    => Especialidad::query()->pluck('nombre','id')->sort()->prepend('Selecciona una especialidad',''),
            'cfdis'             => $facturacionService->usosCfdi()->prepend('Selecciona un cfdi','')
        ]);
    }

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

    public function edit(Alumno $alumno,FacturacionService $facturacionService)
    {
        return view('alumnos.pre_registro.edit', [
            'alumno'            => $alumno,
            'especialidades'    => Especialidad::query()->pluck('nombre','id')->sort()->prepend('Selecciona una especialidad',''),
            'cfdis'             => $facturacionService->usosCfdi()->prepend('Selecciona un cfdi',''),

        ]);
    }

    public function update(Request $request, Alumno $alumno)
    {
        $rules = [
            'id_sucursal'           => 'required',
            'como_supiste_nosotros' => 'nullable',
            'nuevo_numero_control'  => 'nullable',
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
            'forma_pago'            => 'nullable',
            'status'                => 'nullable',
        ];

        $sucursal = optional(session('sucursal'));

        $request->request->add([
            'id_sucursal'         => $sucursal->id,
            'solicitud_factura'   => $request->has('solicitud_factura'),
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



        return redirect()->route('pre-registro-alumnos.index')->with([
            'message' => 'El alumno se edito con éxito'
        ]);
    }

    public function formulario_inscripcion(Alumno $alumno,FacturacionService $facturacionService)
    {
        $sucursal = optional(session('sucursal'));

        return view('alumnos.pre_registro.inscribir', [
            'alumno'            => $alumno,
            'especialidades'    => Especialidad::query()->pluck('nombre','id')->sort()->prepend('Selecciona una especialidad',''),
            'cfdis'             => $facturacionService->usosCfdi()->prepend('Selecciona un cfdi',''),
            'asesores'          => User::query()->get()->pluck('fullname','id')->sort()->prepend('CNCM',''),
        ]);
    }

    public function inscribir(Request $request, $id,PagoInscripcionService $pis)
    {
        $rules = [
            'id_sucursal'           => 'required',
            'como_supiste_nosotros' => 'nullable',
            'nuevo_numero_control'  => 'required',
            'foto'                  => 'nullable',
            'nombres'               => 'required',
            'apellido_paterno'      => 'required',
            'apellido_materno'      => 'required',
            'edad'                  => 'required',
            'fecha_nacimiento'      => 'required',
            'domicilio'             => 'required',
            'colonia'               => 'required',
            'municipio'             => 'required',
            'telefono'              => 'required',
            'celular'               => 'required',
            'email'                 => 'required',
            'codigo_postal'         => 'required',
            'ocupacion'             => 'required',
            'grado_estudios'        => 'required',
            'otro_grado_estudios'   => 'nullable',
            'tutor'                 => 'required',
            'id_especialidad'       => 'required',
            'otra_especialidad'     => 'nullable',
            'escuela_procedencia'   => 'nullable',
            'objetivo_inscripcion'  => 'required',
            'enfermedad_cronica'    => 'required',
            'solicitud_factura'     => 'nullable',
            'id_asesor_educativo'   => 'nullable',
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

        $alumno = Alumno::find($id);

        $request->request->add([
            'id_sucursal'           => $sucursal->id,
            'solicitud_factura'     => $request->has('solicitud_factura'),
            'status'                => config('alumnos.status.Alumno'),
            'nuevo_numero_control'  => generar_folio_alumno($sucursal->id),
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

            $grupo_inscripcion = Grupo::findOrFail($request->input('id_grupo'));

            $pis->setRequest($request);
            $pis->setAlumno($alumno);

            switch ($request->input('forma_pago')) {
                case config('alumnos.forma_pago.mensual','mensual'):
                    $pis->mensualPorGrupo($grupo_inscripcion);
                break;
                case config('alumnos.forma_pago.semanal','semanal'):
                    $pis->semanalPorGrupo($grupo_inscripcion);
                break;
            }
        }

        return redirect()->route('pre-registro-alumnos.index')->with([
            'message' => 'El alumno se inscribio con éxito'
        ]);
    }

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
