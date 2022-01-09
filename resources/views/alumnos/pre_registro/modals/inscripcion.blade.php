<div class="modal inmodal fade animated" id="modal-inscripcion" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-md">
        <div class="modal-content animated bounceInRight">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Inscripción de alumno <small>(*) Campos requeridos</small></h5>
                    <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">
                        &times;</span><span class="sr-only">Close</span>
                    </button>
                </div>
                {!! Form::open(['id' => 'form-inscripcion']) !!}
                <div class="modal-body">

                    <div class="row">
                        <div class="col-12">
                            <p class="form-desc font-weight-bold p-0 m-0 border-0" id="inscripcion-detalle"></p>
                        </div>
                    </div>

                    <fieldset class="form-group">
                        <legend><span>Forma de pago</span></legend>
                        <div class="row">
                            <div class="col-12">
                                {!! Form::label('tipo_pago','Tipo pago:*') !!}
                                {!! Form::select('tipo_pago', [
                                    'Efectivo'              => 'Efectivo',
                                    'Tarjate de debito'     => 'Tarjate de debito',
                                    'Tarjate de crédito'    => 'Tarjate de crédito',
                                    'Transferencia'         => 'Transferencia'
                                ],null, ['class' => 'form-control form-control-sm','form-selector'=> '','placeholder' =>'Selecciona una forma de pago' ,'required' => true]) !!}
                            </div>
                        </div>
                    </fieldset>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Inscribir</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
