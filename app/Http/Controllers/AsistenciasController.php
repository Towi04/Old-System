<?php

namespace App\Http\Controllers;

use App\Models\Abono;
use App\Models\Pago;
use App\Models\Alumno;
use App\Models\Asistencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Jenssegers\Date\Date;


class AsistenciasController extends Controller
{
    public function index(){
        return view('asistencias.index');
    }

    public function registrar_asistencia(Request $request){
        $alumno = Alumno::find($request->id_alumno);

        $asistencia = new Asistencia();
        $asistencia->id_alumno = $alumno->id;
        $asistencia->fecha = date('Y-m-d H:i:s');
        $asistencia->save();

        return response()->json([
            'alumno'=>$alumno,
            'asistencia'=>$asistencia
        ], 200);
    }

    public function eliminar_asistencia(Request $request){
       

        $asistencia = Asistencia::find($request->id);
        $asistencia->delete();
    
        return response()->json([
            
            'asistencia'=>$asistencia
        ], 200);
    }
}
