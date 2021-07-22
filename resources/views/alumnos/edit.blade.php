@extends('layouts.template-'.config('settings.template').'.plantilla')

@section('titulo')
    Alumnos <small></small>
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ url('/') }}">Inicio</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('alumnos.index') }}">Alumnos</a>
        </li>
        <li class="breadcrumb-item active">
            <strong>Editar alumno {{ $alumno->nombres }}</strong>
        </li>
    </ol>
@endsection

@section('contenido')
    <div class="row">
        <div class="col-md-12">
            <div class="element-box">
                {!! Form::model($alumno, ['route' => ['alumnos.update', $alumno], 'method' => 'PUT', 'accept-charset' => 'UTF-8', 'enctype' => 'multipart/form-data','onsubmit' => "wait.modal('show')"]) !!}
                    <h5 class="form-header">
                        Llena el formulario
                    </h5>

                    @include('alumnos.partials._fields')

                    <div class="form-buttons-w text-right">
                        <button class="btn btn-success" type="submit"><i class="fa fa-plus"></i> Guardar</button>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

@endsection


@section('scripts')
<script src="{{ asset('template-clean-admin/bower_components/select2/dist/js/i18n/es.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {
        $('#id_grupo').select2({
            language: "es",
            placeholder:'Selecciona un grupo',
            ajax: {
                method: 'POST',
                data:function (params) {
                    const $checkbox = document.querySelector('input[type="checkbox"][data-especialidad]:checked');

                    return {
                        term: params.term,
                        page: params.page || 1,
                        _token: '{{ csrf_token() }}',
                        id_sucursal: "{{ optional(session('sucursal'))->id }}",
                        especialidad: $checkbox.value,
                    }
                },
                url: '{{ route("grupos.traer_grupos_select2") }}',
                dataType: 'json',
                cache: false,
                delay:250,
                beforeSend:function(xhr,type){
                    xhr.setRequestHeader('X-CSRF-Token',$('meta[name="csrf-token"]').attr('content'))
                }
            },
            escapeMarkup: function (markup) { return markup; },
            minimumInputLength: 3,
            templateResult: function(option){
                if (option.loading) {
                    return option.text;
                }

                if(!option.especialidad || !option.horario || !option.dias){
                    return option.text
                }

                return `${option.especialidad} | ${option.horario} | ${option.dias}`;
            },
            templateSelection:function(option){
                if(!option.especialidad || !option.horario || !option.dias){
                    return option.text
                }

                return `${option.especialidad} | ${option.horario} | ${option.dias}`;
            }
        });
    });


    @if($alumno->especialidad)
        @php
            $grupo = $alumno->grupos->firstWhere('especialidad',$alumno->especialidad[0]);
        @endphp
        @if(!empty($grupo))
            var newOption = new Option('{{ $grupo->especialidad }} | {{ $grupo->horario }} | {{ $grupo->especialidad }} ', '{{ $grupo->id }}', false, false);
            $('#id_grupo').append(newOption).trigger('change');
        @endif
    @endif
</script>
@endsection
