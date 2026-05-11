<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - HRM' : 'HRM Portal'; ?></title>
    <!-- Use base URL or relative paths from index.php -->
    <link rel="stylesheet" href="assets/css/style.css?v=2.0">
    <?php if(file_exists("assets/css/logo.css")): ?>
    <link rel="stylesheet" href="assets/css/logo.css">
    <?php endif; ?>
    <link rel="stylesheet" href="assets/css/custom.css">
    <link rel="shortcut icon" href="assets/images/favicon.jpeg" type="image/jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Load required libraries -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
