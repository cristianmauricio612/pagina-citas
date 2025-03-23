<?php

$stripe_secret_key = "sk_test_51R1bm9RoG8JTU539HOAaz4c31iPEzjKSszp5rAfY1OdX8AdZuPaTr3kUe4z3RK00hNz2UC8s7GqskNy9wZwsUT2H00KCODejQd";
$stripe_success_url = 'http://localhost/payment/success.php?session_id={CHECKOUT_SESSION_ID}';
$stripe_cancel_url = 'http://localhost/payment/cancel.html';
$stripe_endpoint_secret = 'whsec_c09dd7c65867d60135b1a8ff3f11224b7f7837b022c5a080df5995b9702ff5ef';

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
