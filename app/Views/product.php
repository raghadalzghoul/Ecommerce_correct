
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details | SimpleShop</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
          <link rel="stylesheet" href="assets/css/bubble.css">

    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php require __DIR__ . '/partials/navbar.php'; ?>

<nav class="navbar bg-light border-bottom">
    <div class="container">
        <a class="navbar-brand fw-bold" href="?page=home">
            SimpleShop
        </a>

        <a href="?page=cart" class="btn btn-outline-dark">
            Cart
        </a>
    </div>
</nav>

<main class="container my-5">

    <a href="?page=home" class="text-dark">
        &larr; Back to Products
    </a>

    <div id="message" class="mt-3"></div>

    <div class="row mt-4 g-4" id="product-container">
        <p>Loading product...</p>
    </div>

</main>

<script src="assets/js/product.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="assets/js/navbar.js"></script>
</body>
</html>
