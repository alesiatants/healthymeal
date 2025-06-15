// Обработка добавления комментария
document.getElementById('add-update-recipe-form')?.addEventListener('submit', async function (e) {
    e.preventDefault();
    const form = e.target;
    const submitBtn = document.getElementById('submit-recipe-btn');
    const modalElement = document.getElementById('error-modal');
    const modalText = document.getElementById('modal-error-text');
    const errorContainer = document.getElementById('error-container');
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

            modalText.textContent = data.message;
            modalElement.classList.remove('hidden');
        } 
        const contentType = response.headers.get('Content-Type');
        if(!contentType?.includes('application/json')) {
                window.location.href = response.url;
                return;
            }   
            if (response.status === 400) {
                const {validation} = JSON.parse(await response.json());
                // Собираем все уникальные сообщения об ошибках
                errorContainer.innerHTML = '<h3 class="font-bold text-red-800">Пожалуйста, исправьте следующие ошибки:</h3>';
            const uniqueErrors = [...new Set(Object.values(validation))];
            
            // Создаем список ошибок
            const errorList = document.createElement('ul');
            errorList.className = 'mt-2 list-disc list-inside';
            
            uniqueErrors.forEach(error => {
                const item = document.createElement('li');
                item.textContent = error;
                errorList.appendChild(item);
            });
            
            errorContainer.appendChild(errorList);
            errorContainer.classList.remove('hidden');
                // Очистка предыдущих ошибок
                document.querySelectorAll('.error-message').forEach(el => el.remove());
                
                
                // Вывод ошибок под полями
                for (const [field, message] of Object.entries(validation)) {
                    const input = form.querySelector(`[name="${field}"]`);
                    if (input) {
                        const errorEl = document.createElement('div');
                        errorEl.className = 'error-message text-red-500 text-sm mt-1';
                        errorEl.textContent = message;
                        input.closest('.form-group').appendChild(errorEl);
                        
                        // Подсветка поля
                        input.classList.add('border-red-500');
                    }
                }
                if(Object.keys(validation).length > 0) {
                    const firstErrorField = Object.keys(validation)[0];
                    const firstInput = form.querySelector(`[name="${firstErrorField}"]`);
                    if (firstInput) {
                        firstInput.focus();
                    }
                }
                
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



// Добавление ингредиента
document.getElementById('add-ingredient').addEventListener('click', function() {
    const container = document.getElementById('ingredients-container');
    const index = container.querySelectorAll('.ingredient-item').length;
    
    const newIngredient = document.createElement('div');
    newIngredient.className = 'ingredient-item grid grid-cols-1 md:grid-cols-12 gap-4 mb-4';
    
    // Создаем select элемент
    const select = document.createElement('select');
    select.name = `ingredients[${index}][product_id]`;
    select.className = 'product-select w-full p-2 border rounded focus:ring-2 focus:ring-[#db2626] focus:outline-none focus:border-transparent';
    
    // Добавляем опцию по умолчанию
    const defaultOption = document.createElement('option');
    defaultOption.value = '';
    defaultOption.textContent = 'Выберите продукт';
    select.appendChild(defaultOption);
    
    // Добавляем продукты из JavaScript-переменной
    products.forEach(product => {
        const option = document.createElement('option');
        option.value = product.id;
        option.textContent = product.name + " (" + product.product_type.type + ")";
        select.appendChild(option);
    });
    
    // Остальная часть HTML
    newIngredient.innerHTML = 
        `<div class="md:col-span-7 form-group"></div>
        <div class="md:col-span-2 form-group">
            <input type="number" step="0.1" 
                   name="ingredients[${index}][quantity]" 
                   class="data quantity-input w-full p-2 border rounded focus:ring-2 focus:ring-[#db2626] focus:outline-none focus:border-transparent"
                   data-initial-value="">
        </div>
        <div class="md:col-span-2 form-group">
                                <select name="ingredients[${index}][unit]" 
                                        class="data w-full p-2 border border rounded focus:ring-2 focus:ring-[#db2626] focus:outline-none focus:border-transparent">
                                   <option value="г" selected>граммы</option>
                                        <option value="мл">миллилитры</option>
                                        <option value="шт">штуки</option>
                                        <option value="кг">килограммы</option>
                                        <option value="ч.л.">чайная ложка</option>
                                        <option value="ст.л.">столовая ложка</option>
                                        <option value="ст">стакан</option>
                                </select>
                            </div>
       <div class="md:col-span-1 flex items-center">
<button type="button" class="remove-ingredient text-red-500">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>`
    
    // Вставляем select в нужное место
    newIngredient.querySelector('.md\\:col-span-7').appendChild(select);
    
    container.appendChild(newIngredient);
    initProductSelect(select);
});

document.getElementById('add-step').addEventListener('click', function() {
    const container = document.getElementById('steps-container');
    const index = container.querySelectorAll('.step-item').length;
    
    const newStep = document.createElement('div');
    newStep.className = 'step-item mb-4';
    newStep.innerHTML = 
        ` <div class="flex gap-2"> 
        <div class="flex-grow form-group"> <x-light-input-label for="steps[${index}][description]">Шаг ${index + 1}</x-light-input-label>
        <textarea name="steps[${index}][description]" 
                  rows="7"
                  class="data w-full p-2 border rounded focus:ring-2 focus:ring-[#db2626] focus:outline-none focus:border-transparent"  data-initial-value=""></textarea></div>
        <div class="flex items-center">
                                    <button type="button" class="remove-step text-red-500">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                </div>`
    
    container.appendChild(newStep);
});
document.getElementById('imageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(event) {
            const preview = document.getElementById('imagePreview');
            preview.src = event.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(file);
    }
});

