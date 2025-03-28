<?php

require_once __DIR__ . "/../vendor/autoload.php";
require_once __DIR__ . "/../php/backend/config.php";
require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/handlers.php";

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

  $metadata = $session->metadata;
  $hook_type = $metadata->type;

  try {
    // Inicia la transacción
    $pdo->beginTransaction();

    switch ($hook_type) {
      case 'credits':
        handle_credits($pdo, $metadata);
        break;
      case 'auto-uploads':
        handle_autosubidas($pdo, $metadata);
        break;
      default:
        http_response_code(400);
        exit();
    }

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
