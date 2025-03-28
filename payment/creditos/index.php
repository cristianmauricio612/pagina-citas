<?php

require_once __DIR__ . '/../../config/config.php';

session_start();

if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_type']) || !isset($_SESSION['user_id'])) {
  http_response_code(303);
  header('Location: /auth/login.php');
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
  <title>Comprar</title>
</head>

<body>
  <form class="form-container" action="/payment/creditos/checkout.php" method="post">
    <h2>Comprar Creditos</h2>
    <div class="row">
      <label for="credits" class="form-label">Creditos</label>

      <div class="select">

        <!-- Radio button to select -->
        <?php foreach ($prices_packs as $key => $pack) : ?>
          <label class="radio-button">
            <input type="radio" name="pack" value="<?= $key ?>" required>
            <div class="option">
              <div class="dot"></div>
              <span><?= $pack['Name'] . ' €' . $pack['Price'] / 100 ?></span>
            </div>
          </label>
        <?php endforeach; ?>

      </div>
    </div>
    <button view-price class="submit-btn" type="submit">Pagar</button>
  </form>

  <?php include './../../footer/footer.php' ?>
</body>

</html>