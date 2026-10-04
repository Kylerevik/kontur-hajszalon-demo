<?php
$assetPrefix ??= '';
$pageDescription ??= '';
$stylesheets ??= ['css/style.css'];
$headExtra ??= '';
?>
<!doctype html>
<html lang="hu">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title><?= e($pageTitle) ?></title>
<?php if ($pageDescription !== ''): ?>
    <meta name="description" content="<?= e($pageDescription) ?>">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="hu_HU">
    <meta property="og:title" content="<?= e($pageTitle) ?>">
    <meta property="og:description" content="<?= e($pageDescription) ?>">
<?php endif; ?>
    <meta name="theme-color" content="#26182b">
    <link rel="icon" href="<?= e($assetPrefix) ?>img/favicon.svg" type="image/svg+xml">
    <link rel="preload" href="<?= e($assetPrefix) ?>css/fonts/fraunces-latin-600-normal.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
<?php foreach ($stylesheets as $stylesheet): ?>
    <link rel="stylesheet" href="<?= e(asset($stylesheet, $assetPrefix)) ?>">
<?php endforeach; ?>
<?= $headExtra ?>
</head>
<body class="d-flex flex-column min-vh-100">
