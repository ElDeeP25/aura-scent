<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../controllers/ResourceController.php';
$rc = new ResourceController();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'store') {
    $rc->store('products', $_POST);
}
if (isset($_GET['action']) && $_GET['action'] === 'delete') {
    $rc->destroy('products', $_GET['id']);
}
$products = $rc->index('products');
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لوحة التحكم | Aura & Scent</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header class="luxury-header">
        <a href="../index.php" class="logo">ADMIN PANEL</a>
        <ul class="nav-links">
            <li><a href="dashboard.php" class="active">إدارة المنتجات</a></li>
            <li><a href="orders.php">طلبات العملاء</a></li>
            <li><a href="../index.php">الذهاب للمتجر</a></li>
        </ul>
    </header>

    <div class="container">
        <div style="background: var(--bg-card); padding: 25px; border-radius: 12px; border: 1px solid var(--gold); margin-bottom: 30px;">
            <h3 style="color: var(--gold); margin-bottom: 15px;">➕ إضافة عطر جديد للبراند</h3>
            <form method="POST">
                <input type="hidden" name="action" value="store">
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 15px; margin-bottom: 15px;">
                    <input type="text" name="name" placeholder="اسم العطر..." required style="padding:12px; background:#080808; border:1px solid #333; color:#fff; border-radius:6px;">
                    <input type="text" name="scent_notes" placeholder="النوتات (عود، ورد)..." required style="padding:12px; background:#080808; border:1px solid #333; color:#fff; border-radius:6px;">
                    <input type="number" step="0.01" name="price" placeholder="السعر..." required style="padding:12px; background:#080808; border:1px solid #333; color:#fff; border-radius:6px;">
                    <input type="text" name="description" placeholder="وصف العطر..." required style="padding:12px; background:#080808; border:1px solid #333; color:#fff; border-radius:6px;">
                </div>
                <button type="submit" class="btn-luxury">حفظ وإضافة العطر</button>
            </form>
        </div>

        <h2 style="color: var(--gold); margin-bottom: 15px;">📦 إدارة منتجات المتجر</h2>
        <table>
            <thead>
                <tr>
                    <th>اسم العطر</th>
                    <th>النوتات</th>
                    <th>السعر</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= htmlspecialchars($p['scent_notes']) ?></td>
                    <td><?= $p['price'] ?> ج.م</td>
                    <td><a href="dashboard.php?action=delete&id=<?= $p['id'] ?>" style="color: var(--danger); text-decoration:none;" onclick="return confirm('حذف العطر نهائياً؟')">حذف</a></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>