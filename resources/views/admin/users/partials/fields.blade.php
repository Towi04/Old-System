     <div class="row">
         <div class="col-lg-12 col-md-12 col-sm-12 col-xl-12 col-12">
             <div class="element-wrapper" style="padding-bottom: 5px">
                 <h3 class="element-header">
                     Datos de la cuenta
                 </h3>
                 <div class="element-box">
                     <div class="row">
                         <div class="col-12 col-lg-8">
                             <div class="form-group">
                                {!! Form::label('nombres', 'Nombres*') !!}
                                {!! Form::text('nombres', null, ['class' => 'form-control', 'placeholder' => 'Escribe aqui los nombre(s)']) !!}
                             </div>

                             <div class="form-group">
                                {!! Form::label('apellido_paterno', 'Apellido paterno*') !!}
                                {!! Form::text('apellido_paterno', null, ['class' => 'form-control', 'placeholder' =>'Escribe aqui el apellido paterno']) !!}
                             </div>

                             <div class="form-group">
                                {!! Form::label('apellido_materno', 'Apellido materno') !!}
                                {!! Form::text('apellido_materno', null, ['class' => 'form-control', 'placeholder' => 'Escribe aquí el apellido materno']) !!}
                             </div>

                             <div class="form-group">
                                {!! Form::label('email', 'Email*') !!}
                                {!! Form::text('email', null, ['class' => 'form-control', 'placeholder' => 'Ingresa el Correo Electrónico', 'type' => 'email']) !!}
                                <p class="help-block">Ejemplos: minombre@gmail.com yo@miempresa.com.mx </p>
                             </div>

                             <div class="form-group">
                                {!! Form::label('celular', 'Celular') !!}
                                {!! Form::text('celular', null, ['class' => 'form-control', 'placeholder' => 'Ingresa el Celular']) !!}
                             </div>

                             <div class="form-group">
                                {!! Form::label('password', 'Contraseña*') !!}
                                {!! Form::password('password', ['class' => 'form-control', 'placeholder' => 'Contraseña', 'id' => 'input_password']) !!}
                             </div>

                             <!--Password-->
                             <div class="form-group" id="div_enviar_datos" @if (@$user) style="display:none" @endif>

                                 <label> <input type="checkbox" class="i-checks" name="enviar_datos"> Enviar datos por
                                     correo </label>

                                {{-- <input id="demo-form-checkbox" class="magic-checkbox" type="checkbox" name="enviar_datos">
                                <label for="demo-form-checkbox">Enviar datos de acceso.</label> --}}

                             </div>
                         </div>

                         <div class="col-xl-4 col-lg-4 col-md-4 col-sm-4 col-12 ">
                             <label for="input-file-now">Foto: </label>
                             <input type="file" id="input-file-now" class="dropify" name="foto"
                                 value="{{ @$user->foto }}" @if (isset($user)) data-default-file="{{ url('archivo/usuarios_foto/' . $user->id . '/' . $user->foto) }}" @endif) />

                             <div class="element-wrapper pt-4">
                                 <h3 class="element-header">
                                     Roles
                                 </h3>
                                 <div class="element-box">
                                     {!! Form::label('role', 'Selecciona los roles del usuario:') !!}
                                     @foreach ($roles as $role)

                                         <br>
                                         <label>
                                            <?php
                                                $algo = false;
                                                $user_r = [];

                                                if (isset($user)) {
                                                    $user_r = $user->roles->toArray();
                                                }
                                            ?>

                                             @foreach ($user_r as $user_roles)
                                                 @if ($user_roles['id'] == $role->id)
                                                    <?php $algo=true; ?>
                                                 @endif
                                             @endforeach

                                             {!! Form::checkbox('role[]', $role->id, $algo, ['class' => 'i-checks']) !!}
                                             {{ $role->display_name }}
                                         </label>
                                     @endforeach
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </div>
         </div>
     </div>
