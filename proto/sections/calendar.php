<?php
// sections/calendar.php

require_once 'conexion.php'; 

// Aseguramos el ID del usuario logueado
$logged_user_id = $_SESSION['usuario_id'] ?? 1; 
$tasks_by_date = [];

$conn->set_charset("utf8");

// -----------------------------------------------------------
// CONSULTA: TAREAS PENDIENTES
// -----------------------------------------------------------
$sql_calendar = "SELECT tarea_id, titulo, fecha_vencimiento, estado 
                 FROM tareas 
                 WHERE usuario_id = ? AND estado = 'Por Hacer'
                 ORDER BY fecha_vencimiento ASC";

if ($stmt_calendar = $conn->prepare($sql_calendar)) {
    $stmt_calendar->bind_param("i", $logged_user_id);
    $stmt_calendar->execute();
    $result_calendar = $stmt_calendar->get_result();
    $all_tasks = $result_calendar->fetch_all(MYSQLI_ASSOC);
    $stmt_calendar->close();
}

// -----------------------------------------------------------
// Agrupar las tareas por fecha de vencimiento
// -----------------------------------------------------------
foreach ($all_tasks as $task) {
    $date = $task['fecha_vencimiento']; 
    if (!isset($tasks_by_date[$date])) {
        $tasks_by_date[$date] = [];
    }
    $tasks_by_date[$date][] = $task;
}

$conn->close();

?>

<style>
/* Estilos básicos para la vista de calendario */
.calendar-header {
    margin-bottom: 20px;
}
.calendar-view {
    max-width: 900px;
    margin: 0 auto;
}
.calendar-day {
    border: 1px solid #ddd; /* Borde más claro */
    margin-bottom: 20px;
    border-radius: 8px;
    overflow: hidden;
    background-color: var(--bg-card, #ffffff); /* Usar variable, fallback a blanco */
}
.day-title {
    /* Color de fondo del título - Azul Fuerte */
    background-color: #3f51b5; 
    color: #ffffff; /* Texto Blanco */
    padding: 15px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}
.day-title h4 {
    margin: 0;
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 10px;
}
.tasks-list {
    padding: 0 15px;
    /* Fondo para contrastar con el texto */
    background-color: var(--bg-card, #ffffff);
}
.task-entry {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #eeeeee;
    /* Texto Negro Fuerte para alto contraste */
    color: var(--text-dark, #333333); 
}
.task-entry:last-child {
    border-bottom: none;
}
.task-icon {
    color: #f44336; /* Rojo de Alerta */
    margin-right: 10px;
}
.view-link {
    color: #673ab7; /* Morado Fuerte */
    text-decoration: none;
    font-weight: 500;
}
.past-due .day-title {
    background-color: #f44336; /* Fondo de día Vencido (Rojo) */
    color: #ffffff; /* Texto Blanco */
}
/* Estilo del badge Vencido */
.badge-past-due {
    background: #ffffff;
    color: #f44336;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 0.8em;
    font-weight: bold;
}
/* Resaltado de Hoy */
.day-title.is-today {
    background-color: #4caf50 !important; /* Verde Fuerte */
}
.badge-today {
    background: #ffffff;
    color: #4caf50;
    padding: 3px 8px;
    border-radius: 4px;
    font-size: 0.8em;
    font-weight: bold;
}

.alert-info {
    padding: 20px;
    background-color: var(--bg-card, #ffffff);
    border: 1px solid #673ab7;
    border-radius: 8px;
    text-align: center;
    color: var(--text-secondary, #666666);
}
.alert-info a {
    color: #3f51b5;
    font-weight: 700;
}
</style>

<div class="calendar-header">
    <h2>Tu Calendario de Tareas</h2>
    <p style="color: var(--text-secondary, #666666);">Las tareas pendientes se muestran agrupadas por la fecha en la que vencen.</p>
</div>

<div class="calendar-view">
    <?php if (empty($tasks_by_date)): ?>
        <div class="alert-info">
            🎉 ¡No tienes tareas pendientes que mostrar en el calendario!
            <a href="index.php?section=tasks&action=create">Crea una tarea ahora.</a>
        </div>
    <?php else: ?>
        <?php 
        // Iterar sobre las fechas y los grupos de tareas
        foreach ($tasks_by_date as $date_str => $tasks): 
            // Formatear la fecha para mostrar
            setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'es');
            $display_date = strftime('%A, %d de %B de %Y', strtotime($date_str)); 
            
            $is_today = (date('Y-m-d') === $date_str); // Para resaltar el día actual
            $is_past = strtotime($date_str) < strtotime(date('Y-m-d'));
        ?>
            <div class="calendar-day <?php echo $is_past ? 'past-due' : 'upcoming'; ?>">
                <div class="day-title <?php echo $is_today ? 'is-today' : ''; ?>">
                    <h4>
                        <i class="fas fa-calendar-alt"></i> 
                        <?php echo ucwords(htmlspecialchars($display_date)); ?>
                        <?php if ($is_past): ?>
                            <span class="badge-past-due">¡Vencido!</span>
                        <?php elseif ($is_today): ?>
                            <span class="badge-today">Hoy</span>
                        <?php endif; ?>
                    </h4>
                </div>
                <div class="tasks-list">
                    <?php foreach ($tasks as $task): ?>
                        <div class="task-entry">
                            <i class="fas fa-exclamation-circle task-icon"></i>
                            <span class="task-title">
                                <?php echo htmlspecialchars($task['titulo']); ?>
                            </span>
                            <a href="index.php?section=tasks" class="view-link">Ver Tarea</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>