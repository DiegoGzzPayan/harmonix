<?php

session_start();
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 🚨 VERIFICACIÓN DE SESIÓN CLAVE 🚨
// -----------------------------------------------------------
// Verifica si NO existe el ID de usuario logueado en la sesión
if (!isset($_SESSION['usuario_id']) || $_SESSION['usuario_id'] === 0) {
    // Si no está logueado, redirige a la página de inicio de sesión
    header('Location: login.php'); 
    exit(); // Detiene la ejecución del script para que no cargue el HTML
}
// -------

// -----------------------------------------------------------
// 1. MANEJO DE ACCIONES CRÍTICAS (GET) - DEBE IR PRIMERO
// -----------------------------------------------------------

// A) CERRAR SESIÓN (LOGOUT)
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy();
    header('Location: login.php'); // O a tu página de inicio de sesión
    exit();
}

// B) BORRAR CUENTA
if (isset($_GET['action']) && $_GET['action'] === 'delete_account' && isset($_SESSION['usuario_id'])) {
    require_once 'conexion.php'; 
    $logged_user_id = (int)$_SESSION['usuario_id'];
    
    // Consulta para borrar al usuario y sus datos relacionados (gracias a ON DELETE CASCADE)
    $sql = "DELETE FROM usuarios WHERE usuario_id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $logged_user_id);
        $stmt->execute();
        $stmt->close();
    }
    $conn->close();
    
    session_destroy();
    header('Location: registro.php'); // O a una página de confirmación/registro
    exit();
}


// -----------------------------------------------------------
// BLOQUE DE MANEJO: ACTUALIZACIÓN DE PERFIL (nombre)
// -----------------------------------------------------------
// index.php (Bloque de manejo POST para actualizar el nombre de usuario)
// -----------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_username_submit' && ($_GET['section'] ?? '') === 'profile') {
    
    require_once 'conexion.php'; 
    
    // 🚨 REVISA ESTA LÍNEA 🚨
    // Asegúrate de que estás usando el nombre del campo: 'new_username'
    $new_username = $conn->real_escape_string(trim($_POST['new_username'] ?? '')); 
    
    $logged_user_id = (int)($_SESSION['usuario_id'] ?? 0); 
    
    if ($logged_user_id === 0 || empty($new_username)) {
        // ... (Tu lógica de error o redirección) ...
        $_SESSION['profile_message'] = ['type' => 'error', 'text' => 'El nombre de usuario no puede estar vacío.'];
        // Si entra aquí, te borra el nombre.
        header('Location: index.php?section=profile');
        exit();
    }
    
    $sql = "UPDATE usuarios SET nombre_usuario = ? WHERE usuario_id = ?";
    $_SESSION['nombre_usuario'] = $new_username;
    if ($stmt = $conn->prepare($sql)) {
        // TIPOS: s (string para nombre), i (int para ID)
        $stmt->bind_param("si", $new_username, $logged_user_id);
        
        if ($stmt->execute()) {
            $_SESSION['profile_message'] = ['type' => 'success', 'text' => '¡Nombre de usuario actualizado con éxito!'];
        } else {
             // ... (manejo de error) ...
        }
        $stmt->close();
    } 
    $conn->close();
    header('Location: index.php?section=profile');
    exit(); 
}
// -----------------------------------------------------------
$logged_user_id = $_SESSION['usuario_id'] ?? 1; 

// --- LÓGICA PHP DEL AVATAR (Extraída de profile_data.php) ---

// ** 1. Simular la obtención del nombre de usuario **
// En un entorno real, esto vendría de la DB. Aquí lo simulamos.
// Necesitarías incluir tu archivo 'conexion.php' real aquí.
// Usamos el nombre de usuario de la sesión si existe, si no, el de ejemplo.
$current_username = $_SESSION['nombre_usuario'] ?? 'Usuario'; 
$user_initials = '';

// ** 2. Generar Iniciales **
if (!empty($current_username) && $current_username !== 'N/A') {
    $words = explode(' ', $current_username);
    if (count($words) >= 2) {
        $user_initials = strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
    } else {
        $user_initials = strtoupper(substr($current_username, 0, 2));
    }
} else {
    $user_initials = '??';
}

// ** 3. Generar Color Dinámico **
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

