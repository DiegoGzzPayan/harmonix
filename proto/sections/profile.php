<?php
// sections/profile_data.php

require_once 'conexion.php'; 

$logged_user_id = $_SESSION['usuario_id'] ?? 0; 
$current_username = '';
$user_initials = '';

if ($logged_user_id > 0) {
    // Obtener el nombre de usuario actual (asumiendo que la columna es 'nombre_usuario')
    $sql_user = "SELECT nombre_usuario FROM usuarios WHERE usuario_id = ?";
    if ($stmt_user = $conn->prepare($sql_user)) {
        $stmt_user->bind_param("i", $logged_user_id);
        $stmt_user->execute();
        $result_user = $stmt_user->get_result();
        $user_data = $result_user->fetch_assoc();
        $current_username = htmlspecialchars($user_data['nombre_usuario'] ?? 'N/A');
        
        // Generar iniciales (máximo 2 letras)
        if (!empty($current_username) && $current_username !== 'N/A') {
            $words = explode(' ', $current_username);
            if (count($words) >= 2) {
                // Si tiene dos o más palabras, toma la primera letra de las dos primeras
                $user_initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
            } else {
                // Si solo tiene una palabra, toma las dos primeras letras
                $user_initials = strtoupper(substr($current_username, 0, 2));
            }
        } else {
            $user_initials = '??';
        }
        
        $stmt_user->close();
    }
}

// Generar un color de fondo basado en el ID del usuario (consistente)
$avatar_colors = [
    ['from' => '#93c5fd', 'to' => '#60a5fa'], // azul
    ['from' => '#86efac', 'to' => '#4ade80'], // verde
    ['from' => '#fde047', 'to' => '#facc15'], // amarillo
    ['from' => '#c4b5fd', 'to' => '#a78bfa'], // púrpura
    ['from' => '#fca5a5', 'to' => '#f87171'], // rojo
    ['from' => '#fdba74', 'to' => '#fb923c'], // naranja
    ['from' => '#a5f3fc', 'to' => '#22d3ee'], // cyan
    ['from' => '#f9a8d4', 'to' => '#f472b6'], // rosa
];

$color_index = $logged_user_id % count($avatar_colors);
$avatar_color = $avatar_colors[$color_index];

// Nota: La conexión se cierra aquí si no se usa después.
// Si este archivo es incluido, la conexión debe cerrarse en el archivo principal.
$conn->close();
?>

<style>
/* Estilos solo para esta sección */
.profile-container {
    max-width: 900px;
    margin: 0 auto;
    padding: 20px;
}

.profile-header {
    background: linear-gradient(135deg, var(--pastel-blue) 0%, var(--pastel-purple) 100%);
    border-radius: 20px;
    padding: 40px;
    text-align: center;
    margin-bottom: 30px;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    position: relative;
    overflow: hidden;
}

.profile-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
    animation: pulse 8s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 0.5; }
    50% { transform: scale(1.1); opacity: 0.3; }
}

.profile-avatar {
    width: 120px;
    height: 120px;
    background: linear-gradient(135deg, <?php echo $avatar_color['from']; ?> 0%, <?php echo $avatar_color['to']; ?> 100%);
    border-radius: 50%;
    margin: 0 auto 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 48px;
    font-weight: 700;
    color: white;
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.15);
    position: relative;
    z-index: 1;
    text-transform: uppercase;
    letter-spacing: 2px;
    border: 4px solid rgba(255, 255, 255, 0.3);
}

.profile-username {
    font-size: 32px;
    font-weight: 700;
    color: white;
    margin: 0;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    position: relative;
    z-index: 1;
}

.profile-sections {
    display: grid;
    gap: 25px;
}

