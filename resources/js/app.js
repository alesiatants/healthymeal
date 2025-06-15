import './bootstrap';

import Alpine from 'alpinejs';
import { renderDailyCharts } from './calculator-charts';

window.Alpine = Alpine;

Alpine.start();
const weeklyPlan = window.weeklyPlan || {};
document.addEventListener('DOMContentLoaded', () => {
    if (Object.keys(weeklyPlan).length > 0) {
        renderDailyCharts(weeklyPlan);
    }
})