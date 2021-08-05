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

        @if($grupo->exists)
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('precio_semanal', 'Precio semanal:'); !!}
                    {!! Form::number('precio_semanal', null, ['class' => 'form-control','disabled' => true]) !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('precio_mensualidad_pronto_pago', 'Pronto Pago:*'); !!}
                    {!! Form::number('precio_mensualidad_pronto_pago', null, ['class' => 'form-control','disabled' => true]) !!}
                </div>
            </div>

            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('precio_mensualidad', 'Mensualidad:*'); !!}
                    {!! Form::number('precio_mensualidad', null, ['class' => 'form-control','disabled' => true]) !!}
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    {!! Form::label('precio_inscripcion', 'Inscripcion:*'); !!}
                    {!! Form::number('precio_inscripcion', null, ['class' => 'form-control','disabled' => true]) !!}
                </div>
            </div>
        @endif

        <div class="col-md-12">
            <br>
            {!! Form::label('infantil', '¿Grupo Infantil?*'); !!} &nbsp;
            <label>
                {!! Form::checkbox('infantil', 1, null, ['class' => 'i-checks']) !!}
            </label>
        </div>
    </div>
</fieldset>
