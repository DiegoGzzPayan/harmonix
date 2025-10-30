<?php
require_once 'conexion.php'; 

$posts = [];
$logged_user_id = $_SESSION['usuario_id'] ?? 0;

$conn->set_charset("utf8mb4");

// -----------------------------------------------------------
// MANEJO: NUEVA PUBLICACIÓN
// -----------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'post_submit') {
    $contenido = trim($_POST['contenido'] ?? '');
    $es_anonimo = isset($_POST['es_anonimo']) ? 1 : 0;
    
    if ($logged_user_id <= 0) {
        $_SESSION['post_message'] = ['type' => 'error', 'text' => 'Debes iniciar sesión para publicar'];
    } elseif (empty($contenido)) {
        $_SESSION['post_message'] = ['type' => 'error', 'text' => 'El contenido no puede estar vacío'];
    } elseif (strlen($contenido) > 5000) {
        $_SESSION['post_message'] = ['type' => 'error', 'text' => 'El contenido es demasiado largo (máx. 5000 caracteres)'];
    } else {
        $sql_insert = "INSERT INTO posts (usuario_id, contenido, es_anonimo) VALUES (?, ?, ?)";
        if ($stmt = $conn->prepare($sql_insert)) {
            $stmt->bind_param("isi", $logged_user_id, $contenido, $es_anonimo);
            if ($stmt->execute()) {
                $_SESSION['post_message'] = ['type' => 'success', 'text' => '¡Publicación creada exitosamente!'];
            } else {
                $_SESSION['post_message'] = ['type' => 'error', 'text' => 'Error al crear la publicación'];
            }
            $stmt->close();
        }
    }
    
    $conn->close();
    header('Location: index.php?section=free_space');
    exit();
}

// -----------------------------------------------------------
// FUNCIÓN: GENERAR INICIALES Y COLOR DE AVATAR
// -----------------------------------------------------------
function generateAvatarData($username, $user_id) {
    $initials = '';
    if (!empty($username)) {
        $words = explode(' ', trim($username));
        if (count($words) >= 2) {
            $initials = mb_strtoupper(mb_substr($words[0], 0, 1) . mb_substr($words[1], 0, 1));
        } else {
            $initials = mb_strtoupper(mb_substr($username, 0, 2));
        }
    } else {
        $initials = '??';
    }
    
    $avatar_colors = [
        ['from' => '#93c5fd', 'to' => '#60a5fa', 'shadow' => 'rgba(96, 165, 250, 0.3)'],
        ['from' => '#86efac', 'to' => '#4ade80', 'shadow' => 'rgba(74, 222, 128, 0.3)'],
        ['from' => '#fde047', 'to' => '#facc15', 'shadow' => 'rgba(250, 204, 21, 0.3)'],
        ['from' => '#c4b5fd', 'to' => '#a78bfa', 'shadow' => 'rgba(167, 139, 250, 0.3)'],
        ['from' => '#fca5a5', 'to' => '#f87171', 'shadow' => 'rgba(248, 113, 113, 0.3)'],
        ['from' => '#fdba74', 'to' => '#fb923c', 'shadow' => 'rgba(251, 146, 60, 0.3)'],
        ['from' => '#a5f3fc', 'to' => '#22d3ee', 'shadow' => 'rgba(34, 211, 238, 0.3)'],
        ['from' => '#f9a8d4', 'to' => '#f472b6', 'shadow' => 'rgba(244, 114, 182, 0.3)'],
    ];
    
    $color_index = abs($user_id) % count($avatar_colors);
    $color = $avatar_colors[$color_index];
    
    return [
        'initials' => $initials,
        'color' => $color
    ];
}

// -----------------------------------------------------------
// FUNCIÓN: CALCULAR TIEMPO TRANSCURRIDO
// -----------------------------------------------------------
function timeAgo($timestamp) {
    $diff = time() - strtotime($timestamp);
    
    if ($diff < 60) {
        return 'ahora';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . 'm';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . 'h';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . 'd';
    } else {
        return date("d M", strtotime($timestamp));
    }
}

// -----------------------------------------------------------
// CONSULTA: OBTENER PUBLICACIONES
// -----------------------------------------------------------
$sql_posts = "SELECT p.post_id, p.contenido, p.fecha_publicacion, p.es_anonimo, 
                     p.usuario_id, u.nombre_usuario
              FROM posts p
              JOIN usuarios u ON p.usuario_id = u.usuario_id
              ORDER BY p.fecha_publicacion DESC
              LIMIT 50"; 

if ($stmt = $conn->prepare($sql_posts)) {
    $stmt->execute();
    $result = $stmt->get_result();
    $posts = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
}

$conn->close();
?>

<style>
/* Variables y reset mejorado */
:root {
    --transition-fast: 0.2s ease;
    --transition-smooth: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.06);
    --shadow-md: 0 4px 16px rgba(0, 0, 0, 0.08);
    --shadow-lg: 0 8px 24px rgba(0, 0, 0, 0.12);
    --shadow-hover: 0 12px 32px rgba(0, 0, 0, 0.15);
}

/* Header mejorado */
.community-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    margin-bottom: 32px;
    padding: 20px;
    background: linear-gradient(135deg, rgba(169, 209, 223, 0.08), rgba(182, 162, 219, 0.08));
    border-radius: 16px;
    border: 1px solid rgba(169, 209, 223, 0.2);
}

.community-header h2 {
    font-size: 2em;
    color: var(--pastel-purple);
    margin: 0;
    display: flex;
    align-items: center;
    gap: 12px;
}

.community-stats {
    display: flex;
    gap: 24px;
    font-size: 0.9em;
    color: var(--text-secondary);
}

.community-stats span {
    display: flex;
    align-items: center;
    gap: 6px;
    padding: 6px 12px;
    background: rgba(255, 255, 255, 0.5);
    border-radius: 20px;
}

/* Tarjeta de nueva publicación mejorada */
.new-post-card {
    background: linear-gradient(135deg, rgba(169, 209, 223, 0.05), rgba(182, 162, 219, 0.05));
    border: 2px solid var(--pastel-blue);
    margin-bottom: 32px;
    position: relative;
    overflow: hidden;
}

.new-post-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, var(--pastel-blue), var(--pastel-purple), var(--pastel-green));
}

.new-post-card h3 {
    color: var(--pastel-purple);
    margin-bottom: 24px;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.3em;
}

.post-form-header {
    display: flex;
    align-items: center;
    gap: 15px;
    margin-bottom: 15px;
}

.form-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 20px;
    font-weight: 700;
    color: white;
    text-transform: uppercase;
    letter-spacing: 1px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    flex-shrink: 0;
    box-shadow: var(--shadow-sm);
}

textarea {
    width: 100%;
    padding: 16px;
    border-radius: 12px;
    border: 2px solid var(--border-color);
    min-height: 120px;
    margin-bottom: 16px;
    resize: vertical;
    background-color: var(--bg-task-item);
    color: var(--text-dark);
    font-family: 'Play', sans-serif;
    font-size: 15px;
    line-height: 1.6;
    transition: var(--transition-smooth);
}

textarea:focus {
    outline: none;
    border-color: var(--pastel-blue);
    box-shadow: 0 0 0 4px rgba(169, 209, 223, 0.15);
    background-color: white;
}

textarea::placeholder {
    color: var(--text-secondary);
    opacity: 0.7;
}

.post-form-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 16px;
    border-top: 1px solid var(--border-color);
}

.anonymous-checkbox {
    display: flex;
    align-items: center;
    gap: 10px;
    color: var(--text-dark);
    cursor: pointer;
    padding: 10px 14px;
    border-radius: 10px;
    transition: var(--transition-smooth);
    user-select: none;
}

.anonymous-checkbox:hover {
    background-color: rgba(169, 209, 223, 0.1);
}

.anonymous-checkbox input[type="checkbox"] {
    width: 20px;
    height: 20px;
    cursor: pointer;
    accent-color: var(--pastel-purple);
}

.submit-btn {
    background: linear-gradient(135deg, var(--pastel-green), #a7f3d0);
    color: var(--text-dark);
    padding: 12px 32px;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition-smooth);
    box-shadow: 0 4px 12px rgba(134, 239, 172, 0.3);
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 15px;
}

.submit-btn:hover:not(:disabled) {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(134, 239, 172, 0.5);
}

.submit-btn:active:not(:disabled) {
    transform: translateY(0);
}

.submit-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

/* Contenedor de publicaciones */
.posts-container {
    display: flex;
    flex-direction: column;
    gap: 24px;
}

/* Tarjeta de publicación ultra mejorada */
.post-card {
    background-color: var(--bg-card);
    padding: 28px;
    border-radius: 16px;
    box-shadow: var(--shadow-md);
    transition: var(--transition-smooth);
    border: 1px solid transparent;
    position: relative;
    overflow: hidden;
}

.post-card::before {
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    bottom: 0;
    width: 4px;
    background: linear-gradient(180deg, var(--pastel-blue), var(--pastel-purple));
    opacity: 0;
    transition: var(--transition-smooth);
}

.post-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-hover);
    border-color: rgba(169, 209, 223, 0.3);
}

.post-card:hover::before {
    opacity: 1;
}

.post-header {
    display: flex;
    align-items: center;
    margin-bottom: 18px;
    gap: 14px;
}

.post-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    font-weight: 700;
    color: white;
    text-transform: uppercase;
    letter-spacing: 1px;
    border: 3px solid rgba(255, 255, 255, 0.3);
    flex-shrink: 0;
    box-shadow: var(--shadow-sm);
    transition: var(--transition-fast);
}

.post-card:hover .post-avatar {
    transform: scale(1.05);
}

.post-author-info {
    flex-grow: 1;
}

.post-author {
    font-weight: 700;
    color: var(--text-dark);
    font-size: 1.05em;
    display: block;
    margin-bottom: 4px;
}

.post-time {
    font-size: 0.85em;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    gap: 4px;
}

.anon-badge {
    background: linear-gradient(135deg, var(--pastel-purple), #c4b5fd);
    color: white;
    padding: 6px 14px;
    border-radius: 14px;
    font-size: 0.75em;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    box-shadow: 0 2px 8px rgba(167, 139, 250, 0.3);
}

.post-content {
    padding: 18px 0;
    line-height: 1.7;
}

.post-content p {
    margin: 0;
    color: var(--text-dark);
    font-size: 1em;
    white-space: pre-wrap;
    word-wrap: break-word;
}

/* Alertas mejoradas */
.alert {
    padding: 16px 24px;
    border-radius: 12px;
    margin-bottom: 28px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: var(--shadow-md);
    animation: slideInAlert 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55);
    border: 1px solid transparent;
}

@keyframes slideInAlert {
    from {
        opacity: 0;
        transform: translateY(-20px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

.alert-success {
    background: linear-gradient(135deg, #d4edda, #c3e6cb);
    color: #155724;
    border-color: #28a745;
}

.alert-error {
    background: linear-gradient(135deg, #f8d7da, #f5c6cb);
    color: #721c24;
    border-color: #dc3545;
}

.alert i {
    font-size: 1.4em;
}

/* Estado vacío mejorado */
.empty-state {
    text-align: center;
    padding: 80px 20px;
    color: var(--text-secondary);
}

.empty-state i {
    font-size: 5em;
    color: var(--pastel-blue);
    margin-bottom: 24px;
    opacity: 0.4;
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.empty-state h3 {
    color: var(--text-dark);
    margin-bottom: 12px;
    font-size: 1.5em;
}

.empty-state p {
    font-size: 1.1em;
}

/* Loading spinner */
.loading-spinner {
    display: inline-block;
    width: 18px;
    height: 18px;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top-color: white;
    border-radius: 50%;
    animation: spin 0.6s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* Responsive mejorado */
@media (max-width: 768px) {
    .community-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }
    
    .community-stats {
        width: 100%;
        justify-content: space-between;
    }
    
    .post-form-footer {
        flex-direction: column;
        gap: 16px;
        align-items: stretch;
    }
    
    .submit-btn {
        width: 100%;
        justify-content: center;
    }
}

@media (max-width: 480px) {
    .community-header h2 {
        font-size: 1.5em;
    }
    
    .post-card {
        padding: 20px;
    }
}

/* Mejoras de accesibilidad */
.submit-btn:focus-visible {
    outline: 3px solid var(--pastel-blue);
    outline-offset: 2px;
}

textarea:focus-visible {
    outline: 3px solid var(--pastel-blue);
    outline-offset: 2px;
}

/* Animación para nuevos elementos */
.post-card.new-post {
    animation: slideInPost 0.5s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

@keyframes slideInPost {
    from {
        opacity: 0;
        transform: translateY(30px) scale(0.95);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}
</style>

<div class="community-header">
    <h2><i class="fas fa-comments"></i> Comunidad</h2>
    <div class="community-stats">
        <span><i class="fas fa-fire"></i> <?php echo count($posts); ?> publicaciones</span>
        <span><i class="fas fa-users"></i> Espacio libre</span>
    </div>
</div>

<?php 
// Mostrar mensajes de éxito/error
if (isset($_SESSION['post_message'])): ?>
    <div class="alert alert-<?php echo $_SESSION['post_message']['type']; ?>">
        <i class="fas fa-<?php echo $_SESSION['post_message']['type'] == 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
        <span><?php echo htmlspecialchars($_SESSION['post_message']['text']); ?></span>
    </div>
    <?php unset($_SESSION['post_message']); ?>
<?php endif; ?>

<?php 
// Obtener datos del avatar del usuario actual
if ($logged_user_id > 0) {
    $current_user_avatar = generateAvatarData($_SESSION['nombre_usuario'] ?? 'Usuario', $logged_user_id);
}
?>

<?php if ($logged_user_id > 0): ?>
<div class="card new-post-card">
    <h3><i class="fas fa-pen"></i> ¿Qué estás pensando?</h3>
    <form method="POST" action="index.php?section=free_space" id="postForm">
        <input type="hidden" name="action" value="post_submit">
        
        <div class="post-form-header">
            <div class="form-avatar" style="background: linear-gradient(135deg, <?php echo $current_user_avatar['color']['from']; ?>, <?php echo $current_user_avatar['color']['to']; ?>);">
                <?php echo $current_user_avatar['initials']; ?>
            </div>
            <span style="color: var(--text-dark); font-weight: 600;">
                <?php echo htmlspecialchars($_SESSION['nombre_usuario'] ?? 'Usuario'); ?>
            </span>
        </div>
        
        <textarea name="contenido" 
                  id="postContent"
                  placeholder="Comparte tus pensamientos, ideas o preguntas con la comunidad..." 
                  maxlength="5000"
                  required></textarea>
        
        <div style="text-align: right; margin-top: -10px; margin-bottom: 10px; font-size: 0.85em; color: var(--text-secondary);">
            <span id="charCount">0</span>/5000 caracteres
        </div>
        
        <div class="post-form-footer">
            <label class="anonymous-checkbox">
                <input type="checkbox" name="es_anonimo" value="1">
                <i class="fas fa-user-secret"></i>
                <span>Publicar de forma anónima</span>
            </label>
            
            <button type="submit" class="submit-btn" id="submitBtn">
                <i class="fas fa-paper-plane"></i> Publicar
            </button>
        </div>
    </form>
</div>
<?php else: ?>
<div class="card new-post-card">
    <h3><i class="fas fa-sign-in-alt"></i> Únete a la conversación</h3>
    <p style="color: var(--text-secondary); margin: 0;">Inicia sesión para crear publicaciones en la comunidad.</p>
</div>
<?php endif; ?>

<div class="posts-container">
    <?php if (empty($posts)): ?>
        <div class="empty-state">
            <i class="fas fa-comment-slash"></i>
            <h3>Aún no hay publicaciones</h3>
            <p>¡Sé el primero en compartir algo con la comunidad!</p>
        </div>
    <?php else: ?>
        <?php foreach ($posts as $post): 
            // Determinar si es anónimo
            $is_anon = ($post['es_anonimo'] == 1);
            $author_name = $is_anon ? 'Usuario Anónimo' : htmlspecialchars($post['nombre_usuario']);
            
            // Generar datos del avatar
            if ($is_anon) {
                $avatar_data = [
                    'initials' => '??',
                    'color' => ['from' => '#9CA3AF', 'to' => '#6B7280', 'shadow' => 'rgba(107, 114, 128, 0.3)']
                ];
            } else {
                $avatar_data = generateAvatarData($post['nombre_usuario'], $post['usuario_id']);
            }
        ?>
            <div class="post-card">
                <div class="post-header">
                    <div class="post-avatar" style="background: linear-gradient(135deg, <?php echo $avatar_data['color']['from']; ?>, <?php echo $avatar_data['color']['to']; ?>); box-shadow: 0 4px 12px <?php echo $avatar_data['color']['shadow']; ?>;">
                        <?php echo $avatar_data['initials']; ?>
                    </div>
                    
                    <div class="post-author-info">
                        <span class="post-author">
                            <?php echo $author_name; ?>
                        </span>
                        <span class="post-time">
                            <i class="far fa-clock"></i> <?php echo timeAgo($post['fecha_publicacion']); ?>
                        </span>
                    </div>
                    
                    <?php if ($is_anon): ?>
                        <span class="anon-badge">
                            <i class="fas fa-user-secret"></i> Anónimo
                        </span>
                    <?php endif; ?>
                </div>
                
                <div class="post-content">
                    <p><?php echo nl2br(htmlspecialchars($post['contenido'])); ?></p>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script>
// Contador de caracteres para el formulario de publicación
const postContent = document.getElementById('postContent');
const charCount = document.getElementById('charCount');
const postForm = document.getElementById('postForm');
const submitBtn = document.getElementById('submitBtn');

if (postContent && charCount) {
    postContent.addEventListener('input', function() {
        const count = this.value.length;
        charCount.textContent = count;
        
        if (count > 4500) {
            charCount.style.color = 'var(--pastel-red)';
        } else {
            charCount.style.color = 'var(--text-secondary)';
        }
    });
}

// Prevenir envío duplicado
if (postForm && submitBtn) {
    postForm.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<span class="loading-spinner"></span> Publicando...';
    });
}
</script>