// Asignamos las variables PHP a variables CSS para el estilo inline
$avatar_from = $avatar_color['from'];
$avatar_to = $avatar_color['to'];

// --- FIN LÓGICA PHP DEL AVATAR ---
// -----------------------------------------------------------


// -----------------------------------------------------------
// BLOQUE DE MANEJO: BORRADO DE CUENTA
// -----------------------------------------------------------
if (isset($_GET['section']) && $_GET['section'] === 'profile' && isset($_GET['action']) && $_GET['action'] === 'delete') {
    
    require_once 'conexion.php'; 
    $user_id_to_delete = $_SESSION['usuario_id'] ?? 0; 
    
    if ($user_id_to_delete > 0) { 
        $sql = "DELETE FROM usuarios WHERE usuario_id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("i", $user_id_to_delete);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    $conn->close();
    session_destroy();
    header('Location: registro.php'); 
    exit(); 
}

// -----------------------------------------------------------
// BLOQUE DE MANEJO: CREACIÓN DE EQUIPO
// -----------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_submit' && ($_GET['section'] ?? '') === 'teams') {
    
    require_once 'conexion.php'; 
    $nombre_equipo = $conn->real_escape_string(trim($_POST['nombre_equipo'] ?? ''));
    $descripcion = $conn->real_escape_string(trim($_POST['descripcion'] ?? ''));
    $logged_user_id = (int)($_SESSION['usuario_id'] ?? 0); 
    
    if ($logged_user_id === 0) {
        $_SESSION['team_message'] = ['type' => 'error', 'text' => 'Error: Debes iniciar sesión para crear un equipo.'];
        header('Location: index.php?section=teams');
        exit();
    }
    
    if (empty($nombre_equipo) || empty($descripcion)) {
        $_SESSION['team_message'] = ['type' => 'error', 'text' => 'El nombre y la descripción del equipo no pueden estar vacíos.'];
        header('Location: index.php?section=teams&action=create');
        exit();
    }
    
    $sql = "INSERT INTO equipos (nombre_equipo, descripcion, usuario_id, fecha_creacion) VALUES (?, ?, ?, NOW())";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssi", $nombre_equipo, $descripcion, $logged_user_id); 
        
        if ($stmt->execute()) {
            $_SESSION['team_message'] = ['type' => 'success', 'text' => '¡Equipo "' . htmlspecialchars($nombre_equipo) . '" creado con éxito!'];
        } else {
             $_SESSION['team_message'] = ['type' => 'error', 'text' => 'Error al crear el equipo: ' . $stmt->error];
        }
        $stmt->close();
    } else {
        $_SESSION['team_message'] = ['type' => 'error', 'text' => 'Error al preparar la consulta: ' . $conn->error];
    }

    $conn->close();
    header('Location: index.php?section=teams');
    exit(); 
}
// -----------------------------------------------------------
// BLOQUE DE MANEJO: ELIMINACIÓN DE EQUIPO
// -----------------------------------------------------------
if (isset($_GET['section']) && $_GET['section'] === 'teams' && isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
    
    require_once 'conexion.php'; 
    
    $equipo_id_to_delete = (int)($_GET['id'] ?? 0);
    $logged_user_id = (int)($_SESSION['usuario_id'] ?? 0); 
    
    if ($logged_user_id === 0 || $equipo_id_to_delete === 0) {
        $_SESSION['team_message'] = ['type' => 'error', 'text' => 'Error de seguridad o ID de equipo inválido para eliminar.'];
        header('Location: index.php?section=teams');
        exit();
    }

    $sql = "DELETE FROM equipos WHERE equipo_id = ? AND usuario_id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $equipo_id_to_delete, $logged_user_id);
        
        if ($stmt->execute()) {
            if ($stmt->affected_rows > 0) {
                 $_SESSION['team_message'] = ['type' => 'success', 'text' => '✅ ¡Equipo eliminado con éxito!'];
            } else {
                 $_SESSION['team_message'] = ['type' => 'error', 'text' => 'No se pudo eliminar el equipo (quizás no eres el creador).'];
            }
        } else {
             $_SESSION['team_message'] = ['type' => 'error', 'text' => 'Error al ejecutar borrado: ' . $stmt->error];
        }
        $stmt->close();
    }
    
    $conn->close();
    header('Location: index.php?section=teams');
    exit(); 
}

// ===========================================================
// === 🚀 BLOQUES DE MANEJO: TAREAS (¡FALTABAN ESTOS!) ===
// ===========================================================

// -----------------------------------------------------------
// BLOQUE DE MANEJO: CREACIÓN DE TAREA (POST)
// -----------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'create_submit' && ($_GET['section'] ?? '') === 'tasks') {
    
    require_once 'conexion.php'; 
    
    // 1. Corrección: Asegurar que el índice exista
    $titulo = $conn->real_escape_string(trim($_POST['titulo'] ?? ''));
    $descripcion = $conn->real_escape_string(trim($_POST['descripcion'] ?? ''));
    $fecha_vencimiento = $_POST['fecha_vencimiento'] ?? null;
    // Corregido con el operador ??
    $equipo_id = (int)($_POST['equipo_id'] ?? 0); 
    
    $logged_user_id = (int)($_SESSION['usuario_id'] ?? 0); 
    
    if ($logged_user_id === 0 || empty($titulo)) {
        $_SESSION['task_message'] = ['type' => 'error', 'text' => 'Error de seguridad o falta el título.'];
        header('Location: index.php?section=tasks&action=create');
        exit();
    }
    
    $equipo_id_param = ($equipo_id > 0) ? $equipo_id : null;

    // Columna: usuario_id, equipo_id (opcional), titulo, descripcion, fecha_vencimiento
    // Usamos placeholders para todos, luego modificamos el SQL si es NULL.
    $sql_base = "INSERT INTO tareas (usuario_id, equipo_id, titulo, descripcion, fecha_vencimiento, estado) VALUES (?, ?, ?, ?, ?, 'Por Hacer')";
    
    if ($equipo_id_param === null) {
        // Opción 1: No hay equipo_id. Reemplazamos el segundo placeholder por 'NULL'
        
        // 1. Encontrar la posición del segundo '?'
        $pos = strpos($sql_base, '?', strpos($sql_base, '?') + 1); 
        
        // 2. Insertar 'NULL' en esa posición
        $sql = substr_replace($sql_base, 'NULL', $pos, 1);
        
        if ($stmt = $conn->prepare($sql)) {
            // El SQL ahora es: VALUES (?, NULL, ?, ?, ?, 'Por Hacer')
            // Parámetros a enlazar (4): usuario_id, titulo, descripcion, fecha_vencimiento
            $stmt->bind_param("isss", $logged_user_id, $titulo, $descripcion, $fecha_vencimiento);
        }
    } else {
        // Opción 2: Sí hay equipo_id. Usamos la query original.
        $sql = $sql_base;
        
        if ($stmt = $conn->prepare($sql)) {
            // Parámetros a enlazar (5): usuario_id, equipo_id, titulo, descripcion, fecha_vencimiento
            $stmt->bind_param("issss", $logged_user_id, $equipo_id_param, $titulo, $descripcion, $fecha_vencimiento);
        }
    }
    
    if (isset($stmt) && $stmt) {
        if ($stmt->execute()) {
            $_SESSION['task_message'] = ['type' => 'success', 'text' => '¡Tarea "' . htmlspecialchars($titulo) . '" creada con éxito!'];
        } else {
             $_SESSION['task_message'] = ['type' => 'error', 'text' => 'Error al ejecutar la inserción: ' . $stmt->error];
        }
        $stmt->close();
    } else {
        $_SESSION['task_message'] = ['type' => 'error', 'text' => 'Error al preparar la consulta: ' . $conn->error];
    }

    $conn->close();
    header('Location: index.php?section=tasks');
    exit();
}


