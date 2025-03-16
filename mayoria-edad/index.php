<?php
session_start();

// Si el usuario envía el formulario, establecer la cookie y redirigir
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    setcookie('mayoria_edad', 'aceptado', time() + (86400 * 30), "/"); // Cookie válida por 30 días
    header("Location: /"); // Redirigir al index principal
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Anuncio de Mayoria de edad • FantaSexAnuncios.com</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="mayoria-edad.css">
</head>
<body class="body-mayoria-edad">
    <div class="form-container">
        <div class="text-center mb-4">
            <img src="/assets/img/logos/logo.png" alt="Logo" class="rounded-circle" width="100">
            <h4>Fanta<b style="color: #c60024;">SexAnuncios</b>.com</h4>
            <h2>¡Atención! sitio con contenido para adultos</h2>
        </div>
        <div class="mb-3">
            <label class="form-label" style="font-weight: bold; font-size: 18px">Para acceder debes aceptar las siguientes condiciones:</label>
        </div>
        <ul class="mb-3" style="font-size: 14px">
            <li class="form-label">Ser <b>mayor de edad</b> según la jurisdicción de tu país.</li>
            <li class="form-label">Permitir el uso de cookies propias y de terceros para tareas de análisis.</li>
            <li class="form-label">Nuestros textos de <a href="/privacidad/">avisos legales</a></li>
            <li class="form-label">Página de anuncios de contactos entre hombres y mujeres donde está <b>estrictamente prohibida</b> la publicidad que utilice estereotipos de género que fomenten o normalicen las violencias sexuales contra las mujeres, niñas, niños y adolescentes.</li>
        </u>
        <div class="buttons-container">
            <form method="POST">
                <button type="submit" class="auth-btn">Aceptar</button>
            </form>
            <button class="close-btn" onclick="window.location.href='https://www.google.com.pe/?hl=es'">Abandonar el sitio</button>
        </div>
    </div>
</body>
</html>