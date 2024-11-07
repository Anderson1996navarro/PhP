<?php
// Iniciar la sesión
session_start();

// Configuración de conexión a la base de datos
$serverName = "TR-HF6GMJ3"; // Cambia esto si tu servidor no es localhost
$connectionOptions = array(
    "Database" => "cliente", // Nombre de tu base de datos
    "Uid" => "alessandra", // Usuario de la base de datos
    "PWD" => "Ale@254725" // Contraseña de la base de datos
);

// Conexión a la base de datos
$conn = sqlsrv_connect($serverName, $connectionOptions);

// Validación del formulario de login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action'])) {
    $action = $_POST['action'];

    // Si la acción es login
    if ($action == 'login') {
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Validar dominio de correo
        if (strpos($email, '@thomsonreuters.com') === false) {
            $error = "No pertenece a Thomson Reuters.";
        } else {
            // Consulta a la base de datos para obtener usuario y contraseña
            $sql = "SELECT * FROM usuarios WHERE email = ?";
            $stmt = sqlsrv_query($conn, $sql, array($email));

            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            }

            $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                header("Location: success.php");
                exit();
            } else {
                $error = "Correo o contraseña incorrectos.";
            }
        }
    }
    
    // Si la acción es resetear la contraseña
    if ($action == 'reset_password') {
        $email = $_POST['email'];

        // Validar dominio de correo
        if (strpos($email, '@thomsonreuters.com') === false) {
            $error = "No pertenece a Thomson Reuters.";
        } else {
            $sql = "SELECT * FROM usuarios WHERE email = ?";
            $stmt = sqlsrv_query($conn, $sql, array($email));

            if ($stmt === false) {
                die(print_r(sqlsrv_errors(), true));
            }

            $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            if ($user) {
                // Aquí podrías enviar un correo para restablecer la contraseña
                $success = "Te hemos enviado un correo con instrucciones para restablecer tu contraseña.";
            } else {
                $error = "El correo no está registrado.";
            }
        }
    }
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Thomson Reuters</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            text-align: center;
            padding: 50px;
        }
        .container {
            width: 400px;
            margin: 0 auto;
            background-color: white;
            padding: 30px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }
        h2 {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input[type="email"], input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 4px;
            border: 1px solid #ccc;
        }
        button {
            width: 100%;
            padding: 10px;
            background-color: #0078d4;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background-color: #005a8a;
        }
        p {
            margin-top: 20px;
            font-size: 14px;
        }
        .error {
            color: red;
        }
        .success {
            color: green;
        }
    </style>
</head>
<body>

<?php
if (isset($error)) {
    echo "<p class='error'>$error</p>";
}

if (isset($success)) {
    echo "<p class='success'>$success</p>";
}
?>

<div class="container">
    <h2>Iniciar sesión</h2>
    <form method="POST" action="">
        <input type="hidden" name="action" value="login">
        <label for="email">Correo electrónico:</label>
        <input type="email" id="email" name="email" required placeholder="Correo @thomsonreuters.com">
        <label for="password">Contraseña:</label>
        <input type="password" id="password" name="password" required placeholder="Tu contraseña">
        <button type="submit">Iniciar sesión</button>
    </form>
    <p><a href="#" onclick="document.getElementById('reset-form').style.display='block';">¿Olvidaste tu contraseña?</a></p>
</div>

<div class="container" id="reset-form" style="display:none;">
    <h2>Restablecer contraseña</h2>
    <form method="POST" action="">
        <input type="hidden" name="action" value="reset_password">
        <label for="reset_email">Correo electrónico:</label>
        <input type="email" id="reset_email" name="email" required placeholder="Correo @thomsonreuters.com">
        <button type="submit">Enviar instrucciones</button>
    </form>
    <p><a href="#" onclick="document.getElementById('reset-form').style.display='none';">Cancelar</a></p>
</div>

</body>
</html>
