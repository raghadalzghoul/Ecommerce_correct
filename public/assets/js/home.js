
const container = document.getElementById('products-container');

async function loadProducts() {
    try {
        const response = await fetch('index.php?api=products');
        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error('Failed to load products');
        }

        container.innerHTML = '';

        if (data.products.length === 0) {
            container.textContent = 'No products available.';
            return;
        }

        data.products.forEach(product => {
            const column = document.createElement('div');
            column.className = 'col-md-4';

            const card = document.createElement('div');
            card.className = 'product-card';

            const image = document.createElement('img');
            image.src = product.image_url || 'https://placehold.co/400x300?text=No+Image';
            image.alt = product.name;

            const body = document.createElement('div');
            body.className = 'card-body';

            const title = document.createElement('h5');
            title.textContent = product.name;

            const price = document.createElement('p');
            price.textContent = '$' + product.price;

            const link = document.createElement('a');
            link.href = '?page=product&id=' + product.product_id;
            link.className = 'btn btn-dark';
            link.textContent = 'View Details';

            body.append(title, price, link);
            card.append(image, body);
            column.appendChild(card);
            container.appendChild(column);
        });

    } catch (error) {
        container.textContent = 'Unable to load products.';
        console.error(error);
    }
}

loadProducts();
