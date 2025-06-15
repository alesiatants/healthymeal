import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);

export function renderNutritionChart(recipeData) {
    const ctx = document.getElementById('nutritionChart');
    if (!ctx) return;

    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Белки', 'Жиры', 'Углеводы'],
            datasets: [{
                data: [recipeData['protein'], recipeData['fat'], recipeData['carbs']],
                backgroundColor: ['#4f46e5', '#f59e0b', '#10b981'],
                borderWidth: 0
            }]
        },
        options: {
            cutout: '70%',
            plugins: { legend: { display: false } }
        }
    });
}
document.addEventListener('DOMContentLoaded', () => {
    const protein = parseFloat(document.getElementById('protein').getAttribute('data-protein-value'));
    const fat = parseFloat(document.getElementById('fat').getAttribute('data-fat-value'));
    const carbs = parseFloat(document.getElementById('carbs').getAttribute('data-carbs-value'));
    const recipeData = {
        protein: protein,
        fat: fat,
        carbs: carbs,
    };
    renderNutritionChart(recipeData);
});