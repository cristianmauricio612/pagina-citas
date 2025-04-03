<?php

session_start();

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../php/backend/config.php";
// _POST: id, id_anuncio

if (!isset($_POST['id']) || !isset($_POST['id_anuncio']) || !isset($_SESSION['user_id'])) {
  http_response_code(400);
  echo json_encode([
    'status' => 'error',
    'message' => 'Invalid request'
  ]);
  exit;
}

$sql = "SELECT * FROM usuarios WHERE id_user = :id";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
  http_response_code(404);
  echo json_encode([
    'status' => 'error',
    'message' => 'User not found'
  ]);
  exit;
}

if ($user['creditos'] <= 0) {
  http_response_code(400);
  echo json_encode([
    'status' => 'error',
    'message' => 'No tienes creditos suficientes'
  ]);
  exit;
}

$status = 500;
try {
  $pdo->beginTransaction();

  $sql = "SELECT * FROM anuncios WHERE anuncio_id = :id_anuncio";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['id_anuncio' => $_POST['id_anuncio']]);
  $anuncio = $stmt->fetch(PDO::FETCH_ASSOC);
  if (!$anuncio) {
    $status = 404;
    throw new Exception('Anuncio not found');
  }

  if ($user['id_user'] != $anuncio['usuario_id']) {
    $status = 403;
    throw new Exception('Unauthorized access');
  }

  if ($anuncio['au_active'] == 1 && $anuncio['au_type'] == 0) {
    $status = 400;
    throw new Exception('Autosubida already activated');
  }

  $index = (int)$_POST['id'];

  if ($index < 0 || $index >= count($subidasfast)) {
    $status = 400;
    throw new Exception('Invalid autosubida index');
  }

  $subidafast = $subidasfast[$index];

  $sql = "UPDATE anuncios SET
    au_active = 1,
    au_type = 1,
    au_start_day = date(now()),
    au_end_day = date(now()),
    au_days = 1,
    au_times = :times,
    au_interval = :interval,
    au_total = :times,
    au_current = 0,
    au_start_hour = time(now()),
    au_end_hour = time(now())
    WHERE anuncio_id = :anuncio_id";

  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    'times' => $subidafast['times'],
    'interval' => $subidafast['minutes'],
    'anuncio_id' => $_POST['id_anuncio']
  ]);

  echo json_encode([
    'status' => 'success',
    'message' => 'Autosubida activated successfully'
  ]);

  $pdo->commit();
} catch (Exception $e) {
  http_response_code($status);
  echo json_encode([
    'status' => 'error',
    'message' => $e->getMessage()
  ]);
  exit;
}
