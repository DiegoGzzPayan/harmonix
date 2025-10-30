<?php
require 'conexion.php';

function mostrarMensaje($tipo, $mensaje) {
    echo "<div class='message-box $tipo'>$mensaje</div>";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre_usuario = trim($_POST["name"]);
    $correo = trim($_POST["email"]);
    $contrasena = $_POST["password"];
    $confirmar_contrasena = $_POST["confirm-password"];

    // Validación del dominio en el servidor
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || !preg_match('/@[^\s@]+\.[^\s@]+$/', $correo)) {
        mostrarMensaje("error", "⚠️ El correo debe tener un dominio válido.");
        exit;
    }

    if ($contrasena !== $confirmar_contrasena) {
        mostrarMensaje("error", "⚠️ Las contraseñas no coinciden.");
        exit;
    }

    if (strlen($contrasena) < 8) {
        mostrarMensaje("error", "⚠️ La contraseña debe tener al menos 8 caracteres.");
        exit;
    }

    if (strlen($nombre_usuario) < 3) {
        mostrarMensaje("error", "⚠️ El nombre de usuario debe tener al menos 3 caracteres.");
        exit;
    }

    $hash = password_hash($contrasena, PASSWORD_DEFAULT);

    // Verificar si el correo ya existe
    $stmt = $conn->prepare("SELECT usuario_id FROM usuarios WHERE correo_electronico = ?");
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        mostrarMensaje("error", "⚠️ El correo ya está registrado.");
        exit;
    }
    $stmt->close();

    // Verificar si el nombre de usuario ya existe
    $stmt = $conn->prepare("SELECT usuario_id FROM usuarios WHERE nombre_usuario = ?");
    $stmt->bind_param("s", $nombre_usuario);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        mostrarMensaje("error", "⚠️ El nombre de usuario ya está en uso.");
        exit;
    }
    $stmt->close();

    // Insertar nuevo usuario
    $stmt = $conn->prepare("INSERT INTO usuarios (nombre_usuario, contrasena_hash, correo_electronico) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $nombre_usuario, $hash, $correo);

    if ($stmt->execute()) {
        mostrarMensaje("success", "✅ Registro exitoso. ¡Ya puedes iniciar sesión!");
        echo "<script>setTimeout(() => { window.location.href = 'login.php'; }, 2000);</script>";
    } else {
        mostrarMensaje("error", "⚠️ Error al registrar: " . $stmt->error);
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
    <title>Harmonix - Registrarse</title>
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
            padding: 20px 0;
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

        .register-container {
            background: var(--card-bg);
            border-radius: 45px;
            box-shadow: 0 20px 50px var(--shadow-medium);
            padding: 40px 45px;
            width: 100%;
            max-width: 480px;
            text-align: center;
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid rgba(255, 255, 255, 0.6);
            animation: fadeInScale 0.7s ease-out;
            position: relative;
            margin: 20px;
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

        .register-container h2 {
            font-size: 2.5em;
            color: var(--accent-color);
            margin-bottom: 8px;
            font-weight: 700;
            letter-spacing: -0.5px;
        }

        .register-container > p {
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

        .input-group input.invalid {
            border-color: #ff6b6b;
            background-color: rgba(255, 107, 107, 0.05);
        }

        .input-group input.valid {
            border-color: var(--pastel-green);
            background-color: rgba(136, 216, 192, 0.05);
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
            z-index: 1;
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
            z-index: 2;
        }

        .toggle-password:hover {
            color: var(--accent-color);
        }

        .error-message {
            color: #ff6b6b;
            font-size: 0.85em;
            margin-top: 6px;
            display: none;
            animation: slideIn 0.3s ease;
        }

        .error-message.show {
            display: block;
        }

        .success-indicator {
            color: var(--pastel-green);
            font-size: 0.85em;
            margin-top: 6px;
            display: none;
            animation: slideIn 0.3s ease;
        }

        .success-indicator.show {
            display: block;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .password-strength {
            margin-top: 8px;
            height: 4px;
            background: rgba(0, 0, 0, 0.1);
            border-radius: 2px;
            overflow: hidden;
            display: none;
        }

        .password-strength.show {
            display: block;
        }

        .password-strength-bar {
            height: 100%;
            width: 0%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .strength-weak { 
            width: 33%; 
            background: #ff6b6b; 
        }

        .strength-medium { 
            width: 66%; 
            background: var(--pastel-yellow); 
        }

        .strength-strong { 
            width: 100%; 
            background: var(--pastel-green); 
        }

        .password-requirements {
            font-size: 0.8em;
            color: #666;
            margin-top: 6px;
            text-align: left;
        }

        .password-requirements ul {
            list-style: none;
            padding: 0;
            margin: 5px 0 0 0;
        }

        .password-requirements li {
            padding: 2px 0;
            transition: color 0.3s ease;
        }

        .password-requirements li.valid {
            color: var(--pastel-green);
        }

        .password-requirements li i {
            margin-right: 5px;
            font-size: 0.9em;
        }

        .register-button {
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

        .register-button:hover:not(:disabled) {
            transform: translateY(-3px);
            box-shadow: 0 15px 30px var(--shadow-medium);
        }

        .register-button:active:not(:disabled) {
            transform: translateY(-1px);
        }

        .register-button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .register-button i {
            transition: transform 0.3s ease;
        }

        .register-button:hover:not(:disabled) i {
            transform: translateX(4px);
        }

        .login-link {
            margin-top: 25px;
            font-size: 0.95em;
            color: #666;
        }

        .login-link a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .login-link a:hover {
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

        .register-container::before {
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

        .register-container:hover::before {
            opacity: 0.3;
        }

        @media (max-width: 550px) {
            .register-container {
                margin: 15px;
                padding: 30px 25px;
                max-width: 95%;
            }

            .register-container h2 {
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

        @media (max-width: 380px) {
            .register-container {
                padding: 25px 20px;
            }

            .register-container h2 {
                font-size: 1.8em;
            }
        }
    </style>
</head>
<body>
    <div class="register-container">
        <h2>¡Únete a Harmonix!</h2>
        <p>Crea tu cuenta en segundos para comenzar.</p>
        
        <form action="" method="POST" id="registerForm">
            <div class="input-group">
                <label for="name">Nombre de Usuario</label>
                <input type="text" id="name" name="name" placeholder="Tu nombre de usuario (no uses tu nombre real)" required autocomplete="username" minlength="3">
                <i class="fas fa-user icon"></i>
                <div class="error-message" id="nameError">El nombre debe tener al menos 3 caracteres</div>
                <div class="success-indicator" id="nameSuccess">✓ Nombre válido</div>
            </div>

            <div class="input-group">
                <label for="email">Correo Electrónico</label>
                <input type="email" id="email" name="email" placeholder="tu.correo@institucion.edu" required autocomplete="email">
                <i class="fas fa-envelope icon"></i>
                <div class="error-message" id="emailError">El correo debe tener un dominio válido (ej: @gmail.com)</div>
                <div class="success-indicator" id="emailSuccess">✓ Correo válido</div>
            </div>

            <div class="input-group">
                <label for="password">Contraseña</label>
                <div class="password-wrapper">
                    <input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres" required autocomplete="new-password" minlength="8">
                    <i class="fas fa-lock icon"></i>
                    <i class="fas fa-eye toggle-password" id="togglePassword"></i>
                </div>
                <div class="password-strength" id="passwordStrength">
                    <div class="password-strength-bar" id="strengthBar"></div>
                </div>
                <div class="password-requirements">
                    <ul id="passwordChecks">
                        <li id="check-length"><i class="fas fa-circle"></i> Mínimo 8 caracteres</li>
                        <li id="check-uppercase"><i class="fas fa-circle"></i> Una mayúscula</li>
                        <li id="check-number"><i class="fas fa-circle"></i> Un número</li>
                    </ul>
                </div>
            </div>

            <div class="input-group">
                <label for="confirm-password">Confirmar Contraseña</label>
                <div class="password-wrapper">
                    <input type="password" id="confirm-password" name="confirm-password" placeholder="Repite la contraseña" required autocomplete="new-password">
                    <i class="fas fa-check-circle icon"></i>
                    <i class="fas fa-eye toggle-password" id="toggleConfirmPassword"></i>
                </div>
                <div class="error-message" id="confirmError">Las contraseñas no coinciden</div>
                <div class="success-indicator" id="confirmSuccess">✓ Las contraseñas coinciden</div>
            </div>

            <button type="submit" class="register-button" id="submitBtn">
                <i class="fas fa-user-plus"></i>
                <span>Registrarse</span>
            </button>
        </form>
        
        <p class="login-link">¿Ya tienes una cuenta? <a href="login.php">Inicia Sesión</a></p>
    </div>

    <script>
        // Toggle password visibility
        function setupPasswordToggle(toggleId, inputId) {
            const toggle = document.getElementById(toggleId);
            const input = document.getElementById(inputId);
            
            toggle.addEventListener('click', function() {
                const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                input.setAttribute('type', type);
                this.classList.toggle('fa-eye');
                this.classList.toggle('fa-eye-slash');
            });
        }

        setupPasswordToggle('togglePassword', 'password');
        setupPasswordToggle('toggleConfirmPassword', 'confirm-password');

        // Email validation
        const emailInput = document.getElementById('email');
        const emailError = document.getElementById('emailError');
        const emailSuccess = document.getElementById('emailSuccess');

        function validarDominioEmail(email) {
            const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            return regex.test(email);
        }

        emailInput.addEventListener('blur', function() {
            if (this.value && !validarDominioEmail(this.value)) {
                this.classList.add('invalid');
                this.classList.remove('valid');
                emailError.classList.add('show');
                emailSuccess.classList.remove('show');
            } else if (this.value) {
                this.classList.add('valid');
                this.classList.remove('invalid');
                emailError.classList.remove('show');
                emailSuccess.classList.add('show');
            }
        });

        emailInput.addEventListener('input', function() {
            if (validarDominioEmail(this.value)) {
                this.classList.add('valid');
                this.classList.remove('invalid');
                emailError.classList.remove('show');
                emailSuccess.classList.add('show');
            }
        });

        // Username validation
        const nameInput = document.getElementById('name');
        const nameError = document.getElementById('nameError');
        const nameSuccess = document.getElementById('nameSuccess');

        nameInput.addEventListener('input', function() {
            if (this.value.length >= 3) {
                this.classList.add('valid');
                this.classList.remove('invalid');
                nameError.classList.remove('show');
                nameSuccess.classList.add('show');
            } else {
                this.classList.remove('valid');
                nameError.classList.remove('show');
                nameSuccess.classList.remove('show');
            }
        });

        nameInput.addEventListener('blur', function() {
            if (this.value.length > 0 && this.value.length < 3) {
                this.classList.add('invalid');
                nameError.classList.add('show');
                nameSuccess.classList.remove('show');
            }
        });

        // Password strength checker
        const passwordInput = document.getElementById('password');
        const strengthBar = document.getElementById('strengthBar');
        const passwordStrength = document.getElementById('passwordStrength');
        const checkLength = document.getElementById('check-length');
        const checkUppercase = document.getElementById('check-uppercase');
        const checkNumber = document.getElementById('check-number');

        passwordInput.addEventListener('input', function() {
            const password = this.value;
            let strength = 0;

            if (password.length > 0) {
                passwordStrength.classList.add('show');
            } else {
                passwordStrength.classList.remove('show');
            }

            // Check length
            if (password.length >= 8) {
                strength++;
                checkLength.classList.add('valid');
            } else {
                checkLength.classList.remove('valid');
            }

            // Check uppercase
            if (/[A-Z]/.test(password)) {
                strength++;
                checkUppercase.classList.add('valid');
            } else {
                checkUppercase.classList.remove('valid');
            }

            // Check number
            if (/[0-9]/.test(password)) {
                strength++;
                checkNumber.classList.add('valid');
            } else {
                checkNumber.classList.remove('valid');
            }

            // Update strength bar
            strengthBar.className = 'password-strength-bar';
            if (strength === 1) {
                strengthBar.classList.add('strength-weak');
            } else if (strength === 2) {
                strengthBar.classList.add('strength-medium');
            } else if (strength === 3) {
                strengthBar.classList.add('strength-strong');
                passwordInput.classList.add('valid');
                passwordInput.classList.remove('invalid');
            } else {
                passwordInput.classList.remove('valid');
            }
        });

        // Confirm password validation
        const confirmInput = document.getElementById('confirm-password');
        const confirmError = document.getElementById('confirmError');
        const confirmSuccess = document.getElementById('confirmSuccess');

        function validatePasswordMatch() {
            if (confirmInput.value && passwordInput.value) {
                if (confirmInput.value === passwordInput.value) {
                    confirmInput.classList.add('valid');
                    confirmInput.classList.remove('invalid');
                    confirmError.classList.remove('show');
                    confirmSuccess.classList.add('show');
                    return true;
                } else {
                    confirmInput.classList.add('invalid');
                    confirmInput.classList.remove('valid');
                    confirmError.classList.add('show');
                    confirmSuccess.classList.remove('show');
                    return false;
                }
            }
            return false;
        }

        confirmInput.addEventListener('input', validatePasswordMatch);
        passwordInput.addEventListener('input', function() {
            if (confirmInput.value) {
                validatePasswordMatch();
            }
        });

        // Form validation
        const registerForm = document.getElementById('registerForm');
        registerForm.addEventListener('submit', function(e) {
            const name = nameInput.value.trim();
            const email = emailInput.value;
            const password = passwordInput.value;
            const confirmPassword = confirmInput.value;

            if (name.length < 3) {
                e.preventDefault();
                nameInput.focus();
                nameInput.classList.add('invalid');
                nameError.classList.add('show');
                return false;
            }

            if (!validarDominioEmail(email)) {
                e.preventDefault();
                emailInput.focus();
                emailInput.classList.add('invalid');
                emailError.classList.add('show');
                return false;
            }

            if (password.length < 8) {
                e.preventDefault();
                passwordInput.focus();
                alert('⚠️ La contraseña debe tener al menos 8 caracteres.');
                return false;
            }

            if (password !== confirmPassword) {
                e.preventDefault();
                confirmInput.focus();
                confirmInput.classList.add('invalid');
                confirmError.classList.add('show');
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