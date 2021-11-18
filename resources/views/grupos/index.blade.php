@extends('layouts.template-'.config('settings.template').'.plantilla')

@section('titulo')
    Grupos
@endsection


@section('breadcrumb')
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="{{ url('/') }}">Inicio</a>
        </li>
        <li class="breadcrumb-item active">
            <strong>Grupos</strong>
        </li>
    </ol>
@endsection

@section('contenido')
<div class="row justify-content-start px-4">
    <div>
        @can('crear_grupo')
            <a class="mb-3" href={{ route('grupos.create') }}>
                <button class="btn btn-success btn-sm" type="button">
                    <i class="fa fa-plus-circle fa-xs" aria-hidden="true"></i> Agregar
                </button>
            </a>
        @endcan
    </div>
</div>

<div class="row widget-list">
    <div class="widget-holder widget-full-height widget-flex col-lg-12">
        <div class="widget-body">
            <div class="form-group">
              <label for="">Status</label>
              <select class="form-control col-6" name="" id="status_grupo">
                <option>Activo</option>
                <option>Programado</option>
                <option>Finalizado</option>
              </select>
            </div>
            <div class="table-responsive mt-3">
                <table id="tb-grupos" class="table table-padded  table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Clave</th>
                            <th>Especialidad</th>
                            <th>Horario</th>
                            <th>Dias</th>
                            <th>Tipo</th>
                            <th>No Alumnos</th>
                            <th>Fecha Inicio</th>
                            <th>Status</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


@endsection

@section('scripts')
<script type="text/javascript">
    $(function() {
        const dom = {
            table: $('#tb-grupos'),
        };

        var dt = dom.table.DataTable({
            processing: true,
            serverSide: true,
            dom: "<'row'<'col-6 d-flex align-items-center' lB><'col-6'f>><'row'<'col-12'tr>><'row'<'col-5'i><'col-7'p>>",
            ajax: {
                url: "{{ route('grupos.datatables') }}",
                method:'POST',
                data: function (d) {
                    d.id_sucursal= "{{ optional(session('sucursal'))->id }}";
                    d.status= $('#status_grupo').val()
                },
                beforeSend: function(xhr,type) {
                    if (!type.crossDomain) {
                        xhr.setRequestHeader('X-CSRF-Token', $('meta[name="csrf-token"]').attr('content'));
                    }
                },
            },
            pageLength: 10,
            lengthMenu: [[10,50,100,-1],['10','50','100','Todos']],
            responsive: true,
            buttons: [{
                extend: 'excel',
                title: 'Grupos'
            }],
            columns: [
                { data: 'id', name: 'id',class: 'text-nowrap'},
                { data: 'clave', name: 'clave',class: 'text-nowrap'},
                { data: 'especialidad.nombre', name: 'especialidad.nombre',class: 'text-nowrap'},
                { data: 'horario', name: 'horario',class: 'text-nowrap'},
                { data: 'days', name: 'days',class: 'text-nowrap'},
                { data: 'infantil', name: 'infantil',class: 'text-nowrap'},
                { data: 'no_alumnos', name: 'infantil',class: 'text-nowrap',orderable: false, searchable: false},
                { data: 'fecha_inicio', name: 'fecha_inicio',class: 'text-nowrap'},
                { data: 'status', name: 'status',class: 'text-nowrap'},
                { data: 'buttons', name: 'buttons', orderable: false, searchable: false }
            ],
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
            order: [[ 0, 'desc' ] ],
            drawCallback: function(settings) {
                $("[data-toggle='tooltip']").tooltip();

                $('.finalizar_grupo').on('click',function(){
                    id= $(this).data('id')
                    swal({
                title: "¿Estas seguro de FINALIZAR el grupo?",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#ff3333",
                cancelButtonColor: "#CDCDCD",
                confirmButtonText: "Si, finalizar",
                cancelButtonText: "Cancelar",
                showLoaderOnConfirm: false,
            }).then(function(result) {
                if (!result.value) {
                    return;
                }

                wait.modal('show');

                $.ajax({
                    url: "{{route('grupos.finalizar_grupo')}}",
                    type: 'POST',
                    cache: false,
                    data: {
                        _token: $("meta[name='csrf-token']").attr("content"),
                        id:id
                    },
                    success: function (response){
                        dt.ajax.reload( function(e){
                            wait.modal('hide');
                            toastr.success('Éxito', 'Se borró con éxito el registro');
                        }, false )
                    },
                    fail:function(error){
                        toastr.error('Error', 'Ocurrio un error inesperado');
                    }
                });
            })
                })
            },
        });

        var searchWait = 0;
        var searchWaitInterval;

        $('.dataTables_filter input')
            .unbind()
            .bind('input', function(e) {
                var item = $(this);
                searchWait = 0;
                if (!searchWaitInterval) searchWaitInterval = setInterval(function() {
                    if (searchWait >= 3) {
                        clearInterval(searchWaitInterval);
                        searchWaitInterval = '';
                        searchTerm = $(item).val();
                        dt.search(searchTerm).draw();
                        searchWait = 0;
                    }
                    searchWait++;
                }, 200);

            });

        dom.table.on('click',"a[data-action='delete']",function(event){
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
                    type: 'DELETE',
                    cache: false,
                    data: {
                        _token: $("meta[name='csrf-token']").attr("content"),
                    },
                    success: function (response){
                        dt.ajax.reload( function(e){
                            wait.modal('hide');
                            toastr.success('Éxito', 'Se borró con éxito el registro');
                        }, false )
                    },
                    fail:function(error){
                        toastr.error('Error', 'Ocurrio un error inesperado');
                    }
                });
            })
        })

        $('#status_grupo').change(function(){
            dt.draw();
        });
    })
</script>
@endsection
