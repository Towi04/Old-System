<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Descuento;
use App\Models\Especialidad;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response as HTTPMessages;
use Yajra\DataTables\Facades\DataTables;

class DescuentosController extends Controller
{
    public function index()
    {
        abort_unless(Auth::user()->can('gestionar_descuentos'), HTTPMessages::HTTP_FORBIDDEN, __('Forbidden'));

        return view('descuentos.index');
    }

    public function datatables(Request $request)
    {
        $query = Descuento::with(['especialidad_1','especialidad_2']);

        return DataTables::eloquent($query)
            ->editColumn('precio_semanal',function($model){
                return '$ '.number_format($model->precio_semanal,2,'.',',');
            })
            ->addColumn('buttons', 'descuentos.datatables._buttons')
            ->rawColumns(['buttons'])
            ->make(true);
    }

    public function show(Descuento $descuento)
    {
        abort_unless(Auth::user()->can('gestionar_descuentos'), HTTPMessages::HTTP_FORBIDDEN, __('Forbidden'));

        return view('descuentos.show',compact('descuento'));
    }

    public function create()
    {
        abort_unless(Auth::user()->can('gestionar_descuentos'), HTTPMessages::HTTP_FORBIDDEN, __('Forbidden'));

        $especialidades = Especialidad::get();
        return view('descuentos.create',[
            'descuento'    => new Descuento(),
            'especialidades' => $especialidades,
        ]);
    }

    public function store(Request $request)
    {
        $rules = [
            'id_especialidad_1'                    => 'required',
            'id_especialidad_2'                    => 'required',
            'porcentaje_descuento'              => 'required',
            'id_usuario'                =>'nullable',
        ];

        $request->request->add([
            'id_usuario'   => Auth::id(),
        ]);

        $data = $request->validate($rules);
        $descuento = Descuento::create($data);
        
        return redirect()->route('descuentos.index')->with([
            'message' => 'Se agregó la descuento con éxito',
        ]);
    }

    public function edit(Descuento $descuento)
    {
        abort_unless(Auth::user()->can('gestionar_descuentos'), HTTPMessages::HTTP_FORBIDDEN, __('Forbidden'));

        $especialidades = Especialidad::get();

        return view('descuentos.edit', [
            'descuento'    => $descuento,
            'especialidades' => $especialidades,
        ]);
    }

    public function update(Request $request, Descuento $descuento)
    {
        $rules = [
            'id_especialidad_1'                    => 'required',
            'id_especialidad_2'                    => 'required',
            'porcentaje_descuento'              => 'required',
        ];
        
        $data = $this->validate($request, $rules);
        $descuento->fill($data);
        $descuento->save();

        return redirect()->route('descuentos.index')->with([
            'message' => 'Se actualizó la descuento con éxito'
        ]);
    }

    public function destroy(Descuento $descuento, Request $request)
    {
        $descuento->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'La descuento fue eliminada con éxito',
            ]);
        }

        return redirect()->route('descuentos.index')->with([
            'message' => 'La descuento fue eliminada con éxito'
        ]);
    }

 
}
