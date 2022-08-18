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
        <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 col-12">
            <div class="element-box">

                <div class="row">
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                        <div class="form-group">
                            {!! Form::label('id_alumno', 'Selecciona al alumno que va a pagar:*'); !!}
                            {!! Form::select('id_alumno',[], null, ['class' => 'form-control','required' => true,'style' => 'width:100%']) !!}
                        </div>
                    </div>
                    <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                        <div class="form-group">
                            {!! Form::label('id_preregistro', 'o Busca un preregistro para realizar un apartado:*'); !!}
                            {!! Form::select('id_preregistro',[], null, ['class' => 'form-control','required' => true,'style' => 'width:100%']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-sm-6">
            <div class="element-wrapper">
                <div class="element-box">
                <div class="os-tabs-w">
                    <div class="os-tabs-controls">
                    <ul class="nav nav-tabs smaller">
                        <li class="nav-item">
                        <a class="nav-link active" data-toggle="tab" href="#tab-pagos-pendientes">Pagos Pendientes</a>
                        </li>
                        @can('registrar_pago_manual')
                            <li class="nav-item">
                                <a class="nav-link" data-toggle="tab" href="#tab-pago-manual">Pago Manual</a>
                            </li>
                        @endcan
                    </ul>
                    </div>

                    <div class="tab-content">
                        <div class="tab-pane active" id="tab-pagos-pendientes">
                            <div class="row">
                                <div class="col-12">
                                    {!! Form::label('id_especialidad', 'Selecciona la especialidad: ', ['class' => 'control-label']) !!}
                                    {!! Form::select('id_especialidad', [], null, ['id'=>'select_especialidad','class'=>'custom-select custom-select-sm','style' => 'width:100%;']) !!}
                                </div>
                                <div class="col-12">
                                    <span class="float-right"> Total pendiente: $ <span class="total_pendiente"></span></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <table class="table" id="tb-pagos" style="clear:both;width: 100%;">
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

                        @can('registrar_pago_manual')
                            <div class="tab-pane" id="tab-pago-manual">
                                <p class="form-desc m-0 border-0">En esta sección puedes dar de alta un pago manual. Primero debes seleccionar un alumno</p>
                                <button class="btn btn-primary btn-sm" id="btn-crear-pago" disabled>Registrar Pago</button>
                            </div>
                        @endcan

                    </div>
                </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6">
            <div class="element-box">
                <h5 class="element-header">
                    Recibir abono
                </h5>

                {!! Form::open(['id' => 'form-recibir-abono','route' => 'punto_de_venta.recibir_abonos']) !!}
                    <div class="row justify-content-end">
                        <div class="col-xl-6 col-lg-6 col-md-6 col-sm-6 col-12">
                            <div class="form-group">
                                {!! Form::label('monto','Monto:') !!}
                                {!! Form::number('monto', null, ['class' => 'form-control form-control-sm','placeholder' => 'Ingresa el monto','required' => true,'autocomplete' => 'off','form-selector' => '','step' => '0.01','disabled' => true,'min' => 0.01]) !!}
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

    <div class="modal inmodal fade animated" id="modal-pago-manual" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-md">
            <div class="modal-content animated bounceInRight">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Pago manual <small>(*) Campos Requeridos</small></h5>
                        <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">
                            &times;</span><span class="sr-only">Close</span>
                        </button>
                    </div>

                    {!! Form::open(['id' => 'form-pago-manual','route' => 'punto_de_venta.pago_manual']) !!}
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('fecha', 'Fecha:*', ['class' => 'form-label']) !!}
                                    {!! Form::text('fecha',null ,['id' => 'fecha','class' => 'form-control form-control-sm','autocomplete' => 'off','required' => true]) !!}
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('concepto', 'Concepto:*', ['class' => 'form-label']) !!}
                                    {!! Form::select('concepto',[
                                        ''=>'Selecciona la forma de pago',
                                        'Inscripción'=>'Inscripción',
                                        'Colegiatura'=>'Colegiatura',
                                        ],null ,[ 'class' => 'form-control form-control-sm','title' => 'Escribe el concepto','autocomplete' => 'off','required' => true]) !!}
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('forma', 'Tipo:*', ['class' => 'form-label']) !!}
                                    {!! Form::select('forma',[
                                        ''=>'Selecciona la forma de pago',
                                        'Mes'=>'Mensual',
                                        'Semana'=>'Semanal',
                                    ],null, [ 'class' => 'form-control form-control-sm','title' => 'Escribe el concepto','autocomplete' => 'off','required' => true]) !!}
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('no_pago', 'No Mes/Semana:*', ['class' => 'form-label']) !!}
                                    {!! Form::text('no_pago',null, [ 'class' => 'form-control form-control-sm','title' => 'Escribe el número de mes o semana','placeholder' => 'Escribe aqui el no de pago','autocomplete' => 'off','required' => true]) !!}
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('monto', 'Monto:*', ['class' => 'form-label']) !!}
                                    {!! Form::number('monto',null, [ 'class' => 'form-control form-control-sm','title' => 'Escribe el monto','placeholder' => 'Escribe aqui el monto','autocomplete' => 'off','required' => true,'step' =>'0.01']) !!}
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                <div class="form-group">
                                    {!! Form::label('folio', 'Folio:*', ['class' => 'form-label']) !!}
                                    {!! Form::number('folio',null, [ 'class' => 'form-control form-control-sm','title' => 'Escribe el monto','placeholder' => 'Escribe el foli asignado','autocomplete' => 'off','required' => true]) !!}
                                </div>
                            </div>

                            <div class="col-12 col-sm-6">
                                {!! Form::label('forma_pago','Forma Pago:') !!}
                                {!! Form::select('forma_pago', [
                                        'Efectivo'              => 'Efectivo',
                                        'Tarjate de debito'     => 'Tarjate de debito',
                                        'Tarjate de crédito'    => 'Tarjate de crédito',
                                        'Transferencia'         => 'Transferencia'
                                    ],null, ['forma_pago_manual','class' => 'form-control form-control-sm','form-selector'=> '','required' => true]) !!}
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-12">
                                {!! Form::label('id_grupo', 'Seleccionar a un grupo:*', ['class' => 'form-label']) !!}
                                {!! Form::select('id_grupo', [], null, ['id' => 'select2_id_especialidad_pago', 'class' => 'custom-select custom-select-sm','required' => true]) !!}
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                    {!! Form::close() !!}
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

            $.fn.datepicker.dates['es'] = CONFIG_DATEPICKER  //👉 DATEPICKE

            const dom = {
                select_alumno: $("#id_alumno"),
                select_preregistro: $("#id_preregistro"),
                tb_pagos: $("#tb-pagos"),
                form_abonos: $("#form-recibir-abono"),
                tikets:{
                    contenido_ticket:$("#contenido-ticket"),
                    modal: $("#modal-ticket"),
                },
                pago_manual:{
                    btn_crear_pago: $("#btn-crear-pago"),
                    modal_pago_manual: $("#modal-pago-manual"),
                    form_pago_manual: $("#form-pago-manual"),
                    fecha: $("#fecha"),
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
                            status:'Alumno'
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
                minimumInputLength: 2,
                templateResult: function(option){
                    if (option.loading) {
                        return option.text;
                    }

                    if(!option.nuevo_numero_control || !option.nombres || !option.apellido_paterno || !option.apellido_materno){
                        return option.text
                    }

                    return `No. Control: ${option.nuevo_numero_control} | Nombre: ${option.nombres} ${option.apellido_paterno} ${option.apellido_materno}`;
                },
                templateSelection:function(option){
                    if(!option.nuevo_numero_control ||  !option.nombres || !option.apellido_paterno || !option.apellido_materno){
                        return option.text
                    }

                    return `No. Control: ${option.nuevo_numero_control} | Nombre: ${option.nombres} ${option.apellido_paterno} ${option.apellido_materno}`;
                }
            });

            @if($alumno_huella)
                var data = {
                    id: {{$alumno_huella->id}},
                    text: '{{$alumno_huella->nuevo_numero_control}} | Nombre: {{$alumno_huella->fullname}}'
                };

                var newOption = new Option(data.text, data.id, false, false);
                dom.select_alumno.append(newOption).trigger('change');

                $.post("{{route('punto_de_venta.traer_grupos')}}", {id:{{$alumno_huella->id}} },
                    function (grupos) {
                        traer_grupos(grupos);
                    },
                    "json"
                );



            @endif

            dom.select_preregistro.select2({
                language: "es",
                placeholder:'Selecciona un pre registro',
                // dropdownParent: dom.modal_asignar_alumno,
                ajax: {
                    method: 'POST',
                    data:function (params) {
                        return {
                            _token: '{{ csrf_token() }}',
                            term: params.term,
                            page: params.page || 1,
                            id_sucursal: "{{ optional(session('sucursal'))->id }}",
                            status:'Pre-Registro'
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
                minimumInputLength: 2,
                templateResult: function(option){
                    if (option.loading) {
                        return option.text;
                    }

                    if(!option.nombres || !option.apellido_paterno || !option.apellido_materno){
                        return option.text
                    }

                    return ` Nombre: ${option.nombres} ${option.apellido_paterno} ${option.apellido_materno}`;
                },
                templateSelection:function(option){
                    if( !option.nombres || !option.apellido_paterno || !option.apellido_materno){
                        return option.text
                    }

                    return `Nombre: ${option.nombres} ${option.apellido_paterno} ${option.apellido_materno}`;
                }
            });

            var dt_pagos = dom.tb_pagos.DataTable({
                dom: "<'row'<'col-6'><'col-6'>><'row'<'col-12'tr>><'row'<'col-5'i><'col-7'p>>",
                processing: true,
                serverSide: true,
                responsive: true,
                pageLength: 10,
                ajax: {
                    url: "{{ route('alumnos.datatables_documentos_pendientes') }}",
                    type: "POST",
                    data: function (d) {
                        d.id_alumno = dom.select_alumno.val();
                        d.status = 'pendiente';
                        d.id_especialidad = $('#select_especialidad').val();
                    },
                    complete: function(data) {
                        let response = data.responseJSON || {};

                        $('.total_pendiente').html(
                            Helpers.number_format(response.total_pendiente || 0,2)
                        );

                        $('#monto').val(response.total_pendiente);
                        
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

                especialidades = e.params.data.especialidades;
                traer_especialidades(especialidades);
            });

            function traer_especialidades(especialidades){
                $('#select_especialidad').empty();

                const $select2_especialidad_pagos = dom.pago_manual.form_pago_manual.find('#select2_id_especialidad_pago');
                $select2_especialidad_pagos.empty();

                $.each(especialidades, function (index, especialidad) {
                    const opcion = `<option value="${especialidad.id}" > ${especialidad.nombre} </option>`;

                    $('#select_especialidad').append(opcion);
                    $select2_especialidad_pagos.append(opcion)
                });

                dt_pagos.draw();
                disableForm( !dom.select_alumno.val());
                dom.select_preregistro.val(null).trigger('change');

                dom.pago_manual.btn_crear_pago.attr('disabled',!dom.select_alumno.val())
            }

            dom.select_preregistro.on('select2:select', function (e) {

                disableForm( false);
                dom.select_alumno.val(null).trigger('change');
            });

            $('#select_especialidad').change(function(){
                dt_pagos.ajax.reload(null, false);
            });


            dom.form_abonos.submit(function(e)
            {
                e.preventDefault();

                wait.modal('show');

                const id_alumno = dom.select_alumno.val()
                const id_preregistro = dom.select_preregistro.val()

                if(!id_alumno && !id_preregistro){
                    toastr.error('Error', 'Debes seleccionar primero un alumno o preregistro');
                }



                let formData = new FormData(this);
                formData.append('id_especialidad',$('#select_especialidad').val());
                if(id_alumno){
                    formData.append('id_alumno',id_alumno);
                }

                if(id_preregistro){
                    formData.append('id_preregistro',id_preregistro);
                }

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

                            setTimeout(function(){
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

            @can('registrar_pago_manual')
                const m_pagos_manuales = (function(pago_manual){
                    pago_manual.btn_crear_pago.click(function(e){
                        pago_manual.form_pago_manual[0].reset();
                        pago_manual.modal_pago_manual.modal('show');
                    })

                    pago_manual.form_pago_manual.submit(function(e){
                        e.preventDefault();

                        pago_manual.modal_pago_manual.modal('hide');

                        const formData = new FormData(this);

                        formData.append('id_alumno',dom.select_alumno.val());

                        $.ajax({
                            url: $(this).attr('action'),
                            type: 'POST',
                            contentType: false,
                            processData: false,
                            data: formData
                        }).done(function(response){
                            if(response.success) {
                                dt_pagos.ajax.reload(function(){
                                    setTimeout(function(){
                                        wait.modal('hide');
                                    }, 200);

                                    toastr.success('Exito', response.message || '');
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
                    })

                    pago_manual.fecha.datepicker({
                        language: 'es',
                        format: 'dd-mm-yyyy',
                        ignoreReadonly: false,
                        todayHighlight: true,
                        todayBtn: true,
                        autoclose: true,
                    });
                })(dom.pago_manual);
            @endcan
        });
    </script>
@endsection
