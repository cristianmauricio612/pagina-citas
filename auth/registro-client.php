<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrarse ( Cliente ) 💋 • FantaSexAnuncios.com 💋</title>
    <link rel="shortcut icon" href="/assets/img/logos/logo.png" type="image/x-icon">

    <!-- SEO CONFING -->
    <meta name="author" content="Cyco Design's">
    <meta name="description" content="Crea tu cuenta en FantaSexAnuncios.com y únete a la mejor plataforma de servicios de Escorts en la web. Encuentra las mejores putas en tu zona.">
    <meta property="og:site_name" content="FantaSexAnuncios.com">
    <meta property="og:title" content="Registrarse 💋 • FantaSexAnuncios.com 💋">
    <meta property="og:description" content="Crea tu cuenta en FantaSexAnuncios.com y únete a la mejor plataforma de servicios de Escorts en la web. Encuentra las mejores putas en tu zona.">
    <meta property="og:url" content="https://fantasexanuncios.com/">
    <meta property="og:image" content="https://fantasexanuncios.com/assets/img/fotos/background/register-client.webp">
    <meta property="og:type" content="website">
    <meta name="robots" content="index, follow">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles-auth.css">

</head>
<body class="body-register">
    <div class="form-container">
        <div class="text-center mb-4">
            <img src="/assets/img/logos/logo.png" alt="Logo" class="rounded-circle" width="100">
            <h4>Fanta<b style="color: #c60024;">SexAnuncios</b>.com</h4>
        </div>
        <h2>Registrarse</h2>
        <span class="badgetype">Cliente</span>
        <form id="register-form" method="POST">
            <input type="hidden" id="register-type" name="type" value="client">
            <div class="mb-3">
                <label for="register-email" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="register-email" required>
            </div>
            <div class="mb-3">
                <label for="register-password" class="form-label">Contraseña</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="register-password" required>
                    <span class="input-group-text" id="toggle-password"><i class="fas fa-eye"></i></span>
                </div>
            </div>
            <div class="mb-3">
                <label for="register-confirmpass" class="form-label">Confirme la Contraseña</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="register-confirmpass" required>
                    <span class="input-group-text" id="toggle-confirm-password"><i class="fas fa-eye"></i></span>
                </div>
            </div>
            <button type="submit" id="auth-btn" class="auth-btn">CONTINUAR</button>
            <div class="auth-feedback"></div>
        </form>
        <div class="text-center mt-3">
            <a href="login.php" class="text-decoration-none">¿Ya tienes cuenta? Inicia sesión</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        // Toggle password visibility
        $("#toggle-password").on("click", function() {
            const passwordField = $("#register-password");
            const type = passwordField.attr("type") === "password" ? "text" : "password";
            passwordField.attr("type", type);
            $(this).find("i").toggleClass("fa-eye fa-eye-slash");
        });

        // Toggle confirm password visibility
        $("#toggle-confirm-password").on("click", function() {
            const confirmPasswordField = $("#register-confirmpass");
            const type = confirmPasswordField.attr("type") === "password" ? "text" : "password";
            confirmPasswordField.attr("type", type);
            $(this).find("i").toggleClass("fa-eye fa-eye-slash");
        });

        $("#register-form").on("submit", function (e) {
            e.preventDefault();
            $("#auth-btn").prop("disabled", true);
            $(".auth-feedback").hide().removeClass("success error");

            $.post("../php/backend/register_action.php", {
                email: $("#register-email").val(),
                password: $("#register-password").val(),
                confirm_password: $("#register-confirmpass").val(),
                type: $("#register-type").val(),
            }, function (data) {
                console.log(data);  // Log para depurar la respuesta
                try {
                    const response = JSON.parse(data);
                    if (response.success) {
                        $(".auth-feedback").addClass("success").text("Creando cuenta...").fadeIn();
                        setTimeout(() => window.location.href = "login.php", 2000);
                    } else {
                        $(".auth-feedback").addClass("error").text(response.message).fadeIn();
                        $("#auth-btn").prop("disabled", false);
                    }
                } catch (e) {
                    $(".auth-feedback").addClass("error").text("Error al procesar la respuesta del servidor").fadeIn();
                    console.error(e);
                }
            });
        });
    </script>
</body>
</html>
