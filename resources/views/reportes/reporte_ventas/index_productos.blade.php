@extends('layouts.template-'.config('settings.template').'.plantilla')

@section('titulo')
    Reporte de ventas de productos {{ date('Y') }}
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
            <strong>Reporte de ventas (Productos)</strong>
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
                <a href="{{ route('reportes.reporte-ventas.index_productos',['tipo' => 'dia']) }}"
                    class="btn @if ($tipo=='dia' ) btn-primary  text-white @else btn-white @endif">Día</a>
                <a href="{{ route('reportes.reporte-ventas.index_productos',['tipo' => 'semanal']) }}"
                    class="btn  @if ($tipo=='semanal' ) btn-primary  text-white @else btn-white @endif">Semanal</a>
                <a href="{{ route('reportes.reporte-ventas.index_productos',['tipo' => 'mes']) }}"
                    class="btn  @if ($tipo=='mes' ) btn-primary  text-white @else btn-white @endif">Mensual</a>
                <a href="{{ route('reportes.reporte-ventas.index_productos',['tipo' => 'anual']) }}"
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
                                <a href="{{ route('reportes.reporte-ventas.index_productos',['tipo' => 'dia','fecha'=>$fecha_antes->format('d-m-Y') ]) }}"
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
                                <a href="{{ route('reportes.reporte-ventas.index_productos',['tipo'=>'dia','fecha' => $fecha_despues->format('d-m-Y') ]) }}"
                                    data-toggle="tooltip" data-title="{{ $fecha_despues->format('d \d\e F \d\e\l Y') }}"
                                    data-placement="top" class="btn btn-primary btn-sm text-white no_print"><i
                                        class="fas fa-arrow-alt-circle-right fa-2x"></i></a>
                            </div>
                        @endif
                        @if (request('tipo','dia') == 'mes')
                            <div class="col-1 col-xxl-1 col-xl-1 col-md-1 col-sm-1">
                                <a href="{{ route('reportes.reporte-ventas.index_productos',['tipo'=>'mes','fecha'=> $fecha_antes->format('d-m-Y') ]) }}"
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
                                <a href="{{ route('reportes.reporte-ventas.index_productos',['tipo' => 'mes','fecha' => $fecha_despues->format('d-m-Y') ]) }}"
                                    data-toggle="tooltip" data-title="{{ $fecha_despues->format('d \d\e F \d\e\l Y') }}"
                                    data-placement="top" class="btn btn-primary btn-sm text-white no_print"><i
                                        class="fas fa-arrow-alt-circle-right fa-2x"></i></a>
                            </div>
                        @endif

                        @if (request('tipo','dia') == 'semanal')
                            <div class="col-1 col-xxl-1 col-xl-1 col-md-1 col-sm-1">
                                <a href="{{ route('reportes.reporte-ventas.index_productos',['tipo' => 'semanal','fecha' => $fecha_antes->startOfWeek()->format('d-m-Y') ]) }}"
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
                                <a href="{{ route('reportes.reporte-ventas.index_productos',['tipo'=> 'semanal','fecha'=>$fecha_despues->startOfWeek()->format('d-m-Y')]) }}"
                                    data-toggle="tooltip" data-title="{{ $fecha_despues->format('d \d\e F \d\e\l Y') }}"
                                    data-placement="top" class="btn btn-primary btn-sm text-white no_print"><i
                                        class="fas fa-arrow-alt-circle-right fa-2x"></i></a>
                            </div>
                        @endif

                        @if (request('tipo','dia') == 'anual')
                            <div class="col-1 col-xxl-1 col-xl-1 col-md-1 col-sm-1">
                                <a href="{{ route('reportes.reporte-ventas.index_productos',['tipo' => 'anual','fecha'=> $fecha_antes->format('d-m-Y'), ]) }}"
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
                                <a href="{{ route('reportes.reporte-ventas.index_productos',['tipo'=>'anual','fecha'=> $fecha_despues->format('d-m-Y') ]) }}"
                                    data-toggle="tooltip" data-title="{{ $fecha_despues->format(' Y') }}"
                                    data-placement="top" class="btn btn-primary btn-sm text-white no_print"><i
                                        class="fas fa-arrow-alt-circle-right fa-2x"></i></a>
                            </div>
                        @endif
                    </div>
                </div>
                <div class="ibox-content mt-2">

                    <div class="table-responsive">
                        <table class="table table-striped table-hover" id="tabla_ventas">
                            <thead>
                                <tr>
                                    <th>Folio</th>
                                    <th>Fecha Venta</th>
                                    <th>Alumno</th>
                                    <th>Concepto</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($ventas as $venta)
                                    <tr class="gradeX" id="venta-{{ $venta->id }}">

                                            <td class="text-danger text-center">
                                                {{ $venta->folio }}
                                            </td>


                                        <td nowrap>{{ optional($venta->fecha)->format('d-m-Y H:i') }}</td>
                                        <td>
                                            {{ optional($venta->alumno)->fullname }}
                                        </td>
                                        <td>
                                            @foreach ($venta->partidas as $partida)
                                                {{$partida->producto->nombre}}<br>
                                            @endforeach
                                        </td>

                                        <td class="text-right text-nowrap" style="cursor:pointer">
                                            $ {{ number_format($venta->total, '2', '.', ',') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>

                        </table>

                        <table class="table table-bordered mt-3 float-right print_only" style="width:50%;">
                            <tr>
                                <td style="font-size: 1rem;">Total: </td>
                                <td class="text-right"><b style="font-size: 1rem;"> ${{ number_format($ventas->sum('total'), 2, '.', ',') }}</b>
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
            {{-- <button onclick="window.print();" class="btn btn-block btn-secondary mb-3">
                <i class="fas fa-print"></i> Imprimir
            </button> --}}
            <div class="col-sm-12 col-xxxl-12 p-1">
                <a class="element-box el-tablo" href="#">
                  <div class="label mb-2">
                    Total
                  </div>
                  <div class="value">
                    $ {{ number_format($ventas->sum('total'), 2, '.', ',') }}
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
                            $ {{ number_format($ventas->where('venta_fiscal',0)->sum('monto'), 2, '.', ',') }}
                        </div>
                        </a>
                    </div>
                    <div class="col-sm-12 col-xxxl-12 p-1">
                        <a class="element-box el-tablo" href="#">
                        <div class="label mb-2">
                            Fiscales
                        </div>
                        <div class="value">
                            $ {{ number_format($ventas->where('venta_fiscal',1)->sum('monto'), 2, '.', ',') }}
                        </div>
                        </a>
                    </div>
                    <div class="col-sm-12 col-xxxl-12 p-1">
                        <a class="element-box el-tablo" href="#">
                        <div class="label mb-2">
                            % de fiscales
                        </div>
                        <div class="value">
                            @if($ventas->sum('monto') > 0)
                            {{ number_format( $ventas->where('venta_fiscal',1)->sum('monto') / $ventas->sum('monto') *100, 2, '.', ',') }} %
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
@endsection

@section('scripts')
    <script type="text/javascript">
        $.fn.datepicker.dates['es'] = {
            days: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"],
            daysShort: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
            daysMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sá"],
            months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
            monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
            today: "Hoy",
            clear: "Borrar"
        };

        $('#tabla_ventas').DataTable({
            responsive: true,
            lengthMenu: [ [-1, 25, 50 ], ["Todos", 25, 50] ],
            buttons: [
                {extend: 'excel', title: 'Ventas'},
            ],
            language: {
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
                @can('descargar_excel_bd')
                {
                    "extend": 'excelHtml5',
                    "text":'Excel <i class="fas fa-file-excel"></i>',
                    'title': 'Reporte de ventas',
                    "className": 'btn btn-primary',
                }
                @endcan
            ],
            order: [[0,'desc']],
        });

        $('#datepicker').datepicker({
            language: 'es',
            format: 'dd-mm-yyyy',
            ignoreReadonly: false,
            todayHighlight: true,
            todayBtn: true
        });

        $('#datepicker').on('changeDate', function() {
            window.location = "{{route('reportes.reporte-ventas.index_productos')}}?tipo={{$tipo}}&fecha="+$('#datepicker').datepicker('getFormattedDate')+"&forma_pago={{@$_GET['forma_pago']}}"
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
    </script>
@endsection
