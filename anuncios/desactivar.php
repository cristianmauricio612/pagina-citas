<?php

session_start();

require '../php/backend/config.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header("Location: /");
  exit();
}

if (!isset($_GET['id'])) {
  header("Location: /perfil-user/perfil.php");
  exit();
}

$sql = "SELECT * FROM anuncios WHERE anuncio_id = :id_anuncio";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id_anuncio' => $_GET['id']]);
$anuncio = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$anuncio) {
  header("Location: /perfil-user/perfil.php");
  exit();
}

if ($anuncio['usuario_id'] != $_SESSION['user_id']) {
  header("Location: /perfil-user/perfil.php");
  exit();
}

if ($anuncio['au_active'] == 0) {
  header("Location: /perfil-user/perfil.php");
  exit();
}

if ($anuncio['au_type'] == 0) {
  header("Location: /perfil-user/perfil.php");
  exit();
}

$sql = "UPDATE anuncios SET au_active = 0 WHERE anuncio_id = :id_anuncio";
$stmt = $pdo->prepare($sql);
$stmt->execute(['id_anuncio' => $_GET['id']]);

http_response_code(303);
header("Location: /perfil-user/perfil.php");
exit();
