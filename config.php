<?php

/**
 * Configuracion de conexion a base de datos.
 *
 * Escenario actual: sistema en tu PC, base de datos en cncm.edu.mx.
 * - Usa host cncm.edu.mx
 * - En cPanel habilita "Remote MySQL" y agrega tu IP publica
 *
 * Cuando subas el sistema al mismo servidor de CNCM, cambia host a localhost.
 */
return [
    'connection' => 'mysql',
    'host'       => 'localhost',
    'port'       => 3306,
    'database'   => 'cncmedum_legado',
    'username'   => 'cncmedum_tovar',
    'password'   => 'ZXCVqwer1234!"#$',
];
