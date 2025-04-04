<?php

if (php_sapi_name() != 'cli') {
  header('HTTP/1.0 404 Not Found', true, 404);
  exit();
}

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../php/backend/config.php';

$sql = "SELECT
  anuncio_id,
  DATE_ADD(
    DATE_ADD(
      CONCAT(au_start_day, ' ', au_start_hour),
      INTERVAL FLOOR((au_current) / au_times) DAY
    ),
    INTERVAL (au_interval * ((au_current) % au_times) * 60) SECOND
  ) active_date,
  au_total,
  au_current,
  au_times,
  au_type,
  usuario_id
  FROM anuncios
  WHERE au_active = 1 AND au_current < au_total
  HAVING active_date <= NOW()";

$stmt = $pdo->query($sql);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// content type json
header('Content-Type: application/json');

try {
  $pdo->beginTransaction();

  echo json_encode($results);

  foreach ($results as $result) {
    $anuncio_date_activation = new DateTime($result['active_date']);
    $current_date = new DateTime();

    if ($result['au_type'] == 0) {
      if ($result['au_current'] + 1 >= $result['au_total']) {
        $sql = "UPDATE anuncios SET
        au_active = 0
        WHERE anuncio_id = :anuncio_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['anuncio_id' => $result['anuncio_id']]);
      } else {
        $sql = "UPDATE anuncios SET
        au_current = au_current + 1,
        activated_at = NOW(),
        expires_at = NOW() + INTERVAL 30 DAY
        WHERE anuncio_id = :anuncio_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['anuncio_id' => $result['anuncio_id']]);
      }
    } else {
      $sql = "SELECT * FROM usuarios WHERE id_user = :user_id";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(['user_id' => $result['usuario_id']]);
      $user = $stmt->fetch(PDO::FETCH_ASSOC);

      if (!$user) {
        throw new Exception('User not found');
      }

      if ($user['creditos'] < $subidasfast_price) {
        $sql = "UPDATE anuncios SET
        au_active = 0
        WHERE anuncio_id = :anuncio_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['anuncio_id' => $result['anuncio_id']]);
      } else {
        $sql = "UPDATE usuarios SET
        creditos = creditos - :creditos
        WHERE id_user = :user_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
          'creditos' => $subidasfast_price,
          'user_id' => $result['usuario_id']
        ]);

        $current = $result['au_current'] + 1;
        if ($current >= $result['au_times'] + 1) {
          $current = 0;
        }

        $sql = "UPDATE anuncios SET
        au_start_day = CASE WHEN au_current+1>=au_times THEN date(au_start_day) + interval 1 day ELSE au_start_day END,
        au_current = CASE WHEN au_current+1>=au_times THEN 0 ELSE au_current+1 END,
        activated_at = NOW(),
        expires_at = NOW() + INTERVAL 30 DAY
        WHERE anuncio_id = :anuncio_id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['anuncio_id' => $result['anuncio_id']]);
      }
      /* $sql = "UPDATE anuncios SET
      WHERE anuncio_id = :anuncio_id";
      $stmt = $pdo->prepare($sql);
      $stmt->execute(['anuncio_id' => $result['anuncio_id']]); */
    }
  }

  $pdo->commit();
} catch (Exception $e) {
  echo $e->getMessage();
  $pdo->rollBack();
}
