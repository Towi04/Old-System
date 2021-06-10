<div class="row">
    <div class="col-lg-12 col-md-12 col-sm-12 col-xl-12 col-12">
        <div class="element-box">
            <div class="form-group">
                {!! Form::label('display_name', 'Nombre:*'); !!}
                {!! Form::text('display_name', null, ['class' => 'form-control', 'placeholder' => 'Escribe aqui el nombre del rol']); !!}
            </div>
            <div class="form-group">
                {!! Form::label('descripcion', 'Descripción'); !!}
                {!! Form::textarea('description', null, ['class' => 'form-control', 'placeholder' => 'Escribe aqui la descripción','rows'=>'5']); !!}
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    window.addEventListener('DOMContentLoaded', (event) => {
    });
</script>
