// dashboard.js
import { fetchBankingData, getMockBankingData } from './algoan_service.js';

let chartCategorias;
let chartHistorial;

// 1. Inicializar gráficas con estados de carga
function inicializarGraficas() {
    const ctxCat = document.getElementById('categoryChart').getContext('2d');
    chartCategorias = new Chart(ctxCat, {
        type: 'doughnut',
        data: { labels: ['Cargando...'], datasets: [{ data: [1], backgroundColor: ['#dfe6e9'] }] }
    });

    const ctxHist = document.getElementById('weeklyChart').getContext('2d');
    chartHistorial = new Chart(ctxHist, {
        type: 'line',
        data: {
            labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
            datasets: [{
                label: 'Gasto (€)',
                data: [0, 0, 0, 0, 0, 0, 0],
                borderColor: '#00b894',
                backgroundColor: 'rgba(0, 184, 148, 0.1)',
                fill: true,
                tension: 0.4
            }]
        }
    });
}

// 2. Lógica para los consejos de Salud Financiera
function actualizarConsejo(score) {
    const msgBox = document.getElementById('scoreMessage');
    if (score >= 700) {
        msgBox.innerText = "¡Excelente! Estás gestionando tus finanzas como un experto.";
        msgBox.className = "msg-good";
    } else if (score >= 500) {
        msgBox.innerText = "Buen trabajo, pero intenta reducir gastos hormiga este mes.";
        msgBox.className = "msg-mid";
    } else {
        msgBox.innerText = "Atención: Tus gastos superan el límite recomendado. ¡Ahorra!";
        msgBox.className = "msg-bad";
    }
}

// 3. Actualizar toda la interfaz
function actualizarInterfaz(data) {
    const scoreVal = data.score || 650;
    document.getElementById('creditScore').innerHTML = `${scoreVal} <span class="label">Puntos</span>`;

    actualizarConsejo(scoreVal);

    if (data.analysis && data.analysis.details) {
        // Actualizar Quesito
        const cats = data.analysis.details.category_distribution;
        chartCategorias.data.labels = Object.keys(cats);
        chartCategorias.data.datasets[0].data = Object.values(cats);
        chartCategorias.data.datasets[0].backgroundColor = ['#55efc4', '#81ecec', '#74b9ff', '#a29bfe', '#ff7675'];
        chartCategorias.update();

        // Actualizar Historial
        chartHistorial.data.datasets[0].data = data.analysis.details.weekly_trend || [0,0,0,0,0,0,0];
        chartHistorial.update();
    }
}

// 4. Arranque de la app
async function arranque() {
    inicializarGraficas();
    try {
        let datos = await fetchBankingData();
        if (!datos || !datos.analysis) datos = getMockBankingData();
        actualizarInterfaz(datos);
    } catch (e) { actualizarInterfaz(getMockBankingData()); }
}

window.onload = arranque;

// Botón de simulación para la demo del TFG
document.getElementById('btnSimular').addEventListener('click', () => {
    // Simulamos un gasto grande que baja la salud financiera
    actualizarInterfaz({
        score: 420,
        analysis: {
            details: {
                category_distribution: { "Deudas": 600, "Ocio": 400 },
                weekly_trend: [200, 300, 450, 100, 50, 800, 900]
            }
        }
    });
    alert("Demo: Se ha registrado un gasto excesivo. Observa cómo cambia el mensaje de salud.");
});