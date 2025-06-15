// Обработка добавления комментария
document.getElementById('add-comment-form')?.addEventListener('submit', async function (e) {
    e.preventDefault();
    const content = document.getElementById('comment-textarea');
    const comment = content.value;
    const recipetype = content.getAttribute('data-recipe-type');
    const recipename = content.getAttribute('data-recipe-name');
    const submitBtn = document.getElementById('submit-comment-btn');
    const errorElement = document.getElementById('comment-error');
    const successElement = document.getElementById('comment-success');
    const modalElement = document.getElementById('error-modal');
    const modalText = document.getElementById('modal-error-text');
    // Сброс состояний
    errorElement.classList.add('hidden');
    successElement.classList.add('hidden');
    submitBtn.disabled = true;
    submitBtn.innerHTML = `
        <span class="flex items-center">
            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            Отправка...
        </span>`;

    const formData = new FormData(this);
    try {
        const response = await fetch(`/recipes/${recipetype}/${recipename}/comments`, {
            method: 'POST',
            body: JSON.stringify({ comment: comment }),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
        });
        if (response.ok) {
            const data = await response.json();
            // Добавляем новый комментарий в список
            const commentHtml = createCommentHtml(data.comment);
            document.getElementById('comments-list').insertAdjacentHTML('beforeend', commentHtml);

            let currentCount = parseInt(document.querySelector('.comments-count').textContent);
            document.querySelectorAll('.comments-count').forEach(el => {
                el.textContent = currentCount + 1;
            });

            // Очищаем форму и показываем успешное сообщение
            this.reset();
            successElement.textContent = data.message;
            successElement.classList.remove('hidden');
            setTimeout(() => {
                successElement.classList.add('hidden');
            }, 1000);
        } else {
            const data = JSON.parse(await response.json());
            if (data.validation) {
                errorElement.textContent = data.validation['comment'];
                errorElement.classList.remove('hidden');
            } else {
                modalText.textContent = data.message;
                setTimeout(() => {
                    modalElement.classList.remove('hidden');
                }, 300);
            }
        }
    } catch (error) {
        console.error('Error:', error);
        modalText.textContent = 'Произошла непредвиденная ошибка ' + error;
        modalElement.classList.remove('hidden');
    } finally {
        submitBtn.disabled = false;
        submitBtn.textContent = 'Отправить';
    }
});

// Function to create HTML for a comment
function createCommentHtml(comment) {
    if (comment.user.avatar === null) {
        var avatar = '/users/avatar.jpg';
    } else {
        var avatar = comment.user.avatar;
    }
    return `
    <div class="bg-white p-4 rounded-lg shadow comment-item" data-comment-id="${comment.id}">
        <div class="flex justify-between items-start mb-2">
            <div class="flex items-center gap-3">
                <img src="/storage/${avatar}" 
                     alt="${comment.user.name}" 
                     class="w-10 h-10 rounded-full object-cover" />
                <div>
                    <div class="font-medium">${comment.user.name}</div>
                    <div class="text-gray-500 text-sm comment-date">
                        только что
                    </div>
                </div>
            </div>
            <div class="flex gap-2 comment-actions">
                <button class="edit-comment-btn text-green-500 hover:text-green-700">
                    <i class="fas fa-pencil-alt"></i>
                </button>
                <button class="delete-comment-btn text-red-500 hover:text-red-700">
                    <i class="fas fa-trash-alt"></i>
                </button>
            </div>
        </div>
        <div class="text-gray-700 comment-content pl-13">
            ${comment.comment}
        </div>
        <div class="comment-edit-form hidden mt-3 pl-13">
            <textarea class="w-full p-3  rounded-lg focus:outline-none focus:border-transparent focus:ring-1 focus:ring-[#db2626] comment-edit-textarea" 
                      rows="3">${comment.comment}</textarea>
            <div class="flex gap-2 mt-2">
                <button class="save-comment-btn bg-[#db2626] text-white px-3 py-1 rounded-md hover:bg-[#c52222]">
                    Сохранить
                </button>
                <button class="cancel-edit-btn bg-gray-500 text-white px-3 py-1 rounded-md hover:bg-gray-600">
                    Отмена
                </button>
            </div>
        </div>
    </div>
    `;
}

