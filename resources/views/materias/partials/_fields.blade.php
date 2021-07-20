<fieldset class="form-group">
    <legend><span>Informacion de la Materia</span></legend>
    <div class="row">

        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('nombre', 'Nombre:*'); !!}
                {!! Form::text('nombre', null, ['class' => 'form-control', 'placeholder' => 'Escribe aqui el nombre','required' => true,'autocomplete' => 'off']); !!}
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('especialidad', 'Especialidad:*'); !!}
                {!! Form::select('especialidad', config('alumnos.especialidad'), null, ['class' => 'form-control']) !!}
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('fase', 'Fase:*'); !!}
                {!! Form::text('fase', null, ['class' => 'form-control', 'placeholder' => 'Escribe aqui la fase','required' => true,'autocomplete' => 'off']); !!}
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('orden', 'Orden:*'); !!}
                {!! Form::number('orden', null, ['class' => 'form-control', 'placeholder' => 'Escribe aqui la fase','required' => true,'autocomplete' => 'off']); !!}
            </div>
        </div>
    </div>
</fieldset>
