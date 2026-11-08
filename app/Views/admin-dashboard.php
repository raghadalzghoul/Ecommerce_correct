
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | SimpleShop</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">


          <link rel="stylesheet" href="assets/css/bubble.css">
</head>
<body>

<?php require __DIR__ . '/partials/navbar.php'; ?>

<nav class="navbar bg-light border-bottom">
    <div class="container">
        <a href="?page=home" class="navbar-brand fw-bold">
            SimpleShop
        </a>
        <a href="?page=home" class="btn btn-outline-dark">
            Back to Store
        </a>
    </div>
</nav>

<main class="container my-5">

    <h2 class="mb-4">Admin Dashboard</h2>

    <div id="message"></div>

    <div class="row g-4">

        <div class="col-md-3">
            <div class="card p-3">
                <h6>Total Revenue</h6>
                <h3 id="revenue">0.00</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3">
                <h6>Orders</h6>
                <h3 id="orders">0</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3">
                <h6>Products</h6>
                <h3 id="products">0</h3>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card p-3">
                <h6>Customers</h6>
                <h3 id="customers">0</h3>
            </div>
        </div>

    </div>

    <h4 class="mt-5 mb-3">Management</h4>

    <div class="d-flex flex-wrap gap-2">
        <a href="?page=admin-products" class="btn btn-dark">
            Manage Products
        </a>

        <a href="?page=admin-categories" class="btn btn-dark">
            Manage Categories
        </a>

        <a href="?page=admin-orders" class="btn btn-dark">
            Manage Orders
        </a>
    </div>

</main>

<script src="assets/js/admin-dashboard.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="assets/js/navbar.js"></script>
</body>
</html>
