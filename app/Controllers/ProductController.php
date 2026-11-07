<?php

require_once __DIR__ . '/../Models/Product.php';
require_once __DIR__ . '/../Core/CloudinaryUploader.php';

class ProductController
{
    private Product $product;

    public function __construct()
    {
        $this->product = new Product();
    }

    public function index(): void
    {
        $products = $this->product->getAllProducts();

        header('Content-Type: application/json; charset=utf-8');

        echo json_encode([
            'success' => true,
            'products' => $products
        ]);
    }





    public function show(): void
{
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if ($id === false || $id === null || $id <= 0) {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid product ID'
        ]);
        return;
    }

    $product = $this->product->getProductById($id);

    if (!$product) {
        http_response_code(404);

        echo json_encode([
            'success' => false,
            'message' => 'Product not found'
        ]);
        return;
    }

    echo json_encode([
        'success' => true,
        'product' => $product
    ]);
}




public function byCategory(): void
{
    $catId = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if ($catId === false || $catId === null || $catId <= 0) {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid category ID'
        ]);
        return;
    }

    $products = $this->product->getProductsByCategory($catId);

    echo json_encode([
        'success' => true,
        'products' => $products
    ]);
}




public function search(): void
{
    $keyword = trim($_GET['keyword'] ?? '');

    if ($keyword === '') {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Search keyword is required'
        ]);
        return;
    }

    $products = $this->product->searchProducts($keyword);

    echo json_encode([
        'success' => true,
        'products' => $products
    ]);
}






public function variants(): void
{
    $productId = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );

    if ($productId === false || $productId === null || $productId <= 0) {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid product ID'
        ]);
        return;
    }

    $product = $this->product->getProductById($productId);

    if ($product === false) {
        http_response_code(404);

        echo json_encode([
            'success' => false,
            'message' => 'Product not found'
        ]);
        return;
    }

    $variants = $this->product->getProductVariants($productId);

    echo json_encode([
        'success' => true,
        'product_id' => $productId,
        'variants' => $variants
    ]);
}





public function create(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Please login first'
        ]);
        return;
    }

    if (($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Admin access required'
        ]);
        return;
    }

    // Read form-data
    $name = $_POST['name'] ?? null;
    $description = $_POST['description'] ?? null;
    $price = $_POST['price'] ?? null;
    $catId = filter_var(
        $_POST['cat_id'] ?? null,
        FILTER_VALIDATE_INT
    );

    if (
        !is_string($name) ||
        trim($name) === '' ||
        strlen(trim($name)) > 255 ||
        !is_string($description) ||
        !is_numeric($price) ||
        !is_finite((float) $price) ||
        (float) $price <= 0 ||
        $catId === false ||
        $catId === null ||
        $catId <= 0
    ) {
        http_response_code(422);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid product information'
        ]);
        return;
    }

    // Validate required image
    $image = $_FILES['image'] ?? null;

    if (
        !is_array($image) ||
        is_array($image['error'] ?? null) ||
        ($image['error'] ?? null) !== UPLOAD_ERR_OK ||
        !isset($image['tmp_name'], $image['size']) ||
        !is_string($image['tmp_name']) ||
        !is_uploaded_file($image['tmp_name']) ||
        $image['size'] <= 0 ||
        $image['size'] > 2 * 1024 * 1024
    ) {
        http_response_code(422);
        echo json_encode([
            'success' => false,
            'message' => 'A valid image (max 2 MB) is required'
        ]);
        return;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($image['tmp_name']);

    if (
        !in_array(
            $mime,
            ['image/jpeg', 'image/png', 'image/webp'],
            true
        ) ||
        getimagesize($image['tmp_name']) === false
    ) {
        http_response_code(422);
        echo json_encode([
            'success' => false,
            'message' => 'Only JPG, PNG and WEBP images are allowed'
        ]);
        return;
    }

    try {
        // Upload image to Cloudinary
        $uploader = new CloudinaryUploader();

        $uploaded = $uploader->upload(
            $image['tmp_name'],
            'ecommerce/products'
        );

        $imageUrl = $uploaded['image_url'];

        // Save product to MySQL
        $productId = $this->product->createProduct(
            trim($name),
            trim($description),
            (float) $price,
            (int) $catId,
            $imageUrl
        );

        http_response_code(201);

        echo json_encode([
            'success' => true,
            'message' => 'Product created successfully',
            'product_id' => $productId,
            'image_url' => $imageUrl
        ]);

    } catch (Throwable $e) {
        error_log($e->getMessage());

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Failed to create product'
        ]);
    }
}






