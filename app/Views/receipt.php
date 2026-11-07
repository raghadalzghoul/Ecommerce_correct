
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Receipt | SimpleShop</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body>

<nav class="navbar bg-light border-bottom">
    <div class="container">
        <a href="?page=home" class="navbar-brand fw-bold">
            SimpleShop
        </a>
    </div>
</nav>

<div class="container my-5" style="max-width: 600px;">

    <h2>Order Receipt</h2>

    <div class="card p-4 mt-4" id="receipt">
        Loading order details...
    </div>

    <a href="?page=home" class="btn btn-dark mt-4">
        Continue Shopping
    </a>

</div>

<script src="assets/js/receipt.js"></script>

</body>
</html>
