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

  $sql = "SELECT * FROM anuncios WHERE expires_at + INTERVAL 20 DAY < NOW()";
  $stmt = $pdo->prepare($sql);
  $stmt->execute();
  $anuncios = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo json_encode($anuncios);

  foreach ($anuncios as $anuncio) {
    $principal_picture = $anuncio['principal_picture'];
    $picture_profile = $anuncio['picture_profile'];
    $pictures = json_decode($anuncio['pictures'], true);

    // Remove the principal picture
    if (file_exists(__DIR__ . '/..' . $principal_picture)) {
      unlink(__DIR__ . '/..' . $principal_picture);
    }

    // Remove the profile picture
    if (file_exists(__DIR__ . '/..' . $picture_profile)) {
      unlink(__DIR__ . '/..' . $picture_profile);
    }

    // Remove additional pictures
    foreach ($pictures as $picture) {
      if (file_exists(__DIR__ . '/..' . $picture)) {
        unlink(__DIR__ . '/..' . $picture);
      }
    }
  }

  $sql = "DELETE FROM anuncios WHERE expires_at + INTERVAL 20 DAY < NOW()";
  $stmt = $pdo->prepare($sql);
  $stmt->execute();

  $pdo->commit();
} catch (Exception $e) {
  echo $e->getMessage();
  $pdo->rollBack();
}
