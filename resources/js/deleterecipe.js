// modal.js - Управление модальным окном подтверждения удаления

// Функция для показа модального окна
export function showDeleteModal(url) {
    const modal = document.getElementById('confirmModal');
    const form = document.getElementById('deleteForm');
    
    if (!modal || !form) {
        console.error('Modal or form element not found!');
        return;
    }
    
    form.action = url; // Устанавливаем URL для удаления
    modal.classList.remove('hidden');
    
    // Запускаем анимацию после добавления в DOM
    setTimeout(() => {
        modal.classList.remove('opacity-0');
        modal.querySelector('div').classList.remove('scale-95');
    }, 10);
}

// Функция для скрытия модального окна
export function hideDeleteModal() {
    const modal = document.getElementById('confirmModal');
    if (!modal) return;
    
    modal.classList.add('opacity-0');
    const modalContent = modal.querySelector('div');
    if (modalContent) modalContent.classList.add('scale-95');
    
    setTimeout(() => {
        modal.classList.add('hidden');
    }, 300);
}

// Инициализация обработчиков событий
export function initModalHandlers() {
    const cancelButton = document.getElementById('cancelButton');
    const modal = document.getElementById('confirmModal');
    
    if (cancelButton) {
        cancelButton.addEventListener('click', hideDeleteModal);
    }
    
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === this) hideDeleteModal();
        });
    }
}

// Автоматическая инициализация при загрузке DOM
document.addEventListener('DOMContentLoaded', initModalHandlers);


// удаление рецепта
document.getElementById('deleteForm')?.addEventListener('submit', async function (e) {
    e.preventDefault();
    const modalElement = document.getElementById('error-modal');
    const modalText = document.getElementById('modal-error-text');

    const formData = new FormData(this);
    try {
        const response = await fetch(this.action, {
            method: this.method,
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            }
        });
        if (!response.ok && response.status == 500) {
            const data = JSON.parse(await response.json());
            hideDeleteModal();
            modalText.textContent = data.message;
            setTimeout(() => {
                modalElement.classList.remove('hidden');
            }, 300);
        } 
        const contentType = response.headers.get('Content-Type');
        if(!contentType?.includes('application/json')) {
                window.location.href = response.url;
                return;
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
// Закрытие модального окна
document.getElementById('close-modal-btn')?.addEventListener('click', function () {
    setTimeout(() => {
        document.getElementById('error-modal').classList.add('hidden');
    }, 300);
});

