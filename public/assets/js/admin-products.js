
const form = document.getElementById("product-form");
const editForm = document.getElementById("edit-product-form");
const message = document.getElementById("message");

const productsTable = document.getElementById("products-table");
const categorySelect = document.getElementById("category-select");
const editCategory = document.getElementById("edit-category");

const variantsSection = document.getElementById("variants-section");
const variantsTable = document.getElementById("variants-table");
const variantForm = document.getElementById("variant-form");

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

            // Product image
            const imageCell = document.createElement("td");

            if (product.image_url) {
                const image = document.createElement("img");
                image.src = product.image_url;
                image.alt = product.name;
                image.width = 70;
                imageCell.appendChild(image);
            } else {
                imageCell.textContent = "No image";
            }

            // Product name
            const nameCell = document.createElement("td");
            nameCell.textContent = product.name;

            // Product price
            const priceCell = document.createElement("td");
            priceCell.textContent = Number(product.price).toFixed(2);

            // Category ID
            const categoryCell = document.createElement("td");
            categoryCell.textContent = product.cat_id;

            // Action buttons
            const actionCell = document.createElement("td");

            // Variants button
            const variantsButton = document.createElement("button");
            variantsButton.className = "btn btn-secondary btn-sm me-2 mb-1";
            variantsButton.textContent = "Variants";

            variantsButton.onclick = function () {
                manageVariants(product.product_id, product.name);
            };

            // Edit button
            const editButton = document.createElement("button");
            editButton.className = "btn btn-primary btn-sm me-2 mb-1";
            editButton.textContent = "Edit";

            editButton.onclick = function () {
                editProduct(product.product_id);
            };

            // Delete button
            const deleteButton = document.createElement("button");
            deleteButton.className = "btn btn-danger btn-sm mb-1";
            deleteButton.textContent = "Delete";

            deleteButton.onclick = function () {
                deleteProduct(product.product_id);
            };

            actionCell.append(
                variantsButton,
                editButton,
                deleteButton
            );

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
        await loadProducts();

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
        showMessage("Product not found.", "danger");
        return;
    }

    document.getElementById("edit-product-id").value =
        product.product_id;

    document.getElementById("edit-name").value =
        product.name;

    document.getElementById("edit-description").value =
        product.description || "";

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

    // Do not send an empty image file
    const imageInput = document.getElementById("edit-image");

    if (imageInput.files.length === 0) {
        formData.delete("image");
    }

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

        await loadProducts();

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

        // Hide variants if the deleted product is selected
        const selectedId =
            document.getElementById("variant-product-id").value;

        if (selectedId == productId) {
            variantsSection.hidden = true;
            document.getElementById("variant-product-id").value = "";
        }

        await loadProducts();

    } catch (error) {
        showMessage(error.message, "danger");
    }
}

// Open product variants
async function manageVariants(productId, productName) {
    document.getElementById("variant-product-id").value = productId;

    document.getElementById("variant-product-name").textContent =
        productName;

    variantsSection.hidden = false;

    await loadVariants(productId);

    variantsSection.scrollIntoView({ behavior: "smooth" });
}

// Load variants
async function loadVariants(productId) {
    try {
        const response = await fetch(
            "index.php?api=product-variants&id=" + productId
        );

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || "Failed to load variants");
        }

        variantsTable.innerHTML = "";

        const variants = data.variants;

        if (variants.length === 0) {
            variantsTable.innerHTML =
                '<tr><td colspan="4">No variants found.</td></tr>';
            return;
        }

        variants.forEach(variant => {
            const row = document.createElement("tr");

            // Size
            const sizeCell = document.createElement("td");
            sizeCell.textContent = variant.size;

            // Color
            const colorCell = document.createElement("td");
            colorCell.textContent = variant.color;

            // Quantity input
            const quantityCell = document.createElement("td");

            const quantityInput = document.createElement("input");
            quantityInput.type = "number";
            quantityInput.min = "0";
            quantityInput.step = "1";
            quantityInput.value = variant.quantity;
            quantityInput.className = "form-control";
            quantityInput.style.width = "100px";

            quantityCell.appendChild(quantityInput);

            // Save quantity button
            const actionCell = document.createElement("td");

            const saveButton = document.createElement("button");
            saveButton.className = "btn btn-primary btn-sm";
            saveButton.textContent = "Save Quantity";

            saveButton.onclick = function () {
                updateVariantQuantity(
                    variant.variant_id,
                    quantityInput.value
                );
            };

            actionCell.appendChild(saveButton);

            row.append(
                sizeCell,
                colorCell,
                quantityCell,
                actionCell
            );

            variantsTable.appendChild(row);
        });

    } catch (error) {
        showMessage(error.message, "danger");
    }
}

// Add new variant
variantForm.onsubmit = async function (event) {
    event.preventDefault();

    const productId =
        document.getElementById("variant-product-id").value;

    if (!productId) {
        showMessage("Choose a product first.", "warning");
        return;
    }

    const formData = new FormData(variantForm);

    const variant = {
        product_id: Number(productId),
        size: String(formData.get("size")).trim(),
        color: String(formData.get("color")).trim(),
        quantity: Number(formData.get("quantity"))
    };

    if (
        !variant.size ||
        !variant.color ||
        !Number.isInteger(variant.quantity) ||
        variant.quantity < 0
    ) {
        showMessage("Enter valid variant details.", "warning");
        return;
    }

    try {
        const response = await fetch(
            "index.php?api=admin-create-variant",
            {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify(variant)
            }
        );

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || "Failed to add variant");
        }

        showMessage("Variant added successfully!");

        variantForm.reset();
        await loadVariants(productId);

    } catch (error) {
        showMessage(error.message, "danger");
    }
};

// Update variant quantity
async function updateVariantQuantity(variantId, quantity) {
    const productId =
        document.getElementById("variant-product-id").value;

    if (
        quantity === "" ||
        !Number.isInteger(Number(quantity)) ||
        Number(quantity) < 0
    ) {
        showMessage("Enter a valid quantity.", "warning");
        return;
    }

    try {
        const response = await fetch(
            "index.php?api=admin-update-variant-quantity&id=" + variantId,
            {
                method: "PATCH",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    quantity: Number(quantity)
                })
            }
        );

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || "Failed to update quantity");
        }

        showMessage("Quantity updated successfully!");

        await loadVariants(productId);

    } catch (error) {
        showMessage(error.message, "danger");
    }
}

// Initial loading
loadCategories();
loadProducts();
