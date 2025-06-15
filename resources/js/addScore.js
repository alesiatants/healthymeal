document.addEventListener('DOMContentLoaded', function() {
    // Обработка кликов по звездам
    document.querySelectorAll('.star-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const rating = this.getAttribute('data-rating');
            const recipe = this.getAttribute('data-recipe');
            const isUpdate = this.getAttribute('data-update');
            const recipeType = this.getAttribute('data-recipe-type');
            const stars = this.parentElement.querySelectorAll('.star-btn');
            const ratingStatus = document.querySelector('.rating-status');
            const modalElement = document.getElementById('error-modal');
            const modalText = document.getElementById('modal-error-text');
            
            try{
            // Подсветка звезд
                stars.forEach((star, index) => {
                    const icon = star.querySelector('i');
                    if (index < rating) {
                        icon.classList.remove('text-gray-300');
                        icon.classList.add('text-yellow-400');
                    } else {
                        icon.classList.remove('text-yellow-400');
                        icon.classList.add('text-gray-300');
                    }
                });
                
                // Обновление статуса
                ratingStatus.textContent = `Ваша оценка: ${rating}`;
                
                // Отправка на сервер
                const method = isUpdate === 'true' ? 'PUT' : 'POST';
               
                fetch(`/${recipeType}/${recipe}/rate`, {
                    method: method,
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({ rating: rating })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        stars.forEach(star => {
                            star.setAttribute('data-update', 'true');
                        });
                        // Можно обновить общий рейтинг на странице
                        updateAverageRating(data.average_rating, data.ratings_count);
                        console.log('Рейтинг сохранен');
                    } else {
                        data = JSON.parse(data);
                        modalText.textContent = data.message;
                        setTimeout(() => {
                            modalElement.classList.remove('hidden');
                        }, 300);
                    }
                });

        } catch (error) {
            console.error('Error:', error);
            modalText.textContent = 'Произошла непредвиденная ошибка ' + error;
            setTimeout(() => {
                modalElement.classList.remove('hidden');
            }, 300);
        }
    });
    });

    function updateAverageRating(averageRating, ratingsCount) {
        const averageRatingContainer = document.getElementById('average-rating-container');
        const ratingsCountElement = document.getElementById('ratings-count');
        if (!averageRatingContainer) return;

        // Очищаем контейнер
        averageRatingContainer.innerHTML = '';

        // Добавляем звезды
        for (let i = 1; i <= 5; i++) {
            const star = document.createElement('i');
            star.className = `fa-star text-xl ${i <= Math.round(averageRating) ? 'fas text-yellow-400' : 'far'}`;
            star.setAttribute('data-rating', i);
            averageRatingContainer.appendChild(star);
        }

        // Обновляем счетчик оценок
        if (ratingsCountElement) {
            ratingsCountElement.textContent = `${ratingsCount}`;
        }
    }
    
});