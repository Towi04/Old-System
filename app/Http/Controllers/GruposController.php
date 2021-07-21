<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\GrupoMateria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as HTTPMessages;
use Yajra\DataTables\Facades\DataTables;

class GruposController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index()
    {
        abort_unless(Auth::user()->can('listar_grupos'), HTTPMessages::HTTP_FORBIDDEN, __('Forbidden'));

        return view('grupos.index');
    }

    public function datatables(Request $request)
    {
        $query = Grupo::query()
            ->when($request->input('id_sucursal'),function($q,$id_sucursal){
                $q->where('id_sucursal',$id_sucursal);
            });

        return DataTables::eloquent($query)
            ->editColumn('fecha_inicio',function($model){
                return optional($model->fecha_inicio)->format('d/m/Y');
            })
            ->editColumn('infantil',function($model){
                $tipo_grupo = ($model->infantil)? 'Infantil':'Adulto';
                return "<a class='badge badge-primary text-white'>{$tipo_grupo}</a>";
            })
            ->addColumn('buttons', 'grupos.datatables._buttons')
            ->rawColumns(['buttons','infantil'])
            ->make(true);
    }

    public function show(Grupo $grupo)
    {
        abort_unless(Auth::user()->can('consultar_grupo'), HTTPMessages::HTTP_FORBIDDEN, __('Forbidden'));

        return view('grupos.show',compact('grupo'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        abort_unless(Auth::user()->can('crear_grupo'), HTTPMessages::HTTP_FORBIDDEN, __('Forbidden'));

        return view('grupos.create',[
            'grupo'    => new Grupo,

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
            'id_sucursal'   => 'required',
            'especialidad'  => 'required',
            'horario'       => 'required',
            'dias'          => 'required',
            'infantil'      => 'required',
            'fecha_inicio'  => 'required',
        ];

        $request->request->add([
            'id_sucursal'   => optional(session('sucursal'))->id,
            'infantil'      => $request->has('infantil'),
        ]);

        $data = $request->validate($rules);

        Grupo::create($data);

        return redirect()->route('grupos.index')->with([
            'message' => 'Se agregó el grupo con éxito',
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Grupo  $grupo
     * @return \Illuminate\Http\Response
     */
    public function edit(Grupo $grupo)
    {
        abort_unless(Auth::user()->can('editar_grupo'), HTTPMessages::HTTP_FORBIDDEN, __('Forbidden'));

        return view('grupos.edit', [
            'grupo'    => $grupo,
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Grupo  $grupo
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Grupo $grupo)
    {
        $rules = [
            'id_sucursal'           => 'required',
            'especialidad'          => 'required',
            'horario'               => 'required',
            'dias'                  => 'required',
            'infantil'              => 'required',
            'fecha_inicio'          => 'required',
        ];

        $request->request->add([
            'id_sucursal'   => optional(session('sucursal'))->id,
            'infantil'      => $request->has('infantil'),
        ]);

        $data = $this->validate($request, $rules);
        $grupo->fill($data);

        $grupo->save();

        return redirect()->route('grupos.index')->with([
            'message' => 'Se actualizó el grupo con éxito'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Grupo  $grupo
     * @return \Illuminate\Http\Response
     */
    public function destroy(Grupo $grupo, Request $request)
    {
        $grupo->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'El grupo fue eliminado con éxito',
            ]);
        }

        return redirect()->route('grupos.index')->with([
            'message' => 'El grupo fue eliminado con éxito'
        ]);
    }

    # NOTE: ASIGNACION DE MATERIAS

    public function asignar_materias(Grupo $grupo)
    {
        abort_unless(Auth::user()->can('crear_grupo'), HTTPMessages::HTTP_FORBIDDEN, __('Forbidden'));

        return view('grupos.asignar_materias',compact('grupo'));
    }

    public function guardar_materias(Grupo $grupo, Request $request)
    {
        $rules = [
            'id_materia'            => 'required',
            'horas_semana'          => 'nullable',
            'id_profesor'           => 'nullable',
        ];

        $this->validate($request, $rules);

        $grupo->materias()->attach($request->input('id_materia'), [
            'horas_semana'  => $request->input('horas_semana'),
            'id_profesor'   => $request->input('id_profesor'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Materia asociada correctamente',
        ]);
    }

    public function actualizar_materias_xeditable(Request $request)
    {
        $grupo_materia = GrupoMateria::find($request->pk);
        $grupo_materia[$request->name] = $request->value;
        $grupo_materia->save();

        $grupo_materia->load(['materia','profesor']);

        return response()->json([
            'grupo_materia' => $grupo_materia
        ]);
    }

    public function eliminar_materias(Request $request)
    {
        $rules = [
            'id_grupo_materia'            => 'required',
        ];

        $this->validate($request, $rules);

        $grupo_materia = GrupoMateria::findOrFail($request->input('id_grupo_materia'));
        $grupo_materia->delete();

        return response()->json([
            'message' => 'Materia eliminada correctamente'
        ]);
    }

    public function datatables_materias(Request $request)
    {
        $query = GrupoMateria::query()
            ->when($request->input('id_grupo'),function($q,$grupo){
                $q->where('id_grupo',$grupo);
            })->with(['materia','profesor']);

        return DataTables::eloquent($query)
            ->addColumn('nombre_materia',function($model){
                $route = route('grupos.actualizar_materias_xeditable');

                return " <a  class='editable_id_materia editable'
                    data-type='select2'
                    data-name='id_materia'
                    data-pk='{$model->id}'
                    data-url='{$route}'
                    data-value='{$model->id_materia}}'
                    data-title='Selecciona una materia'>
                    {$model->materia->nombre}
                </a>";
            })
            ->addColumn('nombre_profesor',function($model){
                $route = route('grupos.actualizar_materias_xeditable');
                return "
                <a  class='editable_id_profesor editable'
                    data-type='select2'
                    data-name='id_profesor'
                    data-pk='{$model->id}'
                    data-url='{$route}'
                    data-value='{$model->id_profesor}'
                    data-title='Selecciona un profesor'>
                    {$model->profesor->full_name }
                </a>";
            })
            ->editColumn('horas_semana',function($model){
                $route = route('grupos.actualizar_materias_xeditable');

                return "<a class='editable_horas_semana editable'
                    data-type='text'
                    data-name='horas_semana'
                    data-pk='{$model->id}'
                    data-url='{$route}'
                    data-value='{$model->horas_semana}'
                    data-placeholder='Horas por semana'> {$model->horas_semana} </a>";
            })
            ->addColumn('buttons', 'grupos.datatables._buttons_materias')
            ->rawColumns(['nombre_materia','nombre_profesor','horas_semana','buttons'])
            ->make(true);
    }
}
