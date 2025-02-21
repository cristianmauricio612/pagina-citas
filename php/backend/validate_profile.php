<?php
require 'config.php'; // Configuración de la base de datos
session_start(); 

$response = ['success' => true, 'redirect' => null, 'message' => ''];

// Verifica que el usuario esté logueado y que tenga el campo user_type en la sesión
if (isset($_SESSION['user_id']) && isset($_SESSION['user_type'])) {
    $userId = $_SESSION['user_id'];
    $userType = $_SESSION['user_type'];

    // Conexión a la base de datos
    $stmt = $pdo->prepare("SELECT id_profile FROM perfiles WHERE usuario_id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    $profile = $stmt->fetch();

    if (!$profile) {
        // Si no tiene perfil asociado, devolver información para la alerta
        $response['success'] = false;
        $response['message'] = 'Por favor, completa los datos de tu perfil para continuar.';
        $response['redirect'] = ($userType === 'advertiser') 
            ? '/perfil-advertiser/datos.php' 
            : '/perfil-client/datos.php';
    }
}

// Devuelve la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
exit();
?>
