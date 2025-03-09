document.addEventListener('DOMContentLoaded', function() {
    function updatePrice(element) {
        var productId = element.closest('tr').querySelector('.quantity-input').dataset.productId;
        var basePrice = parseFloat(element.closest('tr').querySelector('.product-price').dataset.basePrice);
        var quantity = parseInt(element.closest('tr').querySelector('.quantity-input').value);
        var days = parseInt(element.closest('tr').querySelector('.days-select').value);

        var newPrice = basePrice * quantity * days;
        element.closest('tr').querySelector('.product-price').textContent = newPrice + ' ₽';
    }

    document.querySelectorAll('.change-quantity').forEach(function(button) {
        button.addEventListener('click', function() {
            var input = this.parentNode.querySelector('.quantity-input');
            var quantity = parseInt(input.value);
            var max = parseInt(input.getAttribute('max'));

            if (this.classList.contains('btn-plus')) {
                quantity = Math.min(quantity + 1, max);
            } else if (this.classList.contains('btn-minus')) {
                quantity = Math.max(quantity - 1, 1);
            }

            input.value = quantity;
            updatePrice(this);
        });
    });

    document.querySelectorAll('.quantity-input').forEach(function(input) {
        input.addEventListener('change', function() {
            updatePrice(this);
        });
    });

    document.querySelectorAll('.days-select').forEach(function(select) {
        select.addEventListener('change', function() {
            updatePrice(this);
        });
    });

    document.querySelectorAll('.add-to-cart').forEach(function(button) {
        button.addEventListener('click', function() {
            var productId = this.dataset.productId;
            var quantity = parseInt(this.closest('tr').querySelector('.quantity-input').value);
            var days = parseInt(this.closest('tr').querySelector('.days-select').value);

            fetch('handlers/add_to_cart.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    product_id: productId,
                    quantity: quantity,
                    days: days
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    alert('Товар добавлен в корзину!');
                } else {
                    alert(data.message || 'Произошла ошибка.');
                }
            });
        });
    });
    
});
