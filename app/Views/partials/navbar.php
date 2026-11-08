
<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$isLoggedIn = isset($_SESSION['user_id']);
$isAdmin = $isLoggedIn &&
    ($_SESSION['role'] ?? '') === 'admin';
?>

<nav class="navbar navbar-expand-lg bubble-navbar">
    <div class="container">

        <a class="navbar-brand bubble-logo"
           href="index.php?page=home">
            🫧 Bubble<span>.</span>
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#bubbleNavbar"
                aria-controls="bubbleNavbar"
                aria-expanded="false"
                aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="bubbleNavbar">

            <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">

                <li class="nav-item">
                    <a class="nav-link" href="index.php?page=home">
                        Home
                    </a>
                </li>

                <?php if ($isLoggedIn): ?>

                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=cart">
                            Cart
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="index.php?page=profile">
                            Profile
                        </a>
                    </li>

                    <?php if ($isAdmin): ?>
                        <li class="nav-item">
                            <a class="nav-link bubble-dashboard"
                               href="index.php?page=admin-dashboard">
                                Dashboard
                            </a>
                        </li>
                    <?php endif; ?>

                    <li class="nav-item">
                        <button type="button"
                                id="navbar-logout"
                                class="btn bubble-btn-outline">
                            Logout
                        </button>
                    </li>

                <?php else: ?>

                    <li class="nav-item">
                        <a class="nav-link"
                           href="index.php?page=login">
                            Login
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="btn bubble-btn"
                           href="index.php?page=register">
                            Sign Up
                        </a>
                    </li>

                <?php endif; ?>

            </ul>
        </div>
    </div>
</nav>
