<?php

/**
 * @author adn <a.aranza@aranzaycervantes.mx>
 *
 * Archivo de configuracion para los parametros de cada proyecto
 */
return [
    'template' => env('FRONT_TEMPLATE', 'clean-admin'),
    'company' => [
        'autor'         => env('APP_AUTOR', 'Aldo David Aranza Mora'),
        'description'   => env('APP_DESCRIPTION', "Página de administración de " . env('APP_NAME', 'Laravel')),
        'empresa'       => env('APP_EMPRESA', 'ADN - Administración Digital de Negocios'),
        'web'           => env('APP_WEB', 'https://adndigital.mx'),
        'telefono'      => env('APP_TELEFONO', '461 1567492'),
        'soporte_email' => env('APP_SOPORTE_EMAIL', 'a.aranza@aranzaycervantes.mx'),
    ],
    'site' => [
        'web'   => 'www.cncm.com.mx'
    ],

    /**
     * Permisos de prueba (Puedes agregar permisos en esta seccion)
     * PARA ACTUALIZAR, EJECUTAR php artisan db:seed
     */
    'permissions' => [
        [
            'name'          => 'gestionar_usuarios',
            'display_name'  => 'Gestión de usuarios',
            'description'   => 'Permite la gestion de usuarios'
        ],
        [
            'name'          => 'gestionar_roles',
            'display_name'  => 'Gestión de roles',
            'description'   => 'Permite la gestion de roles'
        ],
        [
            'name'          => 'gestionar_permisos',
            'display_name'  => 'Gestión de permisos',
            'description'   => 'Permite la gestion de permisos'
        ],
        [
            'name'          => 'gestionar_sucursales',
            'display_name'  => 'Gestión de sucursales',
            'description'   => 'Permite la gestion de sucursales'
        ],
        [
            'name'          => 'asignar_varias_sucursales',
            'display_name'  => 'Asignar varias sucursales',
            'description'   => 'Permite la asignacion de multiples sucursales'
        ],
    ],

    # Roles del sistema (Puedes agregar mas roles de prueba)
    'roles' => [
        [
            'name'          => 'administrador',
            'display_name'  => 'Administrativo',
            'description'   => 'Usuario con permisos de administrativo',
        ],
    ],

    /**
     * Usuarios de prueba
     */
    'users' => [
        [
            'nombres'           => 'Aldo David',
            'apellido_paterno'  => 'Aranza',
            'apellido_materno'  => 'Mora',
            'email'             => 'a.aranza@aranzaycervantes.mx',
            'password'          => '26227426',
            'celular'           => '4611567492',
            'remember_token'    => null,
        ]
    ],

    'sucursales' => [
        [
            'nombre'            => 'cncm',
            'direccion'         => 'Andador Gongora 101 Zona Centro',
            'municipio'         => 'Celaya',
            'estado'            => 'Guanajuato',
        ]
    ],
];
