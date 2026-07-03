<?php $__env->startSection('titulo'); ?>
    Inicio
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('breadcrumb'); ?>
    <ol class="breadcrumb">
        <li class="breadcrumb-item">
            <a href="<?php echo e(url('/')); ?>">Inicio</a>
        </li>
    </ol>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('contenido'); ?>

    <div class="row">
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ver_cumpleaños_personal')): ?>
            <div class="col-6">
                <div class="element-box">
                    <h4 class="element-header">
                        Cumpleaños de personal
                    </h4>
                    <table  class="table">
                        <thead>
                            <tr>
                                <th>Personal</th>
                                <th>Fecha nacimiento</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $usuarios_cumples; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $personal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($personal->fullname); ?></td>
                                    <td><?php echo e($personal->fecha_nacimiento->format('d-m-Y')); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ver_cumpleaños_alumnos')): ?>
            <div class="col-6">
                <div class="element-box">
                    <h4 class="element-header">
                        Cumpleaños de alumnos
                    </h4>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Alumno</th>
                                <th>Fecha nacimiento</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $alumnos_cumples; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alumno): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($alumno->fullname); ?> </td>
                                    <td><?php echo e($alumno->fecha_nacimiento->format('d-m-Y')); ?></td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
              
            </div>
        <?php endif; ?>

        <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('ver_alertas')): ?>
        <div class="col-6">
            <div class="element-box">
                <h4 class="element-header">
                    Alertas de hoy
                    <span class="text-right">
                        <a href="<?php echo e(route('reportes.alertas')); ?>">Ver reporte</a>
                    </span>
                </h4>
                <table class="table table-padded">
                    <tbody>
                        <?php $__currentLoopData = $alertas; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $alerta): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td>
                                    <h5><?php echo e($alerta->titulo); ?></h5>
                                    <?php echo $alerta->descripcion; ?>


                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
          
        </div>
    <?php endif; ?>
    </div>

    <?php if(empty(session('sucursal'))): ?>
        <div class="element-wrapper">
            <div class="element-box">
                <h5 class="form-header">
                    No tienes asignada una sucursal
                </h5>
                <div class="form-desc">
                    Para poder continuar, solicita que te asignen una sucursal
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.template-'.config('settings.template').'.plantilla', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /Users/aldo/Sites/cncm/resources/views/home.blade.php ENDPATH**/ ?>