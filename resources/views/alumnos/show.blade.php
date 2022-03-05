@extends('layouts.template-'.config('settings.template').'.plantilla')

@section('titulo')
    Detalle del Alumno <small></small>
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
            <strong>Detalle Alumno</strong>
        </li>
    </ol>
@endsection

@section('contenido')
    <link rel="stylesheet" href="{{ asset('plugins/xeditable/css/bootstrap-editable.css') }}">
    <style>
        .contact-box:hover {
            transform: scale(1.05)
        }

        .borderless td,
        .borderless th {
            border: none;
        }

        .contact-box:hover {
            transform: scale(1.05)
        }

        .activity-boxes-w .activity-box:before {
            position: absolute;
            top: 50%;
            left: -30px;
            content: "";
            width: 12px;
            height: 12px;
            border: 0px solid #60769f;
            background-color: #f2f4f8;
            border-radius: 20px;
            -webkit-transform: translateY(-50%);
            transform: translateY(-50%);
            z-index: 2;
        }

        .content-box {
            padding: 0px !important;
        }

    </style>

    <div class="row p-3">
        <div class="col-5 col-lg-5 col-sm-5 col-md-5 col-xs-12">
            <div class="user-profile compact">
                <div class="up-head-w"
                    style="background-image: linear-gradient( var(--primary), 70%, var(--primary));">

                    <div class="up-main-info " style="padding-bottom: 150px; padding-top:10px">
                        <img alt="" src="{{ $alumno->url_foto }}" style="width: 50%">
                        <h2 class="up-header">
                            {{ $alumno->nuevo_numero_control }} - {{ $alumno->full_name }}
                        </h2>
                        <h6 class="up-sub-header">
                        Alumno
                        </h6>
                    </div>
                    <svg class="decor" width="842px" height="219px" viewBox="0 0 842 219"
                        preserveAspectRatio="xMaxYMax meet" version="1.1" xmlns="http://www.w3.org/2000/svg"
                        xmlns:xlink="http://www.w3.org/1999/xlink">
                        <g transform="translate(-381.000000, -362.000000)" fill="#FFFFFF">
                            <path class="decor-path"
                                d="M1223,362 L1223,581 L381,581 C868.912802,575.666667 1149.57947,502.666667 1223,362 Z">
                            </path>
                        </g>
                    </svg>
                </div>

                <div class="up-controls">
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="value-pair">
                                {{-- <div class="label">
                                    Status:
                                </div>
                                <div class="value badge badge-pill badge-{{ $cliente->statusClass }}">
                                    {{ $cliente->status }}
                                </div> --}}
                            </div>
                        </div>
                        <div class="col-sm-6 text-right">
                        </div>
                    </div>
                </div>

                <div class="up-contents">
                    <div class="m-b">
                        <div class="row m-b">
                            <div class="col-sm-12 b-b">

                            </div>
                        </div>
                        <div class="p-2">
                            @can(['editar_alumno'])
                                <a href="{{ route('alumnos.edit', $alumno) }}"
                                    class="btn btn-info btn-sm btn-circle float-right text-white mb-2" data-toggle="tooltip"
                                    data-placement="left" title="Editar informacion">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            @endcan
                            <h3>Grupos</h3>
                            @foreach ($alumno->grupos as $grupo)
                            <div class="post-box">
                                {{-- <div class="post-media" style="background-image: url(img/portfolio1.jpg)"></div> --}}
                                <div class="post-content">
                                <h6 class="post-title">
                                    Grupo: {{$grupo->clave}} - {{$grupo->especialidad->nombre}}
                                </h6>
                                <div class="post-text">
                                    Fecha inicio: {{ $grupo->fecha_inicio->format('d-m-Y')}}<br>
                                    Horario:<br> {!!$grupo->horario_corto!!}
                                    N° Semanas a cursar:<br> {!!$grupo->materias->sum('semanas')!!}<br>
                                    Fecha Inicio:<br> {!!  optional($grupo->pivot->fecha_inicio)->format('d-m-Y') !!}
                                    Semanas cursadas:<br> {!! (!empty($grupo->pivot->fecha_inicio)) ? $grupo->pivot->fecha_inicio->diffInWeeks( now() ): '' !!}<br>
                                </div>
                                <div class="post-foot">
                                    <div class="row">
                                        <div class="col-12">
                                            @can('cambio_horario_grupo')
                                            <a href="{{route('alumnos.cambio_horario', [$alumno->id,$grupo->id])}}" class="btn btn-sm btn-dark">Cambio Horario</a>
                                            @endcan

                                            @can('baja_grupo')
                                            <button data-id="{{$grupo->id}}" class="btn btn-sm btn-danger baja_grupo">Baja</button>
                                            @endcan
                                        </div>
                                        <div class="col-12">
                                            <div class="post-tags mt-2">
                                                <div class="badge badge-primary">
                                                    Alumnos {{$grupo->alumnos->count()}}
                                                </div>

                                                <a class="post-link float-right" href="{{route('grupos.show', $grupo)}}"><span>Ir a grupo</span><i class="os-icon os-icon-arrow-right7"></i></a>
                                            </div>
                                        </div>
                                    </div>



                                <br>


                                </div>
                                </div>
                            </div>



                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-7 col-lg-7 col-sm-7 col-md-7 col-xs-12">

            <div class="row">
                <div class="col-sm-12 col-xxxl-9">
                    <div class="element-wrapper">
                        <div class="element-box">
                            <div class="os-tabs-w">
                                <div class="os-tabs-controls">
                                    <ul class="nav nav-tabs smaller">

                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#tab-pagos-pendientes">Documentos</a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link active" data-toggle="tab" href="#tab-historial-pagos">Historial de pagos</a>
                                        </li>

                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#tab-info-alumno">Información del alumno</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#tab-info-asistencias">Asistencias</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" data-toggle="tab" href="#tab-info-productos">Productos</a>
                                        </li>

                                    </ul>
                                    <ul class="nav nav-pills smaller d-none d-md-flex">
                                    </ul>
                                </div>

                                <div class="tab-content">
                                    <div class="tab-pane" id="tab-pagos-pendientes">
                                        <div class="form-group">
                                            {!! Form::label('id_grupo','Selecciona el grupo:') !!}
                                            {!! Form::select('id_grupo', $alumno->grupos->pluck('nombre_compuesto','id'), optional($alumno->grupos->first())->id, ['id'=>'select_grupo','class'=>'form-control w-100']) !!}
                                        </div>

                                        <table class="table table-striped table-bordered table-hover" id="tb-pagos" width="100%">
                                            <thead>
                                                <tr>
                                                    <th># Pago</th>
                                                    <th>Concepto</th>
                                                    <th>Monto</th>
                                                    <th>Saldo</th>
                                                    <th>Fecha Limite</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>

                                        @can('asignar_apoyos_especiales')
                                            <fieldset class="form-group">
                                                <legend>Apoyos especiales</legend>
                                                <div class="row">
                                                    <div class="col-12">
                                                        <button type="button" id="btn-agregar-apoyo-especial" title="Apoyo especial" class="btn btn-sm btn-primary">Agregar Apoyo</button>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-12">
                                                        <table class="table table-striped table-bordered table-hover" id="tb-apoyos-especiales" width="100%">
                                                            <thead>
                                                                <tr>
                                                                    <th></th>
                                                                    <th>Fecha Final</th>
                                                                    <th>Monto</th>
                                                                    <th>Acciones</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                </div>
                                            </fieldset>
                                        @endcan

                                    </div>

                                    <div class="tab-pane active" id="tab-historial-pagos">

                                        <table class="table table-striped table-bordered table-hover" id="tb-historial-pagos" width="100%">
                                            <thead>
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Pago</th>
                                                    <th>Forma Pago</th>
                                                    <th>Cubrio</th>
                                                    <th>Recibio</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                            </tbody>
                                        </table>


                                    </div>

                                    <div class="tab-pane" id="tab-info-alumno">
                                        @include('alumnos.partials._info_alumno')
                                    </div>

                                    <div class="tab-pane" id="tab-info-asistencias">
                                        <table class="table">
                                            @foreach ($alumno->asistencias as $asistencia)
                                                <tr>
                                                    <td>
                                                        <i class="fas fa-check text-success   "></i>
                                                    </td>
                                                    <td>
                                                        {{$asistencia->fecha->format('l d \d\e F \d\e\l Y \a \l\a\s H:i')}}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>


                                    <div class="tab-pane" id="tab-info-productos">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Fecha</th>
                                                    <th>Conceptos</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            @foreach ($alumno->ventas as $venta)

                                                    <td>
                                                        {{$venta->fecha->format('d-m-Y')}}
                                                    </td>
                                                    <td>
                                                        @foreach ($venta->partidas as $partida)
                                                            {{optional($partida->producto)->nombre}}<br>
                                                        @endforeach
                                                    </td>
                                                    <td class="text-right">
                                                        {{number_format($partida->total,2,'.',',')}}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    @include('alumnos.modals.apoyos_especiales')
