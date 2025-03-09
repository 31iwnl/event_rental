<?php
session_start();
require_once '../google_sheets.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $productId = $data['product_id'] ?? null;
    $action = $data['action'] ?? null;
    $quantity = $data['quantity'] ?? null;

    if (!$productId || !$action) {
        echo json_encode(['status' => 'error', 'message' => 'Неверные данные']);
        exit;
    }

    if (!isset($_SESSION['cart'][$productId])) {
        echo json_encode(['status' => 'error', 'message' => 'Товар не найден в корзине']);
        exit;
    }
        
   if ($action === 'set') {
       if ($quantity !== null && is_numeric($quantity) && $quantity >=0) {
           $_SESSION['cart'][$productId]['quantity'] = (int)$quantity;
           if ($_SESSION['cart'][$productId]['quantity'] <= 0) {
               unset($_SESSION['cart'][$productId]);
           }
       } else {
           echo json_encode(['status' => 'error', 'message' => 'Неверное количество']);
           exit;
       }
   }    
   else  {
        echo json_encode(['status' => 'error', 'message' => 'Неверное действие']);
        exit;
    }

    echo json_encode(['status' => 'success']);
    exit;
} else {
    echo json_encode(['status' => 'error', 'message' => 'Метод не разрешен']);
    exit;
}
?>