// -----------------------------------------------------------
// BLOQUE DE MANEJO: COMPLETAR/ELIMINAR TAREA (GET)
// -----------------------------------------------------------
if (isset($_GET['section']) && $_GET['section'] === 'tasks' && isset($_GET['action']) && isset($_GET['id'])) {
    
    require_once 'conexion.php'; 
    
    $task_id = (int)($_GET['id'] ?? 0);
    $action = $_GET['action'];
    $logged_user_id = (int)($_SESSION['usuario_id'] ?? 0);
    
    if ($logged_user_id === 0 || $task_id === 0) {
        $_SESSION['task_message'] = ['type' => 'error', 'text' => 'Error: ID o sesión inválida para la acción de tarea.'];
        header('Location: index.php?section=tasks');
        exit();
    }
    
    $message = '';
    $sql = '';

    if ($action === 'complete') {
        $sql = "UPDATE tareas SET estado = 'Completada' WHERE tarea_id = ? AND usuario_id = ?";
        $message = 'Tarea marcada como completada. ¡Bien hecho!';
    } elseif ($action === 'reopen') {
        $sql = "UPDATE tareas SET estado = 'Por Hacer' WHERE tarea_id = ? AND usuario_id = ?";
        $message = 'Tarea reabierta.';
    } elseif ($action === 'delete') {
        $sql = "DELETE FROM tareas WHERE tarea_id = ? AND usuario_id = ?";
        $message = 'Tarea eliminada con éxito.';
    }

    if ($sql) {
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ii", $task_id, $logged_user_id);
            
            if ($stmt->execute() && $stmt->affected_rows > 0) {
                 $_SESSION['task_message'] = ['type' => 'success', 'text' => $message];
            } else {
                 $_SESSION['task_message'] = ['type' => 'error', 'text' => 'Error: No se modificó la tarea (quizás no eres el dueño).'];
            }
            $stmt->close();
        } else {
            $_SESSION['task_message'] = ['type' => 'error', 'text' => 'Error al preparar la consulta de acción: ' . $conn->error];
        }
    }
    
    $conn->close();
    header('Location: index.php?section=tasks');
    exit();
}
// -----------------------------------------------------------

// -----------------------------------------------------------
// BLOQUE DE MANEJO: CREACIÓN DE PUBLICACIÓN (POST) - AJUSTADO PARA ANÓNIMO
// -----------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'post_submit' && ($_GET['section'] ?? '') === 'free_space') {
    
    require_once 'conexion.php'; 
    
    $contenido = trim($_POST['contenido'] ?? '');
    $logged_user_id = (int)($_SESSION['usuario_id'] ?? 0); 
    
    // 🚨 NUEVO: Capturar si la casilla de anónimo fue marcada
    $es_anonimo = isset($_POST['es_anonimo']) ? 1 : 0; 
    
    if ($logged_user_id === 0 || empty($contenido)) {
        $_SESSION['post_message'] = ['type' => 'error', 'text' => 'Error de seguridad o falta el contenido.'];
    } else {
        // 🚨 CONSULTA ACTUALIZADA: Ahora inserta el valor de es_anonimo
        $sql = "INSERT INTO posts (usuario_id, contenido, es_anonimo) VALUES (?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            // 🚨 bind_param ACTUALIZADO: Agregamos 'i' para el campo es_anonimo
            $stmt->bind_param("isi", $logged_user_id, $contenido, $es_anonimo);
            
            if ($stmt->execute()) {
                $_SESSION['post_message'] = ['type' => 'success', 'text' => '¡Publicación enviada!'];
            } else {
                 $_SESSION['post_message'] = ['type' => 'error', 'text' => 'Error al publicar: ' . $stmt->error];
            }
            $stmt->close();
        } else {
            $_SESSION['post_message'] = ['type' => 'error', 'text' => 'Error al preparar la consulta: ' . $conn->error];
        }
    }

    $conn->close(); 

    header('Location: index.php?section=free_space');
    exit(); 
}
// ===========================================================
// === FIN DE BLOQUES DE MANEJO DE ACCIONES ===
// ===========================================================


// Obtener la sección a cargar desde el parámetro GET, por defecto 'dashboard'
$section = isset($_GET['section']) ? htmlspecialchars($_GET['section']) : 'dashboard';

$sections_map = [
    'dashboard' => ['title' => 'Inicio', 'file' => 'sections/dashboard.php'],
    'teams' => ['title' => 'Mis Equipos', 'file' => 'sections/teams.php'],
    'tasks' => ['title' => 'Tareas', 'file' => 'sections/tasks.php'],
    'calls' => ['title' => 'Videollamadas', 'file' => 'sections/calls.php'],
    'calendar' => ['title' => 'Calendario', 'file' => 'sections/calendar.php'],
    'free_space' => ['title' => 'Espacio Libre', 'file' => 'sections/free_space.php'],
    'settings' => ['title' => 'Configuración', 'file' => 'sections/settings.php'],
    'profile' => ['title' => 'Perfil', 'file' => 'sections/profile.php'], 
];


