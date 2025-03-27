<?php

$stripe_secret_key = "sk_test_51R1bm9RoG8JTU539HOAaz4c31iPEzjKSszp5rAfY1OdX8AdZuPaTr3kUe4z3RK00hNz2UC8s7GqskNy9wZwsUT2H00KCODejQd";
$stripe_endpoint_secret = 'whsec_c09dd7c65867d60135b1a8ff3f11224b7f7837b022c5a080df5995b9702ff5ef';

$stripe_success_credits_url = 'http://localhost:8080/payment/creditos/success.php?session_id={CHECKOUT_SESSION_ID}';
$stripe_cancel_credits_url = 'http://localhost:8080/payment/creditos/cancel.html';

$stripe_success_anuncios_url = 'http://localhost:8080/payment/anuncios/success.php?session_id={CHECKOUT_SESSION_ID}';
$stripe_cancel_anuncios_url = 'http://localhost:8080/payment/anuncios/cancel.html';

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
    'price' => 25
  ],
  [
    'days' => 7,
    'times' => 36,
    'price' => 20
  ],
  [
    'days' => 7,
    'times' => 26,
    'price' => 15
  ],
  [
    'days' => 7,
    'times' => 16,
    'price' => 10
  ],
  [
    'days' => 7,
    'times' => 10,
    'price' => 5
  ],
  [
    'days' => 7,
    'times' => 4,
    'price' => 4
  ],
  [
    'days' => 30,
    'times' => 48,
    'price' => 62
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
    'price' => 15,
  ],
  [
    'days' => 30,
    'times' => 4,
    'price' => 9,
  ]
];
