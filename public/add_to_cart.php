<?php
session_start();
require_once __DIR__ . '/config/db.php';

// للتجربة المؤقتة: لو مش مسجل دخول، بنعمل مستخدم تجريبي رقم 1 تلقائياً
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $user_id = $_SESSION['user_id'];
    $product_id = intval($_POST['product_id']);

    if (isset($pdo)) {
        // التحقق لو المنتج موجود مسبقاً في السلة
        $stmt = $pdo->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
        $stmt->execute([$user_id, $product_id]);
        $existing = $stmt->fetch();

        if ($existing) {
            $update = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?");
            $update->execute([$existing['id']]);
        } else {
            $insert = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
            $insert->execute([$user_id, $product_id]);
        }
    }
}

header('Location: cart.php');
exit;
?>