<?php

require_once __DIR__ . "/../../vendor/autoload.php";
require_once __DIR__ . "/../../php/backend/config.php";
require_once __DIR__ . "/../../config/config.php";

session_start();

require_once __DIR__ . '/_parse.php';

$sql = "SELECT * FROM usuarios WHERE id_user = :user_id";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

?>


<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <script src="/sources/jquery-3.7.1/jquery-3.7.1.min.js"></script>
  <link rel="stylesheet" href="/assets/css/payment.css">
  <title>Comprar</title>
</head>

<body>
  <form class="form-container gap-2" action="/payment/anuncios/request-credits.php" method="post">
    <h2>Subidas Automáticas</h2>

    <input type="hidden" name="timezone_offset" value="<?= $_POST['timezone_offset']; ?>">
    <input type="hidden" name="anuncio_id" value="<?= $_POST['anuncio_id']; ?>">
    <input type="hidden" name="autosubidas_id" value="<?= $_POST['autosubidas_id']; ?>">
    <input type="hidden" name="time_start" value="<?= $_POST['time_start']; ?>">
    <input type="hidden" name="time_end" value="<?= $_POST['time_end']; ?>">

    <span class="text-center fw-bold fs-5">
      Precio: <?= $autosubida['credits']; ?> Creditos
    </span>

    <button view-price class="submit-btn" type="submit">Pagar</button>
  </form>

  <?php include './../../footer/footer.php' ?>

</body>

</html>