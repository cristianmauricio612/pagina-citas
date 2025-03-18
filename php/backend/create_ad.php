<?php
require_once __DIR__ . '/../../vendor/autoload.php'; // Ajusta la ruta si es necesario
require_once 'config.php';

use Gumlet\ImageResize;

header('Content-Type: application/json; charset=utf-8');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Configuración de directorio para almacenamiento de imágenes
$uploadDir = __DIR__ . '/../../uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Respuesta inicial
$response = ["success" => false, "message" => "No se pudo procesar la solicitud."];

// Validar sesión de usuario
session_start();
if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "No tienes permiso para realizar esta acción."]);
    exit;
}

// Función para generar un ID único
function generateUniqueAnuncioId($pdo) {
    do {
        $id = substr(md5(uniqid(rand(), true)), 0, 8);
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM anuncios WHERE anuncio_id = ?");
        $stmt->execute([$id]);
        $exists = $stmt->fetchColumn() > 0;
    } while ($exists);
    return $id;
}

function getPerfilIdByUserId($pdo, $userId) {
    $stmt = $pdo->prepare("SELECT id_profile FROM perfiles WHERE usuario_id = ?");
    $stmt->execute([$userId]);
    $result = $stmt->fetchColumn(); // Obtiene el primer valor de la primera fila
    return $result ?: null; // Devuelve null si no existe resultado
}

// Procesar imágenes y convertirlas a formato WebP
function processImage($file, $uploadDir, $prefix, $width = 800, $quality = 85) {
    $image = new ImageResize($file['tmp_name']);
    $image->resizeToWidth($width);

    // Asegúrate de que el $uploadDir termine con una barra
    $uploadDir = rtrim($uploadDir, '/') . '/';

    $uniqueName = uniqid($prefix, true) . '.webp';
    $path = $uploadDir . $uniqueName;

    // Guarda la imagen en formato WebP
    $image->save($path, IMAGETYPE_WEBP, $quality);

    // Convertir la ruta absoluta a relativa desde /uploads
    // Usar strpos para localizar la posición donde aparece '/uploads'
    $relativePath = substr($path, strpos($path, '/uploads'));

    return $relativePath; // Solo retorna la ruta relativa
}



try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Método no permitido. Solo POST es aceptado.");
    }

    // Validar campos requeridos
    $requiredFields = ['nombre', 'nacimiento', 'sexo', 'pais', 'categoria', 'titulo', 'descripcion'];
    foreach ($requiredFields as $field) {
        if (empty($_POST[$field])) {
            throw new Exception("El campo '$field' es obligatorio.");
        }
    }

    // Decodificar campos JSON
    $idiomas = json_decode($_POST['idiomas'], true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        throw new Exception("Error en el formato JSON de los idiomas.");
    }

    // Procesar imágenes subidas
    $profilePicturePath = null;
    $principalPicturePath = null;
    $additionalPictures = [];

    if (!empty($_FILES['profilePicture']['tmp_name'])) {
        $profilePicturePath = processImage($_FILES['profilePicture'], $uploadDir, 'profile_', 300);
    }
    
    if (!empty($_FILES['principalPicture']['tmp_name'])) {
        $principalPicturePath = processImage($_FILES['principalPicture'], $uploadDir, 'principal_', 800);
    }

    if (isset($_FILES['pictures'])) {
        foreach ($_FILES['pictures']['tmp_name'] as $key => $tmpName) {
            if (!empty($tmpName)) {
                $additionalPictures[] = processImage([
                    'tmp_name' => $tmpName
                ], $uploadDir, 'additional_', 800);
            }
        }
    }

    // Generar un ID único para el anuncio
    $anuncioId = generateUniqueAnuncioId($pdo);
    $perfilId = getPerfilIdByUserId($pdo, $_SESSION['user_id']);

    // Insertar en la base de datos
    $pdo->beginTransaction();
    $stmt = $pdo->prepare(
        "INSERT INTO anuncios (
            anuncio_id, visitas, id_profile, usuario_id, nombre, nacimiento, sexo, pais, bandera, categoria, indicativo, 
            telefono, whatsapp, tarifa, titulo, descripcion, idiomas, disponibilidad, horarios, servicios, ciudad, provincia, 
            latitude, longitude, locationtip, metodo, picture_profile, principal_picture, pictures, created_at
        ) VALUES (
            ?, 0, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, CURRENT_TIMESTAMP
        )"
    );
    
    $stmt->execute([
        $anuncioId,
        $perfilId,
        $_SESSION['user_id'],
        $_POST['nombre'],
        $_POST['nacimiento'],
        $_POST['sexo'],
        $_POST['pais'],
        $_POST['bandera'] ?? null,
        $_POST['categoria'],
        $_POST['indicativo'] ?? null,
        $_POST['telefono'] ?? null,
        isset($_POST['whatsapp']) ? 1 : 0,
        $_POST['tarifa'] ?? null,
        $_POST['titulo'],
        $_POST['descripcion'],
        json_encode($idiomas),
        json_encode(json_decode($_POST['disponibilidad'] ?? '{}', true)),
        json_encode(json_decode($_POST['horarios'] ?? '{}', true)),
        json_encode(json_decode($_POST['servicios'] ?? '{}', true)),
        $_POST['ciudad'] ?? null,
        $_POST['provincia'] ?? null,
        $_POST['latitude'] ?? null,
        $_POST['longitude'] ?? null,
        $_POST['locationtip'] ?? null,
        $_POST['metodo'] ?? null,
        $profilePicturePath,
        $principalPicturePath,
        json_encode($additionalPictures)
    ]);
    
    $pdo->commit();
    echo json_encode([
        "success" => true,
        "message" => "Anuncio creado exitosamente.",
        "anuncio_id" => $anuncioId,
    ]);


} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(400);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
