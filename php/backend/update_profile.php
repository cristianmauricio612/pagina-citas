<?php
require 'config.php'; // Configuración de la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_POST['usuario_id'];

    // Obtener los datos actuales del perfil
    $stmt = $pdo->prepare("SELECT * FROM perfiles WHERE usuario_id = :usuario_id");
    $stmt->execute(['usuario_id' => $usuario_id]);
    $perfil_actual = $stmt->fetch(PDO::FETCH_ASSOC);

    // Valores nuevos enviados desde el formulario
    $nuevos_datos = [
        'nombre' => $_POST['nombre'],
        'nacimiento' => $_POST['nacimiento'],
        'sexo' => $_POST['sexo'],
        'pais' => $_POST['pais'],
        'bandera' => $_POST['bandera'],
        'categoria' => $_POST['categoria'],
        'indicativo' => $_POST['indicativo'],
        'telefono' => $_POST['telefono'],
        'whatsapp' => isset($_POST['whatsapp']) ? 1 : 0,
        'fotografia' => $_POST['fotografia']
    ];

    // Construir la consulta dinámicamente solo con los campos que han cambiado
    $campos_a_actualizar = [];
    $valores = ['usuario_id' => $usuario_id];

    foreach ($nuevos_datos as $campo => $valor) {
        if ($perfil_actual[$campo] != $valor) { // Solo agregar si el valor cambió
            $campos_a_actualizar[] = "$campo = :$campo";
            $valores[$campo] = $valor;
        }
    }

    // Si no hay cambios, no ejecutar la consulta
    if (empty($campos_a_actualizar)) {
        echo json_encode(['success' => false, 'message' => 'No se realizaron cambios']);
        exit;
    }

    // Ejecutar la actualización solo con los campos modificados
    $sql = "UPDATE perfiles SET " . implode(', ', $campos_a_actualizar) . ", updated_at = NOW() WHERE usuario_id = :usuario_id";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($valores);

    echo json_encode(['success' => true, 'message' => 'Perfil actualizado']);
}
?>
