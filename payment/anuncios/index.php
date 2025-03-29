<?php
session_start();

require_once __DIR__ . '/../../php/backend/config.php';
require_once __DIR__ . '/../../config/config.php';

if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_type']) || !isset($_SESSION['user_id'])) {
  http_response_code(303);
  header('Location: /auth/login.php');
  exit();
}

if (!isset($_GET['id'])) {
  http_response_code(303);
  header('Location: /perfil-user/perfil.php');
  exit();
}

$sql = 'SELECT * FROM anuncios WHERE anuncio_id = :id';
$stmt = $pdo->prepare($sql);
$stmt->execute(['id' => $_GET['id']]);
$anuncio = $stmt->fetch();

if (!$anuncio) {
  http_response_code(303);
  header('Location: /perfil-user/perfil.php');
  exit();
}

if ($anuncio['au_active'] == 1) {
  http_response_code(303);
  header('Location: /perfil-user/anuncio.php?id=' . $anuncio['anuncio_id']);
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
  <script src="/sources/jquery-3.7.1/jquery-3.7.1.min.js"></script>
  <link rel="stylesheet" href="/assets/css/payment.css">
  <title>Comprar</title>
</head>

<body>
  <form class="form-container gap-2" action="/payment/anuncios/method.php" method="post">
    <h2>Subidas Automáticas</h2>

    <div anuncio-card class="mb-3 d-flex gap-2">
      <img src="<?= $anuncio['principal_picture']; ?>" class="img-fluid rounded" alt="Profile picture">
      <div>

        <span class="d-block">
          <?= $anuncio['titulo']; ?>
        </span>
        <span>
          <?= $anuncio['descripcion']; ?>
        </span>
      </div>
    </div>

    <input type="hidden" name="timezone_offset">
    <input type="hidden" name="anuncio_id" value="<?= $anuncio['anuncio_id']; ?>">
    <span container-title>
      Elige una de las opciones
    </span>
    <div class="row gap-4">
      <input type="hidden" name="autosubidas_id">
      <div class="option-container">
        <?php foreach ($autosubidas as $key => $item) : ?>
          <button btn-autosubidas btn-key="<?= $key ?>" type="button" class="option-button">
            <span info-days><?= $item['days'] ?> días</span>
            <span info-times><?= $item['times'] ?> veces al día</span>
            <span info-price><?= $item['price'] ?>€</span>
          </button>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="d-flex flex-column gap-4 p-4 border rounded-3">
      <span container-title>Horários de subidas automáticas</span>
      <div class="d-flex gap-2 flex-column flex-md-row">
        <div>
          <label>Hora inicio:</label>
          <input type="time" name="time_start" value="08:00">
        </div>
        <div>
          <label>Hora fin:</label>
          <input type="time" name="time_end" value="16:00">
        </div>
      </div>
      <div container>

      </div>
    </div>
    <div>
      <!-- Select Payment Method | radio button | credits and payment -->
      <span container-title>Selecciona un método de pago</span>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="payment_method" id="use_credits" value="credits" checked>
        <label class="form-check-label" for="use_credits">
          Usar créditos disponibles
        </label>
      </div>
      <div class="form-check">
        <input class="form-check-input" type="radio" name="payment_method" id="pay_now" value="pay_now">
        <label class="form-check-label" for="pay_now">
          Pagar ahora con tarjeta
        </label>
      </div>
    </div>
    <button view-price class="submit-btn" type="submit">Pagar</button>
  </form>

  <?php include './../../footer/footer.php' ?>

  <script>
    $(document).ready(function() {
      const options = <?= json_encode($autosubidas); ?>;

      const inputSubidas = $('[name="autosubidas_id"]');
      const inputTimeStart = $('[name="time_start"]');
      const inputTimeEnd = $('[name="time_end"]');
      const inputTimezoneOffset = $('[name="timezone_offset"]');
      const btns = $('[btn-autosubidas]');

      if (inputSubidas.length + inputTimeStart.length + inputTimeEnd.length + inputTimezoneOffset.length < 4)
        return;

      inputTimezoneOffset.val(new Date().getTimezoneOffset());

      btns.click(function() {
        const btn = $(this);
        activeButton(btn);
      });

      activeButton(btns.first());

      $('input[type="time"], button[btn-autosubidas]').on('click change', generateHours);

      generateHours();

      // * Functions

      function activeButton(btn) {
        inputSubidas.val(btn.attr('btn-key'));
        btns.toggleClass('option-button-active', false);
        btn.toggleClass('option-button-active', true);
      }

      function generateHours() {
        $('div[container]').empty();

        var start = inputTimeStart.val();
        var end = inputTimeEnd.val();

        const intervals = options[parseInt(inputSubidas.val())].times;

        if (!start || !end) {
          return;
        }

        function timeToMinutes(time) {
          var parts = time.split(':');
          return parseInt(parts[0]) * 60 + parseInt(parts[1]);
        }

        var startMinutes = timeToMinutes(start);
        var endMinutes = timeToMinutes(end);

        if (endMinutes <= startMinutes) {
          endMinutes += 24 * 60;
        }
        var totalMinutes = endMinutes - startMinutes;

        var intervalMinutes = totalMinutes / (intervals - 1);

        function minutesToTime(minutes) {
          var hours = Math.floor(minutes / 60) % 24;
          var mins = parseInt(minutes % 60);
          return (hours < 10 ? '0' + hours : hours) + ':' + (mins < 10 ? '0' + mins : mins);
        }

        function minutesToTimeAMPM(minutes) {
          var hours = Math.floor(minutes / 60) % 24;
          var mins = parseInt(minutes % 60);
          var ampm = hours >= 12 ? 'PM' : 'AM';
          hours = hours % 12;
          hours = hours ? hours : 12;
          return (hours < 10 ? '0' + hours : hours) + ':' + (mins < 10 ? '0' + mins : mins) + ' ' + ampm;
        }

        var hoursArray = [];

        for (var i = 0; i < intervals; i++) {
          if (i === intervals - 1) {
            hoursArray.push(minutesToTimeAMPM(endMinutes));
            break;
          }
          hoursArray.push(minutesToTimeAMPM(startMinutes + i * intervalMinutes));
        }

        hoursArray.forEach(function(hour) {
          $('div[container]').append('<div class="chip">' + hour + '</div>');
        });
      }
    });
  </script>
</body>

</html>