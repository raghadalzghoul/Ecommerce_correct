
<?php

require_once __DIR__ . '/../routes.php';

if (isset($_GET['api'])) {
    handleApiRequest();
    exit;
}



$page = $_GET['page'] ?? 'home';

switch ($page) {

    case 'home':
        require __DIR__ . '/../app/Views/home.php';
        break;


    case 'profile':
    require __DIR__ . '/../app/Views/profile.php';
    break;
    case 'register':
    require __DIR__ . '/../app/Views/register.php';
    break;
    case 'login':
    require __DIR__ . '/../app/Views/login.php';
    break;

    case 'product':
    require __DIR__ . '/../app/Views/product.php';
    break;


    case 'cart':
    require __DIR__ . '/../app/Views/cart.php';
    break;

    case 'checkout':
    require __DIR__ . '/../app/Views/checkout.php';
    break;

    case 'receipt':
    require __DIR__ . '/../app/Views/receipt.php';
    break;
    



    case 'admin-dashboard':

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // User not logged in
    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?page=login');
        exit;
    }

    // User is not admin
    if (($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);
        require __DIR__ . '/../app/Views/403.php';
        exit;
    }

    // Admin only
    require __DIR__ . '/../app/Views/admin-dashboard.php';
    break;





    case 'admin-products':

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?page=login');
        exit;
    }

    if (($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);
        require __DIR__ . '/../app/Views/403.php';
        exit;
    }

    require __DIR__ . '/../app/Views/admin-products.php';
    break;

   case 'admin-categories':

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?page=login');
        exit;
    }

    if (($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);
        require __DIR__ . '/../app/Views/403.php';
        exit;
    }

    require __DIR__ . '/../app/Views/admin-categories.php';
    break;

    case 'admin-orders':

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php?page=login');
        exit;
    }

    if (($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);
        require __DIR__ . '/../app/Views/403.php';
        exit;
    }

    require __DIR__ . '/../app/Views/admin-orders.php';
    break;
    default:
        http_response_code(404);
        echo '404 - Page Not Found';
        break;
}




