<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión • FantaSexAnuncios.com</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="styles-auth.css">
</head>
<body class="body-login">
    <div class="form-container">
        <div class="text-center mb-4">
            <img src="/assets/img/logos/logo.png" alt="Logo" class="rounded-circle" width="100">
            <h4>Fanta<b style="color: #c60024;">SexAnuncios</b>.com</h4>
        </div>
        <h2>Iniciar sesión</h2>
        <form id="login-form" method="POST">
            <div class="mb-3">
                <label for="login-email" class="form-label">Correo Electrónico</label>
                <input type="email" class="form-control" id="login-email" required>
            </div>
            <div class="mb-3">
                <label for="login-password" class="form-label">Contraseña</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="login-password" required>
                    <span class="input-group-text" id="toggle-password"><i class="fas fa-eye"></i></span>
                </div>
            </div>
            <!-- Recordar Session -->
            <div class="form-check mb-3">
                <input type="checkbox" class="form-check-input" id="remember-me">
                <label class="form-check-label" for="remember-me">Recordar sesión</label>
            </div>
            <button type="submit" id="auth-btn" class="auth-btn">Ingresar</button>
            <div class="auth-feedback"></div>
        </form>
        <div class="text-center mt-3">
            <a href="registro.php" class="text-decoration-none">¿No tienes cuenta? Regístrate</a>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script>
        // Toggle password visibility
        $("#toggle-password").on("click", function() {
            const passwordField = $("#login-password");
            const type = passwordField.attr("type") === "password" ? "text" : "password";
            passwordField.attr("type", type);
            $(this).find("i").toggleClass("fa-eye fa-eye-slash");
        });

        $("#login-form").on("submit", function (e) {
            e.preventDefault();
            $("#auth-btn").prop("disabled", true);
            $(".auth-feedback").hide().removeClass("success error");

            $.post("../php/backend/login_action.php", {
                email: $("#login-email").val(),
                password: $("#login-password").val(),
                remember: $("#remember-me").is(":checked") ? 1 : 0
            }, function (data) {
                const response = JSON.parse(data);
                if (response.success) {
                    $(".auth-feedback").addClass("success").text("Ingresando...").fadeIn();
                    setTimeout(() => { window.location.replace("/"); }, 2000);

                } else {
                    $(".auth-feedback").addClass("error").text(response.message).fadeIn();
                    $("#auth-btn").prop("disabled", false);
                }
            });
        });
    </script>
</body>
</html>
