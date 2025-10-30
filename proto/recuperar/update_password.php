<?php
require 'db.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Harmonix - Actualizar Contraseña</title>

<!-- Fuente -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet">

<style>
:root {
    --pastel-blue: #A9D1DF;
    --pastel-purple: #B6A2DB;
    --pastel-green: #D1E7D8;
    --pastel-pink: #F5E0E4;
    --pastel-yellow: #FBF6D9;

    --text-dark: #333333;
    --accent-color: var(--pastel-purple);
    --secondary-accent: var(--pastel-blue);
    --card-bg: rgba(255, 255, 255, 0.95);
    --shadow-medium: rgba(0, 0, 0, 0.2);
    --shadow-light: rgba(0, 0, 0, 0.08);
}

/* Fondo */
body {
    font-family: 'Play', sans-serif;
    margin: 0;
    padding: 0;
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    background: linear-gradient(135deg, var(--pastel-pink), var(--pastel-purple), var(--pastel-blue));
    background-size: 400% 400%;
    animation: gradientAnimation 15s ease infinite;
    color: var(--text-dark);
    overflow: hidden;
}

@keyframes gradientAnimation {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Contenedor */
.register-container {
    background: var(--card-bg);
    border-radius: 70px;
    box-shadow: 0 15px 40px var(--shadow-medium);
    padding: 25px 40px;
    width: 100%;
    max-width: 450px;
    text-align: center;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.4);
    animation: fadeInScale 0.7s ease-out;
}

@keyframes fadeInScale {
    from { opacity: 0; transform: scale(0.9); }
    to { opacity: 1; transform: scale(1); }
}

h2 {
    font-size: 2.2em;
    color: var(--accent-color);
    margin-bottom: 10px;
    font-weight: 700;
}

p {
    color: #555;
    font-size: 1em;
    line-height: 1.5;
}

/* Mensajes */
.message {
    background-color: var(--pastel-green);
    border-radius: 12px;
    padding: 15px;
    margin-bottom: 20px;
    font-weight: 600;
    box-shadow: 0 4px 12px var(--shadow-light);
}

.error {
    background-color: #f7b5b5;
}

/* Enlaces */
a {
    color: var(--accent-color);
    font-weight: 600;
    text-decoration: none;
    transition: color 0.3s ease;
}

a:hover {
    color: var(--secondary-accent);
    text-decoration: underline;
}

/* Responsive */
@media (max-width: 500px) {
    .register-container {
        margin: 10px;
        padding: 25px;
    }
    h2 { font-size: 1.8em; }
}
</style>
</head>
<body>

<div class="register-container">
    <h2>Actualizar contraseña</h2>

<?php
if (isset($_POST['token'], $_POST['password'])) {
    $token = $_POST['token'];
    $nueva_contrasena = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $conn->prepare("
        UPDATE usuarios 
        SET contrasena_hash = ?,  
            reset_token = NULL, 
            token_expiry = NULL 
        WHERE reset_token = ?
    ");
    $stmt->bind_param("ss", $nueva_contrasena, $token);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<div class='message'>
                <p>✅ Contraseña actualizada con éxito.</p>
                <p><a href='../login.php'>Iniciar sesión</a></p>
              </div>";
    } else {
        echo "<div class='message error'>
                <p>❌ Error: el token no es válido o ya fue usado.</p>
                <p><a href='forgot.php'>Solicitar nuevo enlace</a></p>
              </div>";
    }
} else {
    echo "<div class='message error'>
            <p>⚠️ Datos incompletos.</p>
            <p><a href='forgot.php'>Intentar nuevamente</a></p>
          </div>";
}
?>
</div>

</body>
</html>
