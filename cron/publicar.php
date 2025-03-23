<?php

// if (php_sapi_name() != 'cli') {
//   header('HTTP/1.0 404 Not Found', true, 404);
//   exit();
// }

require_once __DIR__ . '/../php/backend/config.php';
require_once __DIR__ . '/getanuncio.php';

/*
  User {
    id_user: int,
    email: string,
    password: string,
    type: string,
    created_at: string,
    creditos: int,
    ultima_actualizacion_creditos: string
  }
*/

// just execute and show $pdo SELECT * FROM usuarios

$usuarios = $pdo->query('SELECT * FROM anuncios')->fetchAll();
$keys = array_keys($usuarios[0]);
foreach ($usuarios as $usuario) {
  foreach ($keys as $key) {
    echo $key . ': ' . $usuario[$key] . '<br>';
  }
  break;
}


print_r($usuarios[0]);

foreach ($usuarios as $usuario) {
  echo 'Usuario: ' . $usuario['email'] . PHP_EOL;
}
