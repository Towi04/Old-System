@extends('layouts.template-'.config('settings.template').'.plantilla')

@section('titulo')
    Roles <small>Administra los roles del sistema</small>
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ url('/') }}">Inicio</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('admin.roles.index') }}">Roles</a>
        </li>
        <li class="breadcrumb-item active">
            <strong>Editar rol {{ $role->display_name }}</strong>
        </li>
    </ol>
@endsection

@section('contenido')

    {!! Form::model($role, ['route' => ['admin.roles.update', $role], 'method' => 'PUT', 'accept-charset' => 'UTF-8',
    'enctype' => 'multipart/form-data']) !!}
    <div class="row justify-content-center pt-3">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xl-12">
            <div class="element-wrapper">
                <h3 class="element-header">
                    Llena el formulario
                </h3>
                @include('admin.roles.partials.fields')
                <button class="btn btn-success" type="submit"><i class="fa fa-plus"></i> Guardar</button>
            </div>

        </div>
    </div>
    {!! Form::close() !!}

@endsection


@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
        });
    </script>
@endsection
