<?php
require_once 'conexion.php'; 

// Usamos 'usuario_id' para ser consistentes con la sesión.
$logged_user_id = $_SESSION['usuario_id'] ?? 1; 

$tasks = [];
$teams_list = []; // Para el formulario: lista de equipos a los que puede asignar
$conn->set_charset("utf8");

// Manejo de mensajes de sesión
$notification_message = null;
if (isset($_SESSION['task_message'])) {
    $notification_message = $_SESSION['task_message'];
    unset($_SESSION['task_message']); 
}

// ----------------------------------------------------
// Determinar la vista actual y cargar datos
// ----------------------------------------------------
$current_action = $_GET['action'] ?? 'list'; 

// Cargamos la lista de tareas y la lista de equipos si es necesario
if ($current_action === 'list' || $current_action === 'create') {
    
    // Consulta para obtener tareas del usuario (sin prioridad)
    $sql = "SELECT t.tarea_id, t.titulo, t.descripcion, t.fecha_vencimiento, t.estado, e.nombre_equipo 
            FROM tareas t 
            LEFT JOIN equipos e ON t.equipo_id = e.equipo_id 
            WHERE t.usuario_id = ? 
            ORDER BY t.estado ASC, t.fecha_vencimiento ASC";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $logged_user_id);
        $stmt->execute();
        $tasks = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
        $stmt->close();
    }
    
    // Cargamos la lista de equipos para el <select> si vamos a crear
    if ($current_action === 'create') {
        $sql_teams = "SELECT equipo_id, nombre_equipo FROM equipos WHERE usuario_id = ?";
        if ($stmt_teams = $conn->prepare($sql_teams)) {
            $stmt_teams->bind_param("i", $logged_user_id);
            $stmt_teams->execute();
            $result_teams = $stmt_teams->get_result();
            while($row_team = $result_teams->fetch_assoc()) {
                $teams_list[$row_team['equipo_id']] = $row_team['nombre_equipo'];
            }
            $stmt_teams->close();
        }
    }
}
$conn->close();
?>

<div class="card">
    
    <?php 
    // --- Mostrar la notificación si existe ---
    if ($notification_message): 
        $bg_color = ($notification_message['type'] === 'success') ? 'var(--pastel-green)' : 'var(--pastel-red)';
        $text_color = 'var(--text-dark)';
        ?>
        <div style="padding: 15px; margin-bottom: 20px; border-radius: 10px; background-color: <?php echo $bg_color; ?>; color: <?php echo $text_color; ?>; font-weight: 600; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
            <i class="fas fa-check-circle" style="margin-right: 10px;"></i>
            <?php echo htmlspecialchars($notification_message['text']); ?>
        </div>
    <?php endif; ?>


    <?php if ($current_action === 'create'): ?>
        
        <h3>Crear Nueva Tarea</h3>
        <p style="color: var(--text-secondary); margin-bottom: 30px;">Añade un nuevo pendiente a tu lista de estudio.</p>

        <form action="index.php?section=tasks" method="POST">
            <input type="hidden" name="action" value="create_submit"> 
            
            <div style="margin-bottom: 15px;">
                <label for="titulo">Título de la Tarea</label>
                <input type="text" id="titulo" name="titulo" required>
            </div>
            
            <div style="margin-bottom: 15px;">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="3"></textarea>
            </div>

            <div style="display: flex; gap: 20px; margin-bottom: 30px;">
                <div style="flex: 1;">
                    <label for="vencimiento">Fecha de Vencimiento</label>
                    <input type="date" id="vencimiento" name="fecha_vencimiento" value="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                </div>
                <div style="flex: 1;">
                    <label for="equipo_id">Asignar a Equipo (Opcional)</label>
                    <select id="equipo_id" name="equipo_id">
                        <option value>-- Ninguno --</option>



                
                        <?php foreach($teams_list as $id => $name): ?>
                            <option value="<?php echo $id; ?>"><?php echo htmlspecialchars($name); ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <button type="submit" class="action-btn" 
                    style="background-color: var(--pastel-green); color: var(--text-dark); width: 100%; padding: 15px; border: none; border-radius: 10px; font-weight: 700; cursor: pointer;">
                <i class="fas fa-save"></i> Guardar Tarea
            </button>
            
            <a href="index.php?section=tasks" class="action-btn" 
               style="display: block; text-align: center; margin-top: 15px; background-color: var(--pastel-purple); color: var(--text-light); width: 100%; padding: 15px; border-radius: 10px; font-weight: 700; text-decoration: none;">
                <i class="fas fa-arrow-left"></i> Volver a la Lista
            </a>
        </form>

    <?php else: ?>
        
        <h3 style="margin-bottom: 5px;">Mis Tareas Pendientes</h3>
        <p style="color: var(--text-secondary); margin-bottom: 30px;"></p>

        <a href="index.php?section=tasks&action=create" class="action-btn" 
            style="background-color: var(--pastel-blue); color: var(--text-light); padding: 12px 20px; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; display: inline-block; margin-bottom: 25px; text-decoration: none;">
            <i class="fas fa-plus-circle"></i> Añadir Nueva Tarea
        </a>
        
        <div class="tasks-list" style="display: flex; flex-direction: column; gap: 10px;">
            
            <?php if (empty($tasks)): ?>
                <p style="color: var(--text-secondary); padding: 20px; border: 1px dashed var(--border-color); border-radius: 10px;">¡No tienes tareas pendientes! Tómate un respiro o crea una nueva.</p>
            <?php else: ?>
                <?php foreach ($tasks as $task): 
                    
                    // Asignar color según el estado
                    $border_color = ($task['estado'] === 'Completada') ? 'var(--pastel-green)' : 'var(--pastel-red)';
                    $is_completed = $task['estado'] === 'Completada';
                    $task_style = $is_completed ? 'opacity: 0.6; text-decoration: line-through; background-color: var(--bg-body-hover);' : '';
                    
                    ?>
                    
                    <div class="task-item" 
                         style="display: flex; justify-content: space-between; align-items: center; background-color: var(--bg-body); border-radius: 10px; padding: 15px; box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05); border-left: 5px solid <?php echo $border_color; ?>; <?php echo $task_style; ?>">
                        
                        <div style="flex-grow: 1; padding-right: 15px;">
                            <h4 style="margin: 0; font-size: 1.1em; color: var(--text-dark);"><?php echo htmlspecialchars($task['titulo']); ?></h4>
                            <p style="margin: 5px 0 0; font-size: 0.85em; color: var(--text-secondary);"><?php echo htmlspecialchars($task['descripcion']); ?></p>
                            <?php if ($task['nombre_equipo']): ?>
                                <span style="font-size: 0.75em; padding: 3px 8px; border-radius: 5px; background-color: var(--pastel-blue)55; color: var(--text-dark); margin-top: 5px; display: inline-block;">
                                    <i class="fas fa-users"></i> <?php echo htmlspecialchars($task['nombre_equipo']); ?>
                                </span>
                            <?php endif; ?>
                        </div>

                        <div style="text-align: right; font-size: 0.9em; padding-right: 15px; border-right: 1px solid var(--border-color);">
                            <p style="margin: 0; color: var(--text-dark); font-weight: 600;">Vence: <?php echo date("d M", strtotime($task['fecha_vencimiento'])); ?></p>
                            <p style="margin: 5px 0 0; color: <?php echo $border_color; ?>;"><?php echo htmlspecialchars($task['estado']); ?></p>
                        </div>
                        
                        <div style="padding-left: 15px; display: flex; gap: 10px;">
                            <?php if (!$is_completed): ?>
                                <a href="index.php?section=tasks&action=complete&id=<?php echo $task['tarea_id']; ?>" 
                                   title="Marcar como Completada"
                                   class="action-btn"
                                   style="color: var(--pastel-green); font-size: 1.2em; text-decoration: none;">
                                    <i class="fas fa-check-circle"></i>
                                </a>
                            <?php else: ?>
                                <a href="index.php?section=tasks&action=reopen&id=<?php echo $task['tarea_id']; ?>" 
                                   title="Reabrir Tarea"
                                   class="action-btn"
                                   style="color: var(--pastel-yellow); font-size: 1.2em; text-decoration: none;">
                                    <i class="fas fa-undo"></i>
                                </a>
                            <?php endif; ?>
                            
                            <a href="index.php?section=tasks&action=delete&id=<?php echo $task['tarea_id']; ?>" 
                               title="Eliminar Tarea"
                               class="action-btn"
                               style="color: var(--pastel-red); font-size: 1.2em; text-decoration: none;"
                               onclick="return confirm('¿Seguro que quieres eliminar esta tarea?');">
                                <i class="fas fa-trash-alt"></i>
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
        
    <?php endif; ?>

</div>