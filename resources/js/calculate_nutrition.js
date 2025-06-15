export function setupNutritionCalculator(product) {
    const weightSelect = document.getElementById('weight');
    
    if (weightSelect) {
        weightSelect.addEventListener('change', () => {
            const weight = weightSelect.value / 100;
            
            document.getElementById('calories').textContent = (product.calories * weight).toFixed(1);
            document.getElementById('protein').textContent = (product.protein * weight).toFixed(1);
            document.getElementById('fat').textContent = (product.fat * weight).toFixed(1);
            document.getElementById('carbs').textContent = (product.carbs * weight).toFixed(1);
            
            // Анимация
            ['calories', 'protein', 'fat', 'carbs'].forEach(id => {
                const element = document.getElementById(id);
                element.classList.add('animate-pulse');
                setTimeout(() => element.classList.remove('animate-pulse'), 700);
            });
        });
    }
}