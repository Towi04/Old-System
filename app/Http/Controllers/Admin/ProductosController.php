<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Producto;
use App\Services\FacturacionService;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductosController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.productos.index');
    }

    public function datatables(Request $request)
    {
        $query = Producto::query()
            ->select(['id', 'nombre', 'descripcion', 'clave_sat', 'clave_unidad_sat','precio'])
            ->when($request->input('id_sucursal'),function($q,$id_sucursal){
                $q->where('id_sucursal',$id_sucursal);
            });;

        $datatables = DataTables::eloquent($query)
            ->addColumn('buttons', 'admin.productos.datatables._buttons')
            ->rawColumns(['buttons'])
            ->make(true);

        return $datatables;
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(FacturacionService $fs)
    {
        return view('admin.productos.create',[
            'claves_unidad' => $fs->claveUnidad()->prepend('Selecciona una unidad','')
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
            'nombre'        => 'required',
            'descripcion'   => 'nullable',
            'clave_sat'     => 'nullable',
            'clave_unidad_sat' => 'nullable',
            'id_sucursal'   => 'required',
        ];

        $sucursal = optional(session('sucursal'));

        $request->request->add([
            'id_sucursal'         => $sucursal->id,
        ]);

        $this->validate($request, $rules);

        Producto::create($request->except('_token'));

        return redirect()
            ->route('admin.productos.index')
            ->with(['message' => 'Se creó el producto con éxito']);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Producto  $producto
     * @return \Illuminate\Http\Response
     */
    public function edit(Producto $producto,FacturacionService $fs)
    {
        return view('admin.productos.edit', [
            'producto'      => $producto,
            'claves_unidad' => $fs->claveUnidad()->prepend('Selecciona una unidad','')
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Producto  $producto
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Producto $producto)
    {
        $rules = [
            'nombre'        => 'required',
            'descripcion'   => 'nullable',
            'clave_sat'     => 'nullable',
            'clave_unidad_sat' => 'nullable',
            'id_sucursal'   => 'nullable'
        ];

        $this->validate($request, $rules);

        $sucursal = optional(session('sucursal'));

        $request->request->add([
            'id_sucursal'         => $sucursal->id,
        ]);

        $producto->fill($request->except('_token'));
        $producto->save();

        return redirect()
            ->route('admin.productos.index')
            ->with(['message' => 'Se actualizó el producto correctamente']);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Producto  $producto
     * @return \Illuminate\Http\Response
     */
    public function destroy(Producto $producto, Request $request)
    {
        $producto->delete();

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => "Producto eliminado",
                'id'      => $producto->id
            ]);
        }

        return redirect()
            ->route('admin.productos.index')
            ->with(['message' => "Producto eliminado"]);
    }
}