@endsection


@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            const dom = {
                tb_pagos: $("#tb-pagos"),
                tb_historial_pagos: $("#tb-historial-pagos"),
                tb_apoyos: $("#tb-apoyos-especiales"),
                btn_apoyo_especial: $('#btn-agregar-apoyo-especial'),
                form_apoyo_especial: $("#form-apoyo-especial"),
                modal_apoyo_especial: $("#modal-apoyo-especial")
            }

            const CONFIG_DATEPICKER = {
                days: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"],
                daysShort: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
                daysMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
                months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
                monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
                today: "Hoy",
                monthsTitle: "Meses",
                clear: "Borrar",
            }

            $.fn.datepicker.dates['es'] = CONFIG_DATEPICKER  //👉 DATEPICKER

            dom.form_apoyo_especial.find('[name="fecha_final"]').datepicker({
                language: 'es',
                format: 'dd-mm-yyyy',
                ignoreReadonly: false,
                todayHighlight: true,
                todayBtn: true,
                autoclose: true,
            });

            var dt_pagos = dom.tb_pagos.DataTable({
                dom: "<'row'<'col-12'f>><'row'<'col-12'tr>><'row'<'col-5'i><'col-7'p>>",
                processing: true,
                serverSide: true,
                responsive: true,
                pageLength: 10,
                ajax: {
                    url: "{{ route('alumnos.datatables_pagos') }}",
                    type: "POST",
                    data: function (d) {
                        d.id_alumno = "{{ $alumno->id }}";
                        d.id_grupo = $('#select_grupo').val();
                        d._token = $("meta[name='csrf-token']").attr("content");
                    },
                    beforeSend: function(xhr,type) {
                    if (!type.crossDomain) {
                            xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
                        }
                    },
                },
                columns: [
                    {data: 'DT_RowIndex', name: 'DT_RowIndex', searchable: false,orderable: false},
                    {data: 'concepto', name: 'concepto'},
                    {data: 'monto', name: 'monto',className:"text-right"},
                    {data: 'saldo', name: 'saldo', className:"text-right"},
                    {data: 'fecha_limite', name: 'fecha_limite'},
                    {data: 'status', className:"text-center", name: 'status'},
                ],
                order: [[ 4, "asc" ]],
                language: {
                    "lengthMenu": "Mostrar _MENU_ registros por pagina",
                    "zeroRecords": "No se encontro ningún registro",
                    "info": "Mostrando del _START_ al _END_ de _TOTAL_ registros. (Página _PAGE_ de _PAGES_)",
                    "infoEmpty": "No hay registros disponibles",
                    "infoFiltered": "(Filtrado de un total de _MAX_ registros)",
                    "search": "Buscar:",
                    "paginate": {
                        "first": "Primera",
                        "last": "Última",
                        "previous": '<i class="fas fa-chevron-left"></i>',
                        "next": '<i class="fas fa-chevron-right"></i>'
                    },
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                },
                drawCallback: function (settings) {
                    $("[data-toggle='tooltip']").tooltip();
                },
                initComplete: function(settings, json) {
                    var dt_apoyos = dom.tb_apoyos.DataTable({
                        dom: "<'row'<'col-12'f>><'row'<'col-12'tr>><'row'<'col-5'i><'col-7'p>>",
                        processing: true,
                        serverSide: true,
                        responsive: true,
                        pageLength: 10,
                        ajax: {
                            url: "{{ route('apoyos-especiales.datatables') }}",
                            type: "POST",
                            data: function (d) {
                                d.id_alumno = "{{ $alumno->id }}";
                                d.id_grupo = $('#select_grupo').val();
                                d._token = $("meta[name='csrf-token']").attr("content");
                            },
                            beforeSend: function(xhr,type) {
                            if (!type.crossDomain) {
                                    xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
                                }
                            },
                        },
                        columns: [
                            {data: 'id', name: 'id',visible:false},
                            {data: 'fecha_final', name: 'fecha_final'},
                            {data: 'precio', name: 'precio'},
                            { data: 'buttons', name: 'buttons', orderable: false, searchable: false },
                        ],
                        order: [[ 0, "asc" ]],
                        language: {
                            "lengthMenu": "Mostrar _MENU_ registros por pagina",
                            "zeroRecords": "No se encontro ningún registro",
                            "info": "Mostrando del _START_ al _END_ de _TOTAL_ registros. (Página _PAGE_ de _PAGES_)",
                            "infoEmpty": "No hay registros disponibles",
                            "infoFiltered": "(Filtrado de un total de _MAX_ registros)",
                            "search": "Buscar:",
                            "paginate": {
                                "first": "Primera",
                                "last": "Última",
                                "previous": '<i class="fas fa-chevron-left"></i>',
                                "next": '<i class="fas fa-chevron-right"></i>'
                            },
                            "loadingRecords": "Cargando...",
                            "processing": "Procesando...",
                        },
                        drawCallback: function (settings) {
                            $("[data-toggle='tooltip']").tooltip();
                        },
                    });

                    dom.tb_apoyos.on('click',"a[data-action='delete']",function(event){
                        event.preventDefault();

                        swal({
                            title: "¿Estas seguro de eliminar el registro?",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#ff3333",
                            cancelButtonColor: "#CDCDCD",
                            confirmButtonText: "Borrar",
                            cancelButtonText: "Cancelar",
                            showLoaderOnConfirm: false,
                        }).then(function(result) {
                            if (!result.value) {
                                return;
                            }

                            wait.modal('show');

                            $.ajax({
                                url: event.target.href,
                                type: 'POST',
                                cache: false,
                                data: {
                                    _token: $("meta[name='csrf-token']").attr("content"),
                                    _method: 'DELETE',
                                },
                                success: function (response){
                                    dt_apoyos.ajax.reload( function(e){
                                        wait.modal('hide');
                                        toastr.success('Éxito', 'Se borró con éxito el registro');
                                    }, false )
                                },
                                error:function(error){
                                    setTimeout(() => {
                                        wait.modal('hide');
                                        toastr.error('Error', 'Ocurrio un error inesperado');
                                    }, 250);
                                }
                            });
                        })
                    })

                    $('#select_grupo').change(function(){
                        dt_pagos.draw();
                        dt_apoyos.draw();
                    });

                    dom.btn_apoyo_especial.click(function(e){
                        dom.form_apoyo_especial[0].reset();
                        dom.modal_apoyo_especial.modal('show');
                    })

                    dom.form_apoyo_especial.submit(function(e){
                        e.preventDefault();

                        dom.modal_apoyo_especial.modal('hide');
                        wait.modal('show');

                        const $form = $(this);
                        const formData = new FormData(this);
                        formData.append('id_grupo',$('#select_grupo').val());
                        formData.append('id_alumno',"{{ $alumno->id }}");

                        $.ajax({
                            url: $form.attr('action'),
                            type: 'POST',
                            cache: false,
                            contentType: false,
                            processData: false,
                            data: formData,
                            success: function (response){
                                dt_apoyos.ajax.reload( function(e){
                                    setTimeout(() => {
                                        wait.modal('hide');
                                        toastr.success('Éxito', response.message || 'Apoyo agregado correctamente');
                                    })
                                }, false )
                            },
                            error:function(error){
                                wait.modal('hide');
                                const errors = error.responseJSON || {};

                                setTimeout(() => {
                                    dom.modal_apoyo_especial.modal('show');
                                    toastr.error('Error',  errors.message || 'Ocurrio un error inesperado');
                                }, 250);
                            }
                        });


                    })
                }
            });

            var tb_historial_pagos = dom.tb_historial_pagos.DataTable({
                dom: "<'row'<'col-12'f>><'row'<'col-12'tr>><'row'<'col-5'i><'col-7'p>>",
                processing: true,
                serverSide: true,
                responsive: true,
                pageLength: 10,
                ajax: {
                    url: "{{ route('alumnos.datatables_historial_pagos') }}",
                    type: "POST",
                    data: function (d) {
                        d.id_alumno = "{{ $alumno->id }}";
                        d._token = $("meta[name='csrf-token']").attr("content");
                    },
                    beforeSend: function(xhr,type) {
                    if (!type.crossDomain) {
                            xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
                        }
                    },
                },
                columns: [
                    {data: 'fecha', name: 'fecha'},
                    {data: 'monto', name: 'monto'},
                    {data: 'forma_pago', name: 'forma_pago'},
                    {data: 'abonos.alumno_pago.concepto', name: 'abonos.alumno_pago.concepto'},
                    {data: 'recibio.nombres', name: 'recibio.nombres'},
                ],
                order: [[ 0, "desc" ]],
                language: {
                    "lengthMenu": "Mostrar _MENU_ registros por pagina",
                    "zeroRecords": "No se encontro ningún registro",
                    "info": "Mostrando del _START_ al _END_ de _TOTAL_ registros. (Página _PAGE_ de _PAGES_)",
                    "infoEmpty": "No hay registros disponibles",
                    "infoFiltered": "(Filtrado de un total de _MAX_ registros)",
                    "search": "Buscar:",
                    "paginate": {
                        "first": "Primera",
                        "last": "Última",
                        "previous": '<i class="fas fa-chevron-left"></i>',
                        "next": '<i class="fas fa-chevron-right"></i>'
                    },
                    "loadingRecords": "Cargando...",
                    "processing": "Procesando...",
                },
                drawCallback: function (settings) {
                    $("[data-toggle='tooltip']").tooltip();
                },
                initComplete: function(settings, json) {

                }
            });

            $('.baja_grupo').click(function(){
                id = $(this).data('id');
                swal({
                            title: "¿Estas seguro de dar de baja al alumno de este grupo?",
                            type: "warning",
                            showCancelButton: true,
                            confirmButtonColor: "#ff3333",
                            cancelButtonColor: "#CDCDCD",
                            confirmButtonText: "Si, dar de baja",
                            cancelButtonText: "Cancelar",
                            showLoaderOnConfirm: false,
                        }).then(function(result) {
                            if (!result.value) {
                                return;
                            }

                            wait.modal('show');

                            $.ajax({
                                url: "{{route('alumnos.baja_grupo')}}",
                                type: 'POST',
                                cache: false,
                                data: {
                                    _token: $("meta[name='csrf-token']").attr("content"),
                                    id_alumno: {{$alumno->id}},
                                    id_grupo: id,
                                },
                                success: function (response){
                                    location.reload();
                                },
                                error:function(error){
                                    setTimeout(() => {
                                        wait.modal('hide');
                                        toastr.error('Error', 'Ocurrio un error inesperado');
                                    }, 250);
                                }
                            });
                        })
            });
        });
    </script>
@endsection
