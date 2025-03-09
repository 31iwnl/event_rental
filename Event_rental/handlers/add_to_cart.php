<?php
session_start();
require_once '../google_sheets.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $productId = $data['product_id'] ?? null;
    $quantity = (int)($data['quantity'] ?? 1);
    $days = (int)($data['days'] ?? 1);

    if (!$productId) {
        echo json_encode(['status' => 'error', 'message' => 'product_id не указан']);
        exit;
    }

    $gs = new GoogleSheetsHelper();
    $allSheetsData = [];
    foreach($gs->getCategories() as $cat) {
        $allSheetsData = array_merge($allSheetsData, $gs->getSheetData($cat['name']));
    }

    $product = null;
    foreach ($allSheetsData as $p) {
        if ($p['name'] === $productId) {
            $product = $p;
            break;
        }
    }

    if (!$product) {
        echo json_encode(['status' => 'error', 'message' => 'Товар не найден']);
        exit;
    }

    $availableQuantity = (int)($product['quantity'] ?? 0);
    $price = (int)($product['price'] ?? 0);

    if ($quantity > $availableQuantity) {
        echo json_encode([
            'status' => 'kol',
            'message' => "Недостаточно товара. Доступно: $availableQuantity",
        ]);
        exit;
    }

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    if (isset($_SESSION['cart'][$productId])) {
        $newQuantity = $_SESSION['cart'][$productId]['quantity'] + $quantity;
        if ($newQuantity > $availableQuantity) {
            echo json_encode([
                'status' => 'kol',
                'message' => "Недостаточно товара. Доступно: $availableQuantity",
            ]);
            exit;
        }
        $_SESSION['cart'][$productId]['quantity'] = $newQuantity;
    } else {
        $_SESSION['cart'][$productId] = [
            'name' => $productId,
            'quantity' => $quantity,
            'days' => $days,
            'price' => $price,
        ];
    }

    echo json_encode(['status' => 'success']);
    exit;
} else {
    echo json_encode(['status' => 'error', 'message' => 'Метод не разрешен']);
    exit;
}
?>
