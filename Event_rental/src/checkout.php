<?php
require_once __DIR__.'/../vendor/autoload.php';
session_start();
$is_src = true;

// Конфигурация Google Sheets, SHEET_ID айди таблицы
const SHEET_ID = '1EzLUCZKYyB8G1GR7gJuzGWP80-Qct_PdjSaxvoENcTA';

$client = new Google\Client();
$client->setAuthConfig(__DIR__.'/../credentials.json');
$client->addScope(Google\Service\Sheets::SPREADSHEETS);
$service = new Google\Service\Sheets($client);


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';
    $cart = $_SESSION['cart'] ?? [];

    if (empty($name) || empty($phone)) {
        $_SESSION['messages'][] = 'Пожалуйста, заполните все обязательные поля.';
        header('Location: checkout.php');
        exit;
    }

    $cart_contents = [];
    $total_price = 0;
    foreach ($cart as $product_name => $item) {
        $product_name = $product_name;
        $quantity = $item['quantity'];
        $days = $item['days'];
        $price = $item['price'] ?? 0;
        $total = $price * $quantity * $days;
        $cart_contents[] = "$product_name: $quantity шт. на $days дней (Итого: $total ₽)";
        $total_price += $total;
    }
    $cart_contents_str = implode(", ", $cart_contents);

    $current_date = date("Y-m-d H:i:s");

    try {
        $sheet = $service->spreadsheets_values;
        $range = 'Заказы!A1:E1';
        $values = [
            [
                $current_date,
                $name,
                $phone,
                $payment_method,
                $cart_contents_str
            ],
        ];
        $body = new Google\Service\Sheets\ValueRange([
            'values' => $values
        ]);
        $params = [
            'valueInputOption' => 'USER_ENTERED'
        ];

        $result = $sheet->append(SHEET_ID, $range, $body, $params);

    } catch (Exception $e) {
        error_log('Ошибка при сохранении заказа: ' . $e->getMessage());
        $_SESSION['messages'][] = 'Произошла ошибка при сохранении заказа. Пожалуйста, попробуйте позже.';
        header('Location: checkout.php');
        exit;
    }

    $_SESSION['cart'] = [];
    $_SESSION['messages'][] = 'Заказ успешно оформлен!';
    header('Location: ../index.php');
    exit;
}

$name = $_SESSION['user_name'] ?? '';
$phone = $_SESSION['user_phone'] ?? '';
$cart = $_SESSION['cart'] ?? [];

include '../templates/header.php';
?>

<div class="container my-5">
    <h1 class="text-center mb-4">Оформление заказа</h1>
        <form method="post">
            <div class="mb-3">
                <label for="name" class="form-label">ФИО</label>
                <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($name); ?>" required>
            </div>
            <div class="mb-3">
                <label for="phone" class="form-label">Телефон</label>
                <input type="tel" class="form-control" id="phone" name="phone" value="<?php echo htmlspecialchars($phone); ?>" required>
            </div>
            <div class="mb-3">
                <label for="payment_method" class="form-label">Способ оплаты</label>
                <select class="form-select" id="payment_method" name="payment_method" required>
                    <option value="card">Карта</option>
                    <option value="cash">Наличные</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Подтвердить заказ</button>
        </form>
</div>


