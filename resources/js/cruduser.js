document.addEventListener('DOMContentLoaded', function() {
    document.addEventListener('input', function(e) {
        const phoneInput = e.target.closest('[data-field="phone"]');
        if (phoneInput) {
            let value = e.target.value.replace(/\D/g, ''); // Удаляем все нецифровые символы
            let formattedValue = '+7';

            if (value.length > 1) {
                value = value.substring(1); // Убираем первую цифру, так как она уже добавлена (+7)
            }
            // Форматируем номер по шаблону +7-XXX-XXX-XX-XX
            if (value.length > 0) {
                formattedValue += '-' + value.substring(0, 3);
            }
            if (value.length > 3) {
                formattedValue += '-' + value.substring(3, 6);
            }
            if (value.length > 6) {
                formattedValue += '-' + value.substring(6, 8);
            }
            if (value.length > 8) {
                formattedValue += '-' + value.substring(8, 10);
            }

            e.target.value = formattedValue;
        }
    });

    document.addEventListener('click', async function (e) {
        const userItem = e.target.closest('.user-item');
        if (!userItem) {
            if (e.target.closest('#add-user-btn')) {
                const tbody = document.querySelector('tbody');
                const button = document.getElementById('add-user-btn');
                const currentDate = new Date().toLocaleDateString('ru-RU');
                const isSuperAdmin = button.getAttribute('data-user-issuper');
                const newRow = document.createElement('tr');
                
            
                newRow.className = 'bg-blue-200 user-item';
                newRow.innerHTML = 
                    `<td class="px-1 py-3 whitespace-nowrap form-group">
                        <input class="w-full border  focus:ring-2 focus:ring-[#db2626] focus:outline-none focus:border-transparent rounded px-2 py-1 text-sm" 
                            type="text" name="name" required data-field="name"/>
                        <div class="error-message text-red-500 text-xs mt-1 hidden"></div>
                    </td>
                    <td class="px-1 py-3 whitespace-nowrap form-group">
                        <input class="w-full border focus:ring-2 focus:ring-[#db2626] focus:outline-none focus:border-transparent rounded px-2 py-1 text-sm" 
                            type="email" name="email" required data-field="email"/>
                        <div class="error-message text-red-500 text-xs mt-1 hidden"></div>
                    </td>
                    <td class="px-2 py-3 whitespace-nowrap text-sm text-gray-500">
                        ${currentDate}
                    </td>
                    <td class="px-2 py-3 whitespace-nowrap form-group">
                        <input id="phone" class="w-full border rounded focus:ring-2 focus:ring-[#db2626] focus:outline-none focus:border-transparent px-2 py-1 text-sm" 
                            type="tel" name="phone" required data-field="phone"/>
                        <div class="error-message text-red-500 text-xs mt-1 hidden"></div>
                    </td>
                    <td class="px-1 py-3 whitespace-nowrap form-group">
                        <select class="w-full border rounded px-2 py-1 focus:ring-2 focus:ring-[#db2626] focus:outline-none focus:border-transparent text-sm" data-field="gender">
                            <option value="Мужской">Мужской</option>
                            <option value="Женский">Женский</option>
                        </select>
                    </td>
                     <td class="px-2 py-3 whitespace-nowrap form-group">
                        <input id="birth_date" class="w-full border rounded focus:ring-2 focus:ring-[#db2626] focus:outline-none focus:border-transparent px-2 py-1 text-sm" 
                            type="date" name="birth_date" required data-field="birth_date"/>
                        <div class="error-message text-red-500 text-xs mt-1 hidden"></div>
                    </td>
                    <td class="px-1 py-3 whitespace-nowrap form-group">
                        ${isSuperAdmin ? 
                        `<select class="w-full border focus:ring-2 focus:ring-[#db2626] focus:outline-none focus:border-transparent rounded px-2 py-1 text-sm" data-field="role">
                            <option value="admin">Администратор</option>
                            <option value="dietolog">Диетолог</option>
                        </select>`
                        : 
                        `<div class="w-full border rounded-md px-2 py-1 text-sm bg-gray-100">
                            Диетолог
                        </div>`
                        }
                    </td>
                    <td class="px-1 py-3 whitespace-nowrap flex gap-2">
                        <button 
                                class="save-user-btn text-green-500 hover:text-green-700">
                            <i class="fas fa-check"></i>
                        </button>
                        <button 
                                class="cancel-user-btn text-red-500 hover:text-red-700">
                            <i class="fas fa-times"></i>
                        </button>
                        <button class="edit-user-btn text-green-500 hover:text-green-700 transition-colors duration-200 text-sm hidden"
                                            title="Редактировать">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                        <button 
                                                class="deacivate-user-btn text-red-500 hover:text-red-700 transition-colors duration-200 hidden"
                                                title="Деактивировать">
                                            <i class="fas fa-user-slash"></i>
                                        </button>
                                        <button 
                                                class="acivate-user-btn text-green-500 hover:text-green-700 transition-colors duration-200  hidden"
                                                title="Активировать">
                                            <i class="fas fa-user-check"></i>
                                        </button>
                    </td>`;
            
                    tbody.insertBefore(newRow, tbody.firstChild);
                    newRow.querySelector('[data-field="name"]').focus();
                }
        } else {
    
        const userId = userItem.dataset.userId;
        if (e.target.closest('.cancel-user-btn')) {
            if (confirm('Отменить добавление пользователя?')) {
                userItem.remove();
            }
        }
        if (e.target.closest('.save-user-btn')) {
            if (!confirm('Добавить пользователя?')) return;
            const successElement = document.getElementById('user-success');
            const modalElement = document.getElementById('error-modal');
            const modalText = document.getElementById('modal-error-text');
            const inputs = userItem.querySelectorAll('[data-field]');
            const data = {};
            
            inputs.forEach(input => {
                data[input.dataset.field] = input.value;
            });
            try {
                const response = await fetch(`/users/`, {
                    method: 'POST',
                    body: JSON.stringify(data),
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
    
                if (response.ok) {
                    const data = await response.json();
                    successElement.textContent = data.message;
                    successElement.classList.remove('hidden');
                    setTimeout(() => {
                        successElement.classList.add('hidden');
                    }, 1000);

                    userItem.classList.remove('bg-blue-200');
                    userItem.classList.add('bg-green-200');
                    const deactivatonBtn = userItem.querySelector('.deacivate-user-btn');
                    const updateBtn = userItem.querySelector('.edit-user-btn');
                    updateBtn.classList.remove('hidden');
                    deactivatonBtn.classList.remove('hidden');
                    document.querySelectorAll('.error-message').forEach(el => el.remove());
                    const saveBtn = userItem.querySelector('.save-user-btn');
                    const cancelBtn = userItem.querySelector('.cancel-user-btn');
                    saveBtn.classList.add('hidden');
                    cancelBtn.classList.add('hidden');

                } else {
                    if (response.status === 400) {
                        const {validation} = JSON.parse(await response.json());
                    
                        
                        // Вывод ошибок под полями
                        for (const [field, message] of Object.entries(validation)) {
                            const input = userItem.querySelector(`[data-field="${field}"]`);
                            if (input) {
                                const errorEl = document.createElement('div');
                                errorEl.className = 'error-message text-red-500 text-xs mt-1 break-words whitespace-normal';
                                errorEl.textContent = message;
                                input.closest('.form-group').appendChild(errorEl);
                                
                                // Подсветка поля
                                input.classList.add('border-red-500');
                            }
                        }
                        if(Object.keys(validation).length > 0) {
                            const firstErrorField = Object.keys(validation)[0];
                            const firstInput = userItem.querySelector(`[data-field="${firstErrorField}"]`);
                            if (firstInput) {
                                firstInput.focus();
                            }
                        }
                        
                        return;
                    } else {
                         const data = JSON.parse(await response.json());
                        modalText.textContent = data.message;
                        modalElement.classList.remove('hidden');}
                }
            } catch (error) {
                console.error('Error:', error);
                modalText.textContent = 'Произошла непредвиденная ошибка ' + error;
                modalElement.classList.remove('hidden');
            }
        
        }
        if (e.target.closest('.deacivate-user-btn')) {
            if (!confirm('Деактивировать выбранного пользователя?')) return;
            const successElement = document.getElementById('user-success');
            const modalElement = document.getElementById('error-modal');
            const modalText = document.getElementById('modal-error-text');
            try {
                const response = await fetch(`/users/${userId}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
    
                if (response.ok) {
                    const data = await response.json();
                    successElement.textContent = data.message;
                    successElement.classList.remove('hidden');
                    setTimeout(() => {
                        successElement.classList.add('hidden');
                    }, 1000);
                    userItem.classList.remove('bg-green-200');
                    userItem.classList.add('bg-red-200');
                    const deactivatonBtn = userItem.querySelector('.deacivate-user-btn');
                    const activatonBtn = userItem.querySelector('.acivate-user-btn');
                    const updateBtn = userItem.querySelector('.edit-user-btn');
                    updateBtn.classList.add('hidden');
                    deactivatonBtn.classList.add('hidden');
                    activatonBtn.classList.remove('hidden');
                    

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
        if (e.target.closest('.acivate-user-btn')) {
            if (!confirm('Активировать выбранного пользователя?')) return;
            const successElement = document.getElementById('user-success');
            const modalElement = document.getElementById('error-modal');
            const modalText = document.getElementById('modal-error-text');
            try {
                const response = await fetch(`/users/${userId}`, {
                    method: 'PUT',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
    
                if (response.ok) {
                    const data = await response.json();
                    successElement.textContent = data.message;
                    successElement.classList.remove('hidden');
                    setTimeout(() => {
                        successElement.classList.add('hidden');
                    }, 1000);
                    userItem.classList.remove('bg-red-200');
                    userItem.classList.add('bg-green-200');
                    const deactivatonBtn = userItem.querySelector('.deacivate-user-btn');
                    const activatonBtn = userItem.querySelector('.acivate-user-btn');
                    const updateBtn = userItem.querySelector('.edit-user-btn');
                    activatonBtn.classList.add('hidden');
                    deactivatonBtn.classList.remove('hidden');
                    updateBtn.classList.remove('hidden');
                    

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

        if (e.target.closest('.edit-user-btn')) {
            if (!confirm('Применить изменения?')) return;
            const successElement = document.getElementById('user-success');
            const modalElement = document.getElementById('error-modal');
            const modalText = document.getElementById('modal-error-text');
            const data = {
                name: userItem.querySelector('[data-field="name"]').value, 
                email: userItem.querySelector('[data-field="email"]').value, 
                phone: userItem.querySelector('[data-field="phone"]').value, 
            }
            try {
                const response = await fetch(`/users/${userId}/update`, {
                    method: 'PUT',
                    body: JSON.stringify(data),
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
    
                if (response.ok) {
                    const data = await response.json();
                    successElement.textContent = data.message;
                    successElement.classList.remove('hidden');
                    setTimeout(() => {
                        successElement.classList.add('hidden');
                    }, 1000);
                } else {
                    if (response.status === 400) {
                        const {validation} = JSON.parse(await response.json());
                    
                        
                        // Вывод ошибок под полями
                        for (const [field, message] of Object.entries(validation)) {
                            const input = userItem.querySelector(`[data-field="${field}"]`);
                            if (input) {
                                const errorEl = document.createElement('div');
                                errorEl.className = 'error-message text-red-500 text-xs mt-1 break-words whitespace-normal';
                                errorEl.textContent = message;
                                input.closest('.form-group').appendChild(errorEl);
                                
                                // Подсветка поля
                                input.classList.add('border-red-500');
                            }
                        }
                        if(Object.keys(validation).length > 0) {
                            const firstErrorField = Object.keys(validation)[0];
                            const firstInput = userItem.querySelector(`[data-field="${firstErrorField}"]`);
                            if (firstInput) {
                                firstInput.focus();
                            }
                        }
                        
                        return;
                    } else {
                         const data = JSON.parse(await response.json());
                        modalText.textContent = data.message;
                        modalElement.classList.remove('hidden');}
                }
            } catch (error) {
                console.error('Error:', error);
                modalText.textContent = 'Произошла непредвиденная ошибка ' + error;
                modalElement.classList.remove('hidden');
            }
        }
    }
    });
});

// Закрытие модального окна
document.getElementById('close-modal-btn')?.addEventListener('click', function () {
    setTimeout(() => {
        document.getElementById('error-modal').classList.add('hidden');
    }, 300);
});