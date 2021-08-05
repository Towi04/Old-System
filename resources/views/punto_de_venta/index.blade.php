@extends('layouts.template-'.config('settings.template').'.plantilla')

@section('titulo')
    Punto de venta
@endsection


@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ url('/') }}">Inicio</a>
        </li>
        <li class="breadcrumb-item active">
            <strong>Punto de venta</strong>
        </li>
    </ol>
@endsection

@section('contenido')

    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                {!! Form::label('id_alumno', 'Selecciona al alumno que va a pagar:*'); !!}
                {!! Form::select('id_alumno',[], null, ['class' => 'form-control','required' => true,'style' => 'width:100%']) !!}
            </div>
        </div>
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
            <div class="element-box">
                <h5 class="element-header">
                    Pagos pendientes 
                </h5>
                <div class="row">
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <span class="float-right"> Total pendiente: $ <span class="total_pendiente"></span></span>
                    </div>
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <table class="table" id="tb-pagos" width="100%" style="clear:both">
                            <thead>
                                <tr>
                                    <th>Concepto</th>
                                    <th>Monto</th>
                                    <th>Fecha Limite</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                
            </div>
            
        </div>
        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
            <div class="element-box">
                <h5 class="element-header">
                    Recibir abono
                </h5>
                <div class="row justify-content-end">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                        <div class="form-group">
                          <label for="">Monto</label>
                          <input type="number" name="" id="" class="form-control" placeholder="Ingresa el monto " aria-describedby="helpId">
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">

                          <div class="form-group">
                            <label for="">Forma de pago</label>
                            <select class="form-control form-control-sm" name="" id="">
                              <option>Efectivo</option>
                              <option>Tarjate de debito</option>
                              <option>Tarjate de crédito</option>
                              <option>Transferencia</option>
                            </select>
                          </div>

                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                        <button class="btn btn-success float-right">Recibir abono</button>
                    </div>
                </div>
            </div>
            
        </div>
    </div>



@endsection

@section('scripts')
<script>

$(function() {
    var Helpers = function() {};

    Helpers.prototype.number_format = function(number,decimals) {
      return parseFloat(number).toFixed(decimals).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString()
    }

    Helpers = new Helpers();

            const dom = {
                select_alumno: $("#id_alumno"),
                tb_pagos: $("#tb-pagos"),
            };

            dom.select_alumno.select2({
                language: "es",
                placeholder:'Selecciona un alumnno',
                dropdownParent: dom.modal_asignar_alumno,
                ajax: {
                    method: 'POST',
                    data:function (params) {
                        return {
                            _token: '{{ csrf_token() }}',
                            term: params.term,
                            page: params.page || 1,
                            id_sucursal: "{{ optional(session('sucursal'))->id }}",
                        }
                    },
                    url: '{{ route("alumnos.traer_alumnos_select2") }}',
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

                    if(!option.nombres || !option.apellido_paterno || !option.apellido_materno){
                        return option.text
                    }

                    return `${option.nombres} ${option.apellido_paterno} ${option.apellido_materno}`;
                },
                templateSelection:function(option){
                    if(!option.nombres || !option.apellido_paterno || !option.apellido_materno){
                        return option.text
                    }

                    return `${option.nombres} ${option.apellido_paterno} ${option.apellido_materno}`;
                }
            });

            var dt_pagos = dom.tb_pagos.DataTable({
                dom: "<'row'<'col-6'><'col-6'>><'row'<'col-12'tr>><'row'<'col-5'i><'col-7'p>>",
                processing: true,
                serverSide: true,
                responsive: true,
                pageLength: 10,
                ajax: {
                    url: "{{ route('alumnos.datatables_pagos_pendientes') }}",
                    type: "POST",
                    data: function (d) {
                        d.id_alumno = dom.select_alumno.val();
                        d.status = 'pendiente';
                    },
                    complete: function(data) {
                        let response = data.responseJSON || {};

                        $('.total_pendiente').html(
                            Helpers.number_format(response.total_pendiente || 0,2)
                        );
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
                order: [[ 2, "desc" ]],
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

            dom.select_alumno.on('select2:select', function (e) {
                dt_pagos.draw();
            });
            
});
</script>
@endsection
