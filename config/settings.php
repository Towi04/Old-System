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
        [
            'name'          => 'asignar_alumnos',
            'display_name'  => 'Asignar alumnos',
            'description'   => 'Permite asignar alumnos a un grupo',
        ],
        [
            'name'          => 'realizar_pre_registro',
            'display_name'  => 'Realizar Pre Registro',
            'description'   => 'Permite realizar el pre-registro de los alumnos',
        ],

        [
            'name'          => 'convertir_pre_registro_alumno',
            'display_name'  => 'Convertir Pre-registro en Alunno',
            'description'   => 'Permite transformar un pre-registro en alumno',
        ],

        [
            'name'          => 'listar_especialidades',
            'display_name'  => 'Listar Especialidades',
            'description'   => 'Permite mostrar la lista de especialidades'
        ],
        [
            'name'          => 'crear_especialidad',
            'display_name'  => 'Crear especialidad',
            'description'   => 'Permite crear una especialidad'
        ],
        [
            'name'          => 'consultar_especialidad',
            'display_name'  => 'Consultar especialidad',
            'description'   => 'Permite consultar una especialidad'
        ],
        [
            'name'          => 'editar_especialidad',
            'display_name'  => 'Editar especialidad',
            'description'   => 'Permite editar una especialidad'
        ],
        [
            'name'          => 'eliminar_especialidad
            ',
            'display_name'  => 'Eliminar especialidad',
            'description'   => 'Permite eliminar una especialidad'
        ],

        [
            'name'          => 'listar_cuentas_bancarias',
            'display_name'  => 'Listar cuentas bancarias',
            'description'   => 'Permite mostrar la lista de cuentas_bancarias'
        ],
        [
            'name'          => 'crear_cuenta_bancaria',
            'display_name'  => 'Crear cuenta bancaria',
            'description'   => 'Permite crear una cuenta bancaria'
        ],
        [
            'name'          => 'consultar_cuenta_bancaria',
            'display_name'  => 'Consultar cuenta_bancaria',
            'description'   => 'Permite consultar una cuenta bancaria'
        ],
        [
            'name'          => 'editar_cuenta_bancaria',
            'display_name'  => 'Editar cuenta bancaria',
            'description'   => 'Permite editar una cuenta bancaria'
        ],
        [
            'name'          => 'eliminar_cuenta_bancaria',
            'display_name'  => 'Eliminar cuenta bancaria',
            'description'   => 'Permite eliminar una cuenta bancaria'
        ],
        [
            'name'          => 'ingresar_punto_venta',
            'display_name'  => 'Ingresar al punto de venta',
            'description'   => 'Permite ingresar al punto de venta y realizar cobros a los alumnos'
        ],
        [
            'name'          => 'editar_datos_fiscales',
            'display_name'  => 'Editar Datos Fiscales',
            'description'   => 'Permiso para editar los datos fiscales del alumno'
        ],
        [
            'name'          => 'consultar_preregistros_alumnos',
            'display_name'  => 'Consultar Pre-registros Alumnos',
            'description'   => 'Permiso para mostrar los pre-registros de alumnos'
        ],
        [
            'name'          => 'inscribir_a_otros_grupos',
            'display_name'  => 'Inscribir a alumnos a otros grupos',
            'description'   => 'Inscribir alumnos a otros grupos'
        ],
        [
            'name'          => 'gestionar_configuraciones',
            'display_name'  => 'Gestionar configuraciones',
            'description'   => 'Actualizar y modificar las configuraciones globales del sistema'
        ],
        [
            'name'          => 'convertir_no_fiscales_a_fiscales',
            'display_name'  => 'Convertir ventas no fiscales a fiscales',
            'description'   => 'Permite convertir las ventas no fiscales a fiscales de acuerdo al porcentaje de la configuracion'
        ],

        # PRODUCTOS
        [
            'name'          => 'listar_productos',
            'display_name'  => 'Listar Productos',
            'description'   => 'Permite Mostrar los productos'
        ],
        [
            'name'          => 'crear_producto',
            'display_name'  => 'Crear producto',
            'description'   => 'Permite crear un producto'
        ],
        [
            'name'          => 'editar_producto',
            'display_name'  => 'Editar Producto',
            'description'   => 'Permite editar un producto'
        ],
        [
            'name'          => 'eliminar_producto',
            'display_name'  => 'Eliminar producto',
            'description'   => 'Permite eliminar un producto'
        ],
        [
            'name'          => 'gestionar_compras',
            'display_name'  => 'Gestionar compras',
            'description'   => 'Permite gestionar compras'
        ],
        [
            'name'          => 'punto_de_venta_productos',
            'display_name'  => 'Entrar al punto de venta de productos',
            'description'   => 'Permite generar ventas en el punto de ventas de productos'
        ],
        [
            'name'          => 'registrar_asistencias',
            'display_name'  => 'Registrar asistencias',
            'description'   => 'Permite registrar asistencias de los alumnos'
        ],

        # ASESORIAS
        [
            'name'          => 'impartir_asesorias',
            'display_name'  => 'Impartir asesorias',
            'description'   => 'Permite Impartir asesorias'
        ],

        [
            'name'          => 'ver_horarios_profesores',
            'display_name'  => 'Ver horario de profesores',
            'description'   => 'Permite mostrar los horarios de profesores'
        ],

        [
            'name'          => 'entrar_calendario',
            'display_name'  => 'Entrar al calendario',
            'description'   => 'Permite entrar al calendario'
        ],

        [
            'name'          => 'agendar_asesoria',
            'display_name'  => 'Agendar asesoria',
            'description'   => 'Permite agendar una asesoria'
        ],

        [
            'name'          => 'editar_asesoria',
            'display_name'  => 'Editar Asesorias',
            'description'   => 'Permite editar una asesoria'
        ],

        [
            'name'          => 'cancelar_asesorias',
            'display_name'  => 'Cancelar asesorias',
            'description'   => 'Permite cancelar una asesoria'
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

    'especialidades' => [
        [
            'nombre'             => 'Ingles',
            'id_sucursal'        => 1,
            'descripcion'        => null,
            'precio_inscripcion' => null,
            'precio_mensualidad' => null,
            'precio_mensualidad_pronto_pago' => null,
            'precio_semanal'     => null,
        ],
        [
            'nombre'             => 'Computacion',
            'id_sucursal'        => 1,
            'descripcion'        => null,
            'precio_inscripcion' => null,
            'precio_mensualidad' => null,
            'precio_mensualidad_pronto_pago' => null,
            'precio_semanal'     => null,
        ],
        [
            'nombre'             => 'Computacion 2021',
            'id_sucursal'        => 1,
            'descripcion'        => null,
            'precio_inscripcion' => null,
            'precio_mensualidad' => null,
            'precio_mensualidad_pronto_pago' => null,
            'precio_semanal'     => null,
        ],
        [
            'nombre'             => 'Prepa Abierta',
            'id_sucursal'        => 1,
            'descripcion'        => null,
            'precio_inscripcion' => null,
            'precio_mensualidad' => null,
            'precio_mensualidad_pronto_pago' => null,
            'precio_semanal'     => null,
        ],
        [
            'nombre'             => 'Prepa Abierta',
            'id_sucursal'        => 1,
            'descripcion'        => null,
            'precio_inscripcion' => null,
            'precio_mensualidad' => null,
            'precio_mensualidad_pronto_pago' => null,
            'precio_semanal'     => null,
        ],
        [
            'nombre'             => 'Prepa escoloarizada',
            'id_sucursal'        => 1,
            'descripcion'        => null,
            'precio_inscripcion' => null,
            'precio_mensualidad' => null,
            'precio_mensualidad_pronto_pago' => null,
            'precio_semanal'     => null,
        ],
        [
            'nombre'             => 'Otros',
            'id_sucursal'        => 1,
            'descripcion'        => null,
            'precio_inscripcion' => null,
            'precio_mensualidad' => null,
            'precio_mensualidad_pronto_pago' => null,
            'precio_semanal'     => null,
        ],
    ],
    # Configuraciones del sistema
    'configuraciones' => [
        [
            'nombre'          => 'mostrar_solo_fiscales',
            'descripcion'  => '¿Mostrar solo la información de ventas fiscales?',
            'valor'   => 'No',
        ],
        [
            'nombre'          => 'porcentaje_fiscal',
            'descripcion'  => 'Porcentaje de referencia para convertir ventas no fiscales en fiscales',
            'valor'   => '20',
        ],
    ],
];
