<?php

session_start();

if ($_POST['payment_method'] === 'credits') {
  header('Location: /payment/anuncios/credits.php', true, 307);
} elseif ($_POST['payment_method'] === 'pay_now') {
  header('Location: /payment/anuncios/checkout.php', true, 307);
} else {
  http_response_code(303);
  header('Location: /payment');
}
