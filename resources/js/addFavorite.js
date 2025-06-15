document.addEventListener('DOMContentLoaded', function() {
    const favoriteButtons = document.querySelectorAll('.favorite-btn');

    favoriteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const recipeId = this.getAttribute('data-recipe-id');
            const heartIcon = this.querySelector('i');
            const isFavorited = heartIcon.classList.contains('fas');

            // Определяем URL и метод в зависимости от состояния
            const url = isFavorited ? '/favorites/remove' : '/favorites/add';
            const method = 'POST';

            // Отправляем AJAX-запрос
            fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ recipe_id: recipeId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    if (isFavorited) {
                        heartIcon.classList.remove('fas', 'text-red-500');
                        heartIcon.classList.add('far');
                    } else {
                        heartIcon.classList.add('fas', 'text-red-500');
                        heartIcon.classList.remove('far');
                    }
                }
            })
            .catch(error => console.error('Error:', error));
        });
    });
});