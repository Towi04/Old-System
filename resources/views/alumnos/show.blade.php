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
<link rel="stylesheet"
href="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.1/bootstrap-editable/css/bootstrap-editable.css"
integrity="sha256-YsJ7Lkc/YB0+ssBKz0c0GTx0RI+BnXcKH5SpnttERaY=" crossorigin="anonymous" />

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
                style="background-image: linear-gradient( rgb(24,41,72,0.9), 70%, rgb(24,41,72,0.9));">

                <div class="up-main-info " style="padding-bottom: 150px; padding-top:100px">
                    <h2 class="up-header">
                        Alumno
                    </h2>
                    <h6 class="up-sub-header">
                        {{ $alumno->nombres }} {{ $alumno->apellido_paterno }} {{ $alumno->apellido_materno }}
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
                    <div class="">
                        @can(['editar_alumno'])
                            <a href="{{ route('alumnos.edit', $alumno) }}"
                                class="btn btn-info btn-sm btn-circle float-right text-white mb-2" data-toggle="tooltip"
                                data-placement="left" title="Editar informacion">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        @endcan

                        <table class="table table-bordered">
                            <tr>
                                <td class="bg-primary text-white"><b>Nombre:</b></td>
                                <td>
                                    <a @can('editar_alumno') class="editable_nombres editable" @endcan
                                        data-name="nombres"
                                        data-type="text"
                                        data-value="{{ $alumno->nombres }}"
                                        data-url=""
                                        data-pk="{{ $alumno->id }}"
                                        data-placeholder="Nombre del cliente"> {{ $alumno->nombres }} </a>
                                </td>
                            </tr>
                            <tr>
                                <td class="bg-primary text-white"><b>Email:</b></td>
                                <td>
                                    <a @can('editar_alumno') class="editable_email_alumno editable" @endcan
                                        data-name="email"
                                        data-type="text"
                                        data-value="{{ $alumno->email }}"
                                        data-pk="{{ $alumno->id }}"
                                        data-url=""
                                        data-placeholder="Telefono fijo"> {{ $alumno->email }} </a>
                                </td>
                            </tr>

                            <tr>
                                <td class="bg-primary text-white"><b>Edad:</b></td>
                                <td>
                                    <a @can('editar_alumno') class="editable_edad_alumno editable" @endcan
                                        data-name="email"
                                        data-type="text"
                                        data-value="{{ $alumno->edad }}"
                                        data-pk="{{ $alumno->id }}"
                                        data-url=""
                                        data-placeholder="Edad"> {{ $alumno->edad }} </a>
                                </td>
                            </tr>

                            <tr>
                                <td class="bg-primary text-white"><b>Observaciones:</b></td>
                                <td>
                                    <a @can('editar_alumno') class="editable_observaciones_alumno editable" @endcan
                                        data-name="observaciones"
                                        data-type="text"
                                        data-value="{{ $alumno->observaciones }}"
                                        data-pk="{{ $alumno->id }}"
                                        data-url=""
                                        data-placeholder="Edad"> {{ $alumno->observaciones }} </a>
                                    </a>
                                </td>
                            </tr>

                            {{--

                            <tr>
                                <td class="bg-primary text-white"><b>Celular:</b></td>
                                <td>
                                    <a @can('gestionar_clientes') class="editable_celular_cliente editable" @endcan
                                        data-name="celular"
                                        data-type="text"
                                        data-value="{{ $cliente->celular }}"
                                        data-pk="{{ $cliente->id }}"
                                        data-url="{{ route('clientes.actualizar_informacion_cliente') }}"
                                        data-placeholder="Celular del cliente"> {{ $cliente->celular }} </a>
                                </td>
                            </tr>

                            <tr>
                                <td class="bg-primary text-white"><b>Direccion:</b></td>
                                <td> <a @can('gestionar_clientes') class="editable_direccion_cliente editable" @endcan
                                        data-name="direccion"
                                        data-type="text"
                                        data-value="{{ $cliente->direccion }}"
                                        data-pk="{{ $cliente->id }}"
                                        data-url="{{ route('clientes.actualizar_informacion_cliente') }}"
                                        data-placeholder="Direccion del cliente"> {{ $cliente->direccion }} </a>
                                </td>
                            </tr>

                            <tr>
                                <td class="bg-primary text-white"><b>Ciudad:</b></td>
                                <td> <a @can('gestionar_clientes')class="editable_ciudad_cliente editable" @endcan
                                        data-name="ciudad"
                                        data-type="text"
                                        data-value="{{ $cliente->ciudad }}"
                                        data-pk="{{ $cliente->id }}"
                                        data-url="{{ route('clientes.actualizar_informacion_cliente') }}"
                                        data-placeholder="Ciudad"> {{ $cliente->ciudad }} </a>
                                </td>
                            </tr>

                            <tr>
                                <td class="bg-primary text-white"><b>Estado:</b></td>
                                <td>
                                    <a @can('gestionar_clientes') class="editable_estado_cliente editable" @endcan
                                        data-name="estado"
                                        data-type="text"
                                        data-value="{{ $cliente->estado }}"
                                        data-pk="{{ $cliente->id }}"
                                        data-url="{{ route('clientes.actualizar_informacion_cliente') }}"
                                        data-placeholder="Estado"> {{ $cliente->telefono }} </a>
                                </td>
                            </tr>

                            <tr>
                                <td class="bg-primary text-white"><b>Observaciones:</b></td>
                                <td>
                                    <a @can('gestionar_clientes') class="editable_observaciones_cliente editable" @endcan
                                        data-name="observaciones"
                                        data-type="textarea"
                                        data-value="{{ $cliente->observaciones }}"
                                        data-pk="{{ $cliente->id }}"
                                        data-url="{{ route('clientes.actualizar_informacion_cliente') }}"
                                        data-placeholder="Observaciones"> {{ $cliente->observaciones }}
                                    </a>
                                </td>
                            </tr> --}}
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-7 col-lg-7 col-sm-7 col-md-7 col-xs-12">
        <div class="row">
            <div class="col-sm-12 col-xxxl-9">
              <div class="element-wrapper">
                {{-- <h6 class="element-header">
                </h6> --}}
                <div class="element-box">
                  <div class="os-tabs-w">
                    <div class="os-tabs-controls">
                      <ul class="nav nav-tabs smaller">
                        <li class="nav-item">
                          <a class="nav-link active" data-toggle="tab" href="#tab-pagos">Pagos Pendientes</a>
                        </li>
                      </ul>
                    </div>
                    <div class="tab-content">
                      <div class="tab-pane active" id="tab-pagos">
                        <table class="table table-striped table-bordered table-hover" id="tb-pagos" width="100%">
                            <thead>
                                <tr>
                                    <th>Concepto</th>
                                    <th>Monto</th>
                                    <th>Fecha Limite</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
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
@endsection


@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            const dom = {
                tb_pagos: $("#tb-pagos"),
            }

            var dt_pagos = dom.tb_pagos.DataTable({
                dom: "<'row'<'col-6'f><'col-6'>><'row'<'col-12'tr>><'row'<'col-5'i><'col-7'p>>",
                processing: true,
                serverSide: true,
                responsive: true,
                pageLength: 10,
                ajax: {
                    url: "{{ route('alumnos.datatables_pagos') }}",
                    type: "POST",
                    data: function (d) {
                        d.id_alumno = "{{ $alumno->id }}";
                    },
                    beforeSend: function(xhr,type) {
                    if (!type.crossDomain) {
                            xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
                        }
                    },
                },
                columns: [
                    {data: 'concepto', name: 'concepto'},
                    {data: 'monto', name: 'monto'},
                    {data: 'fecha_limite', name: 'fecha_limite'},
                    {data: 'status', className:"text-center", name: 'status'},
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
            });
        });
    </script>
@endsection
