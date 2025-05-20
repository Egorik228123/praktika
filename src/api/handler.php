<?php
    // handler.php
    require_once "../../models/UsersController.php";

    $controller = new UsersController();
    $action = $_POST['action'] ?? '';

    try {
        switch($action) {
            case 'create':
                $controller->insertUser($_POST);
                break;
                
            case 'edit':
                $controller->updateUser((int)$_POST['id'], $_POST);
                break;
                
            case 'delete':
                $controller->deleteUser((int)$_POST['id']);
                break;
                
            case 'view':
                $controller->getUser((int)$_POST['id']);
                break;
                
            case 'get-all':
                $controller->getAllUsers();
                break;
                
            default:
                throw new Exception("Неизвестное действие");
        }
    } catch(Exception $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false, 
            'error' => $e->getMessage()
        ]);
    }
?>