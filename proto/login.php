<?php
session_start();
require 'conexion.php';

function mostrarMensaje($tipo, $mensaje) {
    echo "<div class='message-box $tipo'>$mensaje</div>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_usuario = trim($_POST["name"]);
    $contrasena = $_POST["password"];

    // Validación básica
    if (empty($nombre_usuario) || empty($contrasena)) {
        mostrarMensaje("error", "⚠️ Por favor completa todos los campos.");
        exit;
    }

    // Buscar usuario en la base de datos
    $stmt = $conn->prepare("SELECT usuario_id, contrasena_hash FROM usuarios WHERE nombre_usuario = ?");
    $stmt->bind_param("s", $nombre_usuario);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();

        // Verificar contraseña
        if (password_verify($contrasena, $usuario['contrasena_hash'])) {
            // ✅ Contraseña correcta → iniciar sesión
            $_SESSION['usuario_id'] = $usuario['usuario_id'];
            $_SESSION['nombre_usuario'] = $nombre_usuario;

            // Redirigir al panel o inicio
            header("Location: index.php");
            exit;
        } else {
            mostrarMensaje("error", "⚠️ Contraseña incorrecta.");
        }
    } else {
        mostrarMensaje("error", "⚠️ Usuario no encontrado.");
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Harmonix - Iniciar Sesión</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --pastel-blue: #7AB8DF;
            --pastel-purple: #A584DB;
            --pastel-green: #88D8C0;
            --pastel-pink: #FFB8C8;
            --pastel-yellow: #FFE083;
            --text-dark: #333333;
            --text-light: #ffffff;
            --accent-color: var(--pastel-purple); 
            --secondary-accent: var(--pastel-blue); 
            --input-bg: rgba(255, 255, 255, 0.85);
            --card-bg: rgba(255, 255, 255, 0.97);
            --shadow-light: rgba(0, 0, 0, 0.08); 
            --shadow-medium: rgba(0, 0, 0, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Play', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, var(--pastel-pink), var(--pastel-purple), var(--pastel-blue));
            background-size: 400% 400%;
            animation: gradientAnimation 15s ease infinite;
            color: var(--text-dark);
            overflow-x: hidden;
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .login-container {
            background: var(--card-bg);
            border-radius: 45px;
            box-shadow: 0 20px 50px var(--shadow-medium); 
            padding: 40px 45px;
            width: 100%;
            max-width: 440px;
            text-align: center;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            animation: fadeInScale 0.7s ease-out;
            position: relative;
        }

        @keyframes fadeInScale {
            from { 
                opacity: 0; 
                transform: scale(0.9) translateY(20px); 
            }
            to { 
                opacity: 1; 
                transform: scale(1) translateY(0); 
            }
        }

        .login-container h2 {
            font-size: 2.5em;
            color: var(--accent-color);
            margin-bottom: 8px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .login-container > p {
            margin-bottom: 30px;
            color: #666;
            font-size: 1em;
        }

        form {
            width: 100%;
        }

        .input-group {
            margin-bottom: 20px;
            position: relative;
            text-align: left;
        }

        .input-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 0.9em;
            font-weight: 600;
            color: var(--text-dark);
            transition: color 0.3s ease;
        }

        .input-group input {
            width: 100%;
            padding: 14px 15px 14px 48px;
            border: 2px solid rgba(0, 0, 0, 0.06);
            border-radius: 14px;
            background-color: var(--input-bg);
            font-size: 0.98em;
            color: var(--text-dark);
            transition: all 0.3s ease;
            font-family: 'Play', sans-serif;
        }

        .input-group input:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 4px rgba(165, 132, 219, 0.15);
            background-color: #ffffff;
            transform: translateY(-2px);
        }

        .input-group .icon {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.1em;
            color: #aaa;
            pointer-events: none;
            transition: color 0.3s ease;
        }

        .input-group input:focus ~ .icon {
            color: var(--accent-color);
        }

        .input-group input:focus ~ label {
            color: var(--accent-color);
        }

        .password-wrapper {
            position: relative;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #aaa;
            transition: color 0.3s ease;
            font-size: 1em;
        }

        .toggle-password:hover {
            color: var(--accent-color);
        }

        .forgot-password {
            text-align: right;
            margin-top: 8px;
            margin-bottom: 8px;
        }

        .forgot-password a {
            color: var(--accent-color);
            text-decoration: none;
            font-size: 0.88em;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .forgot-password a:hover {
            color: var(--secondary-accent);
            text-decoration: underline;
        }

        .login-button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(90deg, var(--pastel-green) 0%, var(--pastel-blue) 100%);
            color: var(--text-dark);
            border: none;
            border-radius: 14px;
            font-size: 1.1em;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 8px 20px var(--shadow-light);
            margin-top: 10px;
            letter-spacing: 0.5px;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            font-family: 'Play', sans-serif;
        }

        .login-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px var(--shadow-medium);
        }

        .login-button:active {
            transform: translateY(-1px);
        }

        .login-button i {
            transition: transform 0.3s ease;
        }

        .login-button:hover i {
            transform: translateX(4px);
        }

        .register-link {
            margin-top: 25px;
            font-size: 0.95em;
            color: #666;
        }

        .register-link a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .register-link a:hover {
            color: var(--secondary-accent);
            text-decoration: underline;
        }

        .message-box {
            position: fixed;
            bottom: 30px;
            left: 50%;
            transform: translateX(-50%);
            width: 90%;
            max-width: 450px;
            padding: 16px 24px;
            border-radius: 14px;
            text-align: center;
            font-size: 0.95em;
            font-weight: 600;
            z-index: 1000;
            animation: slideUp 0.4s ease-out;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateX(-50%) translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateX(-50%) translateY(0);
            }
        }

        .message-box.success {
            background: linear-gradient(135deg, var(--pastel-green), #6dd5a8);
            color: var(--text-dark);
        }

        .message-box.error {
            background: linear-gradient(135deg, #ff6b6b, #ff8787);
            color: white;
        }

        /* Decorative elements */
        .login-container::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, var(--pastel-pink), var(--pastel-purple), var(--pastel-blue));
            border-radius: 45px;
            z-index: -1;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .login-container:hover::before {
            opacity: 0.3;
        }

        @media (max-width: 500px) {
            .login-container {
                margin: 15px;
                padding: 30px 25px;
                max-width: 95%;
            }

            .login-container h2 {
                font-size: 2em;
            }

            .input-group input {
                padding: 12px 12px 12px 45px;
            }

            .message-box {
                width: 90%;
                font-size: 0.9em;
            }
        }

        @media (max-width: 350px) {
            .login-container {
                padding: 25px 20px;
            }

            .login-container h2 {
                font-size: 1.8em;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h2>¡Bienvenido a Harmonix!</h2>
        <p>Inicia sesión para continuar.</p>
        
        <form action="" method="POST" id="loginForm">
            <div class="input-group">
                <label for="name">Nombre de Usuario</label>
                <input type="text" id="name" name="name" placeholder="Tu nombre de usuario" required autocomplete="username">
                <i class="fas fa-user icon"></i>
            </div>
            
            <div class="input-group">
                <label for="password">Contraseña</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres" required autocomplete="current-password">
                    <i class="fas fa-lock icon"></i>
                    <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                </div>
                <div class="forgot-password">
                    <a href="recuperar/forgot.php">¿Olvidaste tu contraseña?</a>
                </div>
            </div>
            
            <button type="submit" class="login-button">
                <i class="fas fa-sign-in-alt"></i>
                <span>Iniciar sesión</span>
            </button>
        </form>
        
        <p class="register-link">¿No tienes una cuenta? <a href="registro.php">Regístrate aquí</a></p>
    </div>

    <script>
        // Toggle password visibility
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });

        // Form validation
        const loginForm = document.getElementById('loginForm');
        loginForm.addEventListener('submit', function(e) {
            const username = document.getElementById('name').value.trim();
            const password = passwordInput.value;

            if (username === '' || password === '') {
                e.preventDefault();
                alert('⚠️ Por favor completa todos los campos.');
                return false;
            }

            if (password.length < 8) {
                e.preventDefault();
                alert('⚠️ La contraseña debe tener al menos 8 caracteres.');
                return false;
            }
        });

        // Auto-hide message after 5 seconds
        const messageBox = document.querySelector('.message-box');
        if (messageBox) {
            setTimeout(() => {
                messageBox.style.animation = 'slideUp 0.4s ease-out reverse';
                setTimeout(() => {
                    messageBox.style.display = 'none';
                }, 400);
            }, 5000);
        }
    </script>
</body>
</html>