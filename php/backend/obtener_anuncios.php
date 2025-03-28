<?php
try {
    // Consulta para obtener los datos del usuario
    $sql = "SELECT * FROM anuncios WHERE usuario_id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $usuario_id, PDO::PARAM_INT);
    $stmt->execute();

    $anuncios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error al obtener el perfil: " . $e->getMessage();
    exit;
}
?>