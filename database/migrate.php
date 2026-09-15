<?php
require_once __DIR__ . '/../config/db.php';

$pdo->exec("CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    scent_notes VARCHAR(255) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)");

$pdo->exec("DELETE FROM products");
$stmt = $pdo->prepare("INSERT INTO products (name, scent_notes, price, description) VALUES (?, ?, ?, ?)");
$stmt->execute(['Royal Oud', 'خشب العود، الفانيليا، العنبر', 1250.00, 'عطر ملكي فخم بثبات عالٍ طوال اليوم.']);
$stmt->execute(['Velvet Rose', 'الورد البلغاري، المسك الأبيض', 950.50, 'عطر نسائي ساحر يجمع بين نعومة الورود وقوة المسك.']);

echo "تم إعداد الجداول والبيانات بنجاح!\n";