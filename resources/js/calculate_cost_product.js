document.getElementById('calculate-cost').addEventListener('click', function() {
    const ingredients = [];
    const modalElement = document.getElementById('error-modal');
    const modalText = document.getElementById('modal-error-text');
    const errorContainer = document.getElementById('error-container')
    document.querySelectorAll('.ingredient-item').forEach(item => {
        ingredients.push({
            id: item.getAttribute('data-product-id'),
            name: item.querySelector('.product_name').textContent.trim(),
            quantity: parseFloat(item.querySelector('.calculated-amount').textContent),
            unit: item.getAttribute('data-product-unit')
        });
    });
    // Отправка данных на сервер
fetch(`/calculate-cost`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN':  document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ ingredients: ingredients })
    })
    .then(response => response.json())
    .then(data => {
        // Обработка ответа от сервера
        if(data.success) {
            const detailsContainer = document.getElementById('cost-details');
            detailsContainer.innerHTML = '';
            let total = 0;
            
            data.costs.forEach(item => {
                const row = document.createElement('tr');
                row.className = 'border-b';
               
                const productsJson = JSON.stringify(item.products).replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                row.innerHTML = 
                    `<td class="py-2 px-4">${item.name}</td>
                    <td class="text-right py-2 px-4">${item.quantity} ${item.unit}.</td>
                    <td class="text-right py-2 px-4">${item.price_per_unit} </td>
                    <td class="text-right py-2 px-4">${item.cost} руб.</td>
                    <td class="text-right py-2 px-4">
                    
                        <button data-products="${productsJson}"
                                class="text-blue-500 hover:text-blue-700 show-details-btn">
                            <i class="fa-solid fa-eye"></i>
                        </button>
                    </td> `
                ;
                detailsContainer.appendChild(row);
                total += parseFloat(item.cost);
            });
            
            document.getElementById('total-cost').textContent = total.toFixed(2) + ' руб.';
            document.getElementById('cost-results').classList.remove('hidden');
            document.querySelectorAll('.show-details-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    showDetails(this);
                })
            })
        } else {
            modalText.textContent = data.message;
            modalElement.classList.remove('hidden');
        }
    })
    .catch(error => console.error('Error:', error));
});


function showDetails(button){
    const productsJson = button.getAttribute('data-products');
           
        var products = JSON.parse(productsJson);
        
        // Преобразуем объект с числовыми ключами в чистый массив
        if (!Array.isArray(products)) {
            // Удаляем числовые ключи и создаем чистый массив
            products = Object.keys(products).map(key => products[key]);
        }
        
        // Если после преобразования это не массив, создаем пустой массив
        if (!Array.isArray(products)) {
            products = [];
        }
    var detailsProductsContainer = document.getElementById('products-details');
    detailsProductsContainer.innerHTML = '';
        products.forEach(item => {
            const row = document.createElement('tr');
            row.className = 'border-b';
            var weighted = "";
            if (item.weighted && item.weighted.shelfLabel) {
                 weighted = item.weighted.shelfLabel
            }
            row.innerHTML = 
                `<td class="py-2 px-4">${item.name} ${weighted}</td>
                <td class="text-right py-2 px-4">${item.price/100} руб.</td>
                <td class="text-right py-2 px-4">${item.avgCost} руб. / ${item.unit}</td> `
            ;
            detailsProductsContainer.appendChild(row);
        });

        const modal = document.getElementById('prepositionsModal');
        modal.classList.remove('hidden');
        modal.classList.add('block');

};

document.getElementById('closeProductsModal').addEventListener('click', function() {
    const modal = document.getElementById('prepositionsModal');
    modal.classList.remove('block');
    modal.classList.add('hidden');

});