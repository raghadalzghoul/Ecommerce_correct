
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart | SimpleShop</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body>

<nav class="navbar bg-light border-bottom">
    <div class="container">
        <a href="?page=home" class="navbar-brand fw-bold">
            SimpleShop
        </a>
        <a href="?page=home" class="btn btn-outline-dark">
            Continue Shopping
        </a>
    </div>
</nav>

<main class="container my-5">

    <h2 class="mb-4">My Shopping Cart</h2>

    <div id="message"></div>

    <div class="table-responsive">
        <table class="table table-bordered align-middle">
            <thead class="table-light">
                <tr>
                    <th>Product</th>
                    <th>Size / Color</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="cart-items">
                <tr>
                    <td colspan="6">Loading cart...</td>
                </tr>
            </tbody>
        </table>
    </div>

    <h4 class="text-end mt-4">
        Total: $<span id="cart-total">0.00</span>
    </h4>

    <div class="text-end mt-3">
        <a href="?page=checkout" class="btn btn-dark">
            Proceed to Checkout
        </a>
    </div>

</main>

<script src="assets/js/cart.js"></script>
</body>
</html>
