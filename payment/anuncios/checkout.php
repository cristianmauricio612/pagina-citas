<?php

require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../../php/backend/config.php";
require_once __DIR__ . "/../../config/config.php";

session_start();

require_once __DIR__ . '/_parse.php';

\Stripe\Stripe::setApiKey($stripe_secret_key);

$session = \Stripe\Checkout\Session::create([
  'mode' => 'payment',
  'payment_method_types' => ['card', 'paypal'],
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
