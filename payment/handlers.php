<?php

require_once __DIR__ . "/../config/config.php";

function handle_autosubidas($pdo, $metadata)
{
  // Get global $autosubidas
  global $autosubidas;

  /** {
    "type": "auto-uploads",
    "anuncio_id": "f0bd332b",
    "autosubidas_id": "0",
    "id": "6",
    "role": "advertiser",
    "email": "admin@gmail.com",
    "interval": "24.468085106383",
    "time_start": "9:0",
    "time_end": "14:50"
  }
   */

  $anuncio_id = $metadata->anuncio_id;
  $autosubidas_id = $metadata->autosubidas_id;
  $time_start = $metadata->time_start;
  $time_end = $metadata->time_end;
  $interval = $metadata->interval;

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
    au_type = 0,
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
    'start_hour' => $time_start,
    'end_hour' => $time_end,
    'anuncio_id' => $anuncio_id
  ]);
}

function handle_credits($pdo, $metadata)
{
  $email = $metadata->email;
  $credits = (int)$metadata->amount;

  $stmt = $pdo->prepare("SELECT id_user, creditos FROM usuarios WHERE email = :email");
  $stmt->execute(['email' => $email]);
  $usuario = $stmt->fetch(PDO::FETCH_ASSOC);


  if (!$usuario) {
    throw new Exception("Usuario no encontrado");
  }

  $nuevos_creditos = $usuario['creditos'] + $credits;
  $stmt = $pdo->prepare("UPDATE usuarios SET creditos = :creditos, ultima_actualizacion_creditos = NOW() WHERE id_user = :id_user");
  $stmt->execute(['creditos' => $nuevos_creditos, 'id_user' => $usuario['id_user']]);

  $stmt = $pdo->prepare("INSERT INTO log_creditos (usuario_id, cambio, motivo, fecha) VALUES (:usuario_id, :cambio, :motivo, NOW())");
  $stmt->execute(['usuario_id' => $usuario['id_user'], 'cambio' => $credits, 'motivo' => 'Compra de créditos']);
}
