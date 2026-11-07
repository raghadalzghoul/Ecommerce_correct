<?php

require_once __DIR__ . '/../Models/User.php';

class AuthController
{
    private User $user;

    public function __construct()
    {
        $this->user = new User();
    }



public function register(): void
{
    header('Content-Type: application/json; charset=utf-8');

    // 1. Read JSON request
    $data = json_decode(file_get_contents("php://input"), true);

    if (!is_array($data)) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid JSON data'
        ]);
        return;
    }

    // 2. Extract user data
    $name = $data['name'] ?? '';
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';
    $phone = $data['phone'] ?? null;

    // 3. Validate data types
    if (
        !is_string($name) ||
        !is_string($email) ||
        !is_string($password) ||
        ($phone !== null && !is_string($phone))
    ) {
        http_response_code(422);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid field types'
        ]);
        return;
    }

    $name = trim($name);
    $email = trim($email);
    $phone = $phone !== null ? trim($phone) : null;

    // 4. Check required fields
    if ($name === '' || $email === '' || $password === '') {
        http_response_code(422);
        echo json_encode([
            'success' => false,
            'message' => 'Name, email and password are required'
        ]);
        return;
    }

    // 5. Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        http_response_code(422);
        echo json_encode([
            'success' => false,
            'message' => 'Invalid email address'
        ]);
        return;
    }

    // 6. Validate password
    if (strlen($password) < 8) {
        http_response_code(422);
        echo json_encode([
            'success' => false,
            'message' => 'Password must be at least 8 characters'
        ]);
        return;
    }

    try {
        // 7. Check if email already exists
        $existingUser = $this->user->findByEmail($email);

        if ($existingUser) {
            http_response_code(409);
            echo json_encode([
                'success' => false,
                'message' => 'Email already registered'
            ]);
            return;
        }

        // 8. Hash password
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // 9. Create new user
        $userId = $this->user->create(
            $name,
            $email,
            $passwordHash,
            $phone
        );

        // 10. Return success response
        http_response_code(201);
        echo json_encode([
            'success' => true,
            'message' => 'Account created successfully',
            'user_id' => $userId
        ]);

    } catch (PDOException $e) {
        error_log($e->getMessage());

        // Handle duplicate email, including concurrent requests
        if ($e->getCode() === '23000') {
            http_response_code(409);
            echo json_encode([
                'success' => false,
                'message' => 'Email already registered'
            ]);
            return;
        }

        http_response_code(500);
        echo json_encode([
            'success' => false,
            'message' => 'Internal server error'
        ]);
    }
}



public function login(): void
{
    header('Content-Type: application/json; charset=utf-8');

    // 1. Read JSON data
    $data = json_decode(file_get_contents("php://input"), true);

    if (!is_array($data)) {
        http_response_code(400);

        echo json_encode([
            'success' => false,
            'message' => 'Invalid JSON data'
        ]);
        return;
    }

    // 2. Extract email and password
    $email = $data['email'] ?? '';
    $password = $data['password'] ?? '';

    // 3. Validate data types
    if (!is_string($email) || !is_string($password)) {
        http_response_code(422);

        echo json_encode([
            'success' => false,
            'message' => 'Email and password must be strings'
        ]);
        return;
    }

    $email = trim($email);

    // 4. Check required fields
    if ($email === '' || $password === '') {
        http_response_code(422);

        echo json_encode([
            'success' => false,
            'message' => 'Email and password are required'
        ]);
        return;
    }

    try {
        // 5. Find user by email
        $user = $this->user->findByEmail($email);

        // 6. Verify credentials
        if (!$user || !password_verify($password, $user['password'])) {
            http_response_code(401);

            echo json_encode([
                'success' => false,
                'message' => 'Invalid email or password'
            ]);
            return;
        }

        // 7. Configure session cookie before starting session
        if (session_status() === PHP_SESSION_NONE) {
            session_set_cookie_params([
                'lifetime' => 0,
                'path' => '/',
                'secure' => !empty($_SERVER['HTTPS'])
                    && $_SERVER['HTTPS'] !== 'off',
                'httponly' => true,
                'samesite' => 'Lax'
            ]);

            session_start();
        }

        // 8. Generate a new session ID
        session_regenerate_id(true);

        // 9. Store user information in session
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['role'] = $user['role'];

        // 10. Return success response
        http_response_code(200);

        echo json_encode([
            'success' => true,
            'message' => 'Login successful',
            'user' => [
                'user_id' => $user['user_id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'role' => $user['role']
            ]
        ]);

    } catch (Throwable $e) {
        error_log($e->getMessage());

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Internal server error'
        ]);
    }
}




public function logout(): void
{
    header('Content-Type: application/json; charset=utf-8');

    // 1. Start or resume the session
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // 2. Remove all session variables
    $_SESSION = [];

    // 3. Delete the session cookie
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();

        setcookie(session_name(), '', [
            'expires' => time() - 42000,
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => $params['samesite'] ?? 'Lax'
        ]);
    }

    // 4. Destroy the server-side session
    session_destroy();

    // 5. Return success response
    http_response_code(200);

    echo json_encode([
        'success' => true,
        'message' => 'Logged out successfully'
    ]);
}


public function me(): void
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);

        echo json_encode([
            'success' => false,
            'message' => 'Please login first'
        ]);

        return;
    }

    try {

        $user = $this->user->getUserById(
            (int) $_SESSION['user_id']
        );

        if (!$user) {
            http_response_code(401);

            echo json_encode([
                'success' => false,
                'message' => 'User account not found'
            ]);

            return;
        }

        http_response_code(200);

        echo json_encode([
            'success' => true,
            'user' => $user
        ]);

    } catch (Throwable $e) {

        error_log($e->getMessage());

        http_response_code(500);

        echo json_encode([
            'success' => false,
            'message' => 'Failed to fetch profile'
        ]);
    }
}


}