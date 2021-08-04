<?php

namespace App\Http\Controllers\Admin;

use App\Models\Especialidad;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Symfony\Component\HttpFoundation\Response as HTTPMessages;

class EspecialidadesController extends Controller
{
/**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index()
    {
        abort_unless(Auth::user()->can('listar_especialidades'), HTTPMessages::HTTP_FORBIDDEN, __('Forbidden'));

        return view('admin.especialidades.index');
    }

    public function datatables(Request $request)
    {
        $query = Especialidad::query()
        ->when($request->input('id_sucursal'),function($q,$id_sucursal){
            $q->where('id_sucursal',$id_sucursal);
        });

        return DataTables::eloquent($query)
            ->editColumn('precio_inscripcion',function($model){
                return '$ '.number_format($model->precio_inscripcion,2,'.',',');
            })
            ->editColumn('precio_mensualidad',function($model){
                return '$ '.number_format($model->precio_mensualidad,2,'.',',');
            })
            ->editColumn('precio_mensualidad_pronto_pago',function($model){
                return '$ '.number_format($model->precio_mensualidad_pronto_pago,2,'.',',');
            })
            ->editColumn('precio_semanal',function($model){
                return '$ '.number_format($model->precio_semanal,2,'.',',');
            })
            ->addColumn('buttons', 'admin.especialidades.datatables._buttons')
            ->rawColumns(['buttons'])
            ->make(true);
    }

    public function show(Especialidad $especialidad)
    {
        abort_unless(Auth::user()->can('consultar_especialidad'), HTTPMessages::HTTP_FORBIDDEN, __('Forbidden'));

        return view('admin.especialidades.show',compact('especialidad'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_unless(Auth::user()->can('crear_especialidad'), HTTPMessages::HTTP_FORBIDDEN, __('Forbidden'));

        return view('admin.especialidades.create',[
            'especialidad'    => new Especialidad(),
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
            'nombre'                            => 'required',
            'descripcion'                       => 'nullable',
            'precio_inscripcion'                => 'nullable',
            'precio_mensualidad'                => 'nullable',
            'precio_mensualidad_pronto_pago'    => 'nullable',
            'precio_semanal'                    => 'nullable',
            'id_sucursal'                       => 'required',
        ];

        $request->request->add([
            'id_sucursal'   => optional(session('sucursal'))->id,
        ]);

        $data = $request->validate($rules);

        Especialidad::create($data);

        return redirect()->route('admin.especialidades.index')->with([
            'message' => 'Se agregó la especialidad con éxito',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Especialidad  $especialidad
     * @return \Illuminate\Http\Response
     */
    public function edit(Especialidad $especialidad)
    {
        abort_unless(Auth::user()->can('editar_especialidad'), HTTPMessages::HTTP_FORBIDDEN, __('Forbidden'));

        return view('admin.especialidades.edit', [
            'especialidad'    => $especialidad,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Especialidad  $especialidad
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Especialidad $especialidad)
    {
        $rules = [
            'nombre'                            => 'required',
            'descripcion'                       => 'nullable',
            'precio_inscripcion'                => 'nullable',
            'precio_mensualidad'                => 'nullable',
            'precio_mensualidad_pronto_pago'    => 'nullable',
            'precio_semanal'                    => 'nullable',
            'id_sucursal'                       => 'required',
        ];

        $request->request->add([
            'id_sucursal'   => optional(session('sucursal'))->id,
        ]);

        $data = $this->validate($request, $rules);
        $especialidad->fill($data);

        $especialidad->save();

        return redirect()->route('admin.especialidades.index')->with([
            'message' => 'Se actualizó la especialidad con éxito'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Especialidad  $especialidad
     * @return \Illuminate\Http\Response
     */
    public function destroy(Especialidad $especialidad, Request $request)
    {
        $especialidad->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'La especialidad fue eliminada con éxito',
            ]);
        }

        return redirect()->route('admin.especialidades.index')->with([
            'message' => 'La especialidad fue eliminada con éxito'
        ]);
    }
}
