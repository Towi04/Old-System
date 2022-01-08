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
            <a href="{{ route('pre-registro-alumnos.index') }}">Pre-registro Alumnos</a>
        </li>
        <li class="breadcrumb-item active">
            <strong>Inscribir alumno {{ $alumno->nombres }}</strong>
        </li>
    </ol>
@endsection

@section('contenido')
    <div class="row">
        <div class="col-md-12">
            <div class="element-box">
                {!! Form::model($alumno, ['route' => ['pre-registro-alumnos.inscribir', $alumno], 'method' => 'PUT', 'accept-charset' => 'UTF-8', 'enctype' => 'multipart/form-data','onsubmit' => "wait.modal('show')",'id'=>'form-inscribir']) !!}
                    <h5 class="form-header">
                        Llena el formulario
                    </h5>

                    @include('alumnos.pre_registro.partials._fields_inscripcion')

                    <div class="form-buttons-w text-right">
                        <button id="inscribir" class="btn btn-success" type="button" ><i class="fa fa-plus"></i> Inscribir</button>
                    </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

   @include('alumnos.pre_registro.modals.inscripcion')
@endsection


@section('scripts')
<script src="{{ asset('template-clean-admin/bower_components/select2/dist/js/i18n/es.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function() {

        const dom = {
            especialidad: $("#id_especialidad"),
            grupo: $('#id_grupo'),
            form_inscribir: $('#form-inscribir'),

            modal_inscripcion: $("#modal-inscripcion"),
            form_inscripcion: $("#form-inscripcion"),
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
                        dom.grupo.append(`<option value="${option.id}">${option.clave || ''} | ${option.horario || ''}| ${option.dias || ''} | ${option.fecha_inicio_format || ''} | ${option.tipo_grupo || ''}</option>`);
                    });

                    dom.grupo.val("{{ optional($alumno->grupos->first())->id }}");
                },
                error:function(error){
                    toastr.error('Error', 'Ocurrio un error inesperado');
                }
            });
        });

        dom.especialidad.trigger('change')


        $('#inscribir').click(function(){

             if(dom.grupo.val() != '' && $('input[name=forma_pago]:checked').val() !== undefined){

                $.post("{{route('grupos.traer_info')}}", {id_alumno:{{$alumno->id}}, id_grupo: dom.grupo.val(), forma_pago: $('input[name=forma_pago]:checked').val() },
                    function (result) {
                        inscripcion = result.grupo.precio_inscripcion;
                        grupo = result.grupo;
                        alumno = result.alumno;
                        saldo = alumno.saldo;
                        inscripcion = inscripcion - saldo;

                        if(saldo > 0){
                            txt = "Se va a inscribir al alumno al grupo de "+grupo.especialidad.nombre+ " que comienza el día "+moment(grupo.fecha_inicio).format('DD-MM-YYYY')+". El alumno ya tiene un apartado por "+saldo+" por lo que solo tienes que solicitar la inscripción de $ "+inscripcion+" que quedará registrada como pagada en la ficha del alumno.";
                        }else{
                            txt = "Se va a inscribir al alumno al grupo de "+grupo.especialidad.nombre+ " que comienza el día "+moment(grupo.fecha_inicio).format('DD-MM-YYYY')+". Tienes que solicitar la inscripción de $ "+inscripcion+" que quedará registrada como pagada en la ficha del alumno.";
                        }

                        dom.modal_inscripcion.find('#inscripcion-detalle').text(txt);
                        dom.modal_inscripcion.modal('show');
                    },
                    "json"
                );

            }else{

                swal({
                    title: "Tienes que seleccionar Grupo y forma de pago",
                    text: '',
                    type: "error",
                    showCancelButton: true,
                    confirmButtonColor: "#DD6B55",
                    cancelButtonColor: "#999999",
                    cancelButtonText: "Cerrar",
                })
            }


        });

        dom.form_inscripcion.submit(function(e){
            e.preventDefault();

            const inputFolio = $('<input>', {
                'type': 'hidden',
                'name': 'folio',
                'value': $("#folio").val(),
            });

            const inputFormaPago = $('<input>', {
                'type': 'hidden',
                'name': 'tipo_pago',
                'value': $("#tipo_pago").val(),
            });

            dom.modal_inscripcion.modal('hide');
            wait.modal('show');

            dom.form_inscribir.append(inputFolio);
            dom.form_inscribir.append(inputFormaPago);
            dom.form_inscribir.submit();

        })
    });

</script>
@endsection
