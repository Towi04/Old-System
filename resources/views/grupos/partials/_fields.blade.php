<fieldset class="form-group">
    <legend><span>Informacion del grupo</span></legend>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('id_especialidad', 'Especialidad:*'); !!}
                {!! Form::select('id_especialidad', $especialidades, null, ['class' => 'form-control','required' => true]) !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('horario', 'Horario:*'); !!}
                {!! Form::select('horario', config('grupos.horario'), null, ['class' => 'form-control','required' => true]) !!}
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('dias', 'Dias:*'); !!}
                {!! Form::select('dias', config('grupos.dias'), null, ['class' => 'form-control','required' => true]) !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('fecha_inicio', 'Fecha inicio:*'); !!}
                {!! Form::date('fecha_inicio', null, ['class' => 'form-control', 'placeholder' => 'Escribe la fecha de inicio','required' => true]); !!}
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('inscripcion', 'Inscripcion:*'); !!}
                {!! Form::number('inscripcion', null, ['class' => 'form-control','required' => true]) !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('colegiatura', 'Colegiatura:*'); !!}
                {!! Form::number('colegiatura', null, ['class' => 'form-control','required' => true]) !!}
            </div>
        </div>

        <div class="col-md-12">
            <br>
            {!! Form::label('infantil', '¿Grupo Infantil?*'); !!} &nbsp;
            <label>
                {!! Form::checkbox('infantil', 1, null, ['class' => 'i-checks']) !!}
            </label>
        </div>
    </div>
</fieldset>
