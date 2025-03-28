<?php

require_once __DIR__ . '/../../config/config.php';

try {
    $pdo = new PDO($db_dsn, $db_username, $db_password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Error al conectar a la base de datos: ' . $e->getMessage());
}
