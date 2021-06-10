<div class="form-group">
    {!! Form::label('nombres', 'Nombres*') !!}
    {!! Form::text('nombres', null, ['class' => 'form-control', 'placeholder' => 'Escribe aqui los nombre(s)','required' => true]) !!}
</div>
<div class="form-group">
    {!! Form::label('apellido_paterno', 'Apellido paterno*') !!}
    {!! Form::text('apellido_paterno', null, ['class' => 'form-control', 'placeholder' =>'Escribe aqui el apellido paterno','required' => true]) !!}
 </div>
 <div class="form-group">
    {!! Form::label('apellido_materno', 'Apellido materno') !!}
    {!! Form::text('apellido_materno', null, ['class' => 'form-control', 'placeholder' => 'Escribe aquí el apellido materno']) !!}
 </div>

 <div class="form-group">
    {!! Form::label('celular', 'Celular') !!}
    {!! Form::text('celular', null, ['class' => 'form-control', 'placeholder' => 'Ingresa el Celular']) !!}
 </div>
