// Объявляем функции в глобальной области видимости
window.showActivityTooltip = function(event) {
    const select = event.target;
    const tooltip = document.getElementById('activityTooltip');
    const content = document.getElementById('tooltipContent');
    
    const activityDescriptions = {
        Умственный: "Сидячая работа, минимум физической активности (офисные работники)",
        Лёгкий: "Легкие упражнения 1-3 раза в неделю или активность в течение дня (почтальоны, учителя)",
        Средний: "Умеренные тренировки 3-5 раз в неделю (строители, обслуживающий персонал)",
        Тяжёлый: "Интенсивные тренировки 6-7 раз в неделю (профессиональные спортсмены)",
        Сверхтяжёлый: "Тяжелая физическая работа + ежедневные тренировки (грузчики, шахтеры)"
    };

    if (select.selectedOptions.length > 0) {
        const value = select.selectedOptions[0].value;
        content.textContent = activityDescriptions[value] || '';
        tooltip.classList.remove('hidden');
    }
}

window.hideActivityTooltip = function() {
    const tooltip = document.getElementById('activityTooltip');
    tooltip.classList.add('hidden');
}

// Обновляем подсказку при изменении выбора
document.getElementById('activity_level')?.addEventListener('change', function(e) {
    
    const tooltip = document.getElementById('activityTooltip');
    const content = document.getElementById('tooltipContent');
    
    const activityDescriptions = {
        Умственный: "Сидячая работа, минимум физической активности (офисные работники)",
        Лёгкий: "Легкие упражнения 1-3 раза в неделю или активность в течение дня (почтальоны, учителя)",
        Средний: "Умеренные тренировки 3-5 раз в неделю (строители, обслуживающий персонал)",
        Тяжёлый: "Интенсивные тренировки 6-7 раз в неделю (профессиональные спортсмены)",
        Сверхтяжёлый: "Тяжелая физическая работа + ежедневные тренировки (грузчики, шахтеры)"
    };

    const value = e.target.value;
    content.textContent = activityDescriptions[value] || '';
    if (!tooltip.classList.contains('hidden')) {
        tooltip.classList.add('hidden');
    }
});