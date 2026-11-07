
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SimpleShop</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body>

<div class="container mt-5" style="max-width: 450px;">

    <h2 class="mb-4">Login</h2>

    <form id="login-form">

        <div class="mb-3">
            <label>Email</label>
            <input type="email" id="email"
                   class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" id="password"
                   class="form-control" required>
        </div>

        <button type="submit" class="btn btn-dark w-100">
            Login
        </button>

    </form>
<p class="mt-3">
    Don't have an account?
    <a href="?page=register">Register here</a>
</p>
    <p id="message" class="mt-3"></p>

</div>

<script src="assets/js/login.js"></script>

</body>
</html>
