
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Products | SimpleShop</title>

    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
    >


    <link rel="stylesheet" href="assets/css/bubble.css">
</head>
<body>

<?php require __DIR__ . '/partials/navbar.php'; ?>

<nav class="navbar bg-light border-bottom">
    <div class="container">
        <a href="index.php?page=admin-dashboard"
           class="navbar-brand fw-bold">
            SimpleShop Admin
        </a>

        <a href="index.php?page=admin-dashboard"
           class="btn btn-outline-dark">
            Dashboard
        </a>
    </div>
</nav>

<main class="container my-5">

    <h2 class="mb-4">Manage Products</h2>

    <div id="message"></div>

    <!-- ADD PRODUCT -->
    <h4>Add New Product</h4>

    <form
        id="product-form"
        class="border rounded p-3 mb-5"
        enctype="multipart/form-data"
    >

        <div class="mb-3">
            <label class="form-label">Product Name</label>
            <input
                type="text"
                name="name"
                class="form-control"
                required
            >
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea
                name="description"
                class="form-control"
                required
            ></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Price</label>
            <input
                type="number"
                name="price"
                class="form-control"
                min="0.01"
                step="0.01"
                required
            >
        </div>

        <div class="mb-3">
            <label class="form-label">Category</label>
            <select
                name="cat_id"
                id="category-select"
                class="form-select"
                required
            >
                <option value="">Choose Category</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Product Image</label>
            <input
                type="file"
                name="image"
                class="form-control"
                accept="image/jpeg,image/png,image/webp"
                required
            >
        </div>

        <button type="submit" class="btn btn-dark">
            Add Product
        </button>

    </form>

    <!-- PRODUCTS TABLE -->
    <h4 class="mb-3">Products List</h4>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">

            <thead class="table-light">
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Category ID</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody id="products-table">
                <tr>
                    <td colspan="5">Loading products...</td>
                </tr>
            </tbody>

        </table>
    </div>

    <!-- PRODUCT VARIANTS -->
    <section id="variants-section" class="mt-5" hidden>

        <h4>Manage Product Variants</h4>

        <p>
            Product:
            <strong id="variant-product-name"></strong>
        </p>

        <input type="hidden" id="variant-product-id">

        <h5>Available Variants</h5>

        <div class="table-responsive">
            <table class="table table-bordered align-middle">

                <thead class="table-light">
                    <tr>
                        <th>Size</th>
                        <th>Color</th>
                        <th>Quantity</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody id="variants-table">
                    <tr>
                        <td colspan="4">
                            Choose a product to view its variants.
                        </td>
                    </tr>
                </tbody>

            </table>
        </div>

        <h5 class="mt-4">Add New Variant</h5>

        <form id="variant-form" class="border rounded p-3">

            <div class="mb-3">
                <label class="form-label">Size</label>
                <input
                    type="text"
                    name="size"
                    class="form-control"
                    placeholder="S, M, L, XL"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">Color</label>
                <input
                    type="text"
                    name="color"
                    class="form-control"
                    placeholder="Black, White, Blue"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">Quantity</label>
                <input
                    type="number"
                    name="quantity"
                    class="form-control"
                    min="0"
                    step="1"
                    required
                >
            </div>

            <button type="submit" class="btn btn-dark">
                Add Variant
            </button>

        </form>

    </section>

    <!-- EDIT PRODUCT -->
    <section class="mt-5">

        <h4>Edit Product</h4>

        <form
            id="edit-product-form"
            class="border rounded p-3 mb-4"
            enctype="multipart/form-data"
        >

            <input type="hidden" id="edit-product-id">

            <div class="mb-3">
                <label class="form-label">Product Name</label>
                <input
                    type="text"
                    name="name"
                    id="edit-name"
                    class="form-control"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">Description</label>
                <textarea
                    name="description"
                    id="edit-description"
                    class="form-control"
                    required
                ></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Price</label>
                <input
                    type="number"
                    name="price"
                    id="edit-price"
                    class="form-control"
                    min="0.01"
                    step="0.01"
                    required
                >
            </div>

            <div class="mb-3">
                <label class="form-label">Category</label>
                <select
                    name="cat_id"
                    id="edit-category"
                    class="form-select"
                    required
                >
                    <option value="">Choose Category</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">
                    New Image (Optional)
                </label>

                <input
                    type="file"
                    name="image"
                    id="edit-image"
                    class="form-control"
                    accept="image/jpeg,image/png,image/webp"
                >

                <small class="text-muted">
                    Leave empty to keep the current image.
                </small>
            </div>

            <button type="submit" class="btn btn-primary">
                Save Changes
            </button>

        </form>

    </section>

</main>

<script src="assets/js/admin-products.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="assets/js/navbar.js"></script>

</body>
</html>
