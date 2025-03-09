<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';
require_once 'google_sheets.php';

$gs = new GoogleSheetsHelper();
$categories = array_filter($gs->getCategories(), function($cat) {
    return $cat['name'] !== 'Заказы';
});

$html_content = '';
if (file_exists('static/text.md')) {
    $parser = new Parsedown();
    $html_content = $parser->text(file_get_contents('static/text.md'));
}

include 'templates/header.php';
?>
<div class="container my-5">
    <h1 class="text-center mb-4">Categories</h1>
    <div class="row">
        <?php foreach ($categories as $category): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <a href="category.php?id=<?= $category['id'] ?>">
                    <img src="static/images/<?= $category['image'] ?>"
                         class="card-img-top"
                         alt="<?= htmlspecialchars($category['name']) ?>">
                </a>
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($category['name']) ?></h5>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="mt-5">
        <?= $html_content ?>
    </div>
</div>

