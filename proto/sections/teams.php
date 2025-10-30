<?php
require_once 'conexion.php'; 

$logged_user_id = $_SESSION['usuario_id'] ?? 1; 

$teams = [];
$conn->set_charset("utf8");

// Manejo de mensajes de sesión
$notification_message = null;
if (isset($_SESSION['team_message'])) {
    $notification_message = $_SESSION['team_message'];
    unset($_SESSION['team_message']); 
}

// ----------------------------------------------------
// Determinar la vista actual (list, create, o details)
// ----------------------------------------------------
$current_action = $_GET['action'] ?? 'list'; 
$team_id = (int)($_GET['id'] ?? 0);
$team_detail = null; // Variable para almacenar los datos del equipo específico

if ($current_action === 'list') {
    // --- Lógica para leer TODOS los equipos del usuario ---
    $sql = "SELECT equipo_id, nombre_equipo, descripcion, fecha_creacion FROM equipos WHERE usuario_id = ? ORDER BY nombre_equipo ASC";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $logged_user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $color_map = [ 1 => 'var(--pastel-blue)', 2 => 'var(--pastel-green)', 3 => 'var(--pastel-yellow)', 4 => 'var(--pastel-red)'];
            $color_index = ($row['equipo_id'] % 4) + 1;
            
            $teams[] = [
                'id' => $row['equipo_id'],
                'name' => $row['nombre_equipo'],
                'description' => $row['descripcion'],
                'creation_date' => date("d M Y", strtotime($row['fecha_creacion'])),
                'color' => $color_map[$color_index]
            ];
        }
        $stmt->close();
    }
}
elseif ($current_action === 'details' && $team_id > 0) {
    // --- Lógica para leer SOLO el equipo solicitado ---
    $sql = "SELECT equipo_id, nombre_equipo, descripcion, usuario_id, fecha_creacion FROM equipos WHERE equipo_id = ? AND usuario_id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ii", $team_id, $logged_user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        // Asignar el resultado a $team_detail
        $team_detail = $result->fetch_assoc();
        $stmt->close();
    }
    
}

$conn->close();
?>

<div class="card">
    
    <?php 
    // Mostrar la notificación si existe
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
        
        <h3>Crear Nuevo Equipo</h3>
        <p style="color: var(--text-secondary); margin-bottom: 30px;">Define el nombre y la descripción de tu nuevo equipo de trabajo.</p>

        <form action="index.php?section=teams" method="POST">
            <input type="hidden" name="action" value="create_submit"> 
            
            <div style="margin-bottom: 20px;">
                <label for="nombre">Nombre del Equipo</label>
                <input type="text" id="nombre" name="nombre_equipo" required>
            </div>

            <div style="margin-bottom: 30px;">
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion" rows="4" required></textarea>
            </div>
            
            <button type="submit" class="action-btn" 
                    style="background-color: var(--pastel-green); color: var(--text-dark); width: 100%; padding: 15px; border: none; border-radius: 10px; font-weight: 700; cursor: pointer;">
                <i class="fas fa-plus"></i> Guardar Equipo
            </button>
            
            <a href="index.php?section=teams" class="action-btn" 
               style="display: block; text-align: center; margin-top: 15px; background-color: var(--pastel-red); color: var(--text-light); width: 100%; padding: 15px; border-radius: 10px; font-weight: 700; text-decoration: none;">
                <i class="fas fa-arrow-left"></i> Cancelar
            </a>
        </form>

    <?php elseif ($current_action === 'details' && $team_detail): ?>
        
        <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid var(--pastel-blue); padding-bottom: 15px; margin-bottom: 25px;">
            <h3 style="margin: 0; color: var(--text-dark);"><i class="fas fa-users-cog" style="color: var(--pastel-blue); margin-right: 10px;"></i> Detalles del Equipo: <?php echo htmlspecialchars($team_detail['nombre_equipo']); ?></h3>
            <a href="index.php?section=teams" class="action-btn" 
               style="background-color: var(--pastel-purple); color: var(--text-light); padding: 10px 15px; border-radius: 8px; font-weight: 600; text-decoration: none;">
                <i class="fas fa-arrow-left"></i> Volver a Equipos
            </a>
        </div>
        
        <div style="padding: 10px 0;">
            <p style="color: var(--text-secondary); margin-bottom: 5px;">Descripción del Proyecto:</p>
            <p style="font-size: 1.1em; padding: 15px; border: 1px solid var(--border-color); border-radius: 10px; background-color: var(--bg-body);">
                <?php echo nl2br(htmlspecialchars($team_detail['descripcion'])); ?>
            </p>
        </div>

        <div style="display: flex; justify-content: space-between; margin-top: 20px;">
            <div style="flex: 1; padding-right: 15px;">
                <p style="color: var(--text-secondary); margin-bottom: 5px;">Creador:</p>
                <p style="font-size: 1.1em; font-weight: 700; color: var(--text-dark);"><?php echo htmlspecialchars($_SESSION['nombre_usuario'] ?? 'Tú'); ?></p>
            </div>
            <div style="flex: 1;">
                <p style="color: var(--text-secondary); margin-bottom: 5px;">Fecha de Creación:</p>
                <p style="font-size: 1.1em; font-weight: 700; color: var(--text-dark);"><?php echo date("d/M/Y", strtotime($team_detail['fecha_creacion'])); ?></p>
            </div>
        </div>
        
        

        <div style="display: flex; justify-content: flex-end; gap: 15px; margin-top: 20px;">
            <a href="index.php?section=teams&action=delete&id=<?php echo htmlspecialchars($team_detail['equipo_id']); ?>" 
               class="action-btn" 
               style="flex: 0 1 200px; text-align: center; background-color: #ff6961; color: var(--text-light); padding: 12px; border-radius: 10px; text-decoration: none; font-weight: 700;"
               onclick="return confirm('⚠️ ¿Estás absolutamente seguro de que quieres eliminar este equipo? Esta acción es IRREVERSIBLE.');">
                <i class="fas fa-trash-alt"></i> Eliminar Equipo
            </a>
        </div>


    <?php elseif ($current_action === 'details' && $team_detail): ?>


    <?php else: ?>
        
        <h3 style="margin-bottom: 5px;">Mis Equipos y Proyectos</h3>
        <p style="color: var(--text-secondary); margin-bottom: 30px;"></p>

        <a href="index.php?section=teams&action=create" class="action-btn" 
            style="background-color: var(--pastel-purple); color: var(--text-light); padding: 12px 20px; border: none; border-radius: 10px; font-weight: 600; cursor: pointer; display: inline-block; margin-bottom: 25px; text-decoration: none;">
            <i class="fas fa-plus-circle"></i> Crear Nuevo Equipo
        </a>
        
        <div class="teams-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 20px;">
            
            <?php if (empty($teams)): ?>
                <p style="grid-column: 1 / -1; color: var(--text-secondary);">Aún no tienes equipos creados. ¡Crea uno nuevo!</p>
            <?php else: ?>
                <?php foreach ($teams as $team): ?>
                    
                    <div class="team-card" 
                         style="background-color: var(--bg-task-item); border-radius: 15px; padding: 20px; box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05); border-left: 5px solid <?php echo $team['color']; ?>;">
                        
                        <h4 style="color: var(--text-dark); margin: 0; font-size: 1.2em; margin-bottom: 10px;"><?php echo htmlspecialchars($team['name']); ?></h4>
                        
                        <p style="margin: 0; color: var(--text-dark); font-size: 0.9em; min-height: 40px;"><?php echo htmlspecialchars($team['description']); ?></p>

                        <div style="margin-top: 15px; margin-bottom: 15px; border-top: 1px solid var(--border-color); padding-top: 10px;">
                            <p style="margin: 5px 0; color: var(--text-secondary); font-size: 0.8em;"><i class="fas fa-calendar-alt" style="margin-right: 5px;"></i> Creado el: <strong><?php echo $team['creation_date']; ?></strong></p>
                        </div>

                        <a href="index.php?section=teams&action=details&id=<?php echo $team['id']; ?>" 
                           class="action-btn"
                           style="display: block; text-align: center; padding: 10px; background-color: var(--pastel-blue); color: var(--text-light); border-radius: 8px; font-weight: 600; text-decoration: none;">
                            <i class="fas fa-eye"></i> Ver Detalles
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>
        
    <?php endif; ?>

</div>