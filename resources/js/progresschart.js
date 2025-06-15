import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

export function renderPlanChart() {
    const ctx = document.getElementById('weightProgressChart').getContext('2d');
    if (!ctx) return
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dateLabels.reverse(),
            datasets: [{
                label: 'Вес (кг)',
                data: weightData.reverse(),
                borderColor: '#4f46e5',
                backgroundColor: 'rgba(79, 70, 229, 0.1)',
                tension: 0.3,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                tooltip: {
                    callbacks: {
                        afterBody: function (context) {
                            const index = context[0].dataIndex;
                            return [
                                `Цель: ${
                                    goals[index]
                                        }`,
                                `Активность: ${
                                    activities[index]
                                        }`,
                               `Приемов пищи: ${
                                    meals[index]
                                        }`
                            ];
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: false,
                    min: Math.min(...weightData) - 5,
                    max: Math.max(...weightData) + 5
                }
            }
        }
    });
}
document.addEventListener('DOMContentLoaded', () => {
    if (planData.length !== 0 ){
        renderPlanChart();
    }
    if (daysSinceUpdate > 30) {
        showNotification(
            'Давно не обновляли данные! Хотите зафиксировать текущие достижения?',
            'info'
        );
    }
});

export function showNotification(message, type) {
    const colors = {
        info: 'bg-blue-200 text-blue-800 border-blue-200',
        warning: 'bg-yellow-200 text-yellow-800 border-yellow-200',
        success: 'bg-green-200 text-green-800 border-green-200'
    };

    const notification = document.createElement('div');
    notification.className = `p-4 mb-4 border rounded-lg ${
        colors[type]
    } flex items-start`;
    notification.innerHTML = `<svg class = "w-5 h-5 mr-3 mt-0.5 flex-shrink-0"
    fill = "none"
    stroke = "currentColor"
    viewBox = "0 0 24 24" >
        <path stroke-linecap = "round"
    stroke-linejoin = "round"
    stroke-width = "2"
    d = "${type === 'info' ? 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' : type === 'warning' ? 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'}" >
        </path> </svg> <div>
        <p class = "font-medium" > ${message}</p>
    ${type === 'warning' ?
            '<div class="mt-2 flex space-x-2"><button type="button" class="text-sm underline">Изменить цель</button><button type="button" class="text-sm underline">Оставить текущую</button></div>' :
            ''
    } </div>`;

    document.getElementById('planNotifications').appendChild(notification);
}