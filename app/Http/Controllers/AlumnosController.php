<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Grupo;
use App\Models\Alumno;
use App\Models\AlumnoPago;
use Illuminate\Http\Request;
use App\Services\FacturacionService;
use App\Services\PagosAlumnosService;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Symfony\Component\HttpFoundation\Response as HTTPMessages;

class AlumnosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // abort_unless(Auth::user()->can('listar_alumnos'), HTTPMessages::HTTP_FORBIDDEN, __('Forbidden'));

        return view('alumnos.index');
    }

    public function datatables(Request $request)
    {
        $query = Alumno::query()
            ->where('status',config('alumnos.status.Alumno'))
            ->when($request->input('id_sucursal'),function($q,$id_sucursal){
                $q->where('id_sucursal',$id_sucursal);
            });

        return DataTables::eloquent($query)
            ->addColumn('nombre_alumno',function($model){
                return "{$model->nombres} {$model->apellido_paterno} {$model->apellido_materno}";
            })
            ->addColumn('fecha_nacimiento',function($model){
                return optional($model->fecha_nacimiento)->format('d/m/Y');
            })
            ->addColumn('buttons', 'alumnos.datatables._buttons')
           
            ->rawColumns(['buttons'])
            ->make(true);
    }

    public function show(Alumno $alumno)
    {
        abort_unless(Auth::user()->can('consultar_alumno'), HTTPMessages::HTTP_FORBIDDEN, __('Forbidden'));

        return view('alumnos.show',compact('alumno'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(FacturacionService $facturacionService)
    {
        abort_unless(Auth::user()->can('crear_alumno'), HTTPMessages::HTTP_FORBIDDEN, __('Forbidden'));

        return view('alumnos.create',[
            'alumno'    => new Alumno,
            'asesores'  => User::query()->get()->pluck('fullname','id')->sort()->prepend('Selecciona un asesor',''),
            'cfdis'      => $facturacionService->usosCfdi()->prepend('Selecciona un cfdi','')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request,PagosAlumnosService $pagosAlumnosService)
    {
        $rules = [
            'id_sucursal'           => 'required',
            'numero_control'        => 'required',
            'foto'                  => 'nullable',
            'nombres'               => 'required',
            'apellido_paterno'      => 'nullable',
            'apellido_materno'      => 'nullable',
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
            'tutor'                 => 'nullable',
            'especialidad'          => 'required',
            'otra_especialidad'     => 'nullable',
            'escuela_procedencia'   => 'nullable',
            'objetivo_inscripcion'  => 'required',
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
            'numero_control'      => $max_alumno + 1,
            'solicitud_factura'   => $request->has('solicitud_factura'),
            'status'              => config('alumnos.status.Alumno'),
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

        if($request->has('id_grupo')) {
            $alumno->grupos()->attach($request->input('id_grupo'));
        }

        $grupo = Grupo::findOrFail($request->input('id_grupo'));

        switch ($request->input('forma_pago')) {
            case config('alumnos.forma_pago.mensual','mensual'):
                $pagosAlumnosService
                    ->setAlumno($alumno)
                    ->mensual($grupo);
            break;

            case config('alumnos.forma_pago.semanal','semanal'):

                $pagosAlumnosService
                    ->setAlumno($alumno)
                    ->semanal($grupo);
            break;
        }

        return redirect()->route('alumnos.index')->with([
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
        abort_unless(Auth::user()->can('editar_alumno'), HTTPMessages::HTTP_FORBIDDEN, __('Forbidden'));

        return view('alumnos.edit', [
            'alumno'    => $alumno,
            'asesores'  => User::query()->get()->pluck('fullname','id')->sort()->prepend('Selecciona un asesor',''),
            'cfdis'     => $facturacionService->usosCfdi()->prepend('Selecciona un cfdi','')
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Alumno  $alumno
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Alumno $alumno)
    {
        $rules = [
            'id_sucursal'           => 'required',
            'foto'                  => 'nullable',
            'nombres'               => 'required',
            'apellido_paterno'      => 'nullable',
            'apellido_materno'      => 'nullable',
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
            'tutor'                 => 'nullable',
            'especialidad'          => 'required',
            'otra_especialidad'     => 'nullable',
            'escuela_procedencia'   => 'nullable',
            'objetivo_inscripcion'  => 'required',
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
            'forma_pago'            => 'required',
        ];

        $request->request->add([
            'id_sucursal'         => optional(session('sucursal'))->id,
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

        return redirect()->route('alumnos.index')->with([
            'message' => 'Se actualizó el alumno con éxito'
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

        return redirect()->route('alumnos.index')->with([
            'message' => 'El alumno fue eliminado con éxito'
        ]);
    }

    public function traer_alumnos_select2(Request $request)
    {
        $term  = $request->input('term');
        $page = $request->input('page', 1);

        $resultCount = 10;
        $offset = ($page - 1) * $resultCount;

        $results = Alumno::query()
            ->where('nombres', 'like', "%{$term}%")
            ->orWhere('apellido_paterno', 'like', "%{$term}%")
            ->orWhere('apellido_materno', 'like', "%{$term}%")
            ->when($request->input('id_sucursal'),function($q,$sucursal){
                $q->where('id_sucursal',$sucursal);
            })
            ->orderBy('nombres', 'asc')
            ->skip($offset)
            ->take($resultCount)
            ->get();

        $count = Alumno::query()
            ->where('nombres', 'like', "%{$term}%")
            ->orWhere('apellido_paterno', 'like', "%{$term}%")
            ->orWhere('apellido_materno', 'like', "%{$term}%")
            ->when($request->input('id_sucursal'),function($q,$sucursal){
                $q->where('id_sucursal',$sucursal);
            })
            ->count();

        $endCount = $offset + $resultCount;
        $morePages = $count > $endCount;

        if ($request->ajax()) {
            return response()->json([
                'results'       => $results,
                'pagination'    => [
                    'more' => $morePages
                ]
            ]);
        }

        return redirect()->back();
    }

    public function datatables_pagos(Request $request)
    {
        $query = AlumnoPago::query()
            ->when($request->input('id_alumno'),function($q,$id_alumno){
                $q->where('id_alumno',$id_alumno);
            });

        return DataTables::eloquent($query)
            ->editColumn('fecha_limite',function($model){
                return optional($model->fecha_limite)->format('d/m/Y');
            })
            ->rawColumns([])
            ->make(true);
    }
}
