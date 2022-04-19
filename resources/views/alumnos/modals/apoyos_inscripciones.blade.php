<div class="onboarding-modal modal fade" id="modal-apoyo-inscripcion" tabindex="-1" data-backdrop="static" data-keyboard="false" role="dialog" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"><span
                        aria-hidden="true">&times;</span><span class="sr-only">Close</span></button>
                <h5 class="modal-title">Agregar precio de inscripcion <small>(*) Campos requeridos</small> </h5>
            </div>

            {!! Form::open(['route' => 'alumnos.store_apoyo_inscripcion','id' => 'form-apoyo-inscripcion']) !!}
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::label('apoyo', 'Apoyo:*'); !!}
                    {!! Form::number('apoyo', null, ['class' => 'form-control', 'placeholder' => 'Escribe el monto del apoyo','required' => true,'step' => '0.01','autocomplete' => 'off','style' => "",]); !!}
                </div>
                <div class="form-group">
                    {!! Form::label('id_grupo','Selecciona el grupo:') !!}
                    {!! Form::select('id_grupo', $alumno->grupos->pluck('nombre_compuesto','id'), optional($alumno->grupos->first())->id, ['id'=>'select_grupo','class'=>'form-control w-100']) !!}
                </div>
                
            </div>

            <input type="hidden" name="id_alumno" value="{{$alumno->id}}">

            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn btn-primary">Guardar</button>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
