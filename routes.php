
<?php

require_once __DIR__ . '/app/Controllers/AuthController.php';
require_once __DIR__ . '/app/Controllers/ProductController.php';
require_once __DIR__ . '/app/Controllers/CartController.php';
require_once __DIR__ . '/app/Controllers/OrderController.php';
require_once __DIR__ . '/app/Controllers/CategoryController.php';
require_once __DIR__ . '/app/Controllers/DashboardController.php';

function handleApiRequest(): void
{
    header('Content-Type: application/json; charset=utf-8');

    $api = $_GET['api'] ?? '';

    switch ($api) {
        case 'register':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                header('Allow: POST');
                http_response_code(405);

                echo json_encode([
                    'success' => false,
                    'message' => 'Method not allowed'
                ]);
                return;
            }

            $controller = new AuthController();
            $controller->register();
            break;

       
         
case 'login':

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Allow: POST');
        http_response_code(405);

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
        return;
    }

    $controller = new AuthController();
    $controller->login();
    break;





    
case 'logout':

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Allow: POST');
        http_response_code(405);

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
        return;
    }

    $controller = new AuthController();
    $controller->logout();
    break;





    case 'products':

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);

        break;
    }

    $controller = new ProductController();
    $controller->index();

    break;





    case 'product':

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        header('Allow: GET');

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
        break;
    }

    $controller = new ProductController();
    $controller->show();

    break;

       

    case 'products-by-category':

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        header('Allow: GET');

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
        break;
    }

    $controller = new ProductController();
    $controller->byCategory();

    break;


    case 'search-products':

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        header('Allow: GET');

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
        break;
    }

    $controller = new ProductController();
    $controller->search();

    break;







    case 'product-variants':

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        header('Allow: GET');

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);

        break;
    }

    $controller = new ProductController();
    $controller->variants();

    break;








    case 'admin-create-product':

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        header('Allow: POST');

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);

        break;
    }

    $controller = new ProductController();
    $controller->create();

    break;






    case 'admin-update-product':

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        header('Allow: PUT');

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
        break;
    }

    $controller = new ProductController();
    $controller->update();

    break;


    case 'admin-delete-product':

    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        http_response_code(405);
        header('Allow: DELETE');

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
        break;
    }

    $controller = new ProductController();
    $controller->delete();

    break;




    case 'admin-restore-product':

    if ($_SERVER['REQUEST_METHOD'] !== 'PATCH') {
        http_response_code(405);
        header('Allow: PATCH');

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
        break;
    }

    $controller = new ProductController();
    $controller->restore();

    break;






    case 'add-to-cart':

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        header('Allow: POST');

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
        break;
    }

    $controller = new CartController();
    $controller->add();

    break;




    case 'get-cart':

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        header('Allow: GET');

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
        break;
    }

    $controller = new CartController();
    $controller->index();

    break;








    case 'update-cart-item':

    if ($_SERVER['REQUEST_METHOD'] !== 'PATCH') {
        http_response_code(405);
        header('Allow: PATCH');

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
        break;
    }

    $controller = new CartController();
    $controller->update();

    break;



    


    case 'remove-cart-item':

    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        http_response_code(405);
        header('Allow: DELETE');

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
        break;
    }

    $controller = new CartController();
    $controller->remove();

    break;




    case 'place-order':

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        header('Allow: POST');

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
        break;
    }

    $controller = new OrderController();
    $controller->place();

    break;
    










    case 'my-orders':

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        header('Allow: GET');

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);

        break;
    }

    $controller = new OrderController();
    $controller->myOrders();

    break;






    case 'order-details':

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        header('Allow: GET');

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
        break;
    }

    $controller = new OrderController();
    $controller->show();

    break;







    case 'admin-orders':

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        header('Allow: GET');

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
        break;
    }

    $controller = new OrderController();
    $controller->adminIndex();

    break;


case 'admin-order-details':

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        header('Allow: GET');

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);

        break;
    }

    $controller = new OrderController();
    $controller->adminShow();

    break;









case 'admin-update-order-status':

    if ($_SERVER['REQUEST_METHOD'] !== 'PATCH') {

        http_response_code(405);
        header('Allow: PATCH');

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);

        break;
    }

    $controller = new OrderController();
    $controller->adminUpdateStatus();

    break;






    case 'admin-cancel-order':

    if ($_SERVER['REQUEST_METHOD'] !== 'PATCH') {

        http_response_code(405);
        header('Allow: PATCH');

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);

        break;
    }

    $controller = new OrderController();
    $controller->adminCancel();

    break;






    case 'admin-create-variant':

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

        http_response_code(405);
        header('Allow: POST');

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);

        break;
    }

    $controller = new ProductController();
    $controller->createVariant();

    break;







    case 'admin-update-variant-quantity':

    if ($_SERVER['REQUEST_METHOD'] !== 'PATCH') {
        http_response_code(405);
        header('Allow: PATCH');

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);

        break;
    }

    $controller = new ProductController();
    $controller->updateVariantQuantity();

    break;









    case 'me':

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {

        http_response_code(405);
        header('Allow: GET');

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);

        break;
    }

    $controller = new AuthController();
    $controller->me();

    break;




    case 'admin-create-category':

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

        http_response_code(405);
        header('Allow: POST');

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);

        break;
    }

    $controller = new CategoryController();
    $controller->create();

    break;












    case 'categories':

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {

        http_response_code(405);
        header('Allow: GET');

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);

        break;
    }

    $controller = new CategoryController();
    $controller->index();

    break;








    case 'admin-update-category':

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {

        http_response_code(405);
        header('Allow: POST');

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);

        break;
    }

    $controller = new CategoryController();
    $controller->update();

    break;









    case 'admin-delete-category':

    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {

        http_response_code(405);
        header('Allow: DELETE');

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);

        break;
    }

    $controller = new CategoryController();
    $controller->delete();

    break;






    case 'admin-dashboard':

    if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
        http_response_code(405);
        header('Allow: GET');

        echo json_encode([
            'success' => false,
            'message' => 'Method not allowed'
        ]);
        break;
    }

    $controller = new DashboardController();
    $controller->index();

    break;









       
    default:
            http_response_code(404);

            echo json_encode([
                'success' => false,
                'message' => 'API endpoint not found'
            ]);
    }


}
