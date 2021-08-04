@extends('layouts.template-'.config('settings.template').'.plantilla')

@section('titulo')
    Editar Pre-Registro Alumnno <small>{{ $alumno->fullname }}</small>
@endsection

@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ url('/') }}">Inicio</a>
        </li>
        <li class="breadcrumb-item">
            <a href="{{ route('pre-registro-alumnos.index') }}">Pre-Registro Alumnos</a>
        </li>
        <li class="breadcrumb-item active">
            <strong>Editar Pre-Registro {{ $alumno->nombres }}</strong>
        </li>
    </ol>
@endsection

@section('contenido')
    <div class="row">
        <div class="col-md-12">
            <div class="element-box">
                {!! Form::model($alumno, ['route' => ['pre-registro-alumnos.update', $alumno], 'method' => 'PUT', 'accept-charset' => 'UTF-8', 'enctype' => 'multipart/form-data','onsubmit' => "wait.modal('show')"]) !!}
                    <h5 class="form-header">
                        Llena el formulario
                    </h5>

                    @include('alumnos.pre_registro.partials._fields')

                    <div class="form-buttons-w text-right">
                        <button class="btn btn-success" type="submit"><i class="fa fa-plus"></i> Inscribir</button>
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
            const dom = {
                especialidad: $("#id_especialidad"),
                grupo: $('#id_grupo'),
            }

            dom.especialidad.change(function(e){

                if(!e.target.value){
                    dom.grupo.empty().append('<option value="">Selecciona antes una especialidad</option>');
                    return;
                }

                $.ajax({
                    url:'{{ route("grupos.traer_grupos_select2") }}',
                    type: 'POST',
                    cache: false,
                    data: {
                        _token: $("meta[name='csrf-token']").attr("content"),
                        id_especialidad: e.target.value,
                        id_sucursal: "{{ optional(session('sucursal'))->id }}"
                    },
                    success: function (response){
                        const grupos = response.results || [];

                        dom.grupo.empty().append('<option value="">Selecciona un grupo</option>');

                        $.each(grupos, function (index, option) {
                            dom.grupo.append(`<option value="${option.id}">${option.id} | ${option.horario}| ${option.dias} | ${option.fecha_inicio_format} | ${option.tipo_grupo}</option>`);
                        });

                    },
                    error:function(error){
                        toastr.error('Error', 'Ocurrio un error inesperado');
                    }
                });
            });

            dom.especialidad.trigger('change');
        });


    // $(document).ready(function() {
    //     $('#id_grupo').select2({
    //         language: "es",
    //         placeholder:'Selecciona un grupo',
    //         ajax: {
    //             method: 'POST',
    //             data:function (params) {
    //                 const $checkbox = document.querySelector('input[type="checkbox"][data-especialidad]:checked');

    //                 return {
    //                     term: params.term,
    //                     page: params.page || 1,
    //                     _token: '{{ csrf_token() }}',
    //                     id_sucursal: "{{ optional(session('sucursal'))->id }}",
    //                     especialidad: $checkbox.value,
    //                 }
    //             },
    //             url: '{{ route("grupos.traer_grupos_select2") }}',
    //             dataType: 'json',
    //             cache: false,
    //             delay:250,
    //             beforeSend:function(xhr,type){
    //                 xhr.setRequestHeader('X-CSRF-Token',$('meta[name="csrf-token"]').attr('content'))
    //             }
    //         },
    //         escapeMarkup: function (markup) { return markup; },
    //         minimumInputLength: 3,
    //         templateResult: function(option){
    //             if (option.loading) {
    //                 return option.text;
    //             }

    //             if(!option.especialidad || !option.horario || !option.dias){
    //                 return option.text
    //             }

    //             return `${option.especialidad} | ${option.horario} | ${option.dias}`;
    //         },
    //         templateSelection:function(option){
    //             if(!option.especialidad || !option.horario || !option.dias){
    //                 return option.text
    //             }

    //             return `${option.especialidad} | ${option.horario} | ${option.dias}`;
    //         }
    //     });
    // });


    // @if($alumno->especialidad)
    //     @php
    //         $grupo = $alumno->grupos->firstWhere('especialidad',$alumno->especialidad[0]);
    //     @endphp
    //     @if(!empty($grupo))
    //         var newOption = new Option('{{ $grupo->especialidad }} | {{ $grupo->horario }} | {{ $grupo->especialidad }} ', '{{ $grupo->id }}', false, false);
    //         $('#id_grupo').append(newOption).trigger('change');
    //     @endif
    // @endif
</script>
@endsection
