<?php

// Database Configuration

$db_dsn = 'mysql:host=localhost; dbname=db6h0aovgdau0i';
$db_username = 'ujjvh6bdg2gyn';
$db_password = '1~l3}1Ke2^d$';

// Payment Configuration

$stripe_secret_key = "sk_...";
$stripe_endpoint_secret = 'whsec_...';

$stripe_success_credits_url = 'https://fantasexanuncios.com/payment/creditos/success.php?session_id={CHECKOUT_SESSION_ID}';
$stripe_cancel_credits_url = 'https://fantasexanuncios.com/payment/creditos/cancel.html';

$stripe_success_anuncios_url = 'https://fantasexanuncios.com/payment/anuncios/success.php?session_id={CHECKOUT_SESSION_ID}';
$stripe_cancel_anuncios_url = 'https://fantasexanuncios.com/payment/anuncios/cancel.html';

$stripe_price = 30;
$stripe_currency = 'eur';

$prices_packs = [
  "basic" => [
    "Name" => "20 Creditos",
    "Credits" => 20,
    "Price" => 600
  ],
  "good" => [
    "Name" => "50 Creditos",
    "Credits" => 50,
    "Price" => 1500
  ],
  "premium" => [
    "Name" => "100 Creditos",
    "Credits" => 100,
    "Price" => 3000
  ],
  "pro" => [
    "Name" => "200 Creditos",
    "Credits" => 200,
    "Price" => 6000
  ],
];

$autosubidas = [
  [
    'days' => 7,
    'times' => 48,
    'price' => 25,
    'credits' => 50,
  ],
  [
    'days' => 7,
    'times' => 36,
    'price' => 20,
    'credits' => 50,
  ],
  [
    'days' => 7,
    'times' => 26,
    'price' => 15,
    'credits' => 50,
  ],
  [
    'days' => 7,
    'times' => 16,
    'price' => 10,
    'credits' => 50,
  ],
  [
    'days' => 7,
    'times' => 10,
    'price' => 5,
    'credits' => 50,
  ],
  [
    'days' => 7,
    'times' => 4,
    'price' => 4,
    'credits' => 50,
  ],
  [
    'days' => 30,
    'times' => 48,
    'price' => 62,
    'credits' => 50,
  ],
  [
    'days' => 30,
    'times' => 36,
    'price' => 48,
    'credits' => 50,
  ],
  [
    'days' => 30,
    'times' => 26,
    'price' => 36,
    'credits' => 50,
  ],
  [
    'days' => 30,
    'times' => 16,
    'price' => 24,
    'credits' => 50,
  ],
  [
    'days' => 30,
    'times' => 10,
    'price' => 15,
    'credits' => 50,
  ],
  [
    'days' => 30,
    'times' => 4,
    'price' => 9,
    'credits' => 50,
  ]
];
