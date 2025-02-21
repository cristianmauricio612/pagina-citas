<?php

// Opciones adicionales (si es necesario)
ini_set('session.gc_maxlifetime', 3600);
ini_set('session.cookie_lifetime', 3600);
session_start();

require 'config.php'; // Configuración de la base de datos

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
    $password = $_POST['password'];

    // Validar que el email y la contraseña no estén vacíos
    if (!$email || empty($password)) {
        echo json_encode(['success' => false, 'message' => 'Por favor, ingresa el correo electrónico y la contraseña.']);
        exit;
    }

    // Buscar usuario en la base de datos
    $stmt = $pdo->prepare("SELECT id_user, password, type FROM usuarios WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    // Verificar la contraseña
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id_user'];  // Establecer el ID de usuario en la sesión
        $_SESSION['user_email'] = $email;  // Guardamos el correo del usuario
        $_SESSION['user_type'] = $user['type'];  // Guardamos el tipo
        $_SESSION['logged_in'] = true;  // Marcar la sesión como iniciada
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Correo o contraseña incorrectos']);
    }
}
?>
