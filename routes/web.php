<?php

use App\Http\Controllers\AbonosController;
use App\Http\Controllers\Admin\CuentasBancariasController;
use App\Http\Controllers\Admin\EspecialidadesController;
use App\Http\Controllers\Admin\MostrarHorariosProfesoresController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SoporteController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\ProductosController;
use App\Http\Controllers\Admin\SucursalesController;
use App\Http\Controllers\AgendarAsesoriaController;
use App\Http\Controllers\AlumnosController;
use App\Http\Controllers\Asesorias\CalendarioProfesorController;
use App\Http\Controllers\Asesorias\HorariosProfesoresController;
use App\Http\Controllers\GruposController;
use App\Http\Controllers\MateriasController;
use App\Http\Controllers\PreRegistrosController;
use App\Http\Controllers\PuntoDeVentaController;
use App\Http\Controllers\ConfiguracionesController;
use App\Http\Controllers\ComprasController;
use App\Http\Controllers\Reportes\ReporteVentasController;
use App\Http\Controllers\PuntoDeVentaProductosController;
use App\Http\Controllers\AsistenciasController;

#NOTE: CONFIGURACION DE RUTAS
Auth::routes(['register'=> false]);

Route::middleware(['auth','sucursal'])->group(function () {
    # NOTE: RUTAS GENERALES
    Route::get('/', [HomeController::class, 'index'])->name('home');

    Route::prefix('archivo')->name('ver_archivo')->group(function () {
        Route::get('{modulo}/{id}/{archivo}', [HomeController::class,'ver_archivo']);
        Route::get('{modulo}/{id}/', [HomeController::class,'ver_archivo']);
    });

    Route::prefix('perfil')->name('profile.')->group(function () {
        Route::get('/',[ProfileController::class,'index'])->name('index');
        Route::get('/editar',[ProfileController::class,'edit'])->name('editar');
        Route::put('/{usuario}/editar',[ProfileController::class,'update'])->name('update');
    });

    Route::prefix('soporte-tecnico')->name('soporte.')->group(function () {
        Route::get('/',[SoporteController::class,'index'])->name('index');
        Route::post('/enviar-correo',[SoporteController::class,'enviar_correo_soporte'] )->name('enviar-correo');
    });

    # NOTE: RUTAS ADMINISTRATIVAS
    Route::middleware(['verified'])->name('admin.')->prefix('admin')->group(function () {
        # NOTE: USUARIOS
        Route::post('usuarios/traer_usuarios_select2', [UsersController::class, 'traer_usuarios_select2']) ->name('usuarios.traer_usuarios_select2');
        Route::resource('usuarios', UsersController::class)
        ->parameters([
            'usuarios' => 'usuario'
        ]);

        #NOTE: ROLES
        Route::post('roles/datatables', [ RolesController::class,'datatables'])->name('roles.datatables');
        Route::resource('roles', RolesController::class)->except(['show'])->parameters([
            'roles' => 'role'
        ]);


        # NOTE: PERMISOS
        Route::resource('permisos', PermissionController::class)->parameters([
            'permisos' => 'permiso'
        ])->only('index');
        Route::post('permisos/guardar_permiso',[PermissionController::class,'guardar_permiso'] )->name('permisos.guardar-permiso');
        Route::post('permisos/traer_permisos',[PermissionController::class,'traer_permisos'] )->name('permisos.traer-permisos');

        # NOTE: SUCURSALES
        Route::get('sucursales/asignar-sucursal', [ SucursalesController::class,'asignar_sucursal'])->name('sucursales.asignar-sucursal');
        Route::post('sucursales/asociar-sucursal', [ SucursalesController::class,'asociar_sucursal'])->name('sucursales.asociar-sucursal');

        Route::post('sucursales/datatables', [ SucursalesController::class,'datatables'])->name('sucursales.datatables');
        Route::resource('sucursales', SucursalesController::class)->parameters([
            'sucursales' => 'sucursal'
        ]);

        # NOTE: ESPECIALIDADES
        Route::get('especialidades/cronograma/{id}', [ EspecialidadesController::class,'cronograma'])->name('especialidades.cronograma');
        Route::post('especialidades/datatables', [ EspecialidadesController::class,'datatables'])->name('especialidades.datatables');
        Route::resource('especialidades', EspecialidadesController::class)->parameters([
            'especialidades' => 'especialidad'
        ]);

        # NOTE: CUENTAS BANCARIAS
        Route::post('cuentas-bancarias/datatables', [ CuentasBancariasController::class,'datatables'])->name('cuentas-bancarias.datatables');
        Route::resource('cuentas-bancarias', CuentasBancariasController::class)->parameters([
            'cuentas-bancarias' => 'cuentaBancaria'
        ]);

        # NOTE: PRODUCTOS
        Route::post('productos/traer_productos_select2', [ProductosController::class, 'traer_productos_select2']) ->name('productos.traer_productos_select2');
        Route::prefix('productos')->name('productos.')->group(function () {
            Route::post('datatables', [ ProductosController::class,'datatables'])->name('datatables');
        });

        Route::resource('productos', ProductosController::class)->parameters([
            'productos' => 'producto',
        ])->except('show');

        # NOTE: COMPRAS
        Route::prefix('compras')->name('compras.')->group(function () {
            Route::post('datatables', [ ComprasController::class,'datatables'])->name('datatables');
        });

        Route::get('compras/{id_producto}', [ ComprasController::class,'index'])->name('compras.index');

        Route::get('compras/create/{id_producto}', [ ComprasController::class,'create'])->name('compras.create');
        Route::post('compras/store', [ ComprasController::class,'store'])->name('compras.store');
        Route::delete('compras/{id}', [ ComprasController::class,'destroy'])->name('compras.destroy');
        Route::get('compras/{id_producto}/edit', [ ComprasController::class,'edit'])->name('compras.edit');
        Route::put('compras/update/{id}', [ ComprasController::class,'update'])->name('compras.update');

        # NOTE: MOSTRAR HORARIOS DE PROFESORES
        Route::prefix('horarios-profesores')->name('horarios-profesores.')->group(function () {
            Route::get('/', [MostrarHorariosProfesoresController::class,'index'])->name('index');
            Route::post('datatables', [MostrarHorariosProfesoresController::class,'datatables'])->name('datatables');
        });
    });

    #RUTAS PRE-REGISTRO ALUMNOS
    Route::get('pre-registro-alumnos/formulario-inscripcion/{alumno}', [ PreRegistrosController::class,'formulario_inscripcion'])->name('pre-registro-alumnos.formulario-inscripcion');
    Route::put('pre-registro-alumnos/inscribir/{id}', [ PreRegistrosController::class,'inscribir'])->name('pre-registro-alumnos.inscribir');
    Route::post('pre-registro-alumnos/datatables', [ PreRegistrosController::class,'datatables'])->name('pre-registro-alumnos.datatables');
    Route::resource('pre-registro-alumnos', PreRegistrosController::class)->parameters([
        'pre-registro-alumnos' => 'alumno'
    ]);

    # NOTE: RUTAS ALUMNOS
    Route::post('alumnos/datatables', [ AlumnosController::class,'datatables'])->name('alumnos.datatables');
    Route::post('alumnos/datatables_pagos', [ AlumnosController::class,'datatables_pagos'])->name('alumnos.datatables_pagos');
    Route::post('alumnos/datatables_pagos_pendientes', [ AlumnosController::class,'datatables_pagos_pendientes'])->name('alumnos.datatables_pagos_pendientes');
    Route::post('alumnos/traer_alumnos_select2', [AlumnosController::class, 'traer_alumnos_select2']) ->name('alumnos.traer_alumnos_select2');
    Route::get('alumnos/formulario_inscribir_otro_grupo/{alumno}', [ AlumnosController::class,'formulario_inscribir_otro_grupo'])->name('alumnos.formulario_inscribir_otro_grupo');
    Route::put('alumnos/inscribir_a_otro_grupo/{id}', [ AlumnosController::class,'inscribir_a_otro_grupo'])->name('alumnos.inscribir_a_otro_grupo');

    Route::resource('alumnos', AlumnosController::class)->parameters([
        'alumnos' => 'alumno'
    ]);

    # NOTE: RUTAS MATERIAS
    Route::post('materias/datatables', [ MateriasController::class,'datatables'])->name('materias.datatables');
    Route::post('materias/traer_materias_select2', [MateriasController::class, 'traer_materias_select2']) ->name('materias.traer_materias_select2');
    Route::get('materias/{id}', [ MateriasController::class,'index'])->name('materias.index');
    Route::get('materias/create/{id}', [ MateriasController::class,'create'])->name('materias.create');
    Route::resource('materias', MateriasController::class)->except(['index', 'show','create'])->parameters([
        'materias' => 'materia'
    ]);

    # NOTE: RUTAS GRUPOS (RESPETAR EL ORDEN DE LAS RUTAS)
    Route::post('grupos/finalizar_grupo', [ GruposController::class,'finalizar_grupo'])->name('grupos.finalizar_grupo');
    Route::post('grupos/datatables', [ GruposController::class,'datatables'])->name('grupos.datatables');
    Route::post('grupos/traer_grupos_select2', [ GruposController::class,'traer_grupos_select2'])->name('grupos.traer_grupos_select2');

    Route::post('grupos/datatables_materias', [ GruposController::class,'datatables_materias'])->name('grupos.datatables_materias');
    Route::post('grupos/actualizar-materias-xeditable', [ GruposController::class,'actualizar_materias_xeditable'])->name('grupos.actualizar_materias_xeditable');
    Route::post('grupos/eliminar-materias', [ GruposController::class,'eliminar_materias'])->name('grupos.eliminar-materias');

    Route::post('grupos/datatables_alumnos', [ GruposController::class,'datatables_alumnos'])->name('grupos.datatables_alumnos');
    Route::post('grupos/actualizar-alumnos-xeditable', [ GruposController::class,'actualizar_alumnos_xeditable'])->name('grupos.actualizar_alumnos_xeditable');
    Route::post('grupos/eliminar-alumnos', [ GruposController::class,'eliminar_alumnos'])->name('grupos.eliminar-alumnos');

    Route::get('grupos/{grupo}/asignar-materias', [ GruposController::class,'asignar_materias'])->name('grupos.asignar-materias');
    Route::post('grupos/{grupo}/guardar-materias', [ GruposController::class,'guardar_materias'])->name('grupos.guardar-materias');
    Route::get('grupos/{grupo}/asignar-alumnos', [ GruposController::class,'asignar_alumnos'])->name('grupos.asignar-alumnos');
    Route::post('grupos/{grupo}/guardar-alumnos', [ GruposController::class,'guardar_alumnos'])->name('grupos.guardar-alumnos');
    Route::get('grupos/{grupo}/lista-asistencia', [ GruposController::class,'lista_asistencia'])->name('grupos.lista-asistencia');
    Route::post('grupos/traer_info', [ GruposController::class,'traer_info'])->name('grupos.traer_info');
    Route::get('grupos/cronograma/{grupo}', [ GruposController::class,'cronograma'])->name('grupos.cronograma');
    Route::resource('grupos', GruposController::class)->parameters([
        'grupos' => 'grupo'
    ]);


    # NOTE RUTAS PUNTO DE DE VENTA ALUMNOS

    Route::prefix('punto_de_venta')->name('punto_de_venta.')->group(function () {
        Route::post('pago_manual',[ PuntoDeVentaController::class,'pago_manual'])->name('pago_manual');
        Route::post('recibir_abonos',[ PuntoDeVentaController::class,'recibir_abonos'])->name('recibir_abonos');
        Route::get('ticket/{id}',[ PuntoDeVentaController::class,'ticket'])->name('ticket');
    });

    Route::resource('punto_de_venta', PuntoDeVentaController::class)->only('index');


    # NOTE RUTAS PUNTO DE DE VENTA
    Route::post('punto_de_venta_productos/actualizar_informacion_partida',[ PuntoDeVentaProductosController::class,'actualizar_informacion_partida'])->name('punto_de_venta_productos.actualizar_informacion_partida');
    Route::post('punto_de_venta_productos/guardar_partida',[ PuntoDeVentaProductosController::class,'guardar_partida'])->name('punto_de_venta_productos.guardar_partida');
    Route::post('punto_de_venta_productos/eliminar_partida/{id}',[ PuntoDeVentaProductosController::class,'eliminar_partida'])->name('punto_de_venta_productos.eliminar_partida');
    Route::post('punto_de_venta_productos/recibir_abonos',[ PuntoDeVentaProductosController::class,'recibir_abonos'])->name('punto_de_venta_productos.recibir_abonos');
    Route::post('punto_de_venta_productos/datatables_partidas',[ PuntoDeVentaProductosController::class,'datatables_partidas'])->name('punto_de_venta_productos.datatables_partidas');
    Route::post('punto_de_venta_productos/cerrar_venta',[ PuntoDeVentaProductosController::class,'cerrar_venta'])->name('punto_de_venta_productos.cerrar_venta');
    Route::get('punto_de_venta_productos/ticket/{id}',[ PuntoDeVentaProductosController::class,'ticket'])->name('punto_de_venta_productos.ticket');
    Route::resource('punto_de_venta_productos', PuntoDeVentaProductosController::class)->only('index');



    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('reporte-ventas',[ReporteVentasController::class,'index'])->name('reporte-ventas.index');
        Route::get('reporte-ventas-productos',[ReporteVentasController::class,'index_productos'])->name('reporte-ventas.index_productos');
        Route::get('vencimientos',[ReporteVentasController::class,'vencimientos'])->name('reporte-ventas.vencimientos');
        Route::post('datatables_vencimientos',[ReporteVentasController::class,'datatables_vencimientos'])->name('reporte-ventas.datatables_vencimientos');
        Route::get('proyeccion',[ReporteVentasController::class,'proyeccion'])->name('reporte-ventas.proyeccion');
        Route::post('datatables_proyeccion',[ReporteVentasController::class,'datatables_proyeccion'])->name('reporte-ventas.datatables_proyeccion');
        Route::get('asesores',[ReporteVentasController::class,'asesores'])->name('reporte-ventas.asesores');
        Route::post('convertir_ventas_fiscales',[ReporteVentasController::class,'convertir_ventas_fiscales'])->name('reporte-ventas.convertir_ventas_fiscales');
    });

    Route::get('configuraciones', [ ConfiguracionesController::class,'index'])->name('configuraciones.index');
    Route::post('configuraciones/actualizar_informacion_xeditables', [ ConfiguracionesController::class,'actualizar_informacion_xeditables'])->name('configuraciones.actualizar_informacion_xeditables');

    # NOTE RUTAS ASISTENCIAS
    Route::get('asistencias',[ AsistenciasController::class,'index'])->name('asistencias.index');
    Route::post('asistencias/registrar_asistencia',[ AsistenciasController::class,'registrar_asistencia'])->name('asistencias.registrar_asistencia');
    Route::post('asistencias/eliminar_asistencia',[ AsistenciasController::class,'eliminar_asistencia'])->name('asistencias.eliminar_asistencia');

    # NOTE: ASESORIAS PROFESORES
    Route::prefix('asesorias')->name('asesorias.')->group(function () {
        Route::prefix('horarios-profesores')->name('horarios-profesores.')->group(function () {
            Route::post('datatables',[HorariosProfesoresController::class,'datatables'])->name('datatables');
        });
        Route::resource('horarios-profesores', HorariosProfesoresController::class)->parameters([
            'horarios-profesores' => 'horarioProfesor'
        ]);

        Route::prefix('calendario-profesor')->name('calendario-profesor.')->group(function(){
            Route::get('/',[CalendarioProfesorController::class,'index'])->name('index');
            Route::post('traer-asesorias', [CalendarioProfesorController::class,'traer_asesorias'])->name('traer-asesorias');
            Route::post('status-asesoria', [CalendarioProfesorController::class,'status_asesoria'])->name('status-asesoria');
        });

    });

    # NOTE: AGENDAR UNA ASESORIA CON UN PROFESOR
    Route::prefix('agendar-asesoria')->name('agendar-asesoria.')->group(function(){
        Route::get('/',[AgendarAsesoriaController::class,'index'])->name('index');
        Route::post('guardar-asesoria',[AgendarAsesoriaController::class,'guardar_asesoria'])->name('guardar-asesoria');
        Route::put('actualizar-asesoria/{asesoria}',[AgendarAsesoriaController::class,'actualizar_asesoria'])->name('actualizar-asesoria');
        Route::delete('eliminar-asesoria/{asesoria}',[AgendarAsesoriaController::class,'eliminar_asesoria'])->name('eliminar-asesoria');
        Route::post('traer-asesorias', [AgendarAsesoriaController::class,'traer_asesorias'])->name('traer-asesorias');
        Route::post('status-asesoria', [AgendarAsesoriaController::class,'status_asesoria'])->name('status-asesoria');
        Route::post('horarios-profesor', [AgendarAsesoriaController::class,'horarios_profesor'])->name('horarios-profesor');
    });

    # NOTE:ABONOS
    Route::prefix('abonos')->name('abonos.')->group(function(){
        Route::post('actualizar_informacion_xeditable', [AbonosController::class,'actualizar_informacion_xeditable'])->name('actualizar_informacion_xeditable');
        Route::post('actualizar_pago_xeditable', [AbonosController::class,'actualizar_pago_xeditable'])->name('actualizar_pago_xeditable');
        Route::post('actualizar_alumno_pago_xeditable', [AbonosController::class,'actualizar_alumno_pago_xeditable'])->name('actualizar_alumno_pago_xeditable');
    });

});
