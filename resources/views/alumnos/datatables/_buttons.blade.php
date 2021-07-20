<div class="text-center">
    <div class="btn-group">
        @can('editar_alumno')
            <a  href="{{ route("alumnos.edit",$id) }}"
                class="btn btn-primary btn-sm text-white fas fa-pencil-alt"
                data-toggle="tooltip"
                data-placement="top"
                data-action="edit"
                title="Editar">
            </a>
        @endcan

        @can('consultar_alumno')
            <a  href="{{ route("alumnos.show",$id) }}"
                class="btn btn-success btn-sm text-white fas fa-id-card"
                data-toggle="tooltip"
                data-placement="top"
                data-action="show"
                title="Ver">
            </a>
        @endcan

        @can('eliminar_alumno')
            <a class="btn btn-danger btn-sm text-white fas fa-trash"
                href="{{ route('alumnos.destroy',$id) }}"
                data-toggle="tooltip"
                data-id="{{ $id }}"
                data-action="delete"
                data-placement="top"
                title="Eliminar">
            </a>
        @endcan
    </div>
</div>
