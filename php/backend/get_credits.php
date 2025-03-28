<?php

function getUserCredits($userId, $pdo) {
    try {
        // Preparar la consulta
        $stmt = $pdo->prepare("SELECT creditos FROM usuarios WHERE id_user = :userId");
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        $stmt->execute();

        // Obtener el resultado
        $result = $stmt->fetch();
        return $result ? $result['creditos'] : 0; // Retorna los créditos o 0 si no existe el usuario
    } catch (PDOException $e) {
        error_log("Error al obtener créditos: " . $e->getMessage());
        return 0;
    }
}
