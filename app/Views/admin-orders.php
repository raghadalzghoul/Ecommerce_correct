
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Orders | SimpleShop</title>

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

    <h2 class="mb-4">Manage Orders</h2>

    <div id="message"></div>

    <h4>Orders List</h4>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Order ID</th>
                    <th>Customer ID</th>
                    <th>Total</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody id="orders-table">
                <tr>
                    <td colspan="6">Loading orders...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <section id="order-details" class="mt-5" hidden>
        <h4>Order Details</h4>

        <div class="border rounded p-3 mb-3">
            <p><strong>Order ID:</strong> <span id="detail-id"></span></p>
            <p><strong>Customer ID:</strong> <span id="detail-customer"></span></p>
            <p><strong>Shipping Address:</strong> <span id="detail-address"></span></p>
            <p><strong>Payment Method:</strong> <span id="detail-payment"></span></p>
            <p><strong>Status:</strong> <span id="detail-status"></span></p>
            <p><strong>Total:</strong> <span id="detail-total"></span></p>
        </div>

        <h5>Ordered Products</h5>

        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>Size</th>
                        <th>Color</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody id="order-items"></tbody>
            </table>
        </div>
    </section>

</main>

<script src="assets/js/admin-orders.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="assets/js/navbar.js"></script>
</body>
</html>
