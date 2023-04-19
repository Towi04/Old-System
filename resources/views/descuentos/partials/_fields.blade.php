<fieldset class="form-group">
    <legend><span>Informacion de la especialidad</span></legend>
    <div class="row">
        
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('id_especialidad_1', 'Especialidad 1:'); !!}
                {!! Form::select('id_especialidad_1', $especialidades->pluck('nombre','id')->prepend('Selecciona una opción',''), null, ['class' => 'form-control']) !!}
                <p class="form-text text-muted">
                    A esta especialidad se le aplicará el descuento. 
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('id_especialidad_2', 'Especialidad 2:'); !!}
                {!! Form::select('id_especialidad_2', $especialidades->pluck('nombre','id')->prepend('Selecciona una opción',''), null, ['class' => 'form-control']) !!}
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('porcentaje_descuento', 'Porcentaje descuento:'); !!}
                {!! Form::text('porcentaje_descuento', null, ['class' => 'form-control']) !!}
            </div>
        </div>
    </div>
</fieldset>
