<?php

session_start();

require_once __DIR__ . "/../config/config.example.php";
require_once __DIR__ . "/../php/backend/config.php";

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
  header("Location: /");
  exit();
}

if (!isset($_GET['id'])) {
  header("Location: /perfil-user/perfil.php");
  exit();
}

$sql = "SELECT * FROM anuncios WHERE anuncio_id = :id_anuncio";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id_anuncio', $_GET['id'], PDO::PARAM_INT);
$stmt->execute();
$anuncio = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$anuncio) {
  header("Location: /perfil-user/perfil.php");
  exit();
}

if ($_SESSION['user_id'] != $anuncio['usuario_id']) {
  header("Location: /perfil-user/perfil.php");
  exit();
}

// [ "anuncio_id", "visitas", "id_profile", "usuario_id", "nombre", "nacimiento", "sexo", "pais", "bandera", "categoria", "indicativo", "telefono", "whatsapp", "principal_picture", "picture_profile", "pictures", "tarifa", "titulo", "descripcion", "idiomas", "disponibilidad", "horarios", "servicios", "ciudad", "provincia", "latitude", "longitude", "locationtip", "au_active", "au_start_day", "au_end_day", "au_days", "au_times", "au_interval", "au_total", "au_current", "au_start_hour", "au_end_hour", "activated_at", "expires_at", "created_at", "updated_at", "metodo", "indice", "hidden" ] 
?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="/assets/css/visibilidad.css">
  <title>Autosubidas</title>
</head>

<body>
  <form action="/anuncios/_visibilidad.php">
    <div anuncio>
      <h1 header>Configurar Autosubida</h1>

      <!-- Back to view anuncio -->
      <a href="perfil-user/perfil.php" class="back">
        <span>Volver al Perfil</span>
      </a>

      <div content>
        <img src="<?= htmlspecialchars($anuncio['principal_picture']); ?>" alt="Anuncio" />
        <div info>
          <h2><?= htmlspecialchars($anuncio['titulo']); ?></h2>
          <p><?= htmlspecialchars($anuncio['descripcion']); ?></p>
        </div>
      </div>
    </div>

    <div h-divisor></div>

    <input type="hidden" name="id" value="<?= htmlspecialchars($_GET['id']); ?>">

    <?php for ($i = 0; $i < count($subidasfast); $i++) : ?>
      <?php
      $text = "Cada " . $subidasfast[$i]['minutes'] . " minutos";
      if ($subidasfast[$i]['minutes'] >= 60) {
        $text = "Cada " . ($subidasfast[$i]['minutes'] / 60) . " horas";
      }
      ?>
      <label label-option>
        <input
          class="autosubida"
          type="radio"
          name="autosubida"
          id="autosubida<?= $i ?>"
          value="<?= $i ?>">
        <span>
          <?= $text ?>
        </span>
      </label>
    <?php endfor; ?>

    <div h-divisor></div>

    <button disabled type="submit">
      Activar Autosubida
    </button>

    <span warning-message>
      Cada autosubida cuesta <?= $subidasfast_price ?> créditos.
    </span>
    <span error></span>
  </form>

  <script src="/sources/jquery-3.7.1/jquery-3.7.1.min.js"></script>
  <script>
    $(document).ready(function() {
      $("input[name='autosubida']").change(function() {
        $("button[type='submit']").prop("disabled", false);
        $("span[error]").text("");
      });

      $("form").submit(function(event) {
        event.preventDefault();

        // Get the selected radio button value
        var selectedValue = $("input[name='autosubida']:checked").val();

        if (selectedValue === undefined) {
          $("span[error]").text("Por favor, selecciona una opción de autosubida.");
          return;
        }

        $("input[name='autosubida']").prop("disabled", true);
        $("button[type='submit']").prop("disabled", true);

        $.ajax({
          url: '/anuncios/_autosubida.php',
          type: 'POST',
          data: {
            id: parseInt(selectedValue),
            id_anuncio: "<?= htmlspecialchars($_GET['id']); ?>",
          },
          success: function(response) {
            var data = JSON.parse(response);
            console.log(data);
            setTimeout(function() {
              window.location.href = "/perfil-user/perfil.php";
            }, 1000);
          },
          error: function(xhr, status, error) {
            $("span[error]").text(JSON.parse(xhr.responseText).message);
          },
          complete: function() {
            $("input[name='autosubida']").prop("disabled", false);
            $("button[type='submit']").prop("disabled", false);
          },
        });
      })
    });
  </script>
</body>

</html>