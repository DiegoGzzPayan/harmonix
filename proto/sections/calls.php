<style>
/* Estilos para la cuadrícula de llamadas */
.calls-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 15px;
}
.call-action-card {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 20px 10px;
    border-radius: 12px;
    text-align: center;
    text-decoration: none;
    background-color: var(--pastel-green, #d1e7d8);
    color: var(--text-dark, #4c4c4c);
    transition: background-color 0.2s, transform 0.2s;
}
.call-action-card i {
    font-size: 2.5em;
    margin-bottom: 10px;
}
.call-action-card h4 {
    margin: 0;
    font-size: 1em;
}
.call-action-card:hover {
    transform: translateY(-2px);
    opacity: 0.9;
}
.team-detail-card {
    padding: 15px;
    border-radius: 8px;
    background-color: var(--bg-task-item, #f8f8f8);
    border: 1px solid var(--border-color);
}
.team-detail-card p i {
    margin-right: 8px;
    color: var(--pastel-blue);
}
</style>

<div class="card">
    <h3>Videollamadas del Equipo</h3>
    <p style="margin-bottom: 20px;">Inicia una nueva reunión o únete a una existente con tus compañeros.</p>
    
    <div class="calls-grid">
        <a href="https://meet.google.com/new" target="_blank" class="call-action-card" style="background-color: var(--pastel-green);">
            <i class="fas fa-video"></i>
            <h4>Iniciar Videollamada</h4>
        </a>
        
        <a href="#" class="call-action-card" id="join-call-btn" style="background-color: var(--pastel-blue); color: var(--text-light);">
            <i class="fas fa-handshake" style="color: var(--text-light);"></i>
            <h4 style="color: var(--text-light);">Unirse a una Reunión</h4>
        </a>
    </div>
    
    <div class="team-detail-card" style="margin-top: 20px;">
        <h4>Próxima Reunión Programada</h4>
        <p><i class="fas fa-clock"></i> **Hoy a las 18:00h**</p>
        <p><i class="fas fa-users"></i> Proyecto 1</p>
        <a href="https://meet.google.com/abc-defg-hij" target="_blank" style="display: inline-block; margin-top: 10px; padding: 5px 10px; background-color: var(--pastel-yellow); color: var(--text-dark); border-radius: 5px; text-decoration: none; font-size: 0.9em;">
             Unirse Ahora
        </a>
    </div>
</div>

<script>
document.getElementById('join-call-btn').addEventListener('click', function(e) {
    e.preventDefault(); // Evita que el '#' de href recargue la página
    
    // Pide al usuario que ingrese el código de la reunión
    let meetCode = prompt('Ingresa el código de la reunión (ej: abc-defg-hij):');
    
    if (meetCode) {
        // Limpia el código de espacios y guiones innecesarios, y lo pasa a minúsculas
        meetCode = meetCode.trim().toLowerCase();
        
        // Forma la URL de Google Meet
        const meetUrl = `https://meet.google.com/${meetCode}`;
        
        // Abre la URL en una nueva pestaña
        window.open(meetUrl, '_blank');
    }
});
</script>