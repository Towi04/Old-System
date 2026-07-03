<!DOCTYPE html>
<html lang="es">
  <head>
    <title><?php echo e(config('app.name')); ?></title>
    <meta charset="utf-8">
    <meta content="ie=edge" http-equiv="x-ua-compatible">
    <meta content="<?php echo e(config('settings.company.autor')); ?>" name="author">
    <meta content="<?php echo e(config('settings.company.description')); ?>"  name="description">
    <meta content="width=device-width, initial-scale=1" name="viewport">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <link href="<?php echo e(asset('icon.jpeg')); ?>" rel="shortcut icon">
    <link href="<?php echo e(asset('icon.jpeg')); ?>" rel="apple-touch-icon">

    <link href="https://fonts.googleapis.com/css?family=Rubik:300,400,500" rel="stylesheet" type="text/css">
    <link href="<?php echo e(asset('template-clean-admin/bower_components/select2/dist/css/select2.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('template-clean-admin/bower_components/bootstrap-daterangepicker/daterangepicker.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('template-clean-admin/bower_components/dropzone/dist/dropzone.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/plugins/dataTables/datatables.min.css')); ?>" rel="stylesheet">

    <link href="<?php echo e(asset('template-clean-admin/bower_components/perfect-scrollbar/css/perfect-scrollbar.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('template-clean-admin/bower_components/slick-carousel/slick/slick.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('template-clean-admin/css/main.css?version=4.3.0')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('js/plugins/dropify/dist/css/dropify.min.css')); ?>" rel="stylesheet" >
    <link href="<?php echo e(asset('css/plugins/datapicker/datepicker3.css')); ?>" rel="stylesheet">
    
    <link href="<?php echo e(asset('js/fontawesome/js/all.min.js')); ?>" rel="stylesheet">
    <script src="<?php echo e(asset('js/fontawesome/js/all.min.js')); ?>" crossorigin="anonymous"></script>
    
    <link href="<?php echo e(asset('css/plugins/toastr/toastr.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/plugins/colorpicker/bootstrap-colorpicker.min.css')); ?>" rel="stylesheet">

    <link href="<?php echo e(asset('css/plugins/sweetalert/sweetalert.css')); ?>" rel="stylesheet" type="text/css">
    
    <script src="<?php echo e(asset('plugins/dragula.js/dist/dragula.min.js')); ?>"></script>
    
    <style type="text/css">
		@media  print {
            a[href]:after {
                content: none !important;
            }

            .no_print{
                isplay: none;
            }

            #version_imprimible, #version_imprimible > *{
                display: inline !important;
            }

            td, td > *{
                font-size: 8px;
            }

            th, th > *{
                font-size: 8px;
            }

        }

        @media  screen{
            #version_imprimible {
                display: none;
            }
        }
        .breadcrumb {

            background:rgba(84,85,85, 0.1) !important;
            border-radius: 0px;
        }
        .breadcrumb li a {
            color: #2c2b2b !important;
        }

        .breadcrumb-item.active {
            color: #b7182E !important;
        }

        .contact-box:hover{
            transform: scale(1.05);
        }

        .table-responsive {
            overflow-x: auto !important;
        }

        .content-w {
            overflow: hidden !important;
        }

        .top-bar.color-scheme-bright h4,.top-bar.color-scheme-bright i {
            color: #fff;
        }

        .table-responsive>.fixed-column {
            position: absolute;
            display: inline-block;
            width: auto;
            border-right: 1px solid #ddd;
        }

        .top-bar.color-scheme-bright h4,.top-bar.color-scheme-bright i {
            color: #fff;
        }

        .menu-w.sub-menu-style-over .sub-menu-w {
            background: #b7182E!important;
        }

        .menu-w.sub-menu-style-over ul.main-menu > li.active {
            border-right-color: #b7182E!important;
            border-bottom-color: #b7182E!important;
        }

        .menu-w.sub-menu-style-over.sub-menu-color-bright ul.main-menu > li.active > a {
            background-color: #b7182E!important;
        }

        .menu-w .logged-user-menu.color-style-bright {
            background-color: #b7182E!important;
        }

        .img-responsive{
          width: 100%;
        }

        /* .menu-w.color-scheme-dark.color-style-bright ul.main-menu .icon-w {
            color: #b7182E!important;
        } */

        @media(min-width:768px) {
            .table-responsive>.fixed-column {
                display: none;
            }
        }

        table.dataTable {
            clear: both;
            margin-top: 0px !important;
            margin-bottom: 0px !important;
        }
    </style>

    <?php echo $__env->yieldContent('css'); ?>
  </head>
  <body class="menu-position-side menu-side-left full-screen with-content-panel">
    <div class="all-wrapper solid-bg-all">
      <div class="layout-w">
        <!--------------------
        START - Mobile Menu
        -------------------->
        <div class="menu-mobile menu-activated-on-click color-scheme-dark no_print" >
          <div class="mm-logo-buttons-w">
            <a class="mm-logo" href="<?php echo e(url('/')); ?>"><img src="<?php echo e(asset('img/logo-horizontal-negativo.png')); ?>"><span><?php echo e(config('app.name')); ?></span></a>
            <div class="mm-buttons">
              
              <div class="mobile-menu-trigger">
                <div class="os-icon os-icon-hamburger-menu-1"></div>
              </div>
            </div>
          </div>
          <div class="menu-and-user">
            <div class="logged-user-w">
              <div class="avatar-w">
                <img alt="" src="<?php echo e(url('archivo/usuarios_foto/')); ?>/<?php echo e(Auth::user()->id); ?>/<?php echo e(Auth::user()->foto); ?>">
              </div>
              <div class="logged-user-info-w">
                <div class="logged-user-name">
                  <?php echo e(Auth::user()->fullname); ?>

                </div>
                <div class="logged-user-role">
                  
                </div>
              </div>
            </div>
            <!--------------------
            START - Mobile Menu List
            -------------------->
            <?php echo $__env->make('layouts.template-clean-admin.partials.menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
            <!--------------------
            END - Mobile Menu List
            -------------------->

          </div>
        </div>
        <!--------------------
        END - Mobile Menu
        --------------------><!--------------------
        START - Main Menu
        -------------------->
        <div class="menu-w no_print no-print color-scheme-dark color-style-bright menu-position-side menu-side-left menu-layout-compact sub-menu-style-over sub-menu-color-bright selected-menu-color-light menu-activated-on-hover menu-has-selected-link">
          <div class="logo-w no_print">
            <a class="logo" href="<?php echo e(url('/')); ?>">
              <center><img src="<?php echo e(asset('img/logo-horizontal-negativo.png')); ?>" style="width:100% !important" alt=""></center>
            </a>
          </div>
          <div class="logged-user-w avatar-inline no_print">
            <div class="logged-user-i">
              <div class="avatar-w">
                <img alt="" src="<?php echo e(url('archivo/usuarios_foto/')); ?>/<?php echo e(Auth::user()->id); ?>/<?php echo e(Auth::user()->foto); ?>">
              </div>
              <div class="logged-user-info-w">
                <div class="logged-user-name">
                  <?php echo e(Auth::user()->fullname); ?>

                </div>
                <div class="logged-user-role">
                  <?php echo e(optional(Auth::user()->roles->first())->display_name); ?>

                </div>
              </div>
              <div class="logged-user-toggler-arrow">
                <div class="os-icon os-icon-chevron-down"></div>
              </div>
              <div class="logged-user-menu color-style-bright">
                <div class="logged-user-avatar-info">
                  <div class="avatar-w">
                    <img alt="" src="<?php echo e(url('archivo/usuarios_foto/')); ?>/<?php echo e(Auth::user()->id); ?>/<?php echo e(Auth::user()->foto); ?>">
                  </div>
                  <div class="logged-user-info-w">
                    <div class="logged-user-name">
                      <?php echo e(Auth::user()->fullname); ?>

                    </div>
                    <div class="logged-user-role">
                      <?php echo e(optional(Auth::user()->roles->first())->display_name); ?>

                    </div>
                  </div>
                </div>
                <div class="bg-icon">
                  <i class="os-icon os-icon-wallet-loaded"></i>
                </div>
                <ul>
                  <li>
                    <a href="<?php echo e(route('profile.index')); ?>"><i class="os-icon os-icon-user-male-circle2"></i><span>Mi cuenta</span></a>
                  </li>
                  <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('impartir_asesorias')): ?>
                    <li>
                        <a href="<?php echo e(route('asesorias.horarios-profesores.index')); ?>"><i class="os-icon os-icon-clock"></i><span>Mis Horarios</span></a>
                    </li>

                    <li>
                        <a href="<?php echo e(route('asesorias.calendario-profesor.index')); ?>"><i class="os-icon os-icon-calendar"></i><span>Mi Calendario</span></a>
                    </li>
                  <?php endif; ?>
                  <li>
                    <a  href="<?php echo e(route('logout')); ?>"  onclick="event.preventDefault();
                    document.getElementById('logout-form').submit();"><i class="os-icon os-icon-signs-11"></i><span>Cerrar Sesión</span></a>
                  </li>
                </ul>
              </div>
            </div>
          </div>
          
          
          <h1 class="menu-page-header">
            Page Header
          </h1>
          <?php echo $__env->make('layouts.template-clean-admin.partials.menu', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

        </div>
        <!--------------------
        END - Main Menu
        -------------------->
        <div class="content-w" style="min-height: 100vh;" >
          <!--------------------
          START - Top Bar
          -------------------->
          <div class="top-bar color-scheme-bright no_print">
            <span class="ml-4 mt-2">
              <h4><?php echo $__env->yieldContent('titulo'); ?></h4>
            </span>

            <!--------------------
            START - Top Menu Controls
            -------------------->
            <div class="top-menu-controls">
              
              <!--------------------
              START - Messages Link in secondary top menu
              -------------------->
              <div class="messages-notifications os-dropdown-trigger os-dropdown-position-left">
                <small><?php echo e(optional(session('sucursal'))->nombre); ?></small>
                <i class="fa fa-exchange"></i>
                <div class="os-dropdown light message-list">
                  <ul>
                  <?php $__empty_1 = true; $__currentLoopData = session('sucursales',[]); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sucursal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <li>
                      <a href="<?php echo e(route('admin.sucursales.show',$sucursal)); ?>">
                        <div class="user-avatar-w">
                          <img alt="<?php echo e($sucursal->id); ?>_<?php echo e($sucursal->nombre); ?>" src="<?php echo e(asset('img/logo.png')); ?>">
                        </div>
                        <div class="message-content">
                          <h6 class="message-from">
                            <?php echo e($sucursal->nombre); ?>

                          </h6>
                          <h6 class="message-title">
                            <?php echo e($sucursal->estado); ?>

                          </h6>
                        </div>
                      </a>
                    </li>
                  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <li>
                      <a href="#">
                        <div class="message-content">
                          <h6 class="message-from">
                            Sin Sucursal
                          </h6>
                        </div>
                      </a>
                    </li>
                  <?php endif; ?>
                  </ul>

                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('gestionar_sucursales')): ?>
                    <div class="text-center pt-2">
                        <a href="<?php echo e(route('admin.sucursales.index')); ?>" class="login-links text-center">Ver sucursales</a>
                    </div>
                    <?php endif; ?>
                </div>
              </div>
              <!--------------------
              END - Messages Link in secondary top menu
              --------------------><!--------------------
              START - Settings Link in secondary top menu
              -------------------->
              
              <!--------------------
              END - Settings Link in secondary top menu
              --------------------><!--------------------
              START - User avatar and menu in secondary top menu
              -------------------->

              <!--------------------
              END - User avatar and menu in secondary top menu
              -------------------->
            </div>
            <!--------------------
            END - Top Menu Controls
            -------------------->
          </div>
          <!--------------------
          END - Top Bar
          --------------------><!--------------------
          START - Breadcrumbs
          -------------------->
          
          <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
            <?php echo csrf_field(); ?>
        </form>
          <?php echo $__env->yieldContent('breadcrumb'); ?>
          <!--------------------
          END - Breadcrumbs
          -------------------->

          <div class="content-i">
            <div class="content-box p-2 " >
              <div class="row">
                <div class="col-sm-12">
                  <div class="element-wrapper">
                    <?php echo $__env->yieldContent('contenido'); ?>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="display-type"></div>
    </div>
    <script src="<?php echo e(asset('template-clean-admin/bower_components/jquery/dist/jquery.min.js')); ?>"></script>
    <script src="<?php echo e(asset('template-clean-admin/bower_components/popper.js/dist/umd/popper.min.js')); ?>"></script>
    <script src="<?php echo e(asset('template-clean-admin/bower_components/moment/moment.js')); ?>"></script>
    <script src="<?php echo e(asset('template-clean-admin/bower_components/chart.js/dist/Chart.min.js')); ?>"></script>
    <script src="<?php echo e(asset('template-clean-admin/bower_components/select2/dist/js/select2.full.min.js')); ?>"></script>
    <script src="<?php echo e(asset('template-clean-admin/bower_components/jquery-bar-rating/dist/jquery.barrating.min.js')); ?>"></script>
    <script src="<?php echo e(asset('template-clean-admin/bower_components/ckeditor/ckeditor.js')); ?>"></script>
    <script src="<?php echo e(asset('template-clean-admin/bower_components/bootstrap-validator/dist/validator.min.js')); ?>"></script>
    <script src="<?php echo e(asset('template-clean-admin/bower_components/bootstrap-daterangepicker/daterangepicker.js')); ?>"></script>
    <script src="<?php echo e(asset('template-clean-admin/bower_components/ion.rangeSlider/js/ion.rangeSlider.min.js')); ?>"></script>
    <script src="<?php echo e(asset('template-clean-admin/bower_components/dropzone/dist/dropzone.js')); ?>"></script>
    <script src="<?php echo e(asset('template-clean-admin/bower_components/editable-table/mindmup-editabletable.js')); ?>"></script>
    <script src="<?php echo e(asset('js/plugins/dataTables/datatables.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/plugins/dataTables/dataTables.bootstrap4.min.js')); ?>"></script>
    <script src="<?php echo e(asset('template-clean-admin/bower_components/tether/dist/js/tether.min.js')); ?>"></script>
    <script src="<?php echo e(asset('template-clean-admin/bower_components/slick-carousel/slick/slick.min.js')); ?>"></script>
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
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <script src="<?php echo e(asset('template-clean-admin/js/main.js?version=4.3.0')); ?>"></script>
    <script src="<?php echo e(asset('js/plugins/dropify/dist/js/dropify.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/plugins/datapicker/bootstrap-datepicker.js')); ?>"></script>
    <script src="<?php echo e(asset('js/plugins/toastr/toastr.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/plugins/colorpicker/bootstrap-colorpicker.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/plugins/sweetalert/sweetalert.min.js')); ?>"></script>
    <script src="<?php echo e(asset('js/plugins/sweetalert/jquery.sweet-alert.custom.js')); ?>"></script>
    <script src="<?php echo e(asset('highcharts/code/highcharts.js')); ?>"></script>
    <script src="https://code.highcharts.com/5.0.14/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/5.0.14/modules/solid-gauge.js"></script>
    <script src="https://code.highcharts.com/5.0.14/highcharts-3d.js"></script>

    <script src="<?php echo e(asset('highcharts/code/modules/exporting.js')); ?>"></script>
    <script src="<?php echo e(asset('highcharts/code/modules/export-data.js')); ?>"></script>
    <?php echo $__env->make('partials.messages', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

    <script type="text/javascript">
        $(document).ready(function () {
            $.fn.datepicker.dates['es'] = {
                days: ["Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado"],
                daysShort: ["Dom", "Lun", "Mar", "Mié", "Jue", "Vie", "Sáb"],
                daysMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sá"],
                months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
                monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
                today: "Hoy",
                clear: "Borrar"
            };

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>
    <?php echo $__env->yieldContent('scripts'); ?>

  </body>
</html>
<?php /**PATH /Users/aldo/Sites/cncm/resources/views/layouts/template-clean-admin/plantilla.blade.php ENDPATH**/ ?>