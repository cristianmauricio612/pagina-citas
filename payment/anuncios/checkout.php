<?php

require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../../php/backend/config.php";
require_once __DIR__ . "/../../config/config.php";

session_start();

if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_type']) || !isset($_SESSION['user_id'])) {
  http_response_code(303);
  header('Location: /auth/login.php');
  exit();
}

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'];
$user_role = $_SESSION['user_type'];

if (!isset($_POST['anuncio_id']) || !isset($_POST['autosubidas_id']) || !isset($_POST['time_start']) || !isset($_POST['time_end']) || !isset($_POST['timezone_offset'])) {
  http_response_code(303);
  header('Location: /perfil-user/perfil.php');
  exit();
}

// Array ( [timezone_offset] => 300 [anuncio_id] => f0bd332b [autosubidas_id] => 0 [time_start] => 08:00 [time_end] => 16:00 )
$time_end = $_POST['time_end'];
$time_start = $_POST['time_start'];

if (!preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $time_start) || !preg_match('/^([01]?[0-9]|2[0-3]):[0-5][0-9](:[0-5][0-9])?$/', $time_end)) {
  http_response_code(303);
  header('Location: /payment/anuncios/?id=' . $_POST['anuncio_id']);
  exit();
}

// Array ( [days] => 7 [times] => 48 [price] => 25 )
$autosubida = $autosubidas[$_POST['autosubidas_id']] ?? null;
if (!$autosubida) {
  http_response_code(303);
  header('Location: /payment/anuncios/?id=' . $_POST['anuncio_id']);
  exit();
}

// Calcular el intervalo de tiempo entre cada subida
list($hours, $minutes) = explode(':', $time_start);
$ts = ($hours * 60) + $minutes;

list($hours, $minutes) = explode(':', $time_end);
$te = ($hours * 60) + $minutes;

$interval = ($te - $ts);
if ($interval < 0) {
  $interval += 24 * 60;
}
$interval = $interval / ($autosubida['times'] - 1);

$ts = $ts + $_POST['timezone_offset'];
$te = $te + $_POST['timezone_offset'];

if ($ts >= 24 * 60) {
  $ts = $ts - 24 * 60;
}
if ($te >= 24 * 60) {
  $te = $te - 24 * 60;
}

// Calcular la hora de inicio en UTC

$start_hora = round($ts / 60);
$start_minuto = round($ts % 60);

$end_hora = round($te / 60);
$end_minuto = round($te % 60);

$sql = 'SELECT * FROM anuncios WHERE anuncio_id = :id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $_POST['anuncio_id']]);
$anuncio = $stmt->fetch();

if ($anuncio['au_active'] == 1) {
  http_response_code(303);
  header('Location: /perfil-user/anuncio.php?id=' . $anuncio['anuncio_id']);
  exit();
}

if ($_SESSION['user_id'] !== $anuncio['usuario_id']) {
  http_response_code(401);
  echo "YOU ARE NOT ALLOWED TO DO THIS";
  exit();
}

\Stripe\Stripe::setApiKey($stripe_secret_key);

$session = \Stripe\Checkout\Session::create([
  'mode' => 'payment',
  'payment_method_types' => ['card'],
  'line_items' => [
    [
      'quantity' => 1,
      'price_data' => [
        'currency' => $stripe_currency,
        'unit_amount' => $autosubida['price'] * 100,
        'product_data' => [
          'name' => 'Subidas automáticas por ' . $autosubida['days'] . ' días y ' . $autosubida['times'] . ' veces al día para publicación con id ' . $_POST['anuncio_id'],
        ],
      ],
    ]
  ],
  'success_url' => $stripe_success_anuncios_url,
  'cancel_url' => $stripe_cancel_anuncios_url,
  'payment_intent_data' => [
    'metadata' => [
      'type' => 'auto-uploads',
      'id' => (int)$user_id,
      'email' => $user_email,
      'role' => $user_role,
      'anuncio_id' => $_POST['anuncio_id'],
      'autosubidas_id' => $_POST['autosubidas_id'],
      'interval' => $interval,
      'time_start' => $start_hora . ':' . $start_minuto,
      'time_end' => $end_hora . ':' . $end_minuto,
    ]
  ]
]);

http_response_code(303);
header("Location: " . $session->url);

exit();
