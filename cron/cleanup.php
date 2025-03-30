<?php

if (php_sapi_name() != 'cli') {
  header('HTTP/1.0 404 Not Found', true, 404);
  exit();
}


require_once __DIR__ . '/../php/backend/config.php';

// expires_at only hidde anuncios
// expores_at + 20 DAYS remove it from database
// REMOVE NOW() > (20 DAYS + expires_at) FROM anuncios

try {
  $pdo->beginTransaction();

  $sql = "DELETE FROM anuncios WHERE expires_at + INTERVAL 20 DAY < NOW()";
  $stmt = $pdo->prepare($sql);
  $stmt->execute();

  $pdo->commit();
} catch (Exception $e) {
  echo $e->getMessage();
  $pdo->rollBack();
}
