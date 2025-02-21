<?php
require 'config.php'; // Configuración de la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_POST['usuario_id'];
    $nombre = $_POST['nombre'];
    $nacimiento = $_POST['nacimiento'];
    $sexo = $_POST['sexo'];
    $pais = $_POST['pais'];
    $bandera = $_POST['bandera'];
    $categoria = $_POST['categoria'];
    $indicativo = $_POST['indicativo'];
    $telefono = $_POST['telefono'];
    $whatsapp = isset($_POST['whatsapp']) ? 1 : 0;
    $fotografia = $_POST['fotografia'];

    $stmt = $pdo->prepare("
        INSERT INTO perfiles (
            usuario_id, nombre, nacimiento, sexo, pais, bandera, categoria,
            indicativo, telefono, whatsapp, fotografia, created_at, updated_at
        ) VALUES (
            :usuario_id, :nombre, :nacimiento, :sexo, :pais, :bandera, :categoria,
            :indicativo, :telefono, :whatsapp, :fotografia, NOW(), NOW()
        )
    ");
    $stmt->execute([
        'usuario_id' => $usuario_id,
        'nombre' => $nombre,
        'nacimiento' => $nacimiento,
        'sexo' => $sexo,
        'pais' => $pais,
        'bandera' => $bandera,
        'categoria' => $categoria,
        'indicativo' => $indicativo,
        'telefono' => $telefono,
        'whatsapp' => $whatsapp,
        'fotografia' => $fotografia
    ]);

    echo json_encode(['success' => true]);
}
?>