// Asegurar que solo se carguen archivos válidos
if (!isset($sections_map[$section]) || !file_exists($sections_map[$section]['file'])) {
    $section = 'dashboard'; // Volver a la sección por defecto si es inválida
}

$page_title = $sections_map[$section]['title'];
$content_file = $sections_map[$section]['file'];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">  
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap" rel="stylesheet">
<style>
@import url('https://fonts.googleapis.com/css2?family=Play:wght@400;700&display=swap');
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css');

:root {
    /* Colores Pastel Intensificados */
    --pastel-blue: #7AB8DF;
    --pastel-purple: #A584DB;
    --pastel-green: #88D8C0;
    --pastel-pink: #FFB8C8;
    --pastel-yellow: #FFE083;

    --text-dark: #333333;
    --text-light: #ffffff;
    --bg-light: #FAFAFA;
    --bg-card: #FFFFFF;
    --bg-task-item: #F5F5F5;
    --border-color: #E0E0E0;
    --text-secondary: #777;
    --post-content-color: #555;
    --post-actions-color: #888;
    --calendar-text: #7d4453;
    --spotify-green: #1DB954;
    --red-action: #FF6B6B;
}

/* --- MODO OSCURO --- */   
.dark-mode {
    --text-dark: #ffffff;
    --text-light: #1A1A1A;
    --bg-light: #121212;
    --bg-card: #1E1E1E;
    --bg-task-item: #2C2C2C;
    --border-color: #333333;
    --text-secondary: #AAAAAA; 
    --post-content-color: #BBBBBB; 
    --post-actions-color: #777777;
    
    --pastel-blue: #4A90E2;
    --pastel-purple: #904AE2;
    --pastel-green: #50E3C2;
    --pastel-pink: #E24A90;
    --pastel-yellow: #FFBF00;
}

/* ===== Reset ===== */
* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
    font-family: 'Play', sans-serif;
    transition: all 0.3s ease;
}

body {
    background-color: var(--bg-light);
    color: var(--text-dark);
    display: flex;
    height: 100vh;
    overflow: hidden;
    animation: fadeBody 0.7s ease;
}

/* ===== SIDEBAR ===== */
.sidebar {
    width: 280px;
    background: linear-gradient(135deg, var(--pastel-blue), var(--pastel-purple));
    color: var(--text-light);
    padding: 30px 20px;
    display: flex;
    flex-direction: column;
    box-shadow: 4px 0 15px rgba(0,0,0,0.05);
    transition: width 0.3s ease;
    animation: slideSidebar 0.6s ease;
}

.logo {
    font-size: 3em;
    font-weight: 700;
    margin-bottom: 50px;
    text-align: center;
    color: var(--text-light);
}

.nav-menu {
    list-style: none;
    width: 100%;
}

.nav-menu li {
    margin-bottom: 12px;
}

.nav-menu a {
    color: var(--text-light);
    text-decoration: none;
    display: flex;
    align-items: center;
    padding: 15px 20px;
    border-radius: 10px;
    font-weight: 500;
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.nav-menu a:hover {
    background-color: rgba(255,255,255,0.2);
    transform: translateX(5px);
}

.nav-menu a.active {
    background-color: var(--bg-card);
    color: var(--pastel-blue);
    font-weight: 700;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.nav-menu a .icon {
    font-size: 1.2em;
    width: 25px;
    text-align: center;
}

.nav-menu a span {
    margin-left: 20px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* ===== MAIN CONTENT ===== */
.main-content {
    flex-grow: 1;
    padding: 40px;
    overflow-y: auto;
}

.header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 40px;
    animation: fadeSection 0.5s ease-in-out;
}

.header h1 {
    font-size: 2.2em;
    font-weight: 700;
    color: var(--pastel-purple);
}

.user-profile {
    display: flex;
    align-items: center;
    gap: 15px;
    cursor: pointer;
    background-color:transparent;
    padding: 8px 15px 8px 8px;
    border-radius: 50px;
    transition: box-shadow 0.2s;
}

.user-profile span {
    font-weight: 600;
    color: var(--text-dark);
}

.header-avatar {
    background: linear-gradient(135deg, #FFB8C8 0%, #7AB8DF 100%);
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    font-weight: 600;
    color: white;
    text-decoration: none;
}

/* ===== CARDS ===== */
.card {
    background-color: var(--bg-card);
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.05);
    margin-bottom: 20px;
    transition: transform 0.3s ease, box-shadow 0.3s, background-color 0.3s;
}

.card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(0,0,0,0.1);
}

.card h3 {
    font-size: 1.4em;
    font-weight: 600;
    color: var(--pastel-blue);
    margin-bottom: 20px;
    padding-bottom: 5px;
    border-bottom: 2px solid var(--border-color);
    transition: border-color 0.3s;
}

.card p {
    line-height: 1.6;
}

/* ===== TASKS ===== */
.task-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    margin-bottom: 10px;
    border-radius: 10px;
    background-color: var(--bg-task-item);
    border-left: 5px solid;
    transition: all 0.3s ease;
}
.task-item.in-progress { border-left-color: var(--pastel-purple); }
.task-item.completed { border-left-color: var(--pastel-green); }

