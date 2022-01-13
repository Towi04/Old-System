<div class="text-center">
    <div class="btn-group">
        {{-- @can('eliminar_alumno') --}}
            <a class="btn btn-danger btn-sm text-white fas fa-trash"
                href="{{ route('apoyos-especiales.destroy',$id) }}"
                data-toggle="tooltip"
                data-id="{{ $id }}"
                data-action="delete"
                data-placement="top"
                title="Eliminar">
            </a>
        {{-- @endcan --}}

    </div>
</div>