public function update(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Authentication
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Please login first'
        ]);
        return;
    }

    // Admin only
    if (($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Admin access required'
        ]);
        return;
    }

    // Validate product ID
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id || $id <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid product ID'
        ]);
        return;
    }

    // Read multipart/form-data
    $name = $_POST['name'] ?? null;
    $description = $_POST['description'] ?? null;
    $price = $_POST['price'] ?? null;

    $catId = filter_var(
        $_POST['cat_id'] ?? null,
        FILTER_VALIDATE_INT
    );

    if (
        !is_string($name) ||
        trim($name) === '' ||
        strlen(trim($name)) > 255 ||
        !is_string($description) ||
        !is_numeric($price) ||
        !is_finite((float) $price) ||
        (float) $price <= 0 ||
        $catId === false ||
        $catId === null ||
        $catId <= 0
    ) {
        http_response_code(422);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid product information'
        ]);
        return;
    }

    // Image is optional
    $image = $_FILES['image'] ?? null;

    $hasImage = is_array($image) &&
        ($image['error'] ?? UPLOAD_ERR_NO_FILE)
            !== UPLOAD_ERR_NO_FILE;

    if ($hasImage) {
        if (
            is_array($image['error'] ?? null) ||
            $image['error'] !== UPLOAD_ERR_OK ||
            !isset($image['tmp_name'], $image['size']) ||
            !is_string($image['tmp_name']) ||
            !is_uploaded_file($image['tmp_name']) ||
            $image['size'] <= 0 ||
            $image['size'] > 2 * 1024 * 1024
        ) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => 'Invalid image or image exceeds 2 MB'
            ]);
            return;
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($image['tmp_name']);

        if (
            !in_array(
                $mime,
                ['image/jpeg', 'image/png', 'image/webp'],
                true
            ) ||
            getimagesize($image['tmp_name']) === false
        ) {
            http_response_code(422);
            echo json_encode([
                'success' => false,
                'message' => 'Only JPG, PNG and WEBP images are allowed'
            ]);
            return;
        }
    }

    try {
        // Get existing product
        $product = $this->product->getProductById((int) $id);

        if (!$product) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Product not found'
            ]);
            return;
        }

        // Keep existing image
        $imageUrl = $product['image_url'];

        // Replace image if a new file was uploaded
        if ($hasImage) {
            $uploader = new CloudinaryUploader();

            $uploaded = $uploader->upload(
                $image['tmp_name'],
                'ecommerce/products'
            );

            $imageUrl = $uploaded['image_url'];
        }

        // Update database
        $this->product->updateProduct(
            (int) $id,
            trim($name),
            trim($description),
            (float) $price,
            (int) $catId,
            $imageUrl
        );

        http_response_code(200);

        echo json_encode([
            'success' => true,
            'message' => 'Product updated successfully',
            'product_id' => (int) $id,
            'image_url' => $imageUrl
        ]);

    } catch (Throwable $e) {
        error_log($e->getMessage());

        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update product'
        ]);
    }
}



public function delete(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check login
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode([
            'success' => false,
            'message' => 'Please login first'
        ]);
        return;
    }

    // Check admin
    if (($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);
        echo json_encode([
            'success' => false,
            'message' => 'Access denied. Admin only'
        ]);
        return;
    }

    // Validate product ID
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if ($id === false || $id === null || $id <= 0) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid product ID'
        ]);
        return;
    }

    try {
        // Check if product exists and is not deleted
        if ($this->product->getProductById($id) === false) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Product not found or already deleted'
            ]);
            return;
        }

        // Soft delete product
        $deleted = $this->product->deleteProduct($id);

        if (!$deleted) {
            http_response_code(404);
            echo json_encode([
                'success' => false,
                'message' => 'Product not found or already deleted'
            ]);
            return;
        }

        http_response_code(200);

        echo json_encode([
            'success' => true,
            'message' => 'Product soft deleted successfully'
        ]);

    } catch (PDOException $e) {
        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Failed to delete product'
        ]);
    }
}