// Обработчики для редактирования/удаления
document.addEventListener('click', async function (e) {
    const commentItem = e.target.closest('.comment-item');
    if (!commentItem) return;

    const commentId = commentItem.dataset.commentId;

    // Редактирование
    if (e.target.closest('.edit-comment-btn')) {
        commentItem.querySelector('.comment-content').classList.add('hidden');
        commentItem.querySelector('.comment-edit-form').classList.remove('hidden');
        commentItem.querySelector('.comment-actions').classList.add('hidden');
    }

    // Отмена редактирования
    if (e.target.closest('.cancel-edit-btn')) {
        commentItem.querySelector('.comment-content').classList.remove('hidden');
        commentItem.querySelector('.comment-edit-form').classList.add('hidden');
        commentItem.querySelector('.comment-actions').classList.remove('hidden');
    }

    // Сохранение изменений
    if (e.target.closest('.save-comment-btn')) {
        const content = commentItem.querySelector('.comment-edit-textarea').value;
        const successElement = document.getElementById('comment-success');
        const modalElement = document.getElementById('error-modal');
        const modalText = document.getElementById('modal-error-text');
        try {
            const response = await fetch(`/comments/${commentId}`, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({ comment: content })
            });

            if (response.ok) {
                const data = await response.json();
                commentItem.querySelector('.comment-content').textContent = content;
                commentItem.querySelector('.comment-content').classList.remove('hidden');
                commentItem.querySelector('.comment-edit-form').classList.add('hidden');
                commentItem.querySelector('.comment-actions').classList.remove('hidden');
                commentItem.querySelector('.comment-date').innerHTML =
                    'только что <span class="text-gray-400">(изменено)</span>';
                successElement.textContent = data.message;
                successElement.classList.remove('hidden');
                setTimeout(() => {
                    successElement.classList.add('hidden');
                }, 1000);
            } else {
                const data = JSON.parse(await response.json());
                modalText.textContent = data.validation['comment'];
                modalElement.classList.remove('hidden');

            }
        } catch (error) {
            console.error('Error:', error);
            modalText.textContent = 'Произошла непредвиденная ошибка ' + error;
            modalElement.classList.remove('hidden');
        }

    }

    // Удаление
    if (e.target.closest('.delete-comment-btn')) {
        if (!confirm('Удалить комментарий?')) return;
        const successElement = document.getElementById('comment-success');
        const modalElement = document.getElementById('error-modal');
        const modalText = document.getElementById('modal-error-text');
        try {
            const response = await fetch(`/comments/${commentId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            if (response.ok) {
                const data = await response.json();
                commentItem.remove();
                let currentCount = parseInt(document.querySelector('.comments-count').textContent);
                document.querySelectorAll('.comments-count').forEach(el => {
                    el.textContent = currentCount - 1;
                });
                successElement.textContent = data.message;
                successElement.classList.remove('hidden');
                setTimeout(() => {
                    successElement.classList.add('hidden');
                }, 1000);
            } else {
                const data = JSON.parse(await response.json());
                modalText.textContent = data.message;
                modalElement.classList.remove('hidden');
            }
        } catch (error) {
            console.error('Error:', error);
            modalText.textContent = 'Произошла непредвиденная ошибка ' + error;
            modalElement.classList.remove('hidden');
        }
    }
});
// Закрытие модального окна
document.getElementById('close-modal-btn')?.addEventListener('click', function () {
    setTimeout(() => {
        document.getElementById('error-modal').classList.add('hidden');
    }, 300);
});