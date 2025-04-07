<?php

// Inicia sesión si no está activa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Incluimos la conexión a la base de datos
include '../../../config/db_connection.php';

// Variable para almacenar mensajes de error
$error = '';

// Procesar el formulario de registro
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];
    $confirmar_contrasena = $_POST['confirmar_contrasena'];

    // Valida que las contraseñas coincidan
    if ($contrasena !== $confirmar_contrasena) {
        $error = "Las contraseñas no coinciden.";
    } else {
        // Verifica si el correo ya está registrado
        $query_check = "SELECT correo FROM usuarios WHERE correo = ?";
        $stmt_check = $conn->prepare($query_check);
        $stmt_check->bind_param("s", $correo);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            // Si el correo ya existe, mostrara el siguiente error
            $error = "El correo electrónico ya está registrado. Intente con otro.";
        } else {
            // Si no existe, procedera con el registro
            $hashed_password = password_hash($contrasena, PASSWORD_DEFAULT);
            $query = "INSERT INTO usuarios (nombre, correo, contrasena) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sss", $nombre, $correo, $hashed_password);

            if ($stmt->execute()) {
                // Registro exitoso, redirige al login
                header('Location: login.php');
                exit();
            } else {
                $error = "Error al registrar el usuario.";
            }
        }
        $stmt_check->close();
    }
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - Planealo</title>
    <link rel="stylesheet" href="../../../Styles.css">
</head>
<body class="formularios">
<main>
        <div class="formularios-container">
            <h2 id="register-title">Registro</h2>
            <!-- Formulario de registro -->
            <form action="registro.php" method="POST" id="register-form">
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>
                <div class="form-group">
                    <label for="correo">Correo Electrónico:</label>
                    <input type="email" id="correo" name="correo" required>
                </div>
                <div class="form-group">
                    <label for="contrasena">Contraseña:</label>
                    <input type="password" id="contrasena" name="contrasena" required>
                </div>
                <div class="form-group">
                    <label for="confirmar_contrasena">Confirmar Contraseña:</label>
                    <input type="password" id="confirmar_contrasena" name="confirmar_contrasena" required>
                        <!-- Mensaje de error -->
                        <?php if (!empty($error)): ?>
                        <div class="error-message"><?php echo $error; ?></div>
                        <?php endif; ?>
                </div>
                <button type="submit" id="submit-button">Registrarse</button>
            </form>
            <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a>.</p>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>
</body>
</html>