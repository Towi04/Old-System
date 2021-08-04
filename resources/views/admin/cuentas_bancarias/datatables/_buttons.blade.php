<div class="text-center">
    <div class="btn-group">
        @can('editar_cuenta_bancaria')
            <a  href="{{ route("admin.cuentas-bancarias.edit",$id) }}"
                class="btn btn-primary btn-sm text-white fas fa-pencil-alt"
                data-toggle="tooltip"
                data-placement="top"
                data-action="edit"
                title="Editar">
            </a>
        @endcan

        {{-- @can('consultar_cuenta_bancaria')
            <a  href="{{ route("admin.cuentas-bancarias.show",$id) }}"
                class="btn btn-success btn-sm text-white fas fa-id-card"
                data-toggle="tooltip"
                data-placement="top"
                title="Panel">
            </a>
        @endcan --}}

        @can('eliminar_cuenta_bancaria')
            <a class="btn btn-danger btn-sm text-white fas fa-trash"
                href="{{ route('admin.cuentas-bancarias.destroy',$id) }}"
                data-toggle="tooltip"
                data-id="{{ $id }}"
                data-action="delete"
                data-placement="top"
                title="Eliminar">
            </a>
        @endcan
    </div>
</div>
