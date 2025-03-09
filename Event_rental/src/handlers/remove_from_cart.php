<?php
session_start();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $productName = $data['product_name'] ?? null;
    if (!$productName) {
        echo json_encode(['status' => 'error', 'message' => 'product_name не указан']);
        exit;
    }

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$productName])) {
        unset($_SESSION['cart'][$productName]);
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Товар не найден в корзине']);
    }

    exit;
} else {
    echo json_encode(['status' => 'error', 'message' => 'Метод не разрешен']);
    exit;
}
?>
