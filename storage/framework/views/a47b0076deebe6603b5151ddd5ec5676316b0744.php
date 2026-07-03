<style>
   @media(max-width:1025px) {
        .d-lg-block {
            display: none !important;
        }
    }
</style>

<ul class="main-menu no_print">
    <li class="sub-header d-none d-sm-none d-md-none d-lg-block d-xl-block">
      <span >Menú principal</span>
    </li>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['gestionar_usuarios','gestionar_roles','gestionar_permisos','gestionar_sucursales','listar_productos'])): ?>
        <li class="selected has-sub-menu">
            <a href="#">
                <div class="icon-w">
                    <div class="fa fa-cogs"></div>
                </div>
                <span>Administración</span>
            </a>
            <div class="sub-menu-w">
                <div class="sub-menu-header d-none d-sm-none d-md-none d-lg-block d-xl-block">
                    Administración
                </div>
                <div class="sub-menu-i">
                    <ul class="sub-menu">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check(['gestionar_configuraciones'])): ?>
                            <li>
                                <a href="<?php echo e(route('configuraciones.index')); ?>">Configuraciones</a>
                            </li>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check(['gestionar_usuarios'])): ?>
                            <li>
                                <a href="<?php echo e(route('admin.usuarios.index')); ?>">Usuarios</a>
                            </li>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check(['gestionar_roles'])): ?>
                            <li>
                                <a href="<?php echo e(route('admin.roles.index')); ?>">Roles</a>
                            </li>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check(['gestionar_permisos'])): ?>
                            <li>
                                <a href="<?php echo e(route('admin.permisos.index')); ?>">Permisos</a>
                            </li>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check(['gestionar_sucursales'])): ?>
                            <li>
                                <a href="<?php echo e(route('admin.sucursales.index')); ?>">Sucursal</a>
                            </li>
                        <?php endif; ?>
                    </ul>
                    <ul class="sub-menu">
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check(['listar_especialidades'])): ?>
                            <li>
                                <a href="<?php echo e(route('admin.especialidades.index')); ?>">Especialidades</a>
                            </li>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check(['listar_cuentas_bancarias'])): ?>
                            <li>
                                <a href="<?php echo e(route('admin.cuentas-bancarias.index')); ?>">Cuentas Bancarias</a>
                            </li>
                        <?php endif; ?>

                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check(['listar_productos'])): ?>
                            <li>
                                <a href="<?php echo e(route('admin.productos.index')); ?>">Productos</a>
                            </li>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check(['ver_horarios_profesores'])): ?>
                        <li>
                            <a href="<?php echo e(route('admin.horarios-profesores.index')); ?>">Horarios Profesores</a>
                        </li>
                        <?php endif; ?>
                        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check(['gestionar_descuentos'])): ?>
                        <li>
                            <a href="<?php echo e(route('descuentos.index')); ?>">Descuentos</a>
                        </li>
                        <?php endif; ?>

                    </ul>
                </div>
            </div>
        </li>
    <?php endif; ?>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['consultar_preregistros_alumnos','realizar_pre_registro','convertir_pre_registro_alumno'])): ?>
        <li class="">
            <a href="<?php echo e(route('pre-registro-alumnos.index')); ?>">
                <div class="icon-w">
                    <div class="fa fa-bookmark"></div>
                </div>
                <span>Pre-Registro Alumnos</span></a>
        </li>
    <?php endif; ?>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('listar_alumnos')): ?>
        <li class="">
            <a href="<?php echo e(route('alumnos.index')); ?>">
                <div class="icon-w">
                <div class="fa fa-user"></div>
                </div>
                <span>Alumnos</span></a>
        </li>
    <?php endif; ?>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('registrar_asistencias')): ?>
        <li class="">
            <a href="<?php echo e(route('asistencias.index')); ?>">
                <div class="icon-w">
                <div class="fas fa-check"></div>
                </div>
                <span>Registrar asistencias</span></a>
        </li>
    <?php endif; ?>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('listar_materias')): ?>
        <li class="selected has-sub-menu">
            <a href="#">
                <div class="icon-w">
                    <div class="fa fa-cogs"></div>
                </div>
                <span>Especialidades</span>
            </a>
            <div class="sub-menu-w">
                <div class="sub-menu-header d-none d-sm-none d-md-none d-lg-block d-xl-block">
                    Especialidades
                </div>
                <div class="sub-menu-i">
                        <?php
                            $especialidades = \App\Models\Especialidad::get();
                            $i=0;
                        ?>
                        <?php $__currentLoopData = $especialidades; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $especialidad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php if($i==0): ?>
                                <ul class="sub-menu">
                            <?php endif; ?>
                                <li>
                                    <a href="<?php echo e(route('materias.index', $especialidad->id)); ?>"><?php echo e($especialidad->nombre); ?></a>
                                </li>
                                <?php if($i==7): ?>
                                    </ul>
                                <?php
                                    $i=-1;
                                ?>
                                <?php endif; ?>
                                    <?php
                                        $i=$i+1;
                                    ?>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                </div>
            </div>
        </li>
    <?php endif; ?>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->any(['listar_grupos','ver_todos_grupos'])): ?>
        <li class="">
            <a href="<?php echo e(route('grupos.index')); ?>">
                <div class="icon-w">
                <div class="fa fa-users"></div>
                </div>
                <span>Grupos</span></a>
        </li>
    <?php endif; ?>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ingresar_punto_venta')): ?>
        <li class="">
            <a href="<?php echo e(route('punto_de_venta.index')); ?>">
                <div class="icon-w">
                <div class="fas fa-cash-register"></div>
                </div>
                <span>Punto de venta</span></a>
        </li>
    <?php endif; ?>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('punto_de_venta_productos')): ?>
        <li class="">
            <a href="<?php echo e(route('punto_de_venta_productos.index')); ?>">
                <div class="icon-w">
                <div class="fas fa-cash-register"></div>
                </div>
                <span>Venta de productos</span></a>
        </li>
    <?php endif; ?>

    <li class="selected has-sub-menu">
        <a href="#">
            <div class="icon-w">
                <div class="fa fa-cogs"></div>
            </div>
            <span>Reportes</span>
        </a>
        <div class="sub-menu-w">
            <div class="sub-menu-header d-none d-sm-none d-md-none d-lg-block d-xl-block">
                Reportes
            </div>
            <div class="sub-menu-i">
                <ul class="sub-menu">
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ver_reporte_ventas')): ?>
                    <li>
                        <a href="<?php echo e(route('reportes.reporte-ventas.index')); ?>">Reporte de Ventas</a>
                    </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ver_reporte_ventas_productos')): ?>
                    <li>
                        <a href="<?php echo e(route('reportes.reporte-ventas.index_productos')); ?>">Reporte de Ventas (Productos)</a>
                    </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ver_reporte_vencimiento')): ?>
                    <li>
                        <a href="<?php echo e(route('reportes.reporte-ventas.vencimientos')); ?>">Reporte de Vencimientos</a>
                    </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ver_reporte_proyeccion')): ?>
                    <li>
                        <a href="<?php echo e(route('reportes.reporte-ventas.proyeccion')); ?>">Reporte de Proyección</a>
                    </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ver_reporte_asesores')): ?>
                    <li>
                        <a href="<?php echo e(route('reportes.reporte-ventas.asesores')); ?>">Reporte de Asesores</a>
                    </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ver_reporte_asistencias_personal')): ?>
                    <li>
                        <a href="<?php echo e(route('reportes.asistencias_personal')); ?>">Reporte de Asistencias Personal</a>
                    </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ver_alertas')): ?>
                    <li>
                        <a href="<?php echo e(route('reportes.alertas')); ?>">Reporte de Alertas</a>
                    </li>
                    <?php endif; ?>
                </ul>
                <ul class="sub-menu">
                    
                    
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ver_reporte_desercion')): ?>
                    <li>
                        <a href="<?php echo e(route('reportes.desercion')); ?>">Reporte de Retención</a>
                    </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ver_reporte_apoyos_inscripcion')): ?>
                    <li>
                        <a href="<?php echo e(route('reportes.apoyos_inscripcion')); ?>">Reporte de Apoyos a la inscripción</a>
                    </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ver_reporte_recomendados')): ?>
                    <li>
                        <a href="<?php echo e(route('reportes.recomendados')); ?>">Reporte de Recomendados</a>
                    </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ver_reporte_programados')): ?>
                    <li>
                        <a href="<?php echo e(route('reportes.programados')); ?>">Reporte de Programados</a>
                    </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ver_reporte_pagos_eliminados')): ?>
                    <li>
                        <a href="<?php echo e(route('reportes.pagos_eliminados')); ?>">Reporte de Pagos Eliminados</a>
                    </li>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ver_reporte_asesores')): ?>
                    <li>
                        <a href="<?php echo e(route('reportes.inscritos')); ?>">Reporte de Inscritos</a>
                    </li>
                    <?php endif; ?>
                    
                </ul>
            </div>
        </div>
    </li>

    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('entrar_calendario')): ?>
        <li class="">
            <a href="<?php echo e(route('agendar-asesoria.index')); ?>">
                <div class="icon-w">
                <div class="os-icon os-icon-calendar"></div>
                </div>
                <span>Calendario asesorias</span></a>
        </li>
    <?php endif; ?>

    <li class="sub-header d-none d-sm-none d-md-none d-lg-block d-xl-block">
        <span>Opciones</span>
    </li>

    <li class="">
        <a href="<?php echo e(route('soporte.index')); ?>">
            <div class="icon-w">
            <div class="fa fa-headphones"></div>
            </div>
            <span>Soporte Técnico</span></a>
    </li>

    <li class=" ">
        <a href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault();document.getElementById('logout-form').submit();">
        <div class="icon-w">
            <div class="os-icon os-icon-signs-11"></div>
        </div>
        <span>Cerrar Sesión</span></a>
    </li>
</ul>
<?php /**PATH /Users/aldo/Sites/cncm/resources/views/layouts/template-clean-admin/partials/menu.blade.php ENDPATH**/ ?>