document.addEventListener('DOMContentLoaded', () => {
    const products = [
        { name: 'Burger', price: 1.50 },
        { name: 'Taco', price: 0.80 },
        { name: 'Donut', price: 0.30 },
        { name: 'Cake', price: 4.99 },
        { name: 'Fries', price: 1.49 },
        { name: 'Hotdog', price: 3.49 },
        { name: 'Pizza', price: 7.99 },
        { name: 'Popcorn', price: 1.99 },
        { name: 'Coke', price: 1.49 },
        { name: 'Icecream', price: 5.99 },
        { name: 'Cookie', price: 3.99 },
        { name: 'Flan', price: 7.99 }
        // Add more products as needed
    ];

    const productList = document.getElementById('product-list');
    const loading = document.getElementById('loading');
    const app = document.getElementById('app');
    const viewOrderBtn = document.getElementById('view-order-btn');

    let cart = {};

    function loadProducts() {
        productList.innerHTML = '';
        products.forEach((product, index) => {
            const productDiv = document.createElement('div');
            productDiv.className = 'product';
            productDiv.innerHTML = `
                <div class="product-name">${product.name}</div>
                <div class="product-price">$${product.price.toFixed(2)}</div>
                <button class="buy-btn" onclick="addToCart(${index})">Buy</button>
                <div class="quantity-btn" style="display: none;">
                    <button onclick="updateQuantity(${index}, -1)">-</button>
                    <span>1</span>
                    <button onclick="updateQuantity(${index}, 1)">+</button>
                </div>
            `;
            productList.appendChild(productDiv);
        });
        loading.style.display = 'none';
        app.style.display = 'block';
    }

    function addToCart(index) {
        if (!cart[index]) {
            cart[index] = { ...products[index], quantity: 1 };
        }
        updateProductUI(index);
        toggleViewOrderButton();
    }

    function updateQuantity(index, change) {
        if (cart[index]) {
            cart[index].quantity += change;
            if (cart[index].quantity <= 0) {
                delete cart[index];
            }
            updateProductUI(index);
            toggleViewOrderButton();
        }
    }

    function updateProductUI(index) {
        const productDiv = productList.children[index];
        const buyBtn = productDiv.querySelector('.buy-btn');
        const quantityBtn = productDiv.querySelector('.quantity-btn');
        if (cart[index]) {
            buyBtn.style.display = 'none';
            quantityBtn.style.display = 'flex';
            quantityBtn.querySelector('span').innerText = cart[index].quantity;
        } else {
            buyBtn.style.display = 'block';
            quantityBtn.style.display = 'none';
        }
    }

    function toggleViewOrderButton() {
        if (Object.keys(cart).length > 0) {
            viewOrderBtn.style.display = 'block';
        } else {
            viewOrderBtn.style.display = 'none';
        }
    }

    window.addToCart = addToCart;
    window.updateQuantity = updateQuantity;
    window.viewOrder = function() {
        const orderList = document.createElement('div');
        orderList.id = 'order-list';
        app.style.display = 'none';
        orderList.style.display = 'flex';
        orderList.innerHTML = '<h2>Your Order</h2>';
        Object.values(cart).forEach(item => {
            const orderItem = document.createElement('div');
            orderItem.className = 'order-item';
            orderItem.innerHTML = `
                <div class="product-name">${item.name}</div>
                <div class="product-price">$${item.price.toFixed(2)} x ${item.quantity}</div>
            `;
            orderList.appendChild(orderItem);
        });
        const backButton = document.createElement('button');
        backButton.className = 'view-order-btn';
        backButton.innerText = 'Back to Products';
        backButton.onclick = () => {
            orderList.style.display = 'none';
            app.style.display = 'block';
        };
        orderList.appendChild(backButton);
        document.body.appendChild(orderList);
    };

    loadProducts();
});
