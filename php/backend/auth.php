<?php
// auth.php - Archivo para autenticar usuarios en cada página

session_start(); // Inicia la sesión

// Regenerar la ID de sesión periódicamente para evitar ataques de fijación de sesión
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id(true); // Regenera el ID de sesión
    $_SESSION['initiated'] = true;
}

// Verificar si el usuario está autenticado
if (!isset($_SESSION['user_id']) || !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Si no está autenticado, redirigir al login
    header("Location: /auth/login.php");
    exit; // Asegurarse de detener la ejecución del script
}

// Protección contra ataques CSRF
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32)); // Generar un token CSRF único
}

// Validar la IP del usuario si es necesario (opcional pero recomendable)
if (!isset($_SESSION['user_ip'])) {
    $_SESSION['user_ip'] = $_SERVER['REMOTE_ADDR']; // Guardar la IP al iniciar sesión
} elseif ($_SESSION['user_ip'] !== $_SERVER['REMOTE_ADDR']) {
    // Si la IP cambia, finalizar la sesión
    session_unset();
    session_destroy();
    header("Location: /auth/login.php");
    exit;
}

// Validar el User-Agent del navegador si es necesario
if (!isset($_SESSION['user_agent'])) {
    $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT']; // Guardar el agente de usuario
} elseif ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
    // Si el User-Agent cambia, finalizar la sesión
    session_unset();
    session_destroy();
    header("Location: /auth/login.php");
    exit;
}

// Validación adicional: limitar la duración de la sesión
$max_session_time = 3600; // Tiempo máximo de sesión en segundos (1 hora)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > $max_session_time)) {
    // Si la sesión expira, destruirla
    session_unset();
    session_destroy();
    header("Location: /auth/login.php");
    exit;
}
$_SESSION['last_activity'] = time(); // Actualizar la última actividad

// Usuario autenticado
?>
