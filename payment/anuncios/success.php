<?php

require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . '/../../config/config.php';

session_start();

\Stripe\Stripe::setApiKey($stripe_secret_key);

$session_id = $_GET['session_id'];

try {
  $session = \Stripe\Checkout\Session::retrieve($session_id);
  $line_items = \Stripe\Checkout\Session::allLineItems($session_id);
} catch (\Exception $e) {
  echo "Error al recuperar la sesión de pago: " . $e->getMessage();
  exit();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/assets/css/payment-style.css">
  <title>Compleado</title>
</head>

<body>

  <div class="form-container success-content">
    <h2>Comprar Exitosa</h2>

    <p class="success-state">
      Estado <?= $session->payment_status ? 'Pagado' : 'En Preceso' ?>
    </p>

    <span>
      <?= $line_items->data[0]->description ?>
    </span>

    <a href="/" class="submit-btn">
      <span>Ir al inicio</span>
    </a>
  </div>

  <?php include './../../footer/footer.php' ?>

</body>

</html>