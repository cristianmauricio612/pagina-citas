<?php

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../php/backend/config.php";
require_once __DIR__ . "/config.php";

\Stripe\Stripe::setApiKey($stripe_secret_key);

// Captura el evento del webhook
$payload = @file_get_contents('php://input');
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'];

try {
  $event = \Stripe\Webhook::constructEvent(
    $payload,
    $sig_header,
    $stripe_endpoint_secret
  );
} catch (\UnexpectedValueException $e) {
  http_response_code(400);
  exit();
} catch (\Stripe\Exception\SignatureVerificationException $e) {
  http_response_code(400);
  exit();
}

if ($event->type == 'payment_intent.succeeded') {
  $session = $event->data->object;

  $email = $session->metadata->email;
  $credits = (int)$session->metadata->amount;

  try {
    // Inicia la transacción
    $pdo->beginTransaction();

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

    // Confirma la transacción
    $pdo->commit();
  } catch (Exception $e) {
    // En caso de error, revierte la transacción
    $pdo->rollBack();
    error_log($e->getMessage());
    http_response_code(500);
    exit();
  }
}

http_response_code(200);
