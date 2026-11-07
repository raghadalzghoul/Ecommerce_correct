
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Products | SimpleShop</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body>

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

    <h2 class="mb-4">Manage Products</h2>

    <div id="message"></div>

    <h4>Add New Product</h4>

    <form id="product-form" class="border rounded p-3 mb-5"
          enctype="multipart/form-data">

        <div class="mb-3">
            <label class="form-label">Product Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Description</label>
            <textarea name="description" class="form-control" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Price</label>
            <input type="number" name="price" class="form-control"
                   min="0.01" step="0.01" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Category</label>
            <select name="cat_id" id="category-select"
                    class="form-select" required>
                <option value="">Choose Category</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Product Image</label>
            <input type="file" name="image" class="form-control"
                   accept="image/jpeg,image/png,image/webp" required>
        </div>

        <button type="submit" class="btn btn-dark">
            Add Product
        </button>

    </form>

    <h4 class="mb-3">Products List</h4>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Price</th>
                    <th>Category ID</th>
                </tr>
            </thead>

            <tbody id="products-table">
                <tr>
                    <td colspan="4">Loading products...</td>
                </tr>
            </tbody>
        </table>
    </div>

</main>

<script src="assets/js/admin-products.js"></script>
</body>
</html>