/* ===== TEAM ===== */
.team-list { list-style: none; padding: 0; }
.team-item {
    display: flex;
    align-items: center;
    gap: 20px;
    padding: 15px;
    border-radius: 10px;
    margin-bottom: 10px;
    background-color: var(--pastel-green);
    cursor: pointer;
    transition: background-color 0.2s;
}
.team-item:hover {
    background-color: color-mix(in srgb, var(--pastel-green) 80%, var(--bg-card));
}

.team-detail-card {
    background-color: var(--pastel-pink);
    color: var(--text-dark);
    padding: 25px;
    border-radius: 10px;
    margin-bottom: 20px;
    transition: background-color 0.3s;
}
.team-detail-card h4 { font-weight: 600; margin-bottom: 10px; color: var(--pastel-purple); }
.member-avatar { width: 35px; height: 35px; border-radius: 50%; margin-right: 5px; }

/* ===== POST ===== */
.post-card {
    background-color: var(--bg-card);
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    margin-bottom: 20px;
    transition: background-color 0.3s, box-shadow 0.3s;
}
.post-header { display: flex; align-items: center; margin-bottom: 15px; }
.post-avatar { width: 40px; height: 40px; border-radius: 50%; margin-right: 10px; }
.post-author { font-weight: 700; color: var(--pastel-blue); }
.post-content { color: var(--post-content-color); line-height: 1.6; transition: color 0.3s; }
.post-actions { margin-top: 20px; display: flex; gap: 20px; color: var(--post-actions-color); font-size: 0.9em; transition: color 0.3s; }

/* ===== FORMULARIOS ===== */
input[type="text"], input[type="email"], input[type="password"], textarea {
    background-color: var(--bg-task-item);
    color: var(--text-dark);
    border: 1px solid var(--border-color);
    font-family: 'Play', sans-serif;
    padding: 12px;
    border-radius: 10px;
    transition: all 0.3s ease;
}
input:focus, textarea:focus { outline: 2px solid var(--pastel-blue); border-color: var(--pastel-blue); }
label { color: var(--text-dark); font-weight: 600; display: block; margin-bottom: 5px; }

/* ===== BOTONES ===== */
.action-btn {
    transition: opacity 0.3s, transform 0.2s, background-color 0.3s;
}
.action-btn:hover { opacity: 0.9; transform: translateY(-2px); }

