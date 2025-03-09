<?php
session_start();
$is_src = true;
require_once __DIR__ . '/../vendor/autoload.php';


$gs = new GoogleSheetsHelper();
$category_id = (int)($_GET['id'] ?? 0);

try {
    $sheets = $gs->getCategories();
    if ($category_id < 1 || $category_id > count($sheets)) {
        throw new Exception('Category not found');
    }

    $current_sheet = $sheets[$category_id - 1]['name'];
    $products = $gs->getSheetData($current_sheet);

    $columns = !empty($products) ? array_keys($products[0]) : [];
} catch (Exception $e) {
    http_response_code(404);
    die($e->getMessage());
}

include __DIR__ . '/../templates/header.php';
?>
<script src="../public/static/js/category.js"></script>
<div class="container my-5">
    <h1 class="text-center mb-4"><?php echo htmlspecialchars($sheets[$category_id - 1]['name']); ?></h1>

    <?php if (isset($_SESSION['messages']) && !empty($_SESSION['messages'])): ?>
        <div class="alert alert-danger">
            <?php foreach ($_SESSION['messages'] as $message): ?>
                <p><?php echo htmlspecialchars($message); ?></p>
            <?php endforeach; ?>
            <?php unset($_SESSION['messages']); ?>
        </div>
    <?php endif; ?>

    <!-- Таблица продуктов -->
    <div class="table-responsive">
        <table class="table table-bordered" style="border-collapse: collapse;">
            <thead>
                <tr>
                    <th style="text-align: center; vertical-align: middle;">Фото</th>
                    <th style="text-align: center; vertical-align: middle;">Описание</th>
                    <th style="text-align: center; vertical-align: middle;"><?php echo isset($columns[2]) ? htmlspecialchars($columns[2]) : ''; ?></th>
                    <th style="text-align: center; vertical-align: middle;">Количество</th>
                    <th style="text-align: center; vertical-align: middle;">Стоимость</th>
                    <th style="text-align: center; vertical-align: middle;">Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td style="border-width: 0; vertical-align: middle; text-align: center;">
                            <?php if (isset($product['photo']) && filter_var($product['photo'], FILTER_VALIDATE_URL)): ?>
                                <img src="<?php echo htmlspecialchars($product['photo']); ?>" alt="<?php echo htmlspecialchars($product['name'] ?? 'Image'); ?>" class="img-fluid" style="max-width: 50px;">
                            <?php else: ?>
                                Нет фото
                            <?php endif; ?>
                        </td>
                        <td style="border-width: 0; vertical-align: middle;">
                            <?php echo htmlspecialchars($product['name'] ?? ''); ?><br>
                            Код товара: <?php echo htmlspecialchars($product['code'] ?? 'N/A'); ?><br>
                            Дата отгрузки: <?php echo htmlspecialchars($product['shipping_date'] ?? 'N/A'); ?>
                        </td>
                        <td style="border-width: 0; vertical-align: middle; text-align: center;">
                            <?php echo isset($columns[2]) && isset($product[$columns[2]]) ? htmlspecialchars($product[$columns[2]]) : ''; ?>
                        </td>
                        <td style="border-width: 0; vertical-align: middle; text-align: center;">
                            <div class="d-flex align-items-center justify-content-center">
                                <button class="btn btn-sm btn-outline-secondary btn-minus change-quantity" data-product-id="<?php echo htmlspecialchars($product['name']); ?>" data-price="<?php echo htmlspecialchars($product['price']); ?>">-</button>
                                <input type="number" class="form-control form-control-sm quantity-input text-center mx-1 change-quantity" value="1" min="1" max="<?php echo htmlspecialchars($product['quantity']); ?>" style="width: 50px;" data-product-id="<?php echo htmlspecialchars($product['name']); ?>" data-price="<?php echo htmlspecialchars($product['price']); ?>">
                                <button class="btn btn-sm btn-outline-secondary btn-plus change-quantity" data-product-id="<?php echo htmlspecialchars($product['name']); ?>" data-price="<?php echo htmlspecialchars($product['price']); ?>">+</button>
                            </div>
                        </td>
                        <td style="border-width: 0; vertical-align: middle; text-align: center;">
                            <span class="product-price" data-base-price="<?php echo htmlspecialchars($product['price']); ?>"><?php echo htmlspecialchars($product['price']); ?></span>
                        </td>
                        <td style="border-width: 0; vertical-align: middle; text-align: center;">
                            <div class="d-flex flex-column align-items-stretch">
                                <select class="form-select form-select-sm mt-2 days-select" data-product-id="<?php echo htmlspecialchars($product['name']); ?>">
                                    <option value="1">1 день</option>
                                    <option value="2">2 дня</option>
                                    <option value="3">3 дня</option>
                                </select>
                                <button class="btn btn-primary btn-sm mt-2 add-to-cart" data-product-id="<?php echo htmlspecialchars($product['name']); ?>">В корзину</button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
