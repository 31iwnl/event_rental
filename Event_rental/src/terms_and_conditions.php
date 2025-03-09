<?php
require_once __DIR__ . '/vendor/autoload.php';
$is_src = true;

$html_content = '';
if (file_exists('static/arenda.md')) {
    $parser = new Parsedown();
    $html_content = $parser->text(file_get_contents('static/arenda.md'));
}

include 'templates/header.php';
?>

<div class="container my-5">
    <h1>Условия аренды</h1>
    <div class="mt-5">
        <?= $html_content ?>
    </div>
</div>

<?php
include 'templates/footer.php';
?>
