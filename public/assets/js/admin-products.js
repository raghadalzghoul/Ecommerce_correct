
const form = document.getElementById("product-form");
const message = document.getElementById("message");
const productsTable = document.getElementById("products-table");
const categorySelect = document.getElementById("category-select");

// Load categories
async function loadCategories() {
    try {
        const response = await fetch("index.php?api=categories");
        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error("Failed to load categories");
        }

        categorySelect.innerHTML =
            '<option value="">Choose Category</option>';

        data.categories.forEach(category => {
            const option = document.createElement("option");
            option.value = category.cat_id;
            option.textContent = category.cat_name;
            categorySelect.appendChild(option);
        });

    } catch (error) {
        message.textContent = error.message;
    }
}

// Load products
async function loadProducts() {
    try {
        const response = await fetch("index.php?api=products");
        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error("Failed to load products");
        }

        productsTable.innerHTML = "";

        data.products.forEach(product => {
            const row = document.createElement("tr");

            const imageCell = document.createElement("td");
            const image = document.createElement("img");
            image.src = product.image_url || "";
            image.alt = product.name;
            image.width = 70;
            imageCell.appendChild(image);

            const nameCell = document.createElement("td");
            nameCell.textContent = product.name;

            const priceCell = document.createElement("td");
            priceCell.textContent = Number(product.price).toFixed(2);

            const categoryCell = document.createElement("td");
            categoryCell.textContent = product.cat_id;

            row.append(imageCell, nameCell, priceCell, categoryCell);
            productsTable.appendChild(row);
        });

    } catch (error) {
        message.textContent = error.message;
    }
}

// Add product
form.onsubmit = async function (event) {
    event.preventDefault();

    const formData = new FormData(form);

    try {
        const response = await fetch(
            "index.php?api=admin-create-product",
            {
                method: "POST",
                body: formData
            }
        );

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || "Failed to add product");
        }

        message.textContent = "Product added successfully!";
        message.className = "alert alert-success";

        form.reset();
        loadProducts();

    } catch (error) {
        message.textContent = error.message;
        message.className = "alert alert-danger";
    }
};

loadCategories();
loadProducts();