.profile-card {
    background: var(--bg-card);
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.profile-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.profile-card h3 {
    color: var(--text-dark);
    font-size: 22px;
    margin-top: 0;
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 3px solid var(--pastel-blue);
    display: flex;
    align-items: center;
    gap: 10px;
}

.profile-card h3 i {
    color: var(--pastel-purple);
}

.form-group {
    margin-bottom: 25px;
}

.form-group label {
    display: block;
    margin-bottom: 10px;
    font-weight: 600;
    color: var(--text-dark);
    font-size: 14px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-group input[type="text"] {
    width: 100%;
    padding: 14px 18px;
    border: 2px solid transparent;
    border-radius: 12px;
    background: linear-gradient(to right, var(--bg-light), rgba(255,255,255,0.5));
    color: var(--text-dark);
    font-size: 16px;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.form-group input[type="text"]:focus {
    outline: none;
    border-color: var(--pastel-blue);
    box-shadow: 0 4px 16px rgba(147, 197, 253, 0.3);
    background: white;
}

.form-group input[type="text"]:disabled {
    opacity: 0.7;
    cursor: not-allowed;
}

.btn-primary {
    background: linear-gradient(135deg, var(--pastel-green) 0%, #a7f3d0 100%);
    color: var(--text-dark);
    border: none;
    padding: 14px 32px;
    border-radius: 12px;
    font-weight: 600;
    font-size: 16px;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(134, 239, 172, 0.3);
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(134, 239, 172, 0.4);
}

.danger-zone {
    border: 2px solid var(--red-action);
    background: linear-gradient(135deg, rgba(248, 113, 113, 0.05) 0%, rgba(239, 68, 68, 0.05) 100%);
    padding: 30px;
    border-radius: 16px;
    position: relative;
    overflow: hidden;
}

.danger-zone::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(to right, var(--red-action), #fca5a5);
}

.danger-zone h3 {
    color: var(--red-action);
    border-bottom-color: var(--red-action);
}

.danger-zone p {
    color: var(--text-secondary);
    margin-bottom: 25px;
    font-size: 14px;
}

.danger-actions {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.btn-logout {
    background: linear-gradient(135deg, var(--pastel-purple) 0%, #c4b5fd 100%);
    color: white;
    border: none;
    padding: 12px 28px;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(196, 181, 253, 0.3);
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-logout:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(196, 181, 253, 0.4);
}

.btn-delete {
    background: linear-gradient(135deg, var(--red-action) 0%, #ef4444 100%);
    color: white;
    border: none;
    padding: 12px 28px;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-delete:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(239, 68, 68, 0.4);
    background: linear-gradient(135deg, #dc2626 0%, #b91c1c 100%);
}

@media (max-width: 768px) {
    .profile-header {
        padding: 30px 20px;
    }
    
    .profile-avatar {
        width: 100px;
        height: 100px;
        font-size: 40px;
    }
    
    .profile-username {
        font-size: 24px;
    }
    
    .danger-actions {
        flex-direction: column;
    }
    
    .btn-logout, .btn-delete {
        width: 100%;
        justify-content: center;
    }
}
</style>

<div class="profile-container">
    <!-- Encabezado del perfil -->
    <div class="profile-header">
        <div class="profile-avatar">
            <?php echo $user_initials; ?>
        </div>
        <h1 class="profile-username"><?php echo $current_username; ?></h1>
    </div>

    <div class="profile-sections">
        <!-- Sección de Datos de Usuario -->
        <div class="profile-card">
            <h3><i class="fas fa-id-card"></i> Información de Usuario</h3>
            <form method="POST" action="index.php?section=profile&action=update_username">
                <input type="hidden" name="action" value="update_username_submit">
                
                <div class="form-group">
                    <label for="current_username">
                        <i class="fas fa-user-circle"></i> Usuario Actual
                    </label>
                    <input type="text" id="current_username" value="<?php echo $current_username; ?>" disabled>
                </div>
                
                <div class="form-group">
                    <label for="new_username">
                        <i class="fas fa-edit"></i> Nuevo Nombre de Usuario
                    </label>
                    <input type="text" id="new_username" name="new_username" required placeholder="Ingresa el nuevo nombre">
                </div>

                <button type="submit" class="btn-primary">
                    <i class="fas fa-save"></i> Guardar Cambios
                </button>
            </form>
        </div>

        <!-- Zona de Peligro -->
        <div class="profile-card danger-zone">
            <h3><i class="fas fa-exclamation-triangle"></i> Zona de Peligro</h3>
            <p>Estas acciones son permanentes y no se pueden deshacer. Procede con precaución.</p>

            <div class="danger-actions">
                <button 
                    onclick="location.href='index.php?action=logout';" 
                    class="btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                </button>

                <button 
                    onclick="if(confirm('¿Estás seguro de que quieres borrar tu cuenta? Esto NO se puede deshacer.')) { location.href='index.php?action=delete_account'; }"
                    class="btn-delete">
                    <i class="fas fa-user-times"></i> Eliminar Cuenta
                </button>
            </div>
        </div>
    </div>
</div>