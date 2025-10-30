<?php
// sections/dashboard.php

require_once 'conexion.php'; 

$logged_user_id = $_SESSION['usuario_id'] ?? 1; 

$recent_tasks = [];
$user_teams = [];

$conn->set_charset("utf8");

// -----------------------------------------------------------
// 1. CONSULTA: TAREAS PENDIENTES (Máximo 5)
// -----------------------------------------------------------

// Buscamos tareas no completadas, ordenadas por fecha de vencimiento.
$sql_tasks = "SELECT tarea_id, titulo, fecha_vencimiento, estado 
              FROM tareas 
              WHERE usuario_id = ? AND estado = 'Por Hacer' 
              ORDER BY fecha_vencimiento ASC 
              LIMIT 5";

if ($stmt_tasks = $conn->prepare($sql_tasks)) {
    $stmt_tasks->bind_param("i", $logged_user_id);
    $stmt_tasks->execute();
    $result_tasks = $stmt_tasks->get_result();
    $recent_tasks = $result_tasks->fetch_all(MYSQLI_ASSOC);
    $stmt_tasks->close();
}


// -----------------------------------------------------------
// 2. CONSULTA: EQUIPOS DEL USUARIO (Máximo 4)
// -----------------------------------------------------------

// Buscamos los equipos creados por el usuario
$sql_teams = "SELECT equipo_id, nombre_equipo FROM equipos WHERE usuario_id = ? LIMIT 4";

if ($stmt_teams = $conn->prepare($sql_teams)) {
    $stmt_teams->bind_param("i", $logged_user_id);
    $stmt_teams->execute();
    $result_teams = $stmt_teams->get_result();
    $user_teams = $result_teams->fetch_all(MYSQLI_ASSOC);
    $stmt_teams->close();
}

$conn->close();

?>

<style>
/* Estilos básicos para el dashboard (puedes mover esto a tu CSS principal) */
.dashboard-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 30px;
}
.dashboard-grid .card {
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 8px 15px rgba(0, 0, 0, 0.05);
}

/* Tareas */
.task-list {
    display: flex;
    flex-direction: column;
    gap: 12px;
    margin-top: 20px;
}
.task-item {
    padding: 12px 15px;
    border-radius: 8px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-left: 5px solid;
    transition: background-color 0.2s;
}
.task-item:hover {
    background-color: var(--bg-body-hover);
}
.task-item .task-title {
    font-weight: 600;
}
.in-progress {
    border-left-color: var(--pastel-red); /* Tareas Pendientes */
}
.completed {
    border-left-color: var(--pastel-green); /* Tareas Completadas */
    opacity: 0.6;
}

/* Equipos */
.team-list {
    list-style: none;
    padding: 0;
    margin-top: 20px;
}
.team-item {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 10px 15px;
    border-radius: 8px;
    background-color: var(--pastel-blue);
    color: var(--text-dark);
    margin-bottom: 10px;
    cursor: pointer;
    transition: transform 0.2s;
}
.team-item:hover {
    transform: translateX(5px);
}
.team-icon {
    font-size: 1.2em;
}
</style>

<div class="dashboard-grid">
    
    <div class="card">
        <h3>Tus Próximas Tareas</h3>
        <div class="task-list">
            
            <?php if (empty($recent_tasks)): ?>
                <div class="task-item" style="border-left: 5px solid var(--pastel-purple); color: var(--text-secondary); display: block;">
                    ¡Todo tranquilo por ahora!
                    <a href="index.php?section=tasks&action=create" style="color: var(--pastel-blue); text-decoration: none; font-weight: 700; display: block; margin-top: 5px;">Crea una nueva tarea.</a>
                </div>
            <?php else: ?>
                <?php foreach ($recent_tasks as $task): ?>
                    <a href="index.php?section=tasks" class="task-item in-progress" style="text-decoration: none; color: inherit;">
                        <span class="task-title"><?php echo htmlspecialchars($task['titulo']); ?></span>
                        <div class="task-info">Vence: <?php echo date("d/m", strtotime($task['fecha_vencimiento'])); ?></div>
                    </a>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        
        <?php if (!empty($recent_tasks)): ?>
            <a href="index.php?section=tasks" style="display: block; text-align: center; margin-top: 20px; color: var(--pastel-blue); text-decoration: none; font-weight: 600;">Ver todas las tareas <i class="fas fa-arrow-right"></i></a>
        <?php endif; ?>

    </div>

    <div class="card">
        <h3>Tus Equipos</h3>
        <ul class="team-list">
            <?php if (empty($user_teams)): ?>
                 <li class="team-item" style="background-color: var(--bg-body-hover); color: var(--text-secondary); display: block;">
                    Aún no has creado equipos.
                    <a href="index.php?section=teams&action=create" style="color: var(--pastel-purple); text-decoration: none; font-weight: 700; display: block; margin-top: 5px;">¡Crea uno ahora!</a>
                 </li>
            <?php else: ?>
                <?php 
                $team_icons = ['fas fa-users', 'fas fa-rocket', 'fas fa-flask', 'fas fa-puzzle-piece'];
                $i = 0;
                foreach ($user_teams as $team): 
                ?>
                    <a href="index.php?section=teams&action=details&id=<?php echo htmlspecialchars($team['equipo_id']); ?>" style="text-decoration: none;">
                        <li class="team-item">
                            <span class="team-icon <?php echo $team_icons[$i % count($team_icons)]; ?>"></span>
                            <span class="team-name"><?php echo htmlspecialchars($team['nombre_equipo']); ?></span>
                        </li>
                    </a>
                <?php 
                $i++;
                endforeach; 
                ?>
            <?php endif; ?>
        </ul>
        
        <?php if (count($user_teams) === 4): ?>
             <a href="index.php?section=teams" style="display: block; text-align: center; margin-top: 20px; color: var(--pastel-blue); text-decoration: none; font-weight: 600;">Ver todos los equipos <i class="fas fa-arrow-right"></i></a>
        <?php endif; ?>
    </div>
</div>