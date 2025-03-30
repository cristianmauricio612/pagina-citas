<?php

// if (php_sapi_name() != 'cli') {
//   header('HTTP/1.0 404 Not Found', true, 404);
//   exit();
// }

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
  au_times
  FROM anuncios
  WHERE au_active = 1 AND au_current < au_total
  HAVING active_date <= NOW()";

$stmt = $pdo->query($sql);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// content type json
header('Content-Type: application/json');
echo json_encode($results);

try {
  $pdo->beginTransaction();

  foreach ($results as $result) {
    $anuncio_date_activation = new DateTime($result['active_date']);
    $current_date = new DateTime();

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
  }

  $pdo->commit();
} catch (Exception $e) {
  echo $e->getMessage();
  $pdo->rollBack();
}
