<?php

namespace App\Http\Controllers;

use App\Models\Grupo;
use App\Models\Alumno;
use App\Models\AlumnoGrupo;
use App\Models\Especialidad;
use App\Models\GrupoMateria;
use App\Models\Materia;
use App\Models\GrupoDia;
use App\Services\PagoInscripcionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Symfony\Component\HttpFoundation\Response as HTTPMessages;

class GruposController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
    */
    public function index()
    {
        // abort_unless(Auth::user()->can('listar_grupos'), HTTPMessages::HTTP_FORBIDDEN, __('Forbidden'));

        return view('grupos.index');
    }

    public function datatables(Request $request)
    {
        $query = Grupo::with('days')
            ->when($request->input('id_sucursal'),function($q,$id_sucursal){
                $q->where('id_sucursal',$id_sucursal);
            })
            ->with('especialidad');

        return DataTables::eloquent($query)
            ->editColumn('fecha_inicio',function($model){
                return optional($model->fecha_inicio)->format('d/m/Y');
            })
            ->editColumn('infantil',function($model){
                $tipo_grupo = ($model->infantil)? 'Infantil':'Adulto';
                return "<a class='badge badge-primary text-white'>{$tipo_grupo}</a>";
            })
            ->addColumn('days', function($model){
                $horario = '';
                foreach($model->days as $day){
                    $horario .= ucfirst($day->dia).' H '.$day->hora_inicio.' - '.$day->hora_final.'<br>';
                }
                return $horario;
            })
            ->addColumn('buttons', 'grupos.datatables._buttons')
            ->rawColumns(['buttons','infantil','days'])
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

        $sucursal = optional(session('sucursal'));
        $dias_semana = [
            'lunes'=>'Lunes',
            'martes'=>'Martes',
            'miercoles'=>'Miércoles',
            'jueves'=>'Jueves',
            'viernes'=>'Viernes',
            'sabado'=>'Sábado',
            'domingo'=>'Domingo',
        ];

        return view('grupos.create',[
            'grupo'         => new Grupo,
            'especialidades'  => Especialidad::query()
                
                ->pluck('nombre','id')
                ->sort()
                ->prepend('Selecciona una especialidad',''),
            'dias_semana'=>$dias_semana
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
            'id_sucursal'                       => 'required',
            'id_especialidad'                   => 'required',
            'horario'                           => 'required',
            'dia'                              => 'required',
            'infantil'                          => 'required',
            'fecha_inicio'                      => 'required',
            'precio_semanal'                    => 'nullable',
            'precio_mensualidad_pronto_pago'    => 'nullable',
            'precio_mensualidad'                => 'nullable',
            'precio_inscripcion'                => 'nullable',
        ];

        $especialidad = Especialidad::findOrFail($request->input('id_especialidad'));

        $materias = Materia::query()->where('id_especialidad',$request->input('id_especialidad'))->pluck('id');

        $request->request->add([
            'id_sucursal'                       => optional(session('sucursal'))->id,
            'infantil'                          => $request->has('infantil'),
            'precio_semanal'                    => $especialidad->precio_semanal,
            'precio_mensualidad_pronto_pago'    => $especialidad->precio_mensualidad_pronto_pago,
            'precio_mensualidad'                => $especialidad->precio_mensualidad,
            'precio_inscripcion'                => $especialidad->precio_inscripcion,
        ]);

        $data = $request->validate($rules);

        $grupo = Grupo::create($data);

        $grupo->materias()->attach($materias, [
            'horas_semana'  => null,
            'id_profesor'   => null,
        ]);

        // CREACION DE HORAS Y DIAS

        foreach($request->dia as $dia){
            $grupo_dia = new GrupoDia();
            $grupo_dia ->id_grupo = $grupo->id;
            $grupo_dia -> dia = $dia;
            $grupo_dia ->hora_inicio = $request['inicio_'.$dia];
            $grupo_dia ->hora_final = $request['fin_'.$dia];
            $grupo_dia->save();

        }
        

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

        $sucursal = optional(session('sucursal'));

        $dias_semana = [
            'lunes'=>'Lunes',
            'martes'=>'Martes',
            'miercoles'=>'Miércoles',
            'jueves'=>'Jueves',
            'viernes'=>'Viernes',
            'sabado'=>'Sábado',
            'domingo'=>'Domingo',
        ];

        return view('grupos.edit', [
            'grupo'             => $grupo,
            'especialidades'    => Especialidad::query()->pluck('nombre','id')->sort()->prepend('Selecciona una especialidad',''),
            'dias_semana' => $dias_semana,
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
            'id_sucursal'                       => 'required',
            'id_especialidad'                   => 'required',
            'horario'                           => 'required',
            // 'dias'                              => 'required',
            'infantil'                          => 'required',
            'fecha_inicio'                      => 'required',
            'precio_semanal'                    => 'nullable',
            'precio_mensualidad_pronto_pago'   => 'nullable',
            'precio_mensualidad'               => 'nullable',
            'precio_inscripcion'                => 'nullable',
        ];

        $especialidad = Especialidad::findOrFail($request->input('id_especialidad'));
        $materias = Materia::query()->where('id_especialidad',$request->input('id_especialidad'))->pluck('id');

        $request->request->add([
            'id_sucursal'                       => optional(session('sucursal'))->id,
            'infantil'                          => $request->has('infantil'),
            'precio_semanal'                    => $especialidad->precio_semanal,
            'precio_mensualidad_pronto_pago'    => $especialidad->precio_mensualidad_pronto_pago,
            'precio_mensualidad'                => $especialidad->precio_mensualidad,
            'precio_inscripcion'                => $especialidad->precio_inscripcion,
        ]);

        $data = $this->validate($request, $rules);
        $grupo->fill($data);
        $grupo->save();

        $grupo->materias()->detach();

        $grupo->materias()->attach($materias, [
            'horas_semana'  => null,
            'id_profesor'   => null,
        ]);

        // CREACION DE HORAS Y DIAS
        $grupo->days()->delete();

        foreach($request->dia as $dia){
            $grupo_dia = new GrupoDia();
            $grupo_dia ->id_grupo = $grupo->id;
            $grupo_dia -> dia = $dia;
            $grupo_dia ->hora_inicio = $request['inicio_'.$dia];
            $grupo_dia ->hora_final = $request['fin_'.$dia];
            $grupo_dia->save();

        }
        

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

    public function traer_grupos_select2(Request $request)
    {
        $results = Grupo::query()
            ->when($request->input('id_sucursal'),function($q,$sucursal){
                $q->where('id_sucursal',$sucursal);
            })
            ->when($request->input('id_especialidad'),function($q,$especialidad){
                $q->where('id_especialidad',$especialidad);
            })
            ->orderBy('fecha_inicio', 'asc')
            ->get();


        if ($request->ajax()) {
            return response()->json([
                'results'       => $results,
            ]);
        }

        return redirect()->back();
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

    # NOTE: ASIGNACION DE ALUMNOS

    public function asignar_alumnos(Grupo $grupo)
    {
        abort_unless(Auth::user()->can('asignar_alumnos'), HTTPMessages::HTTP_FORBIDDEN, __('Forbidden'));

        return view('grupos.asignar_alumnos',compact('grupo'));
    }

    public function guardar_alumnos(Grupo $grupo, Request $request, PagoInscripcionService $pis)
    {
        $rules = [
            'id_alumno' =>  'required',
        ];

        $this->validate($request, $rules);

        $grupo->alumnos()->attach($request->input('id_alumno'));

        $alumno = Alumno::findOrFail($request->input('id_alumno'));

        $alumno->load(['grupos']);

        $pis->setAlumno($alumno);

        switch ($alumno->forma_pago) {
            case config('alumnos.forma_pago.mensual','mensual'):
                $pis->mensualPorGrupo($grupo);
            break;

            case config('alumnos.forma_pago.semanal','semanal'):
                $pis->semanalPorGrupo($grupo);
            break;
        }

        return response()->json([
            'success' => true,
            'message' => 'Alumno asociado correctamente',
        ]);
    }

    public function actualizar_alumnos_xeditable(Request $request)
    {
        $alumno_grupo = AlumnoGrupo::find($request->pk);
        $alumno_grupo[$request->name] = $request->value;
        $alumno_grupo->save();

        $alumno_grupo->load(['alumno']);

        return response()->json([
            'alumno_grupo' => $alumno_grupo
        ]);
    }

    public function eliminar_alumnos(Request $request)
    {
        $rules = [
            'id_alumno_grupo'            => 'required',
        ];

        $this->validate($request, $rules);

        $alumno_grupo = AlumnoGrupo::findOrFail($request->input('id_alumno_grupo'));
        $alumno = $alumno_grupo->alumno;

        $alumno->pagos()
            ->where('id_grupo',$alumno_grupo->id_grupo)
            ->where('status',config('pagos.status.Pendiente'))
            ->delete();

        $alumno_grupo->delete();

        return response()->json([
            'message' => 'Alumno removido correctamente'
        ]);
    }

    public function datatables_alumnos(Request $request)
    {
        $query = AlumnoGrupo::query()
            ->when($request->input('id_grupo'),function($q,$grupo){
                $q->where('id_grupo',$grupo);
            })->with(['alumno']);

        return DataTables::eloquent($query)
            ->addColumn('nombre_alumno',function($model){
                $route = route('grupos.actualizar_alumnos_xeditable');

                return "
                <a  class='editable_id_alumno editable'
                    data-type='select2'
                    data-name='id_alumno'
                    data-pk='{$model->id}'
                    data-url='{$route}'
                    data-value='{$model->id_alumno}'
                    data-title='Selecciona un alumno'>
                    {$model->alumno->full_name }
                </a>";
            })
            ->addColumn('buttons', 'grupos.datatables._buttons_alumnos')
            ->rawColumns(['nombre_alumno','buttons'])
            ->make(true);
    }

    //traer info para ver inscripcion en
    public function traer_info(Request $request){
        $grupo = Grupo::with('especialidad')->find($request->id_grupo);

        return response()->json([
            'grupo'=> $grupo
        ]);
    }
}
