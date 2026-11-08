
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout | SimpleShop</title>

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
        <a href="?page=cart" class="btn btn-outline-dark">
            Back to Cart
        </a>
    </div>
</nav>

<main class="container my-5" style="max-width: 600px;">

    <h2 class="mb-4">Checkout</h2>

    <form id="checkout-form">

        <div class="mb-3">
            <label class="form-label">Shipping Address</label>
            <textarea
                id="shipping-address"
                class="form-control"
                rows="3"
                placeholder="Enter your full shipping address"
                required
            ></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Payment Method</label>
            <input
                type="text"
                class="form-control"
                value="Cash on Delivery (COD)"
                readonly
            >
        </div>

        <button type="submit" id="place-order-btn" class="btn btn-dark w-100">
            Place Order
        </button>

    </form>

    <div id="message" class="mt-3"></div>

</main>

<script src="assets/js/checkout.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="assets/js/navbar.js"></script>

</body>
</html>
