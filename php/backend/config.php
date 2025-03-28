<?php
$dsn = 'mysql:host=mysql; dbname=db6h0aovgdau0i';
$username = 'ujjvh6bdg2gyn';
$password = '1~l3}1Ke2^d$';

try {
    $pdo = new PDO($dsn, $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Error al conectar a la base de datos: ' . $e->getMessage());
}
?>
