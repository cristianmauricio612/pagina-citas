<?php

require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../../config/config.php";

session_start();

if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_type']) || !isset($_SESSION['user_id'])) {
  http_response_code(401);
  echo "YOU MUST TO BE LOGGED IN";
  exit();
}

if (!isset($_POST['pack'])) {
  http_response_code(303);
  header('Location: /payment/creditos/');
  exit();
}

$user_id = $_SESSION['user_id'];
$user_email = $_SESSION['user_email'];
$user_role = $_SESSION['user_type'];

$pack = $prices_packs[$_POST['pack']] ?? null;
if (!$pack) {
  http_response_code(400);
  echo "BAD REQUEST";
  exit();
}
$credits = $pack['Credits'];


\Stripe\Stripe::setApiKey($stripe_secret_key);

$session = \Stripe\Checkout\Session::create([
  'mode' => 'payment',
  'payment_method_types' => ['card'],
  'line_items' => [
    [
      'quantity' => $credits,
      'price_data' => [
        'currency' => $stripe_currency,
        'unit_amount' => $stripe_price,
        'product_data' => [
          'name' => 'Créditos',
        ],
      ],
    ]
  ],
  'success_url' => $stripe_success_credits_url,
  'cancel_url' => $stripe_cancel_credits_url,
  'payment_intent_data' => [
    'metadata' => [
      'type' => 'credits',
      'id' => (int)$user_id,
      'email' => $user_email,
      'role' => $user_role,
      'amount' => $credits
    ]
  ]
]);

http_response_code(303);
header("Location: " . $session->url);

exit();
