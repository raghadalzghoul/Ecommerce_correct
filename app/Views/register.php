
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | SimpleShop</title>

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
    </div>
</nav>

<div class="container my-5" style="max-width: 450px;">

    <h2 class="mb-4">Create Account</h2>

    <form id="register-form">

        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" id="name"
                   class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" id="email"
                   class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Phone</label>
            <input type="tel" id="phone"
                   class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Password</label>
            <input type="password" id="password"
                   class="form-control" required>
        </div>

        <button type="submit" class="btn btn-dark w-100">
            Register
        </button>

    </form>

    <p id="message" class="mt-3"></p>

    <p class="mt-3">
        Already have an account?
        <a href="?page=login">Login</a>
    </p>

</div>

<script src="assets/js/register.js"></script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="assets/js/navbar.js"></script>

</body>
</html>
