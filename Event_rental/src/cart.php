<?php
session_start();
$is_src = true;
require_once 'google_sheets.php';

if (isset($_POST['decrease_product'])) {
    $product_to_decrease = $_POST['decrease_product'];

    if (isset($_SESSION['cart'][$product_to_decrease])) {
        if ($_SESSION['cart'][$product_to_decrease]['quantity'] > 1) {
            $_SESSION['cart'][$product_to_decrease]['quantity']--;
        } else {
            unset($_SESSION['cart'][$product_to_decrease]);
        }
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

if (isset($_POST['remove_all'])) {
    unset($_SESSION['cart']);
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

$cart = $_SESSION['cart'] ?? [];
$gs = new GoogleSheetsHelper();
$all_products = [];

foreach($gs->getCategories() as $cat) {
    $all_products = array_merge($all_products, $gs->getSheetData($cat['name']));
}

$total = 0;

function findProductByName($name, $products) {
    foreach ($products as $product) {
        if ($product['name'] == $name) {
            return $product;
        }
    }
    return null;
}

include '../templates/header.php';
?>

<div class="container my-5">
    <h1>Корзина</h1>

    <?php if (empty($cart)): ?>
        <p>Корзина пуста.</p>
    <?php else: ?>
        <table class="table">
            <thead>
                <tr>
                    <th>Фото</th>
                    <th>Название товара</th>
                    <th>Количество</th>
                    <th>Дней</th>
                    <th>Цена за день</th>
                    <th>Итого</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cart as $product_name => $item):
                    $product = findProductByName($product_name, $all_products);
                    if ($product):
                ?>
                    <tr>
                        <td>
                            <?php if (isset($product['photo']) && filter_var($product['photo'], FILTER_VALIDATE_URL)): ?>
                                <img src="<?php echo htmlspecialchars($product['photo']); ?>" alt="<?php echo htmlspecialchars($product_name); ?>" style="max-width: 50px; max-height: 50px;">
                            <?php else: ?>
                                Нет фото
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($product_name); ?></td>
                        <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                        <td><?php echo htmlspecialchars($item['days']); ?></td>
                        <td><?php echo htmlspecialchars($item['price']); ?></td>
                        <td><?php echo htmlspecialchars($item['price'] * $item['quantity'] * $item['days']); ?></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="decrease_product" value="<?php echo htmlspecialchars($product_name); ?>">
                                <button type="submit" class="btn btn-sm btn-secondary">Удалить</button>
                            </form>
                        </td>
                    </tr>
                <?php
                    $total += $item['price'] * $item['quantity'] * $item['days'];
                    endif;
                endforeach; ?>
            </tbody>
        </table>
        <p>Итого: <?php echo htmlspecialchars($total); ?></p>
    <?php endif; ?>
    <?php
        if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    ?>
    <form method="post">
        <button type="submit" name="remove_all" class="btn btn-danger">Удалить все товары</button>
    </form>

    <a href="checkout.php" class="btn btn-primary">Перейти к оформлению заказа</a>

    <p><a href="terms_and_conditions.php">Условия аренды</a></p>
    <?php
}
    ?>
</div>


