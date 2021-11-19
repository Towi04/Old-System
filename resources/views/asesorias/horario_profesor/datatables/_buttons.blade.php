<div class="text-center">
    <div class="btn-group">
        <a  href="{{ route("asesorias.horarios-profesores.edit",$id) }}"
            class="btn btn-primary btn-sm text-white fas fa-pencil-alt"
            data-toggle="tooltip"
            data-placement="top"
            data-action="edit"
            title="Editar">
        </a>

        <a class="btn btn-danger btn-sm text-white fas fa-trash"
            href="{{ route('asesorias.horarios-profesores.destroy',$id) }}"
            data-toggle="tooltip"
            data-id="{{ $id }}"
            data-action="delete"
            data-placement="top"
            title="Eliminar">
        </a>
    </div>
</div>
