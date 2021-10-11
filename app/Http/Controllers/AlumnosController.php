<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Alumno;
use App\Models\AlumnoPago;
use App\Models\Especialidad;
use App\Models\Grupo;
use Illuminate\Http\Request;
use App\Services\FacturacionService;
use App\Services\PagoInscripcionService;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Session;
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
                return "<a href=".route('alumnos.show', $model->id).">{$model->nombres} {$model->apellido_paterno} {$model->apellido_materno}</a>";
            })
            ->addColumn('fecha_nacimiento',function($model){
                return optional($model->fecha_nacimiento)->format('d/m/Y');
            })
            ->addColumn('buttons', 'alumnos.datatables._buttons')

            ->rawColumns(['buttons','nombre_alumno'])
            ->make(true);
    }

    public function show(Alumno $alumno)
    {
        $alumno->load(['especialidad']);

        return view('alumnos.show',compact('alumno'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(FacturacionService $facturacionService)
    {
        $sucursal = optional(session('sucursal'));

        return view('alumnos.create',[
            'alumno'            => new Alumno,
            'asesores'          => User::query()->get()->pluck('fullname','id')->sort()->prepend('CNCM',''),
            'especialidades'    => Especialidad::query()->where('id_sucursal',$sucursal->id)->pluck('nombre','id')->sort()->prepend('Selecciona una especialidad',''),
            'cfdis'             => $facturacionService->usosCfdi()->prepend('Selecciona un cfdi','')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, PagoInscripcionService $pis)
    {
        $rules = [
            'id_sucursal'           => 'required',
            'como_supiste_nosotros' => 'nullable',
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
            'id_especialidad'       => 'required',
            'otra_especialidad'     => 'nullable',
            'escuela_procedencia'   => 'nullable',
            'objetivo_inscripcion'  => 'required',
            'enfermedad_cronica'    => 'nullable',
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

        if ($request->has('id_grupo')) {
            $alumno->grupos()->attach($request->input('id_grupo'));
            $grupo_inscripcion = Grupo::findOrFail($request->input('id_grupo'));

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
        abort_unless(Auth::user()->canany(['editar_alumno','editar_datos_fiscales']), HTTPMessages::HTTP_FORBIDDEN, __('Forbidden'));

        $sucursal = optional(session('sucursal'));

        return view('alumnos.edit', [
            'alumno'            => $alumno,
            'especialidades'    => Especialidad::query()->where('id_sucursal',$sucursal->id)->pluck('nombre','id')->sort()->prepend('Selecciona una especialidad',''),
            'asesores'          => User::query()->get()->pluck('fullname','id')->sort()->prepend('CNCM',''),
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
    public function update(Request $request, Alumno $alumno)
    {
        if(Auth::user()->can('editar_alumno')){
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
                'id_especialidad'       => 'required',
                'otra_especialidad'     => 'nullable',
                'escuela_procedencia'   => 'nullable',
                'objetivo_inscripcion'  => 'required',
                'enfermedad_cronica'    => 'nullable',
                'solicitud_factura'     => 'nullable',
                'id_asesor_educativo'   => 'nullable',
    
                # DATOS DE FACTURACION
                'razon_social'          => 'required_if:solicitud_factura,1',
                'rfc'                   => 'required_if:solicitud_factura,1',
                'cfdi'                  => 'required_if:solicitud_factura,1',
                'curp'                  => 'required_if:solicitud_factura,1',
                'telefono_general'      => 'nullable',
                'correo_general'        => 'nullable',
                'domicilio_fiscal'      => 'nullable',
    
                'observaciones'         => 'nullable',
                'forma_pago'            => 'required',
            ];
        }else{
            $rules = [
                # DATOS DE FACTURACION
                'solicitud_factura'      => 'nullable',
                'razon_social'          => 'required_if:solicitud_factura,1',
                'rfc'                   => 'required_if:solicitud_factura,1',
                'cfdi'                  => 'required_if:solicitud_factura,1',
                'curp'                  => 'required_if:solicitud_factura,1',
                'telefono_general'      => 'required_if:solicitud_factura,1',
                'correo_general'        => 'required_if:solicitud_factura,1',
                'domicilio_fiscal'      => 'nullable',
            ];
        }
        
       
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
        $alumno->grupos()->detach();
        $alumno->pagos()->where('status',config('pagos.status.Pendiente'))->delete();
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

        $results = Alumno::with('grupos.especialidad')
            ->when($request->input('id_sucursal'),function($q,$sucursal){
                $q->where('id_sucursal',$sucursal);
            })
            ->when($request->input('status'),function($q,$status){
                $q->where('status','=',$status);
            })
            ->where(function($q) use($term){
                $q->where('nombres', 'like', "%{$term}%")
                ->orWhere('apellido_paterno', 'like', "%{$term}%")
                ->orWhere('apellido_materno', 'like', "%{$term}%")
                ->orWhere('numero_control', 'like', "%{$term}%");
            })
            ->orderBy('nombres', 'asc')
            ->skip($offset)
            ->take($resultCount)
            ->get();

        $count = Alumno::query()
            ->when($request->input('id_sucursal'),function($q,$id_sucursal){
                $q->where('id_sucursal',$id_sucursal);
            })
            ->when($request->input('status'),function($q,$status){
                $q->where('status',$status);
            })
            ->where(function($q) use($term){
                $q->where('nombres', 'like', "%{$term}%")
                ->orWhere('apellido_paterno', 'like', "%{$term}%")
                ->orWhere('apellido_materno', 'like', "%{$term}%");
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
            })
            ->when($request->input('status'),function($q,$status){
                $q->where('status',$status);
            })
            ->when($request->input('id_grupo'),function($q,$id_grupo){
                $q->where('id_grupo',$id_grupo);
            });

        return DataTables::eloquent($query)
            ->addIndexColumn()
            ->editColumn('fecha_limite',function($model){
                return optional($model->fecha_limite)->format('d/m/Y');
            })
            ->rawColumns([])
            ->make(true);
    }

    public function datatables_pagos_pendientes(Request $request)
    {
        if(isset($request->id_alumno)){
            $query = AlumnoPago::query()
                ->when($request->input('id_alumno'),function($q,$id_alumno){
                    $q->where('id_alumno',$id_alumno);
                })
                ->when($request->input('status'),function($q,$status){
                    $q->where('status',$status);
                })->when($request->input('id_grupo'),function($q,$id_grupo){
                    $q->where('id_grupo',$id_grupo);
                });

            $total_pendiente = AlumnoPago::query()
                ->when($request->input('id_alumno'),function($q,$id_alumno){
                    $q->where('id_alumno',$id_alumno);
                })
                ->when($request->input('status'),function($q,$status){
                    $q->where('status',$status);
                })->when($request->input('id_grupo'),function($q,$id_grupo){
                    $q->where('id_grupo',$id_grupo);
                })->sum('saldo');
        }else{
            $query = AlumnoPago::where('id_alumno','xxxxxxxxx');
            $total_pendiente = 0;
        }

        return DataTables::eloquent($query)
            ->editColumn('fecha_limite',function($model){
                return optional($model->fecha_limite)->format('d/m/Y');
            })
            ->editColumn('monto',function($model){
                return number_format($model->monto,2,'.',',');
            })
            ->editColumn('saldo',function($model){
                return number_format($model->saldo,2,'.',',');
            })
            ->editColumn('status',function($model){
                return "<span class='badge badge-danger text-white'>{$model->status}</span>";
            })


            ->rawColumns(['status'])
            ->with([
                'total_pendiente' => $total_pendiente
            ])
            ->make(true);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Alumno  $alumno
     * @return \Illuminate\Http\Response
     */
    public function formulario_inscribir_otro_grupo(Alumno $alumno)
    {
        $sucursal = optional(session('sucursal'));

        return view('alumnos.inscripcion', [
            'alumno'            => $alumno,
            'especialidades'    => Especialidad::query()->pluck('nombre','id')->sort()->prepend('Selecciona una especialidad',''),
        ]);
    }

    public function inscribir_a_otro_grupo(Request $request, $id,PagoInscripcionService $pis){

        $sucursal = optional(session('sucursal'));

        $alumno = Alumno::find($id);

        if ($request->has('id_grupo')) {
            $alumno->grupos()->attach($request->input('id_grupo'));

            $grupo_inscripcion = Grupo::findOrFail($request->input('id_grupo'));

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

        Session::flash('message','Se inscribio al alumno con éxito');
        return redirect()->route('alumnos.show', $alumno->id);
    }
}