public function restore(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);

        echo json_encode([
            'success' => false,
            'message' => 'Please login first'
        ]);
        return;
    }

    if (($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);

        echo json_encode([
            'success' => false,
            'message' => 'Access denied. Admin only'
        ]);
        return;
    }

    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if ($id === false || $id === null || $id <= 0) {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid product ID'
        ]);
        return;
    }

    try {
        $restored = $this->product->restoreProduct($id);

        if (!$restored) {
            http_response_code(404);

            echo json_encode([
                'success' => false,
                'message' => 'Product not found or not deleted'
            ]);
            return;
        }

        echo json_encode([
            'success' => true,
            'message' => 'Product restored successfully'
        ]);

    } catch (PDOException $e) {
        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Failed to restore product'
        ]);
    }
}





public function createVariant(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check login
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);

        echo json_encode([
            'success' => false,
            'message' => 'Please login first'
        ]);
        return;
    }

    // Admin only
    if (($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);

        echo json_encode([
            'success' => false,
            'message' => 'Admin access required'
        ]);
        return;
    }

    // Read JSON body
    $data = json_decode(
        file_get_contents('php://input'),
        true
    );

    if (!is_array($data)) {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid JSON body'
        ]);
        return;
    }

    // Validate inputs
    $productId = filter_var(
        $data['product_id'] ?? null,
        FILTER_VALIDATE_INT
    );

    $size = $data['size'] ?? null;
    $color = $data['color'] ?? null;

    $quantity = filter_var(
        $data['quantity'] ?? null,
        FILTER_VALIDATE_INT
    );

    if (
        $productId === false ||
        $productId === null ||
        $productId <= 0 ||
        !is_string($size) ||
        trim($size) === '' ||
        strlen(trim($size)) > 50 ||
        !is_string($color) ||
        trim($color) === '' ||
        strlen(trim($color)) > 50 ||
        $quantity === false ||
        $quantity === null ||
        $quantity < 0
    ) {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid variant data'
        ]);
        return;
    }

    try {
        $variantId = $this->product->createVariant(
            (int) $productId,
            trim($size),
            trim($color),
            (int) $quantity
        );

        http_response_code(201);

        echo json_encode([
            'success' => true,
            'message' => 'Variant created successfully',
            'variant_id' => $variantId
        ]);

    } catch (DomainException $e) {

        http_response_code(409);

        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);

    } catch (Throwable $e) {

        error_log($e->getMessage());

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Failed to create variant'
        ]);
    }
}







public function updateVariantQuantity(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);

        echo json_encode([
            'success' => false,
            'message' => 'Please login first'
        ]);
        return;
    }

    if (($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);

        echo json_encode([
            'success' => false,
            'message' => 'Admin access required'
        ]);
        return;
    }

    $variantId = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );

    $data = json_decode(
        file_get_contents('php://input'),
        true
    );

    $quantity = is_array($data)
        ? filter_var(
            $data['quantity'] ?? null,
            FILTER_VALIDATE_INT
        )
        : false;

    if (
        !$variantId ||
        $variantId <= 0 ||
        $quantity === false ||
        $quantity === null ||
        $quantity < 0
    ) {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid variant ID or quantity'
        ]);
        return;
    }

    try {

        $updated = $this->product->updateVariantQuantity(
            (int) $variantId,
            (int) $quantity
        );

        if (!$updated) {
            http_response_code(404);

            echo json_encode([
                'success' => false,
                'message' => 'Variant not found'
            ]);
            return;
        }

        http_response_code(200);

        echo json_encode([
            'success' => true,
            'message' => 'Variant quantity updated successfully',
            'variant_id' => (int) $variantId,
            'quantity' => (int) $quantity
        ]);

    } catch (Throwable $e) {

        error_log($e->getMessage());

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Failed to update variant quantity'
        ]);
    }
}





}