
<?php

require_once __DIR__ . '/../routes.php';

if (isset($_GET['api'])) {
    handleApiRequest();
    exit;
}

// Temporary homepage
echo "Welcome to Ecommerce!";
