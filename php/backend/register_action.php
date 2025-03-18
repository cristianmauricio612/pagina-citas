<?php
session_start();
require 'config.php'; // Configuración de la base de datos

// Habilitar la visualización de errores para depurar
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Validación de datos recibidos
$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
$password = $_POST['password'];
$confirmPassword = $_POST['confirm_password']; // Confirmar contraseña
$type = $_POST['type'];

// Validación de los campos
if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Correo electrónico no válido']);
    exit;
}

if (strlen($password) < 8) {
    echo json_encode(['success' => false, 'message' => 'La contraseña debe tener al menos 8 caracteres']);
    exit;
}

if ($password !== $confirmPassword) {
    echo json_encode(['success' => false, 'message' => 'Las contraseñas no coinciden']);
    exit;
}

// Verificar que la contraseña sea segura (contenga al menos una mayúscula y un número)
if (!preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
    echo json_encode(['success' => false, 'message' => 'La contraseña debe contener una mayúscula y un número']);
    exit;
}

// Validar el tipo de usuario recibido (se asegura de que el 'type' sea válido)
if (!in_array($type, ['advertiser', 'client'])) {  // Ahora se aceptan 'advertiser' y 'client'
    echo json_encode(['success' => false, 'message' => 'Tipo de usuario no válido']);
    exit;
}

// Comprobar si el correo ya está registrado
$stmt = $pdo->prepare("SELECT id_user FROM usuarios WHERE email = :email");
$stmt->execute(['email' => $email]);
if ($stmt->fetch()) {
    echo json_encode(['success' => false, 'message' => 'El correo ya está registrado']);
    exit;
}

// Insertar el nuevo usuario
$hashedPassword = password_hash($password, PASSWORD_BCRYPT); // Hashear la contraseña para almacenarla de forma segura
$stmt = $pdo->prepare("INSERT INTO usuarios (email, password, type) VALUES (:email, :password, :type)");
$stmt->execute(['email' => $email, 'password' => $hashedPassword, 'type' => $type]);

// Regenerar ID de sesión para la seguridad
session_regenerate_id(true);

// Establecer información de sesión
$_SESSION['user_email'] = $email;  // Guardamos el correo del usuario
$_SESSION['user_type'] = $type;  // Guardamos el tipo de usuario
$_SESSION['logged_in'] = true;

// Redirigir al panel del usuario
echo json_encode(['success' => true, 'message' => 'Registro exitoso. Redirigiendo...']);
exit;
?>
