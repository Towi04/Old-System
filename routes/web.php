<?php

use App\Http\Controllers\Admin\CuentasBancariasController;
use App\Http\Controllers\Admin\EspecialidadesController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SoporteController;
use App\Http\Controllers\Admin\RolesController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\SucursalesController;
use App\Http\Controllers\AlumnosController;
use App\Http\Controllers\GruposController;
use App\Http\Controllers\MateriasController;
use App\Http\Controllers\PreRegistrosController;
use App\Http\Controllers\PuntoDeVentaController;
use App\Http\Controllers\Reportes\ReporteVentasController;

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
        Route::post('especialidades/datatables', [ EspecialidadesController::class,'datatables'])->name('especialidades.datatables');
        Route::resource('especialidades', EspecialidadesController::class)->parameters([
            'especialidades' => 'especialidad'
        ]);

        # NOTE: CUENTAS BANCARIAS
        Route::post('cuentas-bancarias/datatables', [ CuentasBancariasController::class,'datatables'])->name('cuentas-bancarias.datatables');
        Route::resource('cuentas-bancarias', CuentasBancariasController::class)->parameters([
            'cuentas-bancarias' => 'cuentaBancaria'
        ]);
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
    Route::resource('alumnos', AlumnosController::class)->parameters([
        'alumnos' => 'alumno'
    ]);

    # NOTE: RUTAS MATERIAS
    Route::post('materias/datatables', [ MateriasController::class,'datatables'])->name('materias.datatables');
    Route::post('materias/traer_materias_select2', [MateriasController::class, 'traer_materias_select2']) ->name('materias.traer_materias_select2');
    Route::resource('materias', MateriasController::class)->except('show')->parameters([
        'materias' => 'materia'
    ]);

    # NOTE: RUTAS GRUPOS (RESPETAR EL ORDEN DE LAS RUTAS)
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

    Route::post('grupos/traer_info', [ GruposController::class,'traer_info'])->name('grupos.traer_info');

    Route::resource('grupos', GruposController::class)->parameters([
        'grupos' => 'grupo'
    ]);


    # NOTE RUTAS PUNTO DE DE VENTA
    Route::post('punto_de_venta/recibir_abonos',[ PuntoDeVentaController::class,'recibir_abonos'])->name('punto_de_venta.recibir_abonos');
    Route::get('punto_de_venta/ticket/{id}',[ PuntoDeVentaController::class,'ticket'])->name('punto_de_venta.ticket');
    Route::resource('punto_de_venta', PuntoDeVentaController::class)->only('index');



    Route::prefix('reportes')->name('reportes.')->group(function () {
        Route::get('reporte-ventas',[ReporteVentasController::class,'index'])->name('reporte-ventas.index');
        Route::get('vencimientos',[ReporteVentasController::class,'vencimientos'])->name('reporte-ventas.vencimientos');
        Route::post('datatables_vencimientos',[ReporteVentasController::class,'datatables_vencimientos'])->name('reporte-ventas.datatables_vencimientos');
        Route::get('proyeccion',[ReporteVentasController::class,'proyeccion'])->name('reporte-ventas.proyeccion');
        Route::post('datatables_proyeccion',[ReporteVentasController::class,'datatables_proyeccion'])->name('reporte-ventas.datatables_proyeccion');
    });
});
