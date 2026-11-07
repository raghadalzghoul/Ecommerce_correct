
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | SimpleShop</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">
</head>
<body>

<nav class="navbar bg-light border-bottom">
    <div class="container">
        <a href="?page=home" class="navbar-brand fw-bold">
            SimpleShop
        </a>

        <a href="?page=cart" class="btn btn-outline-dark">
            My Cart
        </a>
    </div>
</nav>

<main class="container my-5" style="max-width: 550px;">

    <h2 class="mb-4">My Profile</h2>

    <div class="card p-4">

        <div id="profile">
            Loading profile...
        </div>

        <button id="logout-btn" class="btn btn-danger mt-4" disabled>
            Logout
        </button>

    </div>

    <p id="message" class="mt-3 text-danger"></p>

</main>

<script src="assets/js/profile.js"></script>

</body>
</html>
