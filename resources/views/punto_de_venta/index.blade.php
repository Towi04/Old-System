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
                        Selecciona el grupo:
                        {!! Form::select('id_grupo', [], null, ['id'=>'select_grupo','class'=>'form-control']) !!}
                    </div>
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <span class="float-right"> Total pendiente: $ <span class="total_pendiente"></span></span>
                    </div>
                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
                        <table class="table" id="tb-pagos" width="100%" style="clear:both">
                            <thead>
                                <tr>
                                    <th>Concepto</th>
                                    <th>Monto</th>
                                    <th>Saldo</th>
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

                {!! Form::open(['id' => 'form-recibir-abono','route' => 'punto_de_venta.recibir_abonos']) !!}
                    <div class="row justify-content-end">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="form-group">
                                {!! Form::label('monto','Monto:') !!}
                                {!! Form::number('monto', null, ['class' => 'form-control','placeholder' => 'Ingresa el monto','required' => true,'autocomplete' => 'off','form-selector' => '','step' => '0.01','disabled' => true,'min' => 0.01]) !!}
                            </div>
                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">

                            <div class="form-group">
                                {!! Form::label('forma_pago','Forma Pago:') !!}
                                {!! Form::select('forma_pago', [
                                        'Efectivo'              => 'Efectivo',
                                        'Tarjate de debito'     => 'Tarjate de debito',
                                        'Tarjate de crédito'    => 'Tarjate de crédito',
                                        'Transferencia'         => 'Transferencia'
                                    ],null, ['class' => 'form-control form-control-sm','form-selector'=> '','disabled' => true]) !!}
                            </div>

                        </div>
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                            <button class="btn btn-success float-right" type="submit" disabled form-selector >Recibir abono</button>
                        </div>
                    </div>
                {!! Form::close() !!}
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
    <script src="{{ asset('template-clean-admin/bower_components/select2/dist/js/i18n/es.js') }}"></script>

    <script type="text/javascript">
        $(function() {
            var Helpers = function() {};

            Helpers.prototype.number_format = function(number,decimals) {
                return parseFloat(number).toFixed(decimals).replace(/(\d)(?=(\d{3})+\.)/g, "$1,").toString()
            }

            Helpers = new Helpers();

            const dom = {
                select_alumno: $("#id_alumno"),
                tb_pagos: $("#tb-pagos"),
                form_abonos: $("#form-recibir-abono"),

                tikets:{
                    contenido_ticket:$("#contenido-ticket"),
                    modal: $("#modal-ticket"),
                }
            };

            const disableForm = (disable = false) => {
                const $elements = dom.form_abonos[0].querySelectorAll('[form-selector]');
                Array.from($elements).forEach(formElement => formElement.disabled = disable);
            }

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
                        d.id_grupo = $('#select_grupo').val();
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
                    {data: 'monto', name: 'monto',className:'text-right'},
                    {data: 'saldo', name: 'saldo',className:'text-right'},
                    {data: 'fecha_limite', name: 'fecha_limite'},
                    {data: 'status', className:"text-center", name: 'status'},
                ],
                order: [[ 3, "asc" ]],
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

                grupos = e.params.data.grupos;
                $('#select_grupo').empty();

                $.each(grupos, function (index, grupo) { 
                    $('#select_grupo').append(`<option value="${grupo.id}" > ${grupo.nombre_compuesto} </option>`);
                });

                dt_pagos.draw();

                disableForm( !$(this).val());
            });

            $('#select_grupo').change(function(){
                dt_pagos.draw();
            });


            dom.form_abonos.submit(function(e)
            {
                e.preventDefault();

                wait.modal('show');

                const id_alumno = dom.select_alumno.val()

                if(!id_alumno){
                    toastr.error('Error', 'Debes seleccionar primero un alumno');
                }

                

                let formData = new FormData(this);
                formData.append('id_grupo',$('#select_grupo').val());
                formData.append('id_alumno',id_alumno);


                $.ajax({
                    url: $(this).attr('action'),
                    type: 'POST',
                    contentType: false,
                    processData: false,
                    data: formData
                }).done(function(response){
                    if(response.success) {
                        const pago = response.data.pago;
                        const route = "{{ url('punto_de_venta/ticket/_pago') }}".replace('_pago',pago.id);

                        dt_pagos.ajax.reload(function(){
                            dom.form_abonos[0].reset();
                            
                            setInterval(function(){
                                wait.modal('hide');
                            }, 200);
                            

                            dom.tikets.modal.modal('show');
                            dom.tikets.contenido_ticket.html();
                            dom.tikets.contenido_ticket.html(`<iframe scrolling='auto' type='text/html' scroll='auto' src='${route}' width='100%' height='450px' align='center'></iframe>`);
                        },false);
                    }else {
                        setTimeout(() => {
                            wait.modal('hide');
                        }, 250);
                    }
                }).fail(function(error){
                    setTimeout(() => {
                        wait.modal('hide');
                        toastr.error('Error', 'Ocurrio un error inesperado');
                    }, 250);
                });
            });

        });
    </script>
@endsection
