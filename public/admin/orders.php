<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>طلبات العملاء | لوحة التحكم</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <header class="luxury-header">
        <a href="../index.php" class="logo">ADMIN ORDERS</a>
        <ul class="nav-links">
            <li><a href="dashboard.php">إدارة المنتجات</a></li>
            <li><a href="orders.php" class="active">طلبات العملاء</a></li>
            <li><a href="../index.php">الذهاب للمتجر</a></li>
        </ul>
    </header>

    <div class="container">
        <h2 style="color: var(--gold); margin-bottom: 15px;">📋 قائمة طلبات العملاء الحالية</h2>
        <table>
            <thead>
                <tr>
                    <th>رقم الطلب</th>
                    <th>اسم العميل</th>
                    <th>العنوان</th>
                    <th>الهاتف</th>
                    <th>الحالة</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>#1001</td>
                    <td>أحمد محمد</td>
                    <td>القاهرة، مصر الجديدة</td>
                    <td>01012345678</td>
                    <td><span style="color: var(--gold);">قيد التجهيز</span></td>
                </tr>
            </tbody>
        </table>
    </div>
</body>
</html>