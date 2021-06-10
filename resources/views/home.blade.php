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
@endsection

@section('scripts')
@endsection
