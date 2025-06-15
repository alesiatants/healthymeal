document.addEventListener('DOMContentLoaded', function() {
    const portionCountElement = document.getElementById('portion-count');
    const portionMinusButton = document.getElementById('portion-minus');
    const portionPlusButton = document.getElementById('portion-plus');
    const productRows = document.querySelectorAll('[data-base-amount]');

    let portionCount = 1;

    function updateProductAmounts() {
        productRows.forEach(row => {
            const baseAmount = parseFloat(row.getAttribute('data-base-amount'));
            const calculatedAmount = (baseAmount * portionCount).toFixed(2); // Округляем до двух знаков после запятой
            row.querySelector('.calculated-amount').textContent = calculatedAmount;
        });
    }

    portionMinusButton.addEventListener('click', function() {
        if (portionCount > 1) {
            portionCount--;
            portionCountElement.textContent = portionCount;
            updateProductAmounts();
        }
    });

    portionPlusButton.addEventListener('click', function() {
        portionCount++;
        portionCountElement.textContent = portionCount;
        updateProductAmounts();
    });

    // Инициализация значений при загрузке страницы
    updateProductAmounts();
});