
const addForm = document.getElementById("add-form");
const editForm = document.getElementById("edit-form");
const table = document.getElementById("categories-table");
const message = document.getElementById("message");

let categories = [];

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

        categories = data.categories;
        table.innerHTML = "";

        if (categories.length === 0) {
            table.innerHTML =
                '<tr><td colspan="4">No categories found.</td></tr>';
            return;
        }

        categories.forEach(category => {
            const row = document.createElement("tr");

            // Image
            const imageCell = document.createElement("td");
            const image = document.createElement("img");
            image.src = category.image_url || "";
            image.alt = category.cat_name;
            image.width = 70;
            imageCell.appendChild(image);

            // Name
            const nameCell = document.createElement("td");
            nameCell.textContent = category.cat_name;

            // Description
            const descriptionCell = document.createElement("td");
            descriptionCell.textContent = category.cat_description;

            // Buttons
            const actionCell = document.createElement("td");

            const editButton = document.createElement("button");
            editButton.className = "btn btn-primary btn-sm me-2";
            editButton.textContent = "Edit";

            editButton.onclick = function () {
                editCategory(category.cat_id);
            };

            const deleteButton = document.createElement("button");
            deleteButton.className = "btn btn-danger btn-sm";
            deleteButton.textContent = "Delete";

            deleteButton.onclick = function () {
                deleteCategory(category.cat_id);
            };

            actionCell.append(editButton, deleteButton);

            row.append(
                imageCell,
                nameCell,
                descriptionCell,
                actionCell
            );

            table.appendChild(row);
        });

    } catch (error) {
        showMessage(error.message, "danger");
    }
}

// Add category
addForm.onsubmit = async function (event) {
    event.preventDefault();

    const formData = new FormData(addForm);

    try {
        const response = await fetch(
            "index.php?api=admin-create-category",
            {
                method: "POST",
                body: formData
            }
        );

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || "Failed to add category");
        }

        showMessage("Category added successfully!");

        addForm.reset();
        loadCategories();

    } catch (error) {
        showMessage(error.message, "danger");
    }
};

// Fill edit form
function editCategory(categoryId) {
    const category = categories.find(
        c => c.cat_id == categoryId
    );

    if (!category) {
        return;
    }

    document.getElementById("edit-id").value = category.cat_id;
    document.getElementById("edit-name").value = category.cat_name;
    document.getElementById("edit-description").value =
        category.cat_description || "";

    document.getElementById("edit-image").value = "";

    editForm.scrollIntoView({ behavior: "smooth" });
}

// Update category
editForm.onsubmit = async function (event) {
    event.preventDefault();

    const categoryId = document.getElementById("edit-id").value;

    if (!categoryId) {
        showMessage("Choose a category first.", "warning");
        return;
    }

    const formData = new FormData(editForm);

    try {
        const response = await fetch(
            "index.php?api=admin-update-category&id=" + categoryId,
            {
                method: "POST",
                body: formData
            }
        );

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || "Update failed");
        }

        showMessage("Category updated successfully!");

        editForm.reset();
        document.getElementById("edit-id").value = "";

        loadCategories();

    } catch (error) {
        showMessage(error.message, "danger");
    }
};

// Delete category
async function deleteCategory(categoryId) {
    if (!confirm("Are you sure you want to delete this category?")) {
        return;
    }

    try {
        const response = await fetch(
            "index.php?api=admin-delete-category&id=" + categoryId,
            {
                method: "DELETE"
            }
        );

        const data = await response.json();

        if (!response.ok || !data.success) {
            throw new Error(data.message || "Delete failed");
        }

        showMessage("Category deleted successfully!");

        loadCategories();

    } catch (error) {
        showMessage(error.message, "danger");
    }
}

// Load data
loadCategories();
