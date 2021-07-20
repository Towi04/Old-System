<?php

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
        Route::resource('roles', RolesController::class)->except(['show']);


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

    });

    # NOTE: RUTAS ALUMNOS
    Route::post('alumnos/datatables', [ AlumnosController::class,'datatables'])->name('alumnos.datatables');
    Route::resource('alumnos', AlumnosController::class);

    # NOTE: RUTAS MATERIAS
    Route::post('materias/datatables', [ MateriasController::class,'datatables'])->name('materias.datatables');
    Route::resource('materias', MateriasController::class)->except('show');

    # NOTE: RUTAS GRUPOS
    Route::post('grupos/datatables', [ GruposController::class,'datatables'])->name('grupos.datatables');
    Route::resource('grupos', GruposController::class);
});