// Удаление ингредиента
document.addEventListener('click', function(e) {
    if (e.target.closest('.remove-ingredient')) {
        e.target.closest('.ingredient-item').remove();
        reindexIngredients();
    }
    
    if (e.target.closest('.remove-step')) {
        e.target.closest('.step-item').remove();
        reindexSteps();
    }
});

// Автозаполнение единиц измерения
function initProductSelect(select) {
    select.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const unit = selectedOption.dataset.unit;
        if (unit) {
            const unitInput = this.closest('.ingredient-item').querySelector('.unit-input');
unitInput.value = unit;
        }
    });
}

// Инициализация существующих селектов
document.querySelectorAll('.product-select').forEach(select => {
    initProductSelect(select);
});

// Переиндексация ингредиентов
function reindexIngredients() {
    document.querySelectorAll('.ingredient-item').forEach((item, index) => {
        item.querySelectorAll('select, input').forEach(input => {
            input.name = input.name.replace(/\[\d+\]/, `[${index}]`);
        });
    });
}

// Переиндексация шагов
function reindexSteps() {
    document.querySelectorAll('.step-item').forEach((item, index) => {
        const textarea = item.querySelector('textarea');
        const label = item.querySelector('label');
        textarea.name = `steps[${index}][description]`;
        label.textContent = `Шаг ${index + 1}`;
        
        // Обновляем hidden input если есть
        const hiddenInput = item.querySelector('input[type="hidden"]');
        if (hiddenInput) {
            hiddenInput.name = `steps[${index}][id]`;
        }
    });
}
document.addEventListener('DOMContentLoaded', function() {
    // Инициализация всех полей с классом field-input
    document.querySelectorAll('.data').forEach(field => {
      // Сохраняем начальное значение
      if (!field.hasAttribute('data-initial-value')) {
        field.dataset.initialValue = field.value;
      }
      
      // Обработчики событий
      field.addEventListener('input', handleFieldChange);
      field.addEventListener('select', handleFieldChange);
      field.addEventListener('textarea', handleFieldChange);
      field.addEventListener('change', handleFieldChange);
      
      // Проверяем начальное состояние
      checkFieldState(field);
    });
  
    function handleFieldChange(e) {
      checkFieldState(e.target);
    }
  
    function checkFieldState(field) {
      const currentValue = field.value;
      const initialValue = field.dataset.initialValue;
      
      // Добавляем/убираем классы подсветки
      if (currentValue !== initialValue) {
        field.classList.add('border-red-500', 'border-15');
        field.classList.remove('border-gray-600', 'border');
      } else {
        field.classList.remove('border-red-500', 'border-15');
        field.classList.add('border-gray-600', 'border');
      }
    }

  });

  document.getElementById('close-modal-btn').addEventListener('click', function() {
    document.getElementById('error-modal').classList.add('hidden');
});
