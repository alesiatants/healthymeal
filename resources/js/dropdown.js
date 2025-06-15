let isAnimating = false; // Флаг для отслеживания анимации

function toggleDropdown(event) {
    if (isAnimating) return; // Если анимация выполняется, игнорируем клик
    isAnimating = true; // Блокируем клики

    event.preventDefault();
    const dropdown = event.target.closest('li').querySelector('.dropdown');
    const arrow = event.target.closest('li').querySelector('.arrow');
    const items = dropdown.querySelectorAll('li');

    // Переключаем видимость подменю
    if (dropdown.classList.contains('hidden')) {
        dropdown.classList.remove('hidden');
        dropdown.style.maxHeight = dropdown.scrollHeight + 'px'; // Плавное раскрытие

        // Плавное появление пунктов с задержкой (сверху вниз)
        items.forEach((item, index) => {
            setTimeout(() => {
                item.classList.remove('opacity-0');
            }, index * 100); // Задержка для каждого пункта
        });

        // Вращаем стрелочку
        arrow.classList.add('rotate-180');

        // Разблокируем клики после завершения анимации
        setTimeout(() => {
            isAnimating = false;
        }, items.length * 100 + 300); // Время анимации + запас
    } else {
        // Плавное скрытие пунктов с задержкой (снизу вверх)
        Array.from(items).reverse().forEach((item, index) => {
            setTimeout(() => {
                item.classList.add('opacity-0');
            }, index * 100); // Задержка для каждого пункта
        });

        // Плавное скрытие подменю
        setTimeout(() => {
            dropdown.style.maxHeight = '0';
            setTimeout(() => dropdown.classList.add('hidden'), 200); // Ждем завершения анимации
        }, items.length * 100); // Ждем завершения анимации пунктов

        // Вращаем стрелочку
        arrow.classList.remove('rotate-180');

        // Разблокируем клики после завершения анимации
        setTimeout(() => {
            isAnimating = false;
        }, items.length * 100 + 100); // Время анимации + запас
    }
}

// Назначаем обработчик событий после загрузки DOM
document.addEventListener('DOMContentLoaded', function () {
    const dropdownToggles = document.querySelectorAll('[data-dropdown-toggle]');
    const activeCategory = document.querySelector('.active');
    if (activeCategory) {
        const dropdown = activeCategory.closest('.dropdown');
        const toggle = dropdown.previousElementSibling;
        
        if (dropdown && toggle) {
            dropdown.classList.remove('hidden');
            toggle.querySelector('.arrow').classList.add('rotate-180');
        }
    }
    dropdownToggles.forEach((toggle) => {
        toggle.addEventListener('click', toggleDropdown);
    });
});
