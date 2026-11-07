
const form = document.getElementById("product-form");
const editForm = document.getElementById("edit-product-form");
const message = document.getElementById("message");

const productsTable = document.getElementById("products-table");
const categorySelect = document.getElementById("category-select");
const editCategory = document.getElementById("edit-category");

let products = [];

// Show message
function showMessage(text, type = "success") {
    message.textContent = text;
    message.className = "alert alert-" + type;
}

// Load categories
async function loadCategories() {
    try {
        const response = await fetch("index.php?api=categories");
        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || "Failed to load categories");
        }

        categorySelect.innerHTML =
            '<option value="">Choose Category</option>';

        editCategory.innerHTML =
            '<option value="">Choose Category</option>';

        data.categories.forEach(category => {
            const option = document.createElement("option");
            option.value = category.cat_id;
            option.textContent = category.cat_name;

            categorySelect.appendChild(option);
            editCategory.appendChild(option.cloneNode(true));
        });

    } catch (error) {
        showMessage(error.message, "danger");
    }
}

// Load products
async function loadProducts() {
    try {
        const response = await fetch("index.php?api=products");
        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || "Failed to load products");
        }

        products = data.products;
        productsTable.innerHTML = "";

        if (products.length === 0) {
            productsTable.innerHTML =
                '<tr><td colspan="5">No products found.</td></tr>';
            return;
        }

        products.forEach(product => {
            const row = document.createElement("tr");

            // Image
            const imageCell = document.createElement("td");
            const image = document.createElement("img");
            image.src = product.image_url || "";
            image.alt = product.name;
            image.width = 70;
            imageCell.appendChild(image);

            // Name
            const nameCell = document.createElement("td");
            nameCell.textContent = product.name;

            // Price
            const priceCell = document.createElement("td");
            priceCell.textContent = Number(product.price).toFixed(2);

            // Category
            const categoryCell = document.createElement("td");
            categoryCell.textContent = product.cat_id;

            // Actions
            const actionCell = document.createElement("td");

            const editButton = document.createElement("button");
            editButton.className = "btn btn-primary btn-sm me-2";
            editButton.textContent = "Edit";

            editButton.onclick = function () {
                editProduct(product.product_id);
            };

            const deleteButton = document.createElement("button");
            deleteButton.className = "btn btn-danger btn-sm";
            deleteButton.textContent = "Delete";

            deleteButton.onclick = function () {
                deleteProduct(product.product_id);
            };

            actionCell.append(editButton, deleteButton);

            row.append(
                imageCell,
                nameCell,
                priceCell,
                categoryCell,
                actionCell
            );

            productsTable.appendChild(row);
        });

    } catch (error) {
        showMessage(error.message, "danger");
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

        showMessage("Product added successfully!");

        form.reset();
        loadProducts();

    } catch (error) {
        showMessage(error.message, "danger");
    }
};

// Fill edit form
function editProduct(productId) {
    const product = products.find(
        p => p.product_id == productId
    );

    if (!product) {
        return;
    }

    document.getElementById("edit-product-id").value =
        product.product_id;

    document.getElementById("edit-name").value =
        product.name;

    document.getElementById("edit-description").value =
        product.description;

    document.getElementById("edit-price").value =
        product.price;

    document.getElementById("edit-category").value =
        product.cat_id;

    document.getElementById("edit-image").value = "";

    editForm.scrollIntoView({ behavior: "smooth" });
}

// Save edited product
editForm.onsubmit = async function (event) {
    event.preventDefault();

    const productId = document.getElementById("edit-product-id").value;

    if (!productId) {
        showMessage("Choose a product to edit first.", "warning");
        return;
    }

    const formData = new FormData(editForm);

    try {
        const response = await fetch(
            "index.php?api=admin-update-product&id=" + productId,
            {
                method: "POST",
                body: formData
            }
        );

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || "Update failed");
        }

        showMessage("Product updated successfully!");

        editForm.reset();
        document.getElementById("edit-product-id").value = "";

        loadProducts();

    } catch (error) {
        showMessage(error.message, "danger");
    }
};

// Delete product
async function deleteProduct(productId) {
    const confirmed = confirm(
        "Are you sure you want to delete this product?"
    );

    if (!confirmed) {
        return;
    }

    try {
        const response = await fetch(
            "index.php?api=admin-delete-product&id=" + productId,
            {
                method: "DELETE"
            }
        );

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || "Delete failed");
        }

        showMessage("Product deleted successfully!");

        loadProducts();

    } catch (error) {
        showMessage(error.message, "danger");
    }
}

// Load data when page opens
loadCategories();
loadProducts();
