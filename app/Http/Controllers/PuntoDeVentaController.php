<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PuntoDeVentaController extends Controller
{
    public function index(){
        return view('punto_de_venta.index');
    }
}
