@extends('layouts.template-'.config('settings.template').'.plantilla')

@section('titulo')
    Inicio
@endsection

@section('css')
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ url('/') }}">Inicio</a>
        </li>
    </ol>
@endsection

@section('contenido')
    @empty(session('sucursal'))
        <div class="element-wrapper">
            <div class="element-box">
                <h5 class="form-header">
                    No tienes asignada una sucursal
                </h5>
                <div class="form-desc">
                    Para poder continuar, solicita que te asignen una sucursal
                </div>
            </div>
        </div>
    @endempty
@endsection

@section('scripts')
@endsection
