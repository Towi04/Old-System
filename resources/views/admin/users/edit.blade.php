@extends('layouts.template-'.config('settings.template').'.plantilla')

@section('titulo')
    Usuarios
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{url('/')}}">Inicio</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{route('admin.usuarios.index')}}">Usuarios</a>
        </li>
        <li class="breadcrumb-item active">
            <strong>Editar usuario {{$user->nombre}}</strong>
        </li>
    </ol>
@endsection

@section('contenido')
<style>
.contact-box:hover{
    transform: scale(1.05)
}
</style>

{!! Form::model( $user, ['route' => ['admin.usuarios.update', $user],'method' => 'PUT',  'accept-charset'=>'UTF-8','enctype'=>'multipart/form-data']) !!}
    <div class="row pt-4">
        <div class="col-md-12">
            <div class="widget-holder widget-full-height widget-flex ">
                <div class="widget-body ">
                    @include('admin.users.partials.fields')
                </div>
            </div>
        </div>

        <div class="col-md-1 mr-5">
                <button class="btn btn-primary dim" type="submit"><i class="fas fa-save"></i> Guardar cambios</button>
        </div>
    </div>
{!! Form::close() !!}
@endsection


@section('scripts')
    <script type="text/javascript">
        $(document).ready(function () {
            $('#input_password').keyup(function(){
                if($(this).val()!='')
                $('#div_enviar_datos').show();
                else
                $('#div_enviar_datos').hide();
            });

            // Translated
            $('.dropify').dropify({
                messages: {
                    default: 'Arrastre o pulse para seleccionar imagen',
                    replace: 'Arrastre o pulse para reemplazar imagen',
                    remove: 'Quitar',
                    error: 'Ups, ha ocurrido un error inesperado'
                }
            });
        });
    </script>
@endsection
