import { Chart, registerables } from 'chart.js';
Chart.register(...registerables);
export function renderDailyCharts(weeklyPlan) {
    const ctx = document.getElementById('combinedChart');
    if (!ctx) return;
    const days = Object.keys(weeklyPlan);
    const mealTypes = Object.keys(weeklyPlan[days[0]].meals);
    const datasets = mealTypes.map((meal, i) => ({
        label: meal.charAt(0).toUpperCase() + meal.slice(1),
        data: days.map(day => weeklyPlan[day].meals[meal]),
        backgroundColor: [
            'rgba(197, 34, 34, 0.6)',
            'rgba(46, 134, 171, 0.6)',
            'rgba(245, 213, 71, 0.6)',
            'rgba(106, 141, 115, 0.6)',
            'rgba(102, 51, 153, 0.6)' ][i],
        borderColor: [
            'rgba(197, 34, 34, 1)',
            'rgba(46, 134, 171, 1)',
            'rgba(245, 213, 71, 1)',
            'rgba(106, 141, 115, 1)',
            'rgba102, 51, 153, 1)' ][i],
        borderWidth: 1,
        type: 'bar'}));
    const lineDatasets = mealTypes.map((meal, i) => ({
        label: `${meal.charAt(0).toUpperCase() + meal.slice(1)} линия`,
        data: days.map(day => weeklyPlan[day].meals[meal]),
        borderColor: [
            'rgba(197, 34, 34, 1)',
            'rgba(46, 134, 171, 1)',
            'rgba(245, 213, 71, 1)',
            'rgba(106, 141, 115, 1)',
            'rgba(102, 51, 153, 1)' ][i],
        borderWidth: 2,fill: false, type: 'line' }));
    new Chart(ctx, {
        data: {labels: days,datasets: [...datasets, ...lineDatasets]},
        options: { animation: {duration: 4000,easing: 'easeInOutQuart'},
            responsive: true,
            scales: { x: {stacked: true,},y: { stacked: true,beginAtZero: true,title: {
                        display: true,
                        text: 'Калории'}} },
            plugins: {
                tooltip: {callbacks: {
                        afterBody: (context) => {
                            const day = context[0].label;
                            return `Всего: ${weeklyPlan[day].total} ккал`;}}},
                legend: {
                    position: 'bottom',}}}});}
