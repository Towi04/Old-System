<!DOCTYPE html>
<html lang="es">
    <head>
        <title><?php echo $__env->yieldContent('title'); ?></title>
        <meta charset="utf-8">
        <meta content="ie=edge" http-equiv="x-ua-compatible">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta content="<?php echo e(config('settings.company.autor')); ?>" name="author">
        <meta content="<?php echo e(config('settings.company.description')); ?>"  name="description">
        <link href="<?php echo e(asset('img/logo.png')); ?>" rel="shortcut icon">
        <link href="apple-touch-icon.png" rel="apple-touch-icon">
        <link href="https://fonts.googleapis.com/css?family=Lato:300,400,700" rel="stylesheet" type="text/css">
        <link href="<?php echo e(asset('template-clean-admin/css/main.css?version=4.3.0')); ?>" rel="stylesheet">
        <script src="https://kit.fontawesome.com/1f556c46ab.js" crossorigin="anonymous"></script>
    </head>
    <body>

            <div class="content-box">
                <div class="big-error-w">
                    <div class="text-center pb-2">
                        <center>
                            <img src="<?php echo e(asset('img/logo.png')); ?>" style="width:250px;">
                        </center>
                    </div>
                <h1>
                    <?php echo $__env->yieldContent('code'); ?>
                </h1>
                <h5>
                    <?php echo $__env->yieldContent('message'); ?>
                </h5>


                    <div class="row">
                        <div class="col-md-12">
                        <a class="btn btn-primary btn-block" href="<?php echo e(app('router')->has('home') ? route('home') : url('/')); ?>">
                            <i class="btn-label fas fa-home"></i> <?php echo e(__('ir a Inicio')); ?>

                        </a>
                        </div>
                    </div>
                </div>

            </div>




        <script src="<?php echo e(asset('template-clean-admin/bower_components/jquery/dist/jquery.min.js')); ?>"></script>
        <script src="<?php echo e(asset('template-clean-admin/bower_components/popper.js/dist/umd/popper.min.js')); ?>"></script>
        <script src="<?php echo e(asset('template-clean-admin/js/main.js?version=4.3.0')); ?>"></script>
        <script src="<?php echo e(asset('template-clean-admin/bower_components/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js')); ?>"></script>
        <script src="<?php echo e(asset('template-clean-admin/bower_components/bootstrap-daterangepicker/daterangepicker.js')); ?>"></script>
        <script src="<?php echo e(asset('template-clean-admin/bower_components/perfect-scrollbar/js/perfect-scrollbar.jquery.min.js')); ?>"></script>
        <script src="<?php echo e(asset('template-clean-admin/bower_components/bootstrap/js/dist/util.js')); ?>"></script>
        <script src="<?php echo e(asset('template-clean-admin/bower_components/bootstrap/js/dist/alert.js')); ?>"></script>
        <script src="<?php echo e(asset('template-clean-admin/bower_components/bootstrap/js/dist/button.js')); ?>"></script>
        <script src="<?php echo e(asset('template-clean-admin/bower_components/bootstrap/js/dist/carousel.js')); ?>"></script>
        <script src="<?php echo e(asset('template-clean-admin/bower_components/bootstrap/js/dist/collapse.js')); ?>"></script>
        <script src="<?php echo e(asset('template-clean-admin/bower_components/bootstrap/js/dist/dropdown.js')); ?>"></script>
        <script src="<?php echo e(asset('template-clean-admin/bower_components/bootstrap/js/dist/modal.js')); ?>"></script>
        <script src="<?php echo e(asset('template-clean-admin/bower_components/bootstrap/js/dist/tab.js')); ?>"></script>
        <script src="<?php echo e(asset('template-clean-admin/bower_components/bootstrap/js/dist/tooltip.js')); ?>"></script>
        <script src="<?php echo e(asset('template-clean-admin/bower_components/bootstrap/js/dist/popover.js')); ?>"></script>
        <script src="<?php echo e(asset('template-clean-admin/bower_components/bootstrap/js/dist/collapse.js')); ?>"></script>
    </body>
</html>
<?php /**PATH /Users/aldo/Sites/cncm/resources/views/errors/minimal.blade.php ENDPATH**/ ?>