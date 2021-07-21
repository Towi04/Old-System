<div class="text-center">
    <div class="btn-group">
        @can('editar_grupo')
            <a  href="{{ route("grupos.edit",$id) }}"
                class="btn btn-primary btn-sm text-white fas fa-pencil-alt"
                data-toggle="tooltip"
                data-placement="top"
                data-action="edit"
                title="Editar">
            </a>
        @endcan

        {{-- @can('consultar_grupo')
            <a  href="{{ route("grupo.show",$id) }}"
                class="btn btn-success btn-sm text-white fas fa-id-card"
                data-toggle="tooltip"
                data-placement="top"
                data-action="show"
                title="Ver">
            </a>
        @endcan --}}

        @can('asignar_materias')
            <a  href="{{ route("grupos.asignar-materias",$id) }}"
                class="btn btn-success btn-sm text-white fas fa-id-card"
                data-toggle="tooltip"
                data-placement="top"
                data-action="show"
                title="Asignar Materia">
            </a>
        @endcan

        @can('eliminar_grupo')
            <a class="btn btn-danger btn-sm text-white fas fa-trash"
                href="{{ route('grupos.destroy',$id) }}"
                data-toggle="tooltip"
                data-id="{{ $id }}"
                data-action="delete"
                data-placement="top"
                title="Eliminar">
            </a>
        @endcan
    </div>
</div>
