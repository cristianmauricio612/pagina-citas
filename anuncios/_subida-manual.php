<?php

session_start();

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../php/backend/config.php';

if (!isset($_POST['id'])) {
  http_response_code(400);
  header('Content-Type: application/json');
  echo json_encode(array(
    'status' => 'error',
    'message' => 'Missing required parameters'
  ));
  exit();
}

$sql = "SELECT * FROM anuncios WHERE anuncio_id = :id_anuncio";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id_anuncio', $_POST['id'], PDO::PARAM_INT);
$stmt->execute();
$anuncio = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$anuncio) {
  http_response_code(404);
  header('Content-Type: application/json');
  echo json_encode(array(
    'status' => 'error',
    'message' => 'Anuncio not found'
  ));
  exit();
}

if ($_SESSION['user_id'] != $anuncio['usuario_id']) {
  http_response_code(403);
  header('Content-Type: application/json');
  echo json_encode(array(
    'status' => 'error',
    'message' => 'Unauthorized access'
  ));
  exit();
}

$sql = "SELECT * FROM usuarios WHERE id_user = :id_usuario";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id_usuario', $anuncio['usuario_id'], PDO::PARAM_INT);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$usuario) {
  http_response_code(404);
  header('Content-Type: application/json');
  echo json_encode(array(
    'status' => 'error',
    'message' => 'User not found'
  ));
  exit();
}

if ($usuario['creditos'] < $precio_de_subida) {
  http_response_code(403);
  header('Content-Type: application/json');
  echo json_encode(array(
    'status' => 'error',
    'message' => 'Insufficient credits'
  ));
  exit();
}

try {
  $pdo->beginTransaction();

  $sql = "UPDATE anuncios SET
    activated_at = NOW(),
    expires_at = NOW() + INTERVAL 30 DAY,
    hidden = 0
    WHERE anuncio_id = :anuncio_id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['anuncio_id' => $_POST['id']]);

  $sql = "UPDATE usuarios SET
    creditos = creditos - $precio_de_subida
    WHERE id_user = :id_usuario";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['id_usuario' => $anuncio['usuario_id']]);

  $pdo->commit();
} catch (Exception $e) {
  echo $e->getMessage();
  $pdo->rollBack();
  http_response_code(500);
  header('Content-Type: application/json');
  echo json_encode(array(
    'status' => 'error',
    'message' => 'Invalid request method'
  ));
  exit();
}

http_response_code(200);
header('Content-Type: application/json');
echo json_encode(array(
  'status' => 'success',
  'message' => 'Anuncio activated successfully'
));
