<?php
session_start();
require_once __DIR__ . '/config/db.php';

// للتجربة المؤقتة: لو مش مسجل دخول، بنعمل مستخدم تجريبي رقم 1 تلقائياً
if (!isset($_SESSION['user_id'])) {
    $_SESSION['user_id'] = 1;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $user_id = (int)$_SESSION['user_id'];
    $product_id = (int)$_POST['product_id'];

    if (isset($pdo) && $product_id > 0) {
        try {
            // التحقق لو المنتج موجود مسبقاً في السلة
            $stmt = $pdo->prepare("SELECT id FROM cart WHERE user_id = ? AND product_id = ?");
            $stmt->execute([$user_id, $product_id]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($existing) {
                $update = $pdo->prepare("UPDATE cart SET quantity = quantity + 1 WHERE id = ?");
                $update->execute([$existing['id']]);
            } else {
                $insert = $pdo->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, 1)");
                $insert->execute([$user_id, $product_id]);
            }
        } catch (Exception $e) {
            // تسجيل الخطأ إن وجد والاستمرار دون تعطيل الموقع
        }
    }
}


$bg_file = __DIR__ . '/bg-animation.php';
if (!file_exists($bg_file)) { $bg_file = __DIR__ . '/../bg-animation.php'; }
if (file_exists($bg_file)) { include $bg_file; }
?>

header('Location: cart.php');
exit;