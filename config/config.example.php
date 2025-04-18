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

// 100 = 1 euro -> 30 => 0.30 euros
$stripe_price = 30;
$stripe_currency = 'eur';

$precio_de_subida = 1; // en creditos

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
    'price' => 26,
  ],
  [
    'days' => 7,
    'times' => 36,
    'price' => 20,
  ],
  [
    'days' => 7,
    'times' => 26,
    'price' => 15,
  ],
  [
    'days' => 7,
    'times' => 16,
    'price' => 10,
  ],
  [
    'days' => 7,
    'times' => 10,
    'price' => 7,
  ],
  [
    'days' => 7,
    'times' => 4,
    'price' => 4,
  ],
  [
    'days' => 30,
    'times' => 48,
    'price' => 62,
  ],
  [
    'days' => 30,
    'times' => 36,
    'price' => 48,
  ],
  [
    'days' => 30,
    'times' => 26,
    'price' => 36,
  ],
  [
    'days' => 30,
    'times' => 16,
    'price' => 24,
  ],
  [
    'days' => 30,
    'times' => 10,
    'price' => 18,
  ],
  [
    'days' => 30,
    'times' => 4,
    'price' => 9,
  ]
];

$subidasfast_price = 1;
$subidasfast = [
  [
    'minutes' => 15,
    'times' => 95,
  ],
  [
    'minutes' => 30,
    'times' => 47,
  ],
  [
    'minutes' => 60, // 1h
    'times' => 23,
  ],
  [
    'minutes' => 120, // 2h
    'times' => 11,
  ],
  [
    'minutes' => 240, // 4h
    'times' => 4,
  ],
];
