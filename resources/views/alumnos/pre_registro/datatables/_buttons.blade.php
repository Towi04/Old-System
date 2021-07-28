<div class="text-center">
    <div class="btn-group">
        @can('convertir_pre_registro_alumno')
            <a  href="{{ route("pre-registro-alumnos.edit",$id) }}"
                class="btn btn-success btn-sm text-white fas fa-check"
                data-toggle="tooltip"
                data-placement="top"
                data-action="edit"
                title="Editar">
            </a>
        @endcan

        <a class="btn btn-danger btn-sm text-white fas fa-trash"
            href="{{ route('pre-registro-alumnos.destroy',$id) }}"
            data-toggle="tooltip"
            data-id="{{ $id }}"
            data-action="delete"
            data-placement="top"
            title="Eliminar">
        </a>

        {{-- @can('consultar_alumno')
            <a  href="{{ route("pre-registro-alumnos.show",$id) }}"
                class="btn btn-success btn-sm text-white fas fa-id-card"
                data-toggle="tooltip"
                data-placement="top"
                data-action="show"
                title="Ver">
            </a>
        @endcan --}}
    </div>
</div>
