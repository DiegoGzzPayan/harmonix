<?php
// sections/settings_preferences.php
?>

<style>
/* Estilos solo para esta sección */
.preferences-card {
    max-width: 500px;
    margin: 0 auto 30px;
}
.setting-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid var(--border-color); 
    color: var(--text-dark);
}
.setting-item:last-child {
    border-bottom: none;
}
.setting-item span {
    font-weight: 500;
}
.setting-item button, .setting-item select {
    padding: 8px 15px;
    border: none;
    border-radius: 5px;
    color: var(--text-light, #fff);
    cursor: pointer;
    font-weight: 600;
}
</style>

<div class="card preferences-card">
    <h3>Preferencias de la Aplicación</h3>

    
    <div class="setting-item">
        <span>Modo Oscuro</span>
        <i id="dark-mode-toggle" class="fas fa-toggle-off" style="color: var(--border-color); font-size: 1.5em; cursor: pointer;"></i>
    </div>
    
    <div class="setting-item">
        <span>Formato de Fecha</span>
        <select style="background-color: var(--pastel-purple); color: var(--text-light);">
            <option>dd/mm/yyyy</option>
            <option>mm/dd/yyyy</option>
            <option>yyyy-mm-dd</option>
        </select>
    </div>

    
    <div class="setting-item">
        <span>Cambiar Contraseña</span>
        <button style="background-color: var(--pastel-blue);" onclick="alert('Redirigiendo a cambio de contraseña.')">Cambiar</button>
    </div>
    
