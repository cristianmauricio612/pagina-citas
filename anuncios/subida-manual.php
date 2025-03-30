<?php

session_start();

require_once __DIR__ . "/../config/config.php";
require_once __DIR__ . "/../php/backend/config.php";

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

$sql = "SELECT * FROM usuarios WHERE id_user = :id_usuario";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':id_usuario', $anuncio['usuario_id'], PDO::PARAM_INT);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$usuario) {
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
  <title>Subida Manual</title>
</head>

<body>
  <form action="/anuncios/_visibilidad.php">
    <div anuncio>
      <h1 header>Subida Manual</h1>

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
    <input type="hidden" name="hidden" value="<?= $anuncio['hidden']; ?>">

    <?php if ($usuario['creditos'] < $precio_de_subida) : ?>
      <div error>
        <span>Necesitas almenos <?= $precio_de_subida ?> créditos para activar la visibilidad.</span>
      </div>
      <span>Tienes <?= $usuario['creditos'] ?> créditos </span>
      <a href="/payment/creditos"> Obtener más créditos </a>
    <?php endif; ?>

    <a aria-hidden success href="/perfil-user/perfil.php">
      Verificar en el perfil
    </a>

    <button <?= $usuario['creditos'] < $precio_de_subida ? 'disabled' : ''; ?> type="submit">
      <?= "Subir por " . $precio_de_subida . " creditos"; ?>
    </button>
    <span error></span>
  </form>

  <script src="/sources/jquery-3.7.1/jquery-3.7.1.min.js"></script>
  <script>
    $(document).ready(function() {
      const button = $("button[type=\"submit\"]");
      button.click(function(event) {
        event.preventDefault();

        const form = $(this).closest("form");
        const id = form.find("input[name=\"id\"]").val();

        button.text("Cargando...");
        button.prop("disabled", true);
        $("span[error]").text("");
        $("span[error]").css("display", "none");

        $.ajax({
          url: "/anuncios/_subida-manual.php",
          type: "POST",
          data: {
            id: id,
          },
          success: function(response) {
            button.hide();
            $('[aria-hidden]').attr('aria-hidden', null);
          },
          error: function(xhr, status, error) {
            button.text("Reintentar");
            $("span[error]").text("Error al cambiar la visibilidad.").css("display", "block");
            $("span[error]").css("color", "red");
          },
          complete: function() {
            button.prop("disabled", false);
          }
        });
      });
    });
  </script>
</body>

</html>