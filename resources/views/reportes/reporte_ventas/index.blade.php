@extends('layouts.template-'.config('settings.template').'.plantilla')

@section('titulo')
    Reporte de ventas {{ date('Y') }}
@endsection

@section('breadcrumb')
    <ol class="breadcrumb no_print">
        <li class="breadcrumb-item">
            <a href="{{ url('/') }}">Inicio</a>
        </li>
        <li class="breadcrumb-item">
            Ventas
        </li>
        <li class="breadcrumb-item">
            Reportes
        </li>
        <li class="breadcrumb-item active">
            <strong>Reporte de ventas</strong>
        </li>
    </ol>
@endsection

@section('contenido')
    <style>
        .contact-box:hover {
            transform: scale(1.05)
        }

        .content-box {
            padding: 10px !important;
        }

        @media screen {
            .print_only {
                display: none
            }
        }

        @media print {

            table.dataTable td,
            table.dataTable th {
                font-size: 12pt;
            }
        }
    </style>

    <div class="row ">
        <div class="col-lg-12 no_print">
            <div class="btn-group ">
                <a href="{{ route('reportes.reporte-ventas.index',['tipo' => 'dia']) }}"
                    class="btn @if ($tipo=='dia' ) btn-primary  text-white @else btn-white @endif">Día</a>
                <a href="{{ route('reportes.reporte-ventas.index',['tipo' => 'semanal']) }}"
                    class="btn  @if ($tipo=='semanal' ) btn-primary  text-white @else btn-white @endif">Semanal</a>
                <a href="{{ route('reportes.reporte-ventas.index',['tipo' => 'mes']) }}"
                    class="btn  @if ($tipo=='mes' ) btn-primary  text-white @else btn-white @endif">Mensual</a>
                <a href="{{ route('reportes.reporte-ventas.index',['tipo' => 'anual']) }}"
                    class="btn  @if ($tipo=='anual' ) btn-primary  text-white @else btn-white @endif">Anual</a>
            </div>
        </div>
    </div>
    <div class="row mt-2">
        <div class="col-lg-9">
            <div class="element-box p-3">
                <div class="ibox-title mb-2">
                    <div class="row">

                        @if (request('tipo','dia') == 'dia')
                            <div class="col-1 col-xxl-1 col-xl-1 col-md-1 col-sm-1">
                                <a href="{{ route('reportes.reporte-ventas.index',['tipo' => 'dia','fecha'=>$fecha_antes->format('d-m-Y') ]) }}"
                                    data-toggle="tooltip" data-title="{{ $fecha_antes->format('d \d\e F  \d\e\l  Y') }}"
                                    data-placement="top" class="btn btn-primary btn-sm text-white no_print"><i
                                        class="fas fa-arrow-alt-circle-left fa-2x"></i></a>
                            </div>
                            <div class="col-10 col-xxl-10 col-xl-10 col-md-10 col-sm-10">
                                <center>
                                    <h3>{{ $fecha->format('d \d\e F \d\e\l Y') }} <small><a class="no_print"
                                                data-toggle="modal" data-target="#seleccionarFecha"><i
                                                    class="far fa-calendar-alt"></i></a></small></h3>
                                </center>
                            </div>
                            <div class="col-1 col-xxl-1 col-xl-1 col-md-1 col-sm-1">
                                <a href="{{ route('reportes.reporte-ventas.index',['tipo'=>'dia','fecha' => $fecha_despues->format('d-m-Y') ]) }}"
                                    data-toggle="tooltip" data-title="{{ $fecha_despues->format('d \d\e F \d\e\l Y') }}"
                                    data-placement="top" class="btn btn-primary btn-sm text-white no_print"><i
                                        class="fas fa-arrow-alt-circle-right fa-2x"></i></a>
                            </div>
                        @endif
                        @if (request('tipo','dia') == 'mes')
                            <div class="col-1 col-xxl-1 col-xl-1 col-md-1 col-sm-1">
                                <a href="{{ route('reportes.reporte-ventas.index',['tipo'=>'mes','fecha'=> $fecha_antes->format('d-m-Y') ]) }}"
                                    data-toggle="tooltip"
                                    data-title="{{ $fecha_antes->format('d \d\e F  \d\e\l  Y') }}"
                                    data-placement="top"
                                    class="btn btn-primary btn-sm text-white no_print">
                                    <i class="fas fa-arrow-alt-circle-left fa-2x"></i>
                                </a>
                            </div>
                            <div class="col-10 col-xxl-10 col-xl-10 col-md-10 col-sm-10">
                                <center>
                                    <h3>{{ $fecha->format('F \d\e\l Y') }} <small><a class="no_print" data-toggle="modal"
                                                data-target="#seleccionarFecha"><i
                                                    class="far fa-calendar-alt"></i></a></small></h3>
                                </center>
                            </div>
                            <div class="col-1 col-xxl-1 col-xl-1 col-md-1 col-sm-1">
                                <a href="{{ route('reportes.reporte-ventas.index',['tipo' => 'mes','fecha' => $fecha_despues->format('d-m-Y') ]) }}"
                                    data-toggle="tooltip" data-title="{{ $fecha_despues->format('d \d\e F \d\e\l Y') }}"
                                    data-placement="top" class="btn btn-primary btn-sm text-white no_print"><i
                                        class="fas fa-arrow-alt-circle-right fa-2x"></i></a>
                            </div>
                        @endif

                        @if (request('tipo','dia') == 'semanal')
                            <div class="col-1 col-xxl-1 col-xl-1 col-md-1 col-sm-1">
                                <a href="{{ route('reportes.reporte-ventas.index',['tipo' => 'semanal','fecha' => $fecha_antes->startOfWeek()->format('d-m-Y') ]) }}"
                                    data-toggle="tooltip"
                                    data-title="{{ $fecha_antes->format('d \d\e F  \d\e\l  Y') }}"
                                    data-placement="top"
                                    class="btn btn-primary btn-sm text-white no_print">
                                    <i class="fas fa-arrow-alt-circle-left fa-2x"></i>
                                </a>
                            </div>
                            <div class="col-10 col-xxl-10 col-xl-10 col-md-10 col-sm-10">
                                <center>
                                    <h3>{{ $fecha->startOfWeek()->format('d \d\e F \d\e\l Y') }} al
                                        {{ $fecha->endOfWeek()->format('d \d\e F \d\e\l Y') }} <small><a class="no_print"
                                                data-toggle="modal" data-target="#seleccionarFecha"><i
                                                    class="far fa-calendar-alt"></i></a></small></h3>
                                </center>
                            </div>
                            <div class="col-1 col-xxl-1 col-xl-1 col-md-1 col-sm-1">
                                <a href="{{ route('reportes.reporte-ventas.index',['tipo'=> 'semanal','fecha'=>$fecha_despues->startOfWeek()->format('d-m-Y')]) }}"
                                    data-toggle="tooltip" data-title="{{ $fecha_despues->format('d \d\e F \d\e\l Y') }}"
                                    data-placement="top" class="btn btn-primary btn-sm text-white no_print"><i
                                        class="fas fa-arrow-alt-circle-right fa-2x"></i></a>
                            </div>
                        @endif

                        @if (request('tipo','dia') == 'anual')
                            <div class="col-1 col-xxl-1 col-xl-1 col-md-1 col-sm-1">
                                <a href="{{ route('reportes.reporte-ventas.index',['tipo' => 'anual','fecha'=> $fecha_antes->format('d-m-Y'), ]) }}"
                                    data-toggle="tooltip"
                                    data-title="{{ $fecha_antes->format('Y') }}"
                                    data-placement="top"
                                    class="btn btn-primary btn-sm text-white no_print">
                                    <i class="fas fa-arrow-alt-circle-left fa-2x"></i>
                                </a>
                            </div>
                            <div class="col-10 col-xxl-10 col-xl-10 col-md-10 col-sm-10">
                                <center>
                                    <h3>{{ $fecha->format('Y') }}
                                        <small><a class="no_print" data-toggle="modal"
                                                    data-target="#seleccionarFecha"><i
                                                    class="far fa-calendar-alt"></i></a>
                                        </small>
                                    </h3>
                                </center>
                            </div>
                            <div class="col-1 col-xxl-1 col-xl-1 col-md-1 col-sm-1">
                                <a href="{{ route('reportes.reporte-ventas.index',['tipo'=>'anual','fecha'=> $fecha_despues->format('d-m-Y') ]) }}"
                                    data-toggle="tooltip" data-title="{{ $fecha_despues->format(' Y') }}"
                                    data-placement="top" class="btn btn-primary btn-sm text-white no_print"><i
                                        class="fas fa-arrow-alt-circle-right fa-2x"></i></a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="ibox-content mt-2">

                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tabla_abonos">
                            <thead>
                                <tr>
                                    <th>Folio</th>
                                    <th>Fecha Abono</th>
                                    <th>No. Control</th>
                                    <th>Alumno</th>
                                    <th>Concepto</th>
                                    <th>Recibido por</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($pagos as $pago)
                                    <tr class="gradeX" id="abono-{{ $pago->id }}">
                                        @if( $pago->folio_fiscal )
                                            <td class="text-primary text-center">
                                                <a class='editable_pagos_folio_fiscal editable'
                                                    data-type='text'
                                                    data-name='folio_fiscal'
                                                    data-pk='{{ $pago->id }}'
                                                    data-url='{{ route('abonos.actualizar_pago_xeditable') }}'
                                                    data-value='{{$pago->folio_fiscal }}'>
                                                    {{ $pago->folio_fiscal }}
                                                </a>

                                                <div class="mt-1">
                                                    @can('eliminar_movimiento_reporte_ventas')
                                                        <button class="btn btn-sm btn-danger"
                                                            data-url="{{ route('reportes.reporte-ventas.eliminar-pago',$pago) }}"
                                                            data-action="eliminar"><i class="fas fa-trash"></i>
                                                        </button>
                                                    @endcan
                                                    <button type="button"
                                                        data-action="imprimir"
                                                        data-url="{{ route('punto_de_venta.ticket',$pago) }}"
                                                        class="btn btn-sm btn-primary"
                                                        title="Imprimir"  >
                                                        <i class="fas fa-print"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        @else
                                            <td class="text-danger text-center">
                                                <a class='editable_pagos_folio editable'
                                                    data-type='text'
                                                    data-name='folio'
                                                    data-pk='{{ $pago->id }}'
                                                    data-url='{{ route('abonos.actualizar_pago_xeditable') }}'
                                                    data-value='{{$pago->folio }}'>
                                                    {{ $pago->folio }}
                                                </a>
                                                <div class="mt-1">
                                                    @can('eliminar_movimiento_reporte_ventas')
                                                        <button class="btn btn-sm btn-danger"
                                                            type="button"
                                                            data-url="{{ route('reportes.reporte-ventas.eliminar-pago',$pago) }}"
                                                            data-action="eliminar"><i class="fas fa-trash"></i>
                                                        </button>
                                                    @endcan
                                                    <button type="button"
                                                        data-action="imprimir"
                                                        data-url="{{ route('punto_de_venta.ticket',$pago) }}"
                                                        class="btn btn-sm btn-primary"
                                                        title="Imprimir"  >
                                                        <i class="fas fa-print"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        @endif

                                        <td class="text-nowrap">
                                            <a class="editable_abonos_fecha editable"
                                                data-name="fecha"
                                                data-type="date"
                                                data-value="{{ $pago->fecha->format('Y-m-d') }}"
                                                data-pk="{{ $pago->id }}"
                                                data-url="{{ route('abonos.actualizar_pago_xeditable') }}">
                                                {{ $pago->fecha->format('d-m-Y') }}<br>
                                                {{ $pago->fecha->format('h:i a') }}
                                            </a>

                                        </td>

                                        <td>
                                            {{ $pago->alumno->nuevo_numero_control }}
                                        </td>

                                        <td>
                                            <a class='editable_abonos_id_alumno editable'
                                                data-type='select2'
                                                data-pk='{{ $pago->id }}'
                                                data-url='{{ route('abonos.actualizar_informacion_xeditable') }}'
                                                data-value='{{ $pago->id_alumno }}'
                                                data-name='id_alumno'
                                                >
                                                {{ $pago->alumno->fullname }}
                                            </a>
                                        </td>

                                        <td>
                                            @foreach ($pago->abonos as $abono)
                                                <a class='editable_alumnos_pagos_concepto editable'
                                                    data-type='text'
                                                    data-name='concepto'
                                                    data-pk='{{ $abono->alumno_pago->id }}'
                                                    data-url='{{ route('abonos.actualizar_alumno_pago_xeditable') }}'
                                                    data-value='{{ $abono->alumno_pago->concepto }}'
                                                    >
                                                    {{ $abono->alumno_pago->concepto }}
                                                </a>
                                            @endforeach

                                        </td>
                                        <td>
                                            {{ $pago->recibio->full_name }}
                                        </td>

                                        <td class="text-right text-nowrap" style="cursor:pointer">
                                            <a class='editable_abonos_monto editable'
                                                data-type='number'
                                                data-step="0.01"
                                                data-name='monto'
                                                data-pk='{{ $pago->id }}'
                                                data-url='{{ route('abonos.actualizar_informacion_xeditable') }}'
                                                data-value='{{ $pago->monto }}'>
                                                $ {{ number_format($pago->monto, '2', '.', ',') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>

                        <table class="table table-bordered mt-3 float-right print_only" style="width:50%;">
                            <tr>
                                <td style="font-size: 1rem;">Total: </td>
                                <td class="text-right"><b style="font-size: 1rem;"> ${{ number_format($pagos->sum('monto'), 2, '.', ',') }}</b>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2" class="bg-primary"></td>
                            </tr>
                        </table>
                    </div>

                </div>
            </div>

        </div>

        <div class="col-lg-3 mb-3 no_print">

            @can('generar_corte_caja')
                <div class="row pb-2">
                    <div class="col-12">
                        <a class="btn btn-block btn-primary"
                            target="_blank"
                            href="{{ route('reportes.reporte-ventas.corte-caja',['tipo' => request('tipo','dia'),'fecha'=> request('fecha')]) }}">
                            <i class="fas fa-pdf"></i> Corte de caja
                        </a>
                    </div>
                </div>
            @endcan

            <div class="col-sm-12 col-xxxl-12 p-1">
                <a class="element-box el-tablo p-3" href="#">
                  <div class="label mb-2">
                    Total
                  </div>
                  <div class="value">
                    $ <span id="span_abonos_monto">{{ number_format($pagos->sum('monto'), 2, '.', ',') }}</span>
                  </div>
                </a>
            </div>
            @can('convertir_no_fiscales_a_fiscales')
                @if($tipo == 'semanal' || $tipo == 'mes' )
                    <div class="col-sm-12 col-xxxl-12 p-1">
                        <a class="element-box el-tablo" href="#">
                        <div class="label mb-2">
                            No fiscales
                        </div>
                        <div class="value">
                            $ <span id="span-monto-abono-no-fiscal">{{ number_format($pagos->where('venta_fiscal',0)->sum('monto'), 2, '.', ',') }}</span>
                        </div>
                        </a>
                    </div>
                    <div class="col-sm-12 col-xxxl-12 p-1">
                        <a class="element-box el-tablo" href="#">
                        <div class="label mb-2">
                            Fiscales
                        </div>
                        <div class="value">
                            $ <span id="span-monto-abono-fiscal" >{{ number_format($pagos->where('venta_fiscal',1)->sum('monto'), 2, '.', ',') }}</span>
                        </div>
                        </a>
                    </div>
                    <div class="col-sm-12 col-xxxl-12 p-1">
                        <a class="element-box el-tablo" href="#">
                        <div class="label mb-2">
                            % de fiscales
                        </div>
                        <div class="value">
                            @if($pagos->sum('monto') > 0)
                            {{ number_format( $pagos->where('venta_fiscal',1)->sum('monto') / $pagos->sum('monto') *100, 2, '.', ',') }} %
                            @else
                            NO SE HAN REGISTRADO VENTAS
                            @endif
                        </div>
                        </a>
                    </div>
                    <button id="convertir_fiscales" class="btn btn-primary btn-block ">Convertir ventas no fiscales a fiscales</button>
                @endif

            @endcan
        </div>
    </div>

    <div class="modal fade" id="seleccionarFecha" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Selecciona una fecha</h4>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span
                            class="sr-only">Close</span></button>

                </div>

                <div class="modal-body">
                    <center>
                        <div id="datepicker"></div>
                    </center>
                </div>

            </div>
        </div>
    </div>

    <div class="modal inmodal fade animated" id="modal-ticket" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content animated bounceInRight">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Ticket</h4>
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">
                            &times;</span><span class="sr-only">Close</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div id="contenido-ticket"></div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <link rel="stylesheet" href="{{ asset('plugins/xeditable/css/bootstrap-editable.css') }}">
    <script src="{{ asset('plugins/xeditable/js/bootstrap-editable.min.js') }}"></script>
    <script src="{{ asset('template-clean-admin/bower_components/select2/dist/js/i18n/es.js') }}"></script>

    <script type="text/javascript">
        $(function(){

            const Helpers = {
                number_format: function(number,decimals){
                    return parseFloat(number).toFixed(decimals).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString()
                },
                formTokenDelete:function(){
                    const token = document.head.querySelector('meta[name="csrf-token"]');

                    const form = $('<form>', { 'method': 'POST' });

                    const inputToken = $('<input>', {
                        'type': 'hidden',
                        'name': '_token',
                        'value': token.content
                    });

                    const inputDelete = $('<input>', {
                        'type': 'hidden',
                        'name': '_method',
                        'value': 'DELETE'
                    });

                    form.append(inputToken);
                    form.append(inputDelete);

                    return form;
                },
            };

            const CONFIG_DATEPICKER = {
                days: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"],
                daysShort: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
                daysMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
                months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
                monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
                today: "Hoy",
                monthsTitle: "Meses",
                clear: "Borrar",
                weekStart: 1,
            }

            const dom = {
                tikets:{
                    contenido_ticket:$("#contenido-ticket"),
                    modal: $("#modal-ticket"),
                },
            };

            $.fn.datepicker.dates['es'] = CONFIG_DATEPICKER     //👉 DATEPICKER
            $.fn.bdatepicker.dates['es'] = CONFIG_DATEPICKER    //👉 XEDITABLE DATEPICKER

            $('#tabla_abonos').on('click','button[data-action="eliminar"]',function(e){
                const $button = $(this);


                swal({
                    title: "Deseas eliminar este registro",
                    text:'Esta acción no podrá deshacerse',
                    type: "warning",
                    showCancelButton: true,
                    confirmButtonColor: "#ff3333",
                    cancelButtonColor: "#CDCDCD",
                    confirmButtonText: "Si",
                    cancelButtonText: "Cancelar",
                    showLoaderOnConfirm: false,
                }).then(function(result) {
                    if (!result.value) {
                        return;
                    }

                    wait.modal('show');

                    const form = Helpers.formTokenDelete();

                    form.attr('action',$button.data('url'));

                    form.appendTo('body').submit();

                })
            });

            $('#tabla_abonos').on('click','button[data-action="imprimir"]',function(e){
                const $button = $(this);
                const route = $button.data('url');

                dom.tikets.modal.modal('show');
                dom.tikets.contenido_ticket.html();
                dom.tikets.contenido_ticket.html(`<iframe scrolling='auto' type='text/html' scroll='auto' src='${route}' width='100%' height='450px' align='center'></iframe>`);
            });

            $('#tabla_abonos').DataTable({
                responsive: true,
                "lengthMenu": [ [-1, 25, 50 ], ["Todos", 25, 50] ],
                buttons: [
                    {extend: 'excel', title: 'Ventas'},
                ],
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros por pagina",
                    "zeroRecords": "No se encontro ningún registro",
                "info": "Mostrando del _START_ al _END_ de _TOTAL_ registros. (Página _PAGE_ de _PAGES_)",
                    "infoEmpty": "No hay registros disponibles",
                    "infoFiltered": "(Filtrado de un total de _MAX_ registros)",
                    "search": "Buscar:",
                    "paginate": {
                        first:      "Primera",
                        last:       "Última",
                        previous: '<i class="fas fa-chevron-left"></i>',
                        next: '<i class="fas fa-chevron-right"></i>'
                    },
                "loadingRecords": "Cargando...",
                    "processing":     "Procesando...",
                },
                "dom": "<'row'  <'toolbar col-sm-6 col-xs-3 text-left no_print' B> <'col-sm-6 col-xs-9 no_print'f>>" +
                "<'row'<'col-sm-12 table-responsive'tr>>" +
                "<'row'<'col-sm-12 col-lg-12 col-xs-12 no_print'p>>",
                buttons: [
                {
                    "extend": 'excelHtml5',
                    "text":'Excel <i class="fas fa-file-excel"></i>',
                    'title': 'Reporte de ventas',
                    "className": 'btn btn-primary',
                }],
                order: [[0,'desc']]

            });


            $('#datepicker').datepicker({
                language: 'es',
                format: 'dd-mm-yyyy',
                ignoreReadonly: false,
                todayHighlight: true,
                todayBtn: true
            });

            $('#datepicker').on('changeDate', function() {
                window.location = "{{route('reportes.reporte-ventas.index')}}?tipo={{$tipo}}&fecha="+$('#datepicker').datepicker('getFormattedDate')+"&forma_pago={{@$_GET['forma_pago']}}"
            });

            $('#convertir_fiscales').click(function(){
                    swal({
                        title: "¿Estas seguro de convertir las ventas no fiscales a fiscales en este periodo?",
                        text:'Esta acción no podrá deshacerse',
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "#1bc51c",
                        cancelButtonColor: "#CDCDCD",
                        confirmButtonText: "Si",
                        cancelButtonText: "Cancelar",
                        showLoaderOnConfirm: false,
                    }).then(function(result) {
                        if (!result.value) {
                            return;
                        }
                        // wait.modal('show');

                        $.ajax({
                            url: "{{route('reportes.reporte-ventas.convertir_ventas_fiscales')}}",
                            type: 'POST',
                            data: {
                                tipo: "{{$tipo}}"
                            },
                            success: function (response){
                                //  wait.modal('hide');

                                location.reload();
                            },
                            fail:function(error){
                                toastr.error('Error', 'Ocurrio un error inesperado');
                            }
                        });
                    })
            });

            // 👉 XEDITABLES
            $('.editable').on('shown', function(e, editable) {
                $('.editable-submit').html('<i class="fas fa-check fa-1x"></i>');
                $('.editable-cancel').html('<i class="fas fa-times"></i>');
            });

            $('.editable_alumnos_pagos_concepto').editable({
                emptytext: 'Vacio',
                onblur: 'ignore',
            });

            $('.editable_pagos_folio_fiscal').editable({
                emptytext: 'Vacio',
                onblur: 'ignore',
            });

            $('.editable_pagos_folio').editable({
                emptytext: 'Vacio',
                onblur: 'ignore',
            });

            $('.editable_abonos_monto').editable({
                emptytext: 'Vacio',
                onblur: 'ignore',
                display: function(value) {
                    let format = Helpers.number_format(value,2);
                    $(this).text("$ "+ format);
                }
            });

            $('.editable_abonos_monto').on('save',function(e,params) {
                // 👉 ACTUALIZACION DEL MONTO DE LOS ABONOS
                $.ajax({
                    url: "{{ route('reportes.reporte-ventas.index') }}",
                    type: 'GET',
                    cache: false,
                    data: {
                        tipo:"{{ request('tipo','dia') }}",
                        fecha:"{{ request('fecha') }}"
                    },
                    success: function (response){
                        $("#span_abonos_monto").html(Helpers.number_format(response.monto_abonos,2));
                        $("#span-monto-abono-fiscal").html(Helpers.number_format(response.monto_abono_fiscal,2))
                        $("#span-monto-abono-no-fiscal").html(Helpers.number_format(response.monto_abono_no_fiscal,2))
                    },
                    fail:function(error){
                        toastr.error('Error', 'Ocurrio un error inesperado');
                    }
                });
            });

            $('.editable_abonos_id_alumno').editable({
                select2: {
                    placeholder: 'Selecciona un alumno',
                    allowClear: true,
                    minimumInputLength: 3,
                    ajax: {
                        method: 'POST',
                        url: '{{ route("alumnos.traer_alumnos_select2") }}',
                        dataType: 'json',
                        cache: false,
                        delay:250,
                        data:function (params) {
                            return {
                                _token: '{{ csrf_token() }}',
                                term: params.term,
                                page: params.page || 1,
                                id_sucursal: "{{ optional(session('sucursal'))->id }}",
                                status:'Alumno'
                            }
                        },
                        beforeSend:function(xhr,type){
                            xhr.setRequestHeader('X-CSRF-Token',$('meta[name="csrf-token"]').attr('content'))
                        },
                        processResults: function (data, page) {
                            return data;
                        }
                    },
                    templateResult: function(option){
                        if (option.loading) {
                            return option.text;
                        }

                        if(!option.nuevo_numero_control || !option.nombres || !option.apellido_paterno || !option.apellido_materno){
                            return option.text
                        }

                        return `No. Control: ${option.nuevo_numero_control} | Nombre: ${option.nombres} ${option.apellido_paterno} ${option.apellido_materno}`;
                    },
                    templateSelection: function(option){
                        if(!option.nuevo_numero_control ||  !option.nombres || !option.apellido_paterno || !option.apellido_materno){
                            return option.text
                        }

                        return `No. Control: ${option.nuevo_numero_control} | Nombre: ${option.nombres} ${option.apellido_paterno} ${option.apellido_materno}`;
                    },
                },
                display: function(value, sourceData,response) {
                    if(sourceData){
                        $(this).text(sourceData.alumno);
                    }
                },
                mode:'inline',
                emptytext: 'Vacio',
                tpl: '<select style="width:100%;z-index: 289;">',
            })

            $('.editable_abonos_fecha').editable({
                format: 'yyyy-mm-dd',
                viewformat: 'dd-mm-yyyy',
                emptytext: 'Vacio',
                datepicker: {
                    weekStart: 1,
                    orientation: 'bottom left',
                    language: 'es',
                },
                display: function(value, sourceData,response) {
                    if(sourceData && sourceData.hasOwnProperty('pago')){
                        $(this).text(sourceData.pago.format_fecha);
                    }
                },
            });
        })
    </script>
@endsection
