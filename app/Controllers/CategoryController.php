<?php

require_once __DIR__ . '/../Models/Category.php';
require_once __DIR__ . '/../Core/CloudinaryUploader.php';

class CategoryController
{
    private Category $category;

    public function __construct()
    {
        $this->category = new Category();
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

    // Read multipart/form-data
    $name = $_POST['cat_name'] ?? null;
    $description = $_POST['cat_description'] ?? null;

    if (
        !is_string($name) ||
        trim($name) === '' ||
        strlen(trim($name)) > 255 ||
        !is_string($description) ||
        strlen($description) > 5000
    ) {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid category data'
        ]);
        return;
    }

    // Validate image upload
    if (
        !isset($_FILES['image']) ||
        !is_array($_FILES['image']) ||
        is_array($_FILES['image']['error'] ?? null) ||
        $_FILES['image']['error'] !== UPLOAD_ERR_OK
    ) {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Category image is required'
        ]);
        return;
    }

    $image = $_FILES['image'];

    // Maximum 2 MB
    if (
        $image['size'] <= 0 ||
        $image['size'] > 2 * 1024 * 1024
    ) {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Image must be 2 MB or smaller'
        ]);
        return;
    }

    if (!is_uploaded_file($image['tmp_name'])) {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid uploaded file'
        ]);
        return;
    }

    // Validate actual image MIME type
    $finfo = new finfo(FILEINFO_MIME_TYPE);

    $mime = $finfo->file($image['tmp_name']);

    $allowedTypes = [
        'image/jpeg',
        'image/png',
        'image/webp'
    ];

    if (
        !in_array($mime, $allowedTypes, true) ||
        getimagesize($image['tmp_name']) === false
    ) {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Only valid JPG, PNG and WEBP images are allowed'
        ]);
        return;
    }

    try {

        // Upload image to Cloudinary
        $uploader = new CloudinaryUploader();

        $uploaded = $uploader->upload(
            $image['tmp_name'],
            'ecommerce/categories'
        );

        $imageUrl = $uploaded['image_url'];

        // Save Cloudinary URL in MySQL
        $categoryId = $this->category->createCategory(
            trim($name),
            trim($description),
            $imageUrl
        );

        http_response_code(201);

        echo json_encode([
            'success' => true,
            'message' => 'Category created successfully',
            'cat_id' => $categoryId,
            'image_url' => $imageUrl
        ]);

    } catch (Throwable $e) {

        error_log($e->getMessage());

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Failed to create category'
        ]);
    }
}












public function index(): void
{
    try {

        $categories = $this->category->getAllCategories();

        http_response_code(200);

        echo json_encode([
            'success' => true,
            'categories' => $categories
        ]);

    } catch (Throwable $e) {

        error_log($e->getMessage());

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Failed to fetch categories'
        ]);
    }
}









public function update(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check authentication
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

    // Validate category ID
    $categoryId = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );

    if (!$categoryId || $categoryId <= 0) {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid category ID'
        ]);
        return;
    }

    // Read multipart/form-data
    $name = $_POST['cat_name'] ?? null;
    $description = $_POST['cat_description'] ?? null;

    if (
        !is_string($name) ||
        trim($name) === '' ||
        strlen(trim($name)) > 255 ||
        !is_string($description) ||
        strlen($description) > 5000
    ) {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid category data'
        ]);
        return;
    }

    // Image is optional
    $hasImage = isset($_FILES['image']) &&
        ($_FILES['image']['error'] ?? UPLOAD_ERR_NO_FILE)
            !== UPLOAD_ERR_NO_FILE;

    if ($hasImage) {

        $image = $_FILES['image'];

        if (
            !is_array($image) ||
            is_array($image['error'] ?? null) ||
            $image['error'] !== UPLOAD_ERR_OK
        ) {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Invalid image upload'
            ]);
            return;
        }

        if (
            $image['size'] <= 0 ||
            $image['size'] > 2 * 1024 * 1024 ||
            !is_uploaded_file($image['tmp_name'])
        ) {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Image must be valid and 2 MB or smaller'
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
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Only JPG, PNG and WEBP images are allowed'
            ]);
            return;
        }
    }

    try {

        // Get current category
        $category = $this->category->getCategoryById(
            (int) $categoryId
        );

        if (!$category) {
            http_response_code(404);

            echo json_encode([
                'success' => false,
                'message' => 'Category not found'
            ]);
            return;
        }

        // Keep old image by default
        $imageUrl = $category['image_url'];

        // Upload replacement image if provided
        if ($hasImage) {

            $uploader = new CloudinaryUploader();

            $uploaded = $uploader->upload(
                $_FILES['image']['tmp_name'],
                'ecommerce/categories'
            );

            $imageUrl = $uploaded['image_url'];
        }

        // Update MySQL
        $this->category->updateCategory(
            (int) $categoryId,
            trim($name),
            trim($description),
            $imageUrl
        );

        http_response_code(200);

        echo json_encode([
            'success' => true,
            'message' => 'Category updated successfully',
            'cat_id' => (int) $categoryId,
            'image_url' => $imageUrl
        ]);

    } catch (Throwable $e) {

        error_log($e->getMessage());

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Failed to update category'
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

    // Admin only
    if (($_SESSION['role'] ?? '') !== 'admin') {
        http_response_code(403);

        echo json_encode([
            'success' => false,
            'message' => 'Admin access required'
        ]);
        return;
    }

    // Validate category ID
    $categoryId = filter_input(
        INPUT_GET,
        'id',
        FILTER_VALIDATE_INT
    );

    if (!$categoryId || $categoryId <= 0) {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid category ID'
        ]);
        return;
    }

    try {

        $deleted = $this->category->deleteCategory(
            (int) $categoryId
        );

        if (!$deleted) {
            http_response_code(404);

            echo json_encode([
                'success' => false,
                'message' => 'Category not found'
            ]);
            return;
        }

        http_response_code(200);

        echo json_encode([
            'success' => true,
            'message' => 'Category deleted successfully'
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
            'message' => 'Failed to delete category'
        ]);
    }
}


}