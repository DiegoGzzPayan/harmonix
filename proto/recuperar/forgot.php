<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Harmonix - Recuperar Contraseña</title>

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
    --text-light: #ffffff;
    --accent-color: var(--pastel-purple);
    --secondary-accent: var(--pastel-blue);
    --input-bg: rgba(255, 255, 255, 0.8);
    --card-bg: rgba(255, 255, 255, 0.95);
    --shadow-light: rgba(0, 0, 0, 0.08);
    --shadow-medium: rgba(0, 0, 0, 0.2);
}

/* Fondo animado */
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

.register-container h2 {
    font-size: 2.2em;
    color: var(--accent-color);
    margin-bottom: 5px;
    font-weight: 700;
}

.register-container p {
    margin-bottom: 25px;
    color: #777;
    font-size: 0.95em;
}

/* Input */
.input-group {
    margin-bottom: 20px;
    text-align: left;
    position: relative;
}

.input-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color: var(--text-dark);
}

.input-group input {
    width: 86%;
    padding: 12px 15px 12px 45px;
    border: 2px solid rgba(0, 0, 0, 0.05);
    border-radius: 12px;
    background-color: var(--input-bg);
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
    letter-spacing: 0.5px;
}

.login-button:hover {
    transform: translateY(-4px);
    box-shadow: 0 15px 25px var(--shadow-medium);
}

/* Enlace de regreso */
.login-link {
    margin-top: 20px;
    font-size: 0.95em;
    color: #666;
}

.login-link a {
    color: var(--accent-color);
    text-decoration: none;
    font-weight: 600;
    transition: color 0.3s ease;
}

.login-link a:hover {
    color: var(--secondary-accent);
    text-decoration: underline;
}

/* Fondos animados */
.background-elements {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    pointer-events: none;
    z-index: -1;
}



/* Responsive */
@media (max-width: 500px) {
    .register-container {
        margin: 10px;
        padding: 25px;
    }
    .register-container h2 {
        font-size: 2em;
    }
    .input-group input {
        padding: 10px 10px 10px 40px;
    }
}
</style>
</head>
<body>



<div class="register-container">
    <h2>¿Olvidaste tu contraseña?</h2>
    <p>Ingresa tu correo y te enviaremos un enlace para restablecerla.</p>

    <form action="send_reset.php" method="post">
        <div class="input-group">
            <label for="email">Correo electrónico</label>
            <input type="email" id="email" name="email" placeholder="tucorreo@ejemplo.com" required>
        </div>
        <button type="submit" class="login-button">Enviar enlace</button>
    </form>

    <p class="login-link"><a href="../login.php">Volver al inicio de sesión</a></p>
</div>

</body>
</html>
