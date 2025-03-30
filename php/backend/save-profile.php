<?php
require 'config.php'; // Configuración de la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_POST['usuario_id'];
    $nombre = $_POST['nombre'];
    $nacimiento = $_POST['nacimiento']; // Fecha de nacimiento
    $sexo = $_POST['sexo'];
    $pais = $_POST['pais'];
    $bandera = $_POST['bandera'];
    $categoria = $_POST['categoria'];
    $indicativo = $_POST['indicativo'];
    $telefono = $_POST['telefono'];
    $whatsapp = isset($_POST['whatsapp']) ? 1 : 0;
    $fotografia = $_POST['fotografia'];

    // Validar edad mínima de 18 años
    $fechaNacimiento = new DateTime($nacimiento);
    $hoy = new DateTime();
    $edad = $hoy->diff($fechaNacimiento)->y; // Calcula la diferencia en años

    if ($edad < 18) {
        echo json_encode(['success' => false, 'error' => 'Debes ser mayor de edad.']);
        exit; // Terminar ejecución para no seguir con la inserción
    }

    // Insertar en la base de datos si la edad es válida
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
        'fotografia' => $fotografia,
    ]);

    echo json_encode(['success' => true]);
}
?>
