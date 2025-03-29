<?php

session_start();

require_once __DIR__  . "/../../php/backend/config.php";
require_once __DIR__  . "/_parse.php";

try {
  $pdo->beginTransaction();

  $sql = "SELECT * FROM usuarios WHERE id_user = :id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['id' => $_SESSION['user_id']]);
  $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$usuario) {
    throw new Exception("Usuario no encontrado");
  }

  if ($usuario['creditos'] < $autosubida['credits']) {
    throw new Exception("No tienes suficientes créditos");
  }

  $sql = "UPDATE usuarios SET creditos = creditos - :creditos WHERE id_user = :id";
  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    'creditos' => $autosubida['credits'],
    'id' => $_SESSION['user_id']
  ]);

  $anuncio_id = $_POST['anuncio_id'];
  $autosubidas_id = $_POST['autosubidas_id'];
  $time_start = $_POST['time_start'];
  $time_end = $_POST['time_end'];
  // timezone_offset
  $timezone_offset = $_POST['timezone_offset'];

  list($hour, $minutes) = explode(':', $time_start);

  $sql = 'SELECT * FROM anuncios WHERE anuncio_id = :id';
  $stmt = $pdo->prepare($sql);
  $stmt->execute(['id' => $anuncio_id]);
  $anuncio = $stmt->fetch();

  // Validate time start less greater than current time
  $stmt = $pdo->prepare("SELECT NOW() as now");
  $stmt->execute();
  $nowString = $stmt->fetch(PDO::FETCH_ASSOC)['now'];

  $now = new DateTime($nowString);
  $start = new DateTime($nowString);
  $start->setTime($hour, $minutes);

  if ($start < $now) {
    $fechaGenerada = new DateTime("tomorrow", new DateTimeZone('UTC'));
  } else {
    $fechaGenerada = new DateTime("today", new DateTimeZone('UTC'));
  }

  if (!$anuncio) {
    throw new Exception("Anuncio no encontrado");
  }

  $autosubida = $autosubidas[(int)$autosubidas_id] ?? null;
  if (!$autosubida) {
    throw new Exception("Autosubida no encontrada");
  }

  $fechaInicio = $fechaGenerada->format('Y-m-d');
  $fechaFin = $fechaGenerada->modify('+' . ($autosubida['days'] - 1) . ' days')->format('Y-m-d');

  $sql = "UPDATE anuncios SET
    au_active = 1,
    au_start_day = :start_day,
    au_end_day = :end_day,
    au_days = :days,
    au_times = :times,
    au_interval = :interval,
    au_total = :total,
    au_current = 0,
    au_start_hour = :start_hour,
    au_end_hour = :end_hour
    WHERE anuncio_id = :anuncio_id";

  $stmt = $pdo->prepare($sql);
  $stmt->execute([
    'start_day' => $fechaInicio,
    'end_day' => $fechaFin,
    'days' => $autosubida['days'],
    'times' => $autosubida['times'],
    'interval' => $interval,
    'total' => $autosubida['times'] * $autosubida['days'],
    'start_hour' => $start_hora . ':' . $start_minuto,
    'end_hour' => $end_hora . ':' . $end_minuto,
    'anuncio_id' => $anuncio_id
  ]);

  $pdo->commit();
  header("Location: /payment/anuncios/result.html?error_message=" . urlencode("Compra completa"), true, 303);
  exit;
} catch (Exception $e) {
  $pdo->rollBack();
  header("Location: /payment/anuncios/result.html?error_message=" . urlencode($e->getMessage()), true, 303);
  exit;
}
