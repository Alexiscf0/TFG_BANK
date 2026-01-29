document.addEventListener('DOMContentLoaded', () => {
    // 1. Verificación de Rol
    fetch('../Back/comprobar_rol.php')
        .then(response => response.json())
        .then(data => {
            const linkAdmin = document.getElementById('linkAdmin');
            if (linkAdmin && data.logged_in && data.role === 'admin') {
                linkAdmin.style.display = 'block';
            }
        })
        .catch(err => console.error("Error verificando rol:", err));

    // 2. Lógica de Cerrar Sesión (para todas las páginas)
    const btnLogout = document.getElementById('btnCerrarSesion');
    if (btnLogout) {
        btnLogout.addEventListener('click', (e) => {
            e.preventDefault();
            fetch('../Back/logout.php').finally(() => {
                window.location.replace('login.html');
            });
        });
    }
});