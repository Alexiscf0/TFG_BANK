<?php
// Configuración de errores
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Sistema de seguridad
require_once '../Back/Autorizacion.php';
Prohibido();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Gestión Kibo</title>
    <style>
        :root {
            --bg: #e0e0e0;
            --card: #ffffff;
            --accent: #7986cb;
            --sidebar-bg: #7f7f7f;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: Arial, sans-serif;
            height: 100vh;
            display: flex;
            overflow: hidden;
            background: var(--bg);
        }

        /* SIDEBAR UNIFICADO */
        .sidebar {
            width: 250px;
            background-color: var(--sidebar-bg);
            color: #000;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            transition: width 0.3s ease, padding 0.3s ease;
            padding: 20px;
            white-space: nowrap;
            flex-shrink: 0;
        }
        .sidebar.cerrado { width: 0; padding: 20px 0; border: none; }

        .menu-items {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            height: 100%; /* Necesario para el margin-top: auto */
        }

        .menu-items a {
            display: block;
            text-decoration: none;
            color: black;
            font-size: 18px;
            margin-bottom: 20px;
            padding-left: 10px;
            border-left: 3px solid transparent;
        }
        .menu-items a:hover, .menu-items a.activo { font-weight: bold; border-left: 3px solid #000; }

        /* BOTÓN CERRAR SESIÓN CORREGIDO */
        .logout-box {
            margin-top: auto; /* Empuja el botón al final */
            padding-top: 20px;
            background-color: #58151c;
            border-radius: 30px;
            text-align: center;
        }
        .logout-box a {
            color: white !important;
            font-weight: bold;
            text-decoration: none;
            display: block;
            padding: 10px 0;
            border-left: 3px solid transparent !important; /* Quita el borde de los otros links */
            margin-bottom: 0 !important;
        }

        /* CONTENIDO PRINCIPAL */
        .main-content { flex: 1; padding: 20px; overflow-y: auto; display: flex; flex-direction: column; }
        .btn-menu { align-self: flex-start; font-size: 30px; cursor: pointer; margin-bottom: 20px; background: none; border: none; color: #333; }

        .admin-card {
            background: var(--card);
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            max-width: 900px;
            width: 100%;
            margin: 0 auto;
        }

        .form-crear { margin-bottom: 30px; background: #f9f9f9; padding: 15px; border-radius: 8px; border: 1px solid #eee; }
        input { padding: 10px; border: 1px solid #ddd; border-radius: 4px; margin-right: 5px; margin-bottom: 10px; width: calc(50% - 10px); }
        .btn-add { background: #2e7d32; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; font-weight: bold; width: 100%; }
        .btn-del { background: #ef5350; color: white; border: none; padding: 6px 12px; border-radius: 4px; cursor: pointer; }

        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #eee; }
        th { background-color: var(--accent); color: white; }
    </style>
</head>
<body>

<aside class="sidebar" id="miSidebar">
    <h3 style="margin-bottom: 30px; text-align: center;">Kibo App</h3>
    <nav class="menu-items">
        <a href="index.html">- Inicio</a>
        <a href="historial.html">- Historial</a>
        <a href="estadistica.html">- Estadísticas</a>
        <a href="graficos.html">- Gráficos (Hucha)</a>
        <a href="admin_usuarios.php" class="activo" style="color: #4b0082;">- Panel Admin 👤</a>

        <div class="logout-box">
            <a href="#" id="btnCerrarSesion">Cerrar Sesión</a>
        </div>
    </nav>
</aside>

<main class="main-content">
    <button class="btn-menu" onclick="alternarMenu()">&#9776;</button>

    <div class="admin-card">
        <h1 style="margin-bottom: 20px;">👥 Gestión de Usuarios</h1>

        <div class="form-crear">
            <h3>Registrar Nuevo Usuario</h3>
            <form id="formCrear">
                <input type="email" id="newEmail" placeholder="Correo electrónico" required>
                <input type="password" id="newPass" placeholder="Contraseña temporal" required>
                <button type="submit" class="btn-add">Crear Usuario</button>
            </form>
        </div>

        <input type="text" id="buscador" placeholder="Filtrar usuarios por email..." style="width:100%; padding: 10px; margin-bottom:20px;">

        <table>
            <thead>
            <tr>
                <th>Email</th>
                <th>Fecha Registro</th>
                <th>Acciones</th>
            </tr>
            </thead>
            <tbody id="listaUsuarios">
            <tr><td colspan="3">Cargando lista de usuarios...</td></tr>
            </tbody>
        </table>
    </div>
</main>

<script>
    function alternarMenu() {
        document.getElementById('miSidebar').classList.toggle('cerrado');
    }

    // Cierre de sesión unificado
    document.getElementById('btnCerrarSesion').addEventListener('click', function(e) {
        e.preventDefault();
        fetch('../Back/logout.php').finally(() => {
            window.location.replace('login.html');
        });
    });

    // Lógica de carga de usuarios
    async function cargarUsuarios() {
        try {
            const res = await fetch('../Back/get_all_users.php');
            const r = await res.json();
            if (r.status === "success") {
                renderizarTabla(r.data);
            }
        } catch (e) { console.error("Error al conectar"); }
    }

    function renderizarTabla(lista) {
        const tbody = document.getElementById('listaUsuarios');
        tbody.innerHTML = lista.map(u => `
            <tr>
                <td><strong>${u.email}</strong></td>
                <td>${u.fecha}</td>
                <td><button class="btn-del" onclick="eliminarUsuario('${u.email}')">Eliminar</button></td>
            </tr>
        `).join('');
    }

    async function eliminarUsuario(email) {
        if (!confirm(`¿Eliminar a ${email}?`)) return;
        const res = await fetch('../Back/delete_user.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ email })
        });
        const result = await res.json();
        if (result.status === "success") cargarUsuarios();
    }

    document.getElementById('formCrear').addEventListener('submit', async (e) => {
        e.preventDefault();
        const fd = new FormData();
        fd.append('email', document.getElementById('newEmail').value);
        fd.append('password', document.getElementById('newPass').value);
        const res = await fetch('../Back/create_user_admin.php', { method: 'POST', body: fd });
        const result = await res.json();
        if (result.status === "success") { e.target.reset(); cargarUsuarios(); }
        else { alert(result.message); }
    });

    document.getElementById('buscador').addEventListener('input', (e) => {
        const term = e.target.value.toLowerCase();
        const filas = document.querySelectorAll('#listaUsuarios tr');
        filas.forEach(fila => {
            fila.style.display = fila.innerText.toLowerCase().includes(term) ? '' : 'none';
        });
    });

    window.onload = cargarUsuarios;
</script>
</body>
</html>