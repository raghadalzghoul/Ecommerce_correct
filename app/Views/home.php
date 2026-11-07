
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SimpleShop</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">

    <link rel="stylesheet" href="assets/css/style.css">
</head>

<body>

<nav class="navbar navbar-expand-lg bg-light border-bottom">
    <div class="container">
        <a class="navbar-brand fw-bold" href="?page=home">
            SimpleShop
        </a>

        <div>
            <a href="?page=home" class="btn btn-link">Home</a>
            <a href="#products" class="btn btn-link">Products</a>
            <a href="?page=cart" class="btn btn-outline-dark">Cart</a>
            <a href="?page=profile" class="btn btn-outline-dark">
    My Profile
</a>
        </div>
    </div>
</nav>

<section class="hero text-center">
    <div class="container">
        <h1>Welcome to SimpleShop</h1>
        <p>Find your favorite clothes at great prices.</p>
        <a href="#products" class="btn btn-dark">
            Shop Now
        </a>
    </div>
</section>

<main class="container my-5" id="products">

    <h2 class="mb-4">Our Products</h2>

    <div id="products-container" class="row g-4">
        <p>Loading products...</p>
    </div>

</main>

<footer class="bg-light text-center p-3 border-top">
    <p class="mb-0">SimpleShop &copy; 2026</p>
</footer>

<script src="assets/js/home.js"></script>

</body>
</html>