/* ===== ANIMACIONES ===== */
@keyframes fadeBody { from {opacity:0;} to {opacity:1;} }
@keyframes slideSidebar { from {transform:translateX(-20px); opacity:0;} to {transform:translateX(0); opacity:1;} }
@keyframes fadeSection { from {opacity:0; transform:translateY(20px);} to {opacity:1; transform:translateY(0);} }

    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="logo">Harmonix</div>
        <ul class="nav-menu">
            <li><a href="index.php?section=dashboard" class="nav-link <?php echo ($section == 'dashboard' ? 'active' : ''); ?>" data-target="dashboard"><span class="icon fas fa-home"></span><span>Inicio</span></a></li>
            <li><a href="index.php?section=teams" class="nav-link <?php echo ($section == 'teams' ? 'active' : ''); ?>" data-target="teams"><span class="icon fas fa-users"></span><span>Mis Equipos</span></a></li>
            <li><a href="index.php?section=tasks" class="nav-link <?php echo ($section == 'tasks' ? 'active' : ''); ?>" data-target="tasks"><span class="icon fas fa-tasks"></span><span>Tareas</span></a></li>
            <li><a href="index.php?section=calls" class="nav-link <?php echo ($section == 'calls' ? 'active' : ''); ?>" data-target="calls"><span class="icon fas fa-video"></span><span>Videollamadas</span></a></li>
            <li><a href="index.php?section=calendar" class="nav-link <?php echo ($section == 'calendar' ? 'active' : ''); ?>" data-target="calendar"><span class="icon fas fa-calendar-alt"></span><span>Calendario</span></a></li>
            <li><a href="index.php?section=free_space" class="nav-link <?php echo ($section == 'free_space' ? 'active' : ''); ?>" data-target="free_space"><span class="icon fas fa-comment-dots"></span><span>Espacio Libre</span></a></li>
            <li><a href="index.php?section=settings" class="nav-link <?php echo ($section == 'settings' ? 'active' : ''); ?>" data-target="settings"><span class="icon fas fa-cog"></span><span>Configuración</span></a></li>
        </ul>
    </aside>

    <div class="main-content">
        <!-- Dashboard Header con el nuevo perfil -->
        <div class="header">
            <h1 id="page-title"><?php echo $page_title; ?></h1>
            
            <!-- EL NUEVO PERFIL CON AVATAR DINÁMICO -->
            <div class="user-profile" id="profile-trigger" onclick="window.location.href='index.php?section=profile'">
                <!-- Saludo usando la variable de nombre ya cargada -->
                <span>Hola, <?php echo htmlspecialchars($current_username); ?> 👋</span>
                
                <!-- El nuevo avatar dinámico que reemplaza a la imagen.jpg -->
                <a href="index.php?section=profile" class="header-avatar" title="Ver Perfil">
                    <?php echo $user_initials; ?>
                </a>
            </div>
        </div>

        <div id="<?php echo $section; ?>" class="section-content">
            <?php include $content_file; ?>
        </div>
    </main>
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const darkModeToggle = document.getElementById('dark-mode-toggle');
            const body = document.body;

            // --- LÓGICA DEL MODO OSCURO (Revisada para persistencia) ---
            
            // 1. Aplicar estado guardado (si existe)
            const isDarkMode = localStorage.getItem('darkMode') === 'true';
            
            if (isDarkMode) {
                body.classList.add('dark-mode');
                // Se busca el elemento para actualizar su estado (solo es relevante si el script se carga después de la sección de settings)
                const toggle = document.getElementById('dark-mode-toggle');
                if (toggle) {
                    toggle.classList.remove('fa-toggle-off');
                    toggle.classList.add('fa-toggle-on');
                    toggle.style.color = 'var(--pastel-green)';
                }
            }

            // 2. Función para alternar el modo y guardar la preferencia
            const toggleDarkMode = () => {
                body.classList.toggle('dark-mode');
                const isCurrentlyDark = body.classList.contains('dark-mode');
                
                localStorage.setItem('darkMode', isCurrentlyDark);
                
                // Actualizar el icono y color si existe en la página actual
                const toggle = document.getElementById('dark-mode-toggle');
                if (toggle) {
                    if (isCurrentlyDark) {
                        toggle.classList.remove('fa-toggle-off');
                        toggle.classList.add('fa-toggle-on');
                        toggle.style.color = 'var(--pastel-green)';
                    } else {
                        toggle.classList.remove('fa-toggle-on');
                        toggle.classList.add('fa-toggle-off');
                        toggle.style.color = 'var(--border-color)'; 
                    }
                }
            };
            
            // 3. Listener para el interruptor
            // Importante: El listener se debe añadir DESPUÉS de que el contenido de la sección "settings" haya sido incluido
            // En este caso, el contenido se incluye en la carga inicial de la página, por lo que el listener funciona si el elemento existe.
            if (darkModeToggle) {
                darkModeToggle.addEventListener('click', toggleDarkMode);
            }
        });
    </script>
</body>
</html>