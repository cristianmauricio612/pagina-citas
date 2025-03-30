<?php

session_start();

require_once __DIR__ . "/../php/backend/config.php";

if (!isset($_POST['id']) || !isset($_POST['hidden'])) {
  header("Location: /perfil-user/perfil.php");
  exit();
}

$sql = "SELECT * FROM anuncios WHERE anuncio_id = :id_anuncio";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id_anuncio', $_POST['id'], PDO::PARAM_INT);
$stmt->execute();
$anuncio = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$anuncio) {
  header("Location: /perfil-user/perfil.php");
  exit();
}

if ($_SESSION['user_id'] != $anuncio['usuario_id']) {
  header("Location: /perfil-user/perfil.php");
  exit();
}

try {
  if ($anuncio['hidden'] == 1) {
    $sql = "UPDATE anuncios SET hidden = 0 WHERE anuncio_id = :id_anuncio";
  } else {
    $sql = "UPDATE anuncios SET hidden = 1 WHERE anuncio_id = :id_anuncio";
  }
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['id_anuncio' => $_POST['id']]);

  header("Content-Type: application/json");
  $response = [
    'status' => 'success',
    'message' => 'Visibilidad actualizada correctamente.',
    'hidden' => $anuncio['hidden'] == 1 ? 0 : 1
  ];
  echo json_encode($response);
} catch (Exception $e) {
  http_response_code(500);
  header("Content-Type: application/json");
  $response = [
    'status' => 'error',
    'message' => 'Error al actualizar la visibilidad: ' . $e->getMessage()
  ];
  echo json_encode($response);
}
