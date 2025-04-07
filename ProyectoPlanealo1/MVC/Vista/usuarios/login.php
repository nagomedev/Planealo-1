<?php

// Inicia sesión si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluimos la conexión a la base de datos
include '../../../config/db_connection.php';


// Procesar el formulario de inicio de sesión, se envia a traves de POST
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $correo = $_POST['correo'];
    $password = $_POST['contrasena'];

    // Verifica las credenciales del usuario
    $query = "SELECT id_usuario, nombre, correo, contrasena FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($query);

    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($password, $user['contrasena'])) {
            // Credenciales correctas, iniciar sesión
            $_SESSION['user_id'] = $user['id_usuario'];
            $_SESSION['user_name'] = $user['nombre'];
            $_SESSION['user_email'] = $user['correo'];

            // Redirigir al index
            header('Location: ../../../index.php');
            exit();
            //Mesajes de error
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "No se encontró una cuenta con ese correo electrónico.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Planealo</title>
    <link rel="stylesheet" href="../../../Styles.css">
</head>
<body class="formularios">
    <main>
        <div class="formularios-container">
            <h2 id="login-title">Iniciar Sesión</h2>
            <!-- Formulario para el inicio de sesión-->
            <form action="login.php" method="POST" id="login-form">
                <div class="form-group" id="correo-group">
                    <label for="correo" id="correo-label">Correo Electrónico:</label>
                    <input type="email" id="correo" name="correo" required>
                </div>
                <div class="form-group" id="password-group">
                    <label for="contrasena" id="password-label">Contraseña:</label>
                    <input type="password" id="contrasena" name="contrasena" required>
                    <!-- Mensaje de error -->
                    <?php if (!empty($error)): ?>
                    <div class="error-message"><?php echo $error; ?></div>
                    <?php endif; ?>
                </div>
                <button type="submit" id="submit-button">Iniciar Sesión</button>
            </form>
            <!-- Enlace para registrarse -->
            <p id="signup-link">¿No tienes una cuenta? <a href="registro.php" id="register-link">Regístrate aquí</a>.</p>
        </div>
    </main>
    <?php include '../includes/footer.php'; ?>
</body>
</html>