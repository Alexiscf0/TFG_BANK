import { getMockBankingData } from './algoan_service.js';

let chartGastos, chartIngresos, chartComparativa;

function inicializarGraficas() {
    // 1. Gráfico de Gastos (Doughnut)
    chartGastos = new Chart(document.getElementById('categoryChart'), {
        type: 'doughnut',
        options: { responsive: true, maintainAspectRatio: false }
    });

    // 2. Gráfico de Ingresos (Doughnut)
    chartIngresos = new Chart(document.getElementById('incomeCategoryChart'), {
        type: 'doughnut',
        options: { responsive: true, maintainAspectRatio: false }
    });

    // 3. Gráfico Comparativo de Líneas (Ingresos vs Gastos)
    chartComparativa = new Chart(document.getElementById('weeklyChart'), {
        type: 'line',
        data: {
            labels: ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'],
            datasets: [
                {
                    label: 'Gastos (€)',
                    borderColor: '#ef5350',
                    backgroundColor: 'rgba(239, 83, 80, 0.1)',
                    fill: true, tension: 0.4, data: [0,0,0,0,0,0,0]
                },
                {
                    label: 'Ingresos (€)',
                    borderColor: '#66bb6a',
                    backgroundColor: 'rgba(102, 187, 106, 0.1)',
                    fill: true, tension: 0.4, data: [0,0,0,0,0,0,0]
                }
            ]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
}

function actualizarInterfaz(data) {
    const scoreVal = data.score || 500;
    const scoreElement = document.getElementById('creditScore');
    const msgBox = document.getElementById('scoreMessage');

    scoreElement.innerText = scoreVal;

    // Lógica de colores y mensajes para el Score
    if (scoreVal >= 700) {
        msgBox.innerText = "Salud excelente";
        msgBox.className = "msg-good";
        scoreElement.style.color = "#7986cb";
    } else if (scoreVal >= 450) {
        msgBox.innerText = "Salud estable";
        msgBox.className = "msg-good";
        scoreElement.style.color = "#ffa726";
    } else {
        msgBox.innerText = "Salud crítica";
        msgBox.className = "msg-bad";
        scoreElement.style.color = "#ef5350";
    }

    if (data.analysis && data.analysis.details) {
        const d = data.analysis.details;

        // Actualizar Gráfico Gastos
        const catG = Object.keys(d.expense_categories);
        chartGastos.data.labels = catG.length ? catG : ["Sin datos"];
        chartGastos.data.datasets = [{
            data: catG.length ? Object.values(d.expense_categories) : [1],
            backgroundColor: ['#ef5350', '#ffa726', '#a29bfe', '#74b9ff']
        }];
        chartGastos.update();

        // Actualizar Gráfico Ingresos
        const catI = Object.keys(d.income_categories);
        chartIngresos.data.labels = catI.length ? catI : ["Sin datos"];
        chartIngresos.data.datasets = [{
            data: catI.length ? Object.values(d.income_categories) : [1],
            backgroundColor: ['#66bb6a', '#43a047', '#2e7d32', '#81ecec']
        }];
        chartIngresos.update();

        // Actualizar Comparativa Semanal
        chartComparativa.data.datasets[0].data = d.expense_trend;
        chartComparativa.data.datasets[1].data = d.income_trend;
        chartComparativa.update();
    }
}

async function arranque() {
    inicializarGraficas();
    try {
        const response = await fetch('../Back/get_dashboard_data.php');
        const result = await response.json();

        if (result.status === "success") {
            actualizarInterfaz(result);
        } else if (result.message === "Sesión no iniciada") {
            window.location.href = "login.html";
        }
    } catch (e) {
        console.error("Error al cargar datos reales:", e);
        actualizarInterfaz(getMockBankingData());
    }
}

window.onload = arranque;

document.getElementById('btnSimular').addEventListener('click', () => {
    const sim = {
        score: 350,
        analysis: {
            details: {
                expense_categories: { "Deudas": 400, "Vicios": 200 },
                income_categories: { "Nómina": 800 },
                expense_trend: [100, 200, 800, 300, 150, 400, 900],
                income_trend: [0, 0, 800, 0, 0, 0, 0]
            }
        }
    };
    actualizarInterfaz(sim);
});