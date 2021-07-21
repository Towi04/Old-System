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
        'web'   => 'www.cncm.edu.mx'
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
        [
            'name'          => 'listar_alumnos',
            'display_name'  => 'Listar de alumnos',
            'description'   => 'Permite mostrar la lista de alumnos'
        ],
        [
            'name'          => 'crear_alumno',
            'display_name'  => 'Crear alumno',
            'description'   => 'Permite crear a un alumno'
        ],
        [
            'name'          => 'consultar_alumno',
            'display_name'  => 'Consultar Alumno',
            'description'   => 'Permite consultar a un alumno'
        ],
        [
            'name'          => 'editar_alumno',
            'display_name'  => 'Editar alumno',
            'description'   => 'Permite editar un alumno'
        ],
        [
            'name'          => 'eliminar_alumno',
            'display_name'  => 'Eliminar alumno',
            'description'   => 'Permite eliminar un alumno'
        ],
        [
            'name'          => 'listar_materias',
            'display_name'  => 'Listar Materias',
            'description'   => 'Permite Mostrar las materias'
        ],
        [
            'name'          => 'crear_materia',
            'display_name'  => 'Crear Materia',
            'description'   => 'Permite Crear una materia'
        ],
        [
            'name'          => 'editar_materia',
            'display_name'  => 'Editar Materia',
            'description'   => 'Permite editar una materia'
        ],
        [
            'name'          => 'eliminar_materia',
            'display_name'  => 'Eliminar materia',
            'description'   => 'Permite eliminar una materia'
        ],
        [
            'name'          => 'listar_grupos',
            'display_name'  => 'Listar Grupos',
            'description'   => 'Permite mostrar la lista de grupos'
        ],
        [
            'name'          => 'crear_grupo',
            'display_name'  => 'Crear grupo',
            'description'   => 'Permite crear a un grupo'
        ],
        [
            'name'          => 'consultar_grupo',
            'display_name'  => 'Consultar grupo',
            'description'   => 'Permite consultar a un grupo'
        ],
        [
            'name'          => 'editar_grupo',
            'display_name'  => 'Editar grupo',
            'description'   => 'Permite editar un grupo'
        ],
        [
            'name'          => 'eliminar_grupo',
            'display_name'  => 'Eliminar alumno',
            'description'   => 'Permite eliminar un grupo'
        ],
        [
            'name'          => 'asignar_materias',
            'display_name'  => 'Asignar Materias',
            'description'   => 'Permite asignar una materia',
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
            'nombre'            => 'Plantel Celaya',
            'direccion'         => 'Andador Gongora 101 Zona Centro',
            'municipio'         => 'Celaya',
            'estado'            => 'Guanajuato',
        ],
        [
            'nombre'            => 'Plantel Irapuato',
            'direccion'         => 'Av. Guerrero #378, Col. Centro.',
            'municipio'         => 'Irapuato',
            'estado'            => 'Guanajuato',
        ],
        [
            'nombre'            => 'Plantel Salamanca',
            'direccion'         => 'Portal de los Bravo #121, Col. Centro.',
            'municipio'         => 'Salamnca',
            'estado'            => 'Guanajuato',
        ],
        [
            'nombre'            => 'Plantel San Luis Potosí',
            'direccion'         => 'Zaragoza #875, Col. Centro.',
            'municipio'         => 'San Luis Potosí',
            'estado'            => 'Guanajuato',
        ],

    ],
];
