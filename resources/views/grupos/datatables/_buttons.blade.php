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

        @can('consultar_grupo')
            <a  href="{{ route("grupos.show",$id) }}"
                class="btn btn-warning btn-sm text-white fas fa-id-card"
                data-toggle="tooltip"
                data-placement="top"
                title="Panel">
            </a>
        @endcan

        @can('asignar_materias')
            <a  href="{{ route("grupos.asignar-materias",$id) }}"
                class="btn btn-success btn-sm text-white fas fa-book"
                data-toggle="tooltip"
                data-placement="top"
                data-action="show"
                title="Asignar Materia">
            </a>
        @endcan

        @can('asignar_alumnos')
            <a  href="{{ route("grupos.asignar-alumnos",$id) }}"
                class="btn btn-info btn-sm text-white fas fa-users"
                data-toggle="tooltip"
                data-placement="top"
                title="Asignar alumnos">
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

        @can('consultar_grupo')
        <a  href="{{ route("grupos.cronograma",$id) }}"
            class="btn btn-secondary btn-sm text-white fas fa-clock"
            data-toggle="tooltip"
            data-placement="top"
            title="Cronograma">
        </a>

        <a  href="{{ route("grupos.lista-asistencia",$id) }}"
            class="btn btn-dark btn-sm text-white fas fa-file-pdf"
            target="_blank"
            rel="noopener"
            data-toggle="tooltip"
            data-placement="top"
            title="Lista">
        </a>


    @endcan
    </div>
</div>
