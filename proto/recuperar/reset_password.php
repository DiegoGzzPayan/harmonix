<?php
require 'db.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Harmonix - Restablecer Contraseña</title>

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

/* Inputs */
.input-group {
    margin-bottom: 20px;
    text-align: left;
}

.input-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: var(--text-dark);
}

.input-group input {
    width: 70%;
    padding: 12px 15px;
    border: 2px solid rgba(0, 0, 0, 0.05);
    border-radius: 12px;
    background-color: rgba(255, 255, 255, 0.8);
    font-size: 0.95em;
    color: var(--text-dark);
    transition: all 0.3s ease;
}

.input-group input:focus {
    outline: none;
    border-color: var(--accent-color);
    box-shadow: 0 0 0 4px rgba(182, 162, 219, 0.2);
    background-color: #ffffff;
}

/* Botón */
.login-button {
    width: 100%;
    padding: 14px;
    background: linear-gradient(90deg, var(--pastel-green) 0%, var(--pastel-blue) 100%);
    color: var(--text-dark);
    border: none;
    border-radius: 12px;
    font-size: 1.1em;
    font-weight: 700;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 8px 15px var(--shadow-light);
    margin-top: 15px;
}

.login-button:hover {
    transform: translateY(-4px);
    box-shadow: 0 15px 25px var(--shadow-medium);
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
    <h2>Restablecer contraseña</h2>

<?php
if(isset($_GET['token'])){
    $token = $_GET['token'];

    $stmt = $conn->prepare("SELECT usuario_id, token_expiry FROM usuarios WHERE reset_token=?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if($row = $result->fetch_assoc()){
        if(strtotime($row['token_expiry']) > time()){
            // Formulario de nueva contraseña
            ?>
            <form action="update_password.php" method="post">
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
                <div class="input-group">
                    <label>Nueva contraseña:</label>
                    <input type="password" name="password" placeholder="Mínimo 8 caracteres" required>
                </div>
                <button type="submit" class="login-button">Actualizar</button>
            </form>
            <?php
        } else {
            echo "<div class='message error'>⚠️ El enlace ha expirado.</div>";
        }
    } else {
        echo "<div class='message error'>⚠️ Token inválido.</div>";
    }
} else {
    echo "<div class='message error'>⚠️ No se proporcionó ningún token.</div>";
}
?>

<p class="login-link" style="margin-top:20px;">
    <a href='forgot.php'>Volver a solicitar enlace</a> | 
    <a href='login.php'>Regresar al inicio de sesión</a>
</p>
</div>

</body>
</html>
