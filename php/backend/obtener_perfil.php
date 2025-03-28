<?php
// ID del usuario (esto debe ser dinámico, por ejemplo, desde sesión)
$usuario_id = $_SESSION['user_id']; // Aquí pon el ID del usuario autenticado
$usuario_type = $_SESSION['user_type'];

try {
    // Consulta para obtener los datos del usuario
    $sql = "SELECT * FROM perfiles WHERE usuario_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();

    // Verificar si hay resultados
    if ($stmt->rowCount() > 0) {
        $usuario = $stmt->fetch();
    } 
} catch (PDOException $e) {
    echo "Error al obtener el perfil: " . $e->getMessage();
    exit;
}
