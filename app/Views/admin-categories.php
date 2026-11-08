
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories | SimpleShop</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">


          <link rel="stylesheet" href="assets/css/bubble.css">
</head>
<body>

<?php require __DIR__ . '/partials/navbar.php'; ?>

<nav class="navbar bg-light border-bottom">
    <div class="container">
        <a href="?page=admin-dashboard" class="navbar-brand fw-bold">
            SimpleShop Admin
        </a>

        <a href="?page=admin-dashboard" class="btn btn-outline-dark">
            Dashboard
        </a>
    </div>
</nav>

<main class="container my-5">

    <h2 class="mb-4">Manage Categories</h2>

    <div id="message"></div>

    <!-- Add Category -->
    <h4>Add New Category</h4>

    <form id="add-form" class="border rounded p-3 mb-5"
          enctype="multipart/form-data">

        <div class="mb-3">
            <label class="form-label">Category Name</label>
            <input type="text" name="cat_name"
                   class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="cat_description"
                      class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Category Image</label>
            <input type="file" name="image"
                   class="form-control"
                   accept="image/jpeg,image/png,image/webp"
                   required>
        </div>

        <button type="submit" class="btn btn-dark">
            Add Category
        </button>
    </form>

    <!-- Categories Table -->
    <h4 class="mb-3">Categories List</h4>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody id="categories-table">
                <tr>
                    <td colspan="4">Loading categories...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Edit Category -->
    <h4 class="mt-5">Edit Category</h4>

    <form id="edit-form" class="border rounded p-3"
          enctype="multipart/form-data">

        <input type="hidden" id="edit-id">

        <div class="mb-3">
            <label class="form-label">Category Name</label>
            <input type="text" name="cat_name" id="edit-name"
                   class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="cat_description" id="edit-description"
                      class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">New Image (Optional)</label>
            <input type="file" name="image" id="edit-image"
                   class="form-control"
                   accept="image/jpeg,image/png,image/webp">
        </div>

        <button type="submit" class="btn btn-primary">
            Save Changes
        </button>
    </form>

</main>

<script src="assets/js/admin-categories.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="assets/js/navbar.js"></script>
</body>
</html>
