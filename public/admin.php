<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db_path = __DIR__ . '/config/db.php';
if (!file_exists($db_path)) { $db_path = __DIR__ . '/../config/db.php'; }
require_once $db_path;

// 1. تحديد السوبر أدمن الوحيد صاحب الصلاحية العليا
$super_admin_email = "y.yousefahmed26112001@gmail.com";

// قائمة الأدمنز المسموح لهم بالدخول
$admin_emails = [
    $super_admin_email,
    "kholoudsaied@gmail.com",
    "nahlaamer2004@gmail.com",
    "rizkmariem8@gmail.com",
    "zaftt@gmail.com"
];

if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT id, name, email, role FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $current_user = $stmt->fetch(PDO::FETCH_ASSOC);

    $user_email_clean = strtolower(trim($current_user['email'] ?? ''));
    $user_role_clean = strtolower(trim($current_user['role'] ?? ''));
    $admin_emails_clean = array_map(function($e) { return strtolower(trim($e)); }, $admin_emails);

    if (!$current_user || (!in_array($user_email_clean, $admin_emails_clean) && $user_role_clean !== 'admin')) {
        header("Location: index.php");
        exit();
    }

    $_SESSION['user_name'] = $current_user['name'] ?? 'Admin';
    $_SESSION['user_email'] = $current_user['email'] ?? '';
    $_SESSION['user_role'] = 'admin';

    $is_super_admin = ($user_email_clean === strtolower(trim($super_admin_email)));

} catch (Exception $e) {
    header("Location: index.php");
    exit();
}

// -------------------------------------------------------------------
// AJAX Endpoint: معالجة طلبات المنتجات
// -------------------------------------------------------------------
if (isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');
    $action = $_POST['ajax_action'];

    if ($action === 'edit_product') {
        $id = intval($_POST['product_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $old_price = !empty($_POST['old_price']) ? floatval($_POST['old_price']) : null;
        
        $category_select = trim($_POST['category_select'] ?? '');
        $category_custom = trim($_POST['category_custom'] ?? '');
        $category = (!empty($category_custom)) ? $category_custom : $category_select;
        if (empty($category)) { $category = 'All-Season Elixirs'; }

        $image = trim($_POST['image'] ?? '');

        if ($id > 0 && !empty($name)) {
            $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, old_price = ?, category = ?, image = ?, image_url = ? WHERE id = ?");
            $stmt->execute([$name, $description, $price, $old_price, $category, $image, $image, $id]);
            echo json_encode(['status' => 'success', 'message' => 'Product updated dynamically!', 'product' => [
                'id' => $id, 'name' => $name, 'description' => $description, 'price' => $price, 'old_price' => $old_price, 'category' => $category, 'image' => $image
            ]]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid product data.']);
        }
        exit();
    }
}

// Action Handlers
$message = '';
$error = '';
$tab = $_GET['tab'] ?? 'overview';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['ajax_action'])) {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_product') {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $old_price = !empty($_POST['old_price']) ? floatval($_POST['old_price']) : null;
        
        $category_select = trim($_POST['category_select'] ?? '');
        $category_custom = trim($_POST['category_custom'] ?? '');
        $category = (!empty($category_custom)) ? $category_custom : $category_select;
        if (empty($category)) { $category = 'All-Season Elixirs'; }

        $image = trim($_POST['image'] ?? '');

        if (!empty($name) && $price > 0) {
            $stmt = $pdo->prepare("INSERT INTO products (name, description, price, old_price, category, image, image_url) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$name, $description, $price, $old_price, $category, $image, $image]);
            $message = "Elixir added successfully!";
        }
    } elseif ($action === 'delete_product') {
        $id = intval($_POST['product_id'] ?? 0);
        if ($id > 0) {
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$id]);
            $message = "Product deleted successfully!";
        }
    } elseif ($action === 'edit_user') {
        $user_id = intval($_POST['user_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $new_password = $_POST['password'] ?? '';

        if ($user_id > 0 && !empty($name)) {
            $check_stmt = $pdo->prepare("SELECT email, role FROM users WHERE id = ?");
            $check_stmt->execute([$user_id]);
            $target_user = $check_stmt->fetch(PDO::FETCH_ASSOC);

            $target_email = strtolower(trim($target_user['email'] ?? ''));
            $target_is_admin = in_array($target_email, array_map('strtolower', $admin_emails)) || ($target_user['role'] ?? '') === 'admin';

            if ($target_is_admin && !$is_super_admin && $user_id !== intval($current_user['id'])) {
                $error = "Security Policy: Only Super Admin can modify other Admin accounts!";
            } else {
                if (!empty($new_password)) {
                    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                    $stmt = $pdo->prepare("UPDATE users SET name = ?, password = ? WHERE id = ?");
                    $stmt->execute([$name, $hashed_password, $user_id]);
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
                    $stmt->execute([$name, $user_id]);
                }
                $message = "User credentials updated successfully!";
            }
        }
    } elseif ($action === 'delete_user') {
        $user_id = intval($_POST['user_id'] ?? 0);
        if ($user_id > 0 && $user_id !== intval($_SESSION['user_id'])) {
            $check_stmt = $pdo->prepare("SELECT email, role FROM users WHERE id = ?");
            $check_stmt->execute([$user_id]);
            $target_user = $check_stmt->fetch(PDO::FETCH_ASSOC);

            $target_email = strtolower(trim($target_user['email'] ?? ''));
            $target_is_admin = in_array($target_email, array_map('strtolower', $admin_emails)) || ($target_user['role'] ?? '') === 'admin';

            if ($target_is_admin && !$is_super_admin) {
                $error = "Security Policy: Only Super Admin can delete Admin accounts!";
            } else {
                $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
                $stmt->execute([$user_id]);
                $message = "User deleted successfully!";
            }
        }
    }
}

try {
    $total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
    $total_users = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $total_orders = $pdo->query("SELECT COUNT(*) FROM cart")->fetchColumn();
    $products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
    $users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $products = $users = [];
    $total_products = $total_users = $total_orders = 0;
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | AURA & SCENT — Sylva Edition</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        sylva: {
                            base: '#0b130e',
                            card: '#121f17',
                            accent: '#2d4a36',
                            gold: '#d4af37',
                            light: '#e8ece9'
                        }
                    },
                    fontFamily: {
                        serif: ['Newsreader', 'Georgia', 'serif'],
                        sans: ['Plus Jakarta Sans', 'sans-serif']
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,200..800;1,6..72,200..800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { background-color: #0b130e; color: #e8ece9; font-family: 'Plus Jakarta Sans', sans-serif; overflow-x: hidden; }
        .sylva-plate {
            background: rgba(18, 31, 23, 0.65);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(212, 175, 55, 0.18);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        }
        .cat-pill-active {
            background-color: #d4af37 !important;
            color: #000000 !important;
            border-color: #d4af37 !important;
            font-weight: 800 !important;
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.3);
        }
    </style>
</head>
<body class="min-h-screen relative antialiased selection:bg-sylva-gold selection:text-black">

    <!-- Toast Alert Notification -->
    <div id="ajaxToast" class="fixed top-8 right-8 z-[9999] hidden sylva-plate bg-sylva-gold text-black px-6 py-3 rounded-2xl font-extrabold text-xs uppercase tracking-widest shadow-2xl border border-sylva-gold transition-all">
        ✨ Changes Saved Successfully!
    </div>

    <!-- Background -->
    <?php 
    $bg_file = __DIR__ . '/bg-animation.php';
    if (file_exists($bg_file)) { include $bg_file; }
    ?>

    <!-- Header -->
    <?php 
    $header_file = file_exists(__DIR__ . '/header.php') ? __DIR__ . '/header.php' : __DIR__ . '/../header.php';
    include $header_file; 
    ?>

    <main class="max-w-7xl mx-auto px-4 sm:px-8 pt-32 sm:pt-40 pb-20 relative z-10">
        
        <!-- Header Controls Bar -->
        <div class="flex flex-col lg:flex-row lg:items-end justify-between mb-8 gap-6 border-b border-sylva-accent/30 pb-6">
            <div>
                <div class="flex items-center gap-2 mb-2">
                    <span class="text-sylva-gold text-[10px] font-semibold tracking-[0.25em] uppercase">Control Sanctuary</span>
                    <span class="bg-sylva-gold/20 text-sylva-gold text-[10px] px-3 py-0.5 rounded-full font-bold border border-sylva-gold/30 flex items-center gap-1">
                        <span>✨ <?= htmlspecialchars($current_user['name'] ?? 'Admin') ?></span>
                        <span class="text-sylva-gold text-[9px] font-mono"><?= $is_super_admin ? '★ Super Admin' : '★ Admin' ?></span>
                    </span>
                </div>
                <h1 class="font-serif text-3xl sm:text-4xl font-light italic text-sylva-light">Admin Control Center</h1>
                <p class="text-neutral-400 text-xs uppercase tracking-widest font-light mt-1">Manage botanical elixirs, collectors, revenue, and system security.</p>
            </div>
            
            <div class="flex items-center gap-2 sylva-plate p-1.5 rounded-2xl self-start lg:self-auto overflow-x-auto max-w-full">
                <a href="admin.php?tab=overview" class="px-4 py-2 rounded-xl font-bold text-xs uppercase tracking-wider transition-all whitespace-nowrap <?= $tab === 'overview' ? 'bg-sylva-gold text-black shadow-md shadow-sylva-gold/20' : 'text-neutral-400 hover:text-white' ?>">
                    Overview
                </a>
                <a href="admin.php?tab=products" class="px-4 py-2 rounded-xl font-bold text-xs uppercase tracking-wider transition-all whitespace-nowrap <?= $tab === 'products' ? 'bg-sylva-gold text-black shadow-md shadow-sylva-gold/20' : 'text-neutral-400 hover:text-white' ?>">
                    Products (<?= count($products) ?>)
                </a>
                <a href="admin.php?tab=users" class="px-4 py-2 rounded-xl font-bold text-xs uppercase tracking-wider transition-all whitespace-nowrap <?= $tab === 'users' ? 'bg-sylva-gold text-black shadow-md shadow-sylva-gold/20' : 'text-neutral-400 hover:text-white' ?>">
                    Users (<?= count($users) ?>)
                </a>
            </div>
        </div>

        <?php if (!empty($message)): ?>
            <div class="mb-6 p-3.5 sylva-plate bg-emerald-950/40 border border-emerald-500/40 text-emerald-400 rounded-2xl text-xs uppercase tracking-wider font-bold text-center">
                ✨ <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($error)): ?>
            <div class="mb-6 p-3.5 sylva-plate bg-red-950/40 border border-red-500/40 text-red-400 rounded-2xl text-xs uppercase tracking-wider font-bold text-center">
                ⚠️ <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($tab === 'overview'): ?>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5 mb-8">
                <div class="sylva-plate p-6 rounded-3xl shadow-xl hover:border-sylva-gold/40 transition-all">
                    <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-neutral-400 block mb-1">Total Botanical Elixirs</span>
                    <span class="font-serif text-4xl font-light text-sylva-gold"><?= $total_products ?></span>
                </div>
                <div class="sylva-plate p-6 rounded-3xl shadow-xl hover:border-sylva-gold/40 transition-all">
                    <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-neutral-400 block mb-1">Registered Collectors</span>
                    <span class="font-serif text-4xl font-light text-sylva-gold"><?= $total_users ?></span>
                </div>
                <div class="sylva-plate p-6 rounded-3xl shadow-xl hover:border-sylva-gold/40 transition-all">
                    <span class="text-[11px] font-bold uppercase tracking-[0.2em] text-neutral-400 block mb-1">Cart Records</span>
                    <span class="font-serif text-4xl font-light text-sylva-gold"><?= $total_orders ?></span>
                </div>
            </div>

            <div class="sylva-plate p-6 rounded-3xl mb-8">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h3 class="font-serif text-xl italic text-sylva-light">Store Revenue Analytics</h3>
                        <p class="text-neutral-400 text-xs">Live activity performance timeline</p>
                    </div>
                    <span class="bg-sylva-gold/20 text-sylva-gold text-[10px] font-bold px-3 py-1 rounded-full border border-sylva-gold/30">Live Metrics</span>
                </div>
                <div class="h-60 w-full">
                    <canvas id="sylvaChart"></canvas>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($tab === 'products'): ?>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Add Product Form -->
                <div class="sylva-plate p-6 rounded-3xl h-fit shadow-xl">
                    <h2 class="font-serif text-xl italic mb-5 text-sylva-gold">Add New Elixir</h2>
                    <form method="POST" class="space-y-4 text-xs">
                        <input type="hidden" name="action" value="add_product">
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-neutral-400 mb-1">Product Name</label>
                            <input type="text" name="name" required class="w-full bg-sylva-base/80 border border-sylva-accent/50 rounded-xl px-3.5 py-2.5 focus:border-sylva-gold outline-none text-white">
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block font-bold uppercase tracking-wider text-neutral-400 mb-1">Price ($)</label>
                                <input type="number" step="0.01" name="price" required class="w-full bg-sylva-base/80 border border-sylva-accent/50 rounded-xl px-3.5 py-2.5 focus:border-sylva-gold outline-none text-white">
                            </div>
                            <div>
                                <label class="block font-bold uppercase tracking-wider text-sylva-gold mb-1">Old Price ($ Offer)</label>
                                <input type="number" step="0.01" name="old_price" placeholder="Optional" class="w-full bg-sylva-base/80 border border-sylva-gold/40 rounded-xl px-3.5 py-2.5 focus:border-sylva-gold outline-none text-white">
                            </div>
                        </div>

                        <!-- Sleek Glass Category Selector Cards -->
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-sylva-gold mb-2">Fragrance Category</label>
                            <input type="hidden" name="category_select" id="add_cat_input" value="All-Season Elixirs">
                            
                            <div class="grid grid-cols-2 gap-2 mb-2">
                                <button type="button" onclick="selectPillBadge('add', this, 'All-Season Elixirs')" class="add-cat-btn cat-pill-active sylva-plate p-2.5 rounded-xl border border-sylva-gold text-[10px] font-bold flex items-center justify-center gap-1.5 transition-all cursor-pointer">
                                    <span>🌿</span> All-Season
                                </button>
                                <button type="button" onclick="selectPillBadge('add', this, 'Summer Collection')" class="add-cat-btn sylva-plate p-2.5 rounded-xl border border-sylva-accent/50 text-neutral-300 text-[10px] font-bold flex items-center justify-center gap-1.5 transition-all cursor-pointer hover:border-sylva-gold">
                                    <span>☀️</span> Summer
                                </button>
                                <button type="button" onclick="selectPillBadge('add', this, 'Winter Collection')" class="add-cat-btn sylva-plate p-2.5 rounded-xl border border-sylva-accent/50 text-neutral-300 text-[10px] font-bold flex items-center justify-center gap-1.5 transition-all cursor-pointer hover:border-sylva-gold">
                                    <span>❄️</span> Winter
                                </button>
                                <button type="button" onclick="toggleCustomPillInput('add')" class="add-cat-btn sylva-plate p-2.5 rounded-xl border border-sylva-accent/50 text-sylva-gold text-[10px] font-bold flex items-center justify-center gap-1.5 transition-all cursor-pointer hover:border-sylva-gold">
                                    <span>✨</span> + Custom
                                </button>
                            </div>
                            <input type="text" name="category_custom" id="add_cat_custom" placeholder="Type custom category..." class="hidden w-full bg-sylva-base/90 border border-sylva-gold/60 rounded-xl px-3.5 py-2 text-xs text-sylva-gold font-bold focus:outline-none">
                        </div>

                        <div>
                            <label class="block font-bold uppercase tracking-wider text-neutral-400 mb-1">Image URL</label>
                            <input type="text" name="image" placeholder="https://..." class="w-full bg-sylva-base/80 border border-sylva-accent/50 rounded-xl px-3.5 py-2.5 focus:border-sylva-gold outline-none text-white">
                        </div>
                        <div>
                            <label class="block font-bold uppercase tracking-wider text-neutral-400 mb-1">Description</label>
                            <textarea name="description" rows="3" class="w-full bg-sylva-base/80 border border-sylva-accent/50 rounded-xl px-3.5 py-2.5 focus:border-sylva-gold outline-none text-white resize-none"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-sylva-gold text-black py-3 rounded-full font-bold uppercase tracking-widest hover:bg-white transition-all cursor-pointer">
                            Save Product
                        </button>
                    </form>
                </div>

                <!-- Products Table -->
                <div class="lg:col-span-2 sylva-plate rounded-3xl overflow-hidden shadow-xl">
                    <div class="p-5 border-b border-sylva-accent/30 flex justify-between items-center">
                        <h2 class="font-serif text-xl italic text-sylva-light">All Perfumes (<?= count($products) ?>)</h2>
                    </div>
                    <div class="divide-y divide-sylva-accent/20 max-h-[600px] overflow-y-auto">
                        <?php foreach ($products as $p): 
                            $p_img = !empty($p['image']) ? $p['image'] : (!empty($p['image_url']) ? $p['image_url'] : '');
                        ?>
                            <div id="product_row_<?= $p['id'] ?>" class="p-4 flex items-center justify-between gap-4 hover:bg-sylva-card/40 transition-colors">
                                <div class="flex items-center gap-3">
                                    <div id="product_img_box_<?= $p['id'] ?>" class="w-12 h-12 rounded-xl overflow-hidden bg-sylva-base border border-sylva-accent/40 shrink-0 flex items-center justify-center">
                                        <?php if (!empty($p_img)): ?>
                                            <img src="<?= htmlspecialchars($p_img) ?>" class="w-full h-full object-cover">
                                        <?php else: ?>
                                            <span class="text-xs">🌿</span>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <h4 id="product_title_<?= $p['id'] ?>" class="font-serif text-base text-white"><?= htmlspecialchars($p['name']) ?></h4>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span id="product_price_<?= $p['id'] ?>" class="text-sylva-gold font-bold text-xs">$<?= number_format($p['price'], 2) ?></span>
                                            <span id="product_cat_badge_<?= $p['id'] ?>" class="bg-sylva-accent/40 border border-sylva-gold/20 text-sylva-gold text-[9px] px-2 py-0.5 rounded-full font-mono uppercase">
                                                <?= htmlspecialchars($p['category'] ?? 'All-Season Elixirs') ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <button onclick='openEditProductModal(<?= json_encode($p) ?>)' class="bg-sylva-accent/40 hover:bg-sylva-gold hover:text-black border border-sylva-gold/20 text-sylva-light px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer">Edit</button>
                                    <form method="POST" onsubmit="return confirm('Delete this product?');">
                                        <input type="hidden" name="action" value="delete_product">
                                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                        <button type="submit" class="bg-red-950/40 border border-red-500/30 text-red-400 hover:bg-red-600 hover:text-white px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer">Delete</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($tab === 'users'): ?>
            <div class="sylva-plate rounded-3xl overflow-hidden shadow-xl">
                <div class="p-5 border-b border-sylva-accent/30 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <h2 class="font-serif text-xl italic text-sylva-light">Registered Collectors (<?= count($users) ?>)</h2>
                </div>
                <div class="divide-y divide-sylva-accent/20">
                    <?php foreach ($users as $u): 
                        $u_email = strtolower(trim($u['email'] ?? ''));
                        $u_role = strtolower(trim($u['role'] ?? ''));
                        $u_is_admin = in_array($u_email, array_map('strtolower', $admin_emails)) || $u_role === 'admin';
                        $u_is_super = ($u_email === strtolower(trim($super_admin_email)));
                    ?>
                        <div class="p-4 sm:p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-sylva-card/40 transition-colors">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-sylva-card border border-sylva-gold/40 flex items-center justify-center font-bold text-sylva-gold text-xs shadow-md">
                                    <?= strtoupper(substr($u['name'] ?? 'U', 0, 1)) ?>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2">
                                        <h4 class="font-bold text-sm text-white"><?= htmlspecialchars($u['name'] ?? 'No Name') ?></h4>
                                        <?php if ($u_is_super): ?>
                                            <span class="bg-sylva-gold text-black text-[10px] px-2.5 py-0.5 rounded-full font-black border border-white flex items-center gap-1">★ Super Admin</span>
                                        <?php elseif ($u_is_admin): ?>
                                            <span class="bg-sylva-gold/20 text-sylva-gold text-[10px] px-2 py-0.5 rounded-full font-bold border border-sylva-gold/30 flex items-center gap-1">Admin</span>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-neutral-400 text-xs font-light"><?= htmlspecialchars($u['email'] ?? 'No Email') ?></p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <?php if ($is_super_admin): ?>
                                    <button onclick='openSecurityModal(<?= json_encode($u) ?>)' class="bg-sylva-gold/15 hover:bg-sylva-gold hover:text-black border border-sylva-gold/40 text-sylva-gold px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer shadow-md flex items-center gap-1.5">
                                        <span>🔑</span> Security & Access
                                    </button>
                                <?php endif; ?>

                                <?php if ($is_super_admin || !$u_is_admin || intval($u['id']) === intval($current_user['id'])): ?>
                                    <button onclick='openEditUserModal(<?= json_encode($u) ?>)' class="bg-sylva-accent/40 hover:bg-sylva-gold hover:text-black border border-sylva-gold/20 text-sylva-light px-3.5 py-1.5 rounded-xl text-xs font-bold transition-all cursor-pointer">Edit User</button>
                                <?php else: ?>
                                    <span class="text-[10px] text-neutral-500 font-mono italic px-3 py-1 bg-black/30 rounded-lg border border-neutral-800">Protected Account</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </main>

    <!-- Modal Edit Product -->
    <div id="editProductModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="sylva-plate rounded-3xl p-6 max-w-md w-full relative shadow-2xl">
            <h3 class="font-serif text-xl italic mb-5 text-sylva-gold">Edit Fragrance</h3>
            <form id="ajaxEditProductForm" onsubmit="submitAjaxProductEdit(event)" class="space-y-4 text-xs">
                <input type="hidden" name="ajax_action" value="edit_product">
                <input type="hidden" name="product_id" id="edit_product_id">
                <div>
                    <label class="block font-bold uppercase tracking-wider text-neutral-400 mb-1">Product Name</label>
                    <input type="text" name="name" id="edit_product_name" required class="w-full bg-sylva-base/80 border border-sylva-accent/50 rounded-xl px-3.5 py-2.5 text-white outline-none focus:border-sylva-gold">
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-neutral-400 mb-1">Price ($)</label>
                        <input type="number" step="0.01" name="price" id="edit_product_price" required class="w-full bg-sylva-base/80 border border-sylva-accent/50 rounded-xl px-3.5 py-2.5 text-white outline-none focus:border-sylva-gold">
                    </div>
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-sylva-gold mb-1">Old Price ($ Offer)</label>
                        <input type="number" step="0.01" name="old_price" id="edit_product_old_price" placeholder="Optional" class="w-full bg-sylva-base/80 border border-sylva-gold/40 rounded-xl px-3.5 py-2.5 text-white outline-none focus:border-sylva-gold">
                    </div>
                </div>

                <!-- Category Edit Cards -->
                <div>
                    <label class="block font-bold uppercase tracking-wider text-sylva-gold mb-2">Fragrance Category</label>
                    <input type="hidden" name="category_select" id="edit_cat_input" value="All-Season Elixirs">
                    
                    <div class="grid grid-cols-2 gap-2 mb-2">
                        <button type="button" onclick="selectPillBadge('edit', this, 'All-Season Elixirs')" class="edit-cat-btn sylva-plate p-2.5 rounded-xl border border-sylva-accent/50 text-neutral-300 text-[10px] font-bold flex items-center justify-center gap-1.5 transition-all cursor-pointer hover:border-sylva-gold">
                            <span>🌿</span> All-Season
                        </button>
                        <button type="button" onclick="selectPillBadge('edit', this, 'Summer Collection')" class="edit-cat-btn sylva-plate p-2.5 rounded-xl border border-sylva-accent/50 text-neutral-300 text-[10px] font-bold flex items-center justify-center gap-1.5 transition-all cursor-pointer hover:border-sylva-gold">
                            <span>☀️</span> Summer
                        </button>
                        <button type="button" onclick="selectPillBadge('edit', this, 'Winter Collection')" class="edit-cat-btn sylva-plate p-2.5 rounded-xl border border-sylva-accent/50 text-neutral-300 text-[10px] font-bold flex items-center justify-center gap-1.5 transition-all cursor-pointer hover:border-sylva-gold">
                            <span>❄️</span> Winter
                        </button>
                        <button type="button" onclick="toggleCustomPillInput('edit')" class="edit-cat-btn sylva-plate p-2.5 rounded-xl border border-sylva-accent/50 text-sylva-gold text-[10px] font-bold flex items-center justify-center gap-1.5 transition-all cursor-pointer hover:border-sylva-gold">
                            <span>✨</span> + Custom
                        </button>
                    </div>
                    <input type="text" name="category_custom" id="edit_cat_custom" placeholder="Type custom category..." class="hidden w-full bg-sylva-base/90 border border-sylva-gold/60 rounded-xl px-3.5 py-2 text-xs text-sylva-gold font-bold focus:outline-none">
                </div>

                <div>
                    <label class="block font-bold uppercase tracking-wider text-neutral-400 mb-1">Image URL</label>
                    <input type="text" name="image" id="edit_product_image" class="w-full bg-sylva-base/80 border border-sylva-accent/50 rounded-xl px-3.5 py-2.5 text-white outline-none focus:border-sylva-gold">
                </div>
                <div>
                    <label class="block font-bold uppercase tracking-wider text-neutral-400 mb-1">Description</label>
                    <textarea name="description" id="edit_product_description" rows="3" class="w-full bg-sylva-base/80 border border-sylva-accent/50 rounded-xl px-3.5 py-2.5 text-white resize-none outline-none focus:border-sylva-gold"></textarea>
                </div>
                <div class="flex gap-3 pt-2">
                    <button type="submit" id="saveBtn" class="flex-1 bg-sylva-gold text-black py-2.5 rounded-full font-bold uppercase cursor-pointer hover:bg-white transition-all">Save & Continue</button>
                    <button type="button" onclick="closeModals()" class="flex-1 bg-sylva-accent/40 text-white py-2.5 rounded-full font-bold uppercase cursor-pointer">Close</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Super Admin Security Modal -->
    <div id="superSecurityModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="sylva-plate rounded-3xl p-6 max-w-md w-full relative shadow-2xl border border-sylva-gold/40">
            <div class="flex items-center justify-between pb-3 border-b border-sylva-accent/40 mb-4">
                <div class="flex items-center gap-2">
                    <span class="text-sylva-gold">🔐</span>
                    <h3 class="font-serif text-lg italic text-sylva-light">Security & Hash Inspector</h3>
                </div>
                <button onclick="closeSecurityModal()" class="text-neutral-400 hover:text-white font-bold">&times;</button>
            </div>

            <div class="space-y-4 text-xs">
                <div>
                    <span class="text-neutral-400 uppercase tracking-widest text-[10px] block mb-1">Target Account</span>
                    <div id="sec_user_name" class="font-bold text-white text-sm"></div>
                    <div id="sec_user_email" class="text-sylva-gold font-mono text-[11px]"></div>
                </div>

                <div>
                    <span class="text-neutral-400 uppercase tracking-widest text-[10px] block mb-1">Encrypted Password Hash (Argon2 / BCRYPT)</span>
                    <div id="sec_user_hash" class="bg-sylva-base/90 p-3 rounded-xl border border-sylva-gold/30 text-sylva-gold font-mono text-[10px] break-all leading-relaxed select-all"></div>
                </div>

                <form method="POST" class="pt-3 border-t border-sylva-accent/40 space-y-3">
                    <input type="hidden" name="action" value="edit_user">
                    <input type="hidden" name="user_id" id="sec_user_id">
                    <input type="hidden" name="name" id="sec_user_name_input">
                    
                    <div>
                        <label class="block font-bold uppercase tracking-wider text-sylva-gold mb-1">Set New Password Directly</label>
                        <input type="password" name="password" required placeholder="Enter new password for this user..." class="w-full bg-sylva-base/80 border border-sylva-gold/40 rounded-xl px-3.5 py-2 text-xs text-white focus:outline-none focus:border-sylva-gold">
                    </div>

                    <div class="flex gap-2 pt-1">
                        <button type="submit" class="flex-1 bg-sylva-gold text-black py-2.5 rounded-full font-bold uppercase tracking-wider hover:bg-white transition-all">Update Password 🔑</button>
                        <button type="button" onclick="closeSecurityModal()" class="bg-sylva-accent/40 text-neutral-300 px-5 py-2.5 rounded-full font-bold">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function selectPillBadge(prefix, btn, val) {
            document.getElementById(prefix + '_cat_input').value = val;
            document.getElementById(prefix + '_cat_custom').classList.add('hidden');
            document.getElementById(prefix + '_cat_custom').value = '';

            document.querySelectorAll('.' + prefix + '-cat-btn').forEach(b => {
                b.classList.remove('cat-pill-active');
                b.classList.add('text-neutral-300');
            });
            btn.classList.add('cat-pill-active');
            btn.classList.remove('text-neutral-300');
        }

        function toggleCustomPillInput(prefix) {
            const customInput = document.getElementById(prefix + '_cat_custom');
            customInput.classList.toggle('hidden');
            if (!customInput.classList.contains('hidden')) {
                customInput.focus();
                document.getElementById(prefix + '_cat_input').value = '';
            }
        }

        function openSecurityModal(u) {
            document.getElementById('sec_user_id').value = u.id;
            document.getElementById('sec_user_name_input').value = u.name;
            document.getElementById('sec_user_name').innerText = u.name;
            document.getElementById('sec_user_email').innerText = u.email;
            document.getElementById('sec_user_hash').innerText = u.password || '$2y$10$eImiTXuWVxfM37uY4JANjOQe3248392842938...';
            document.getElementById('superSecurityModal').classList.remove('hidden');
        }

        function closeSecurityModal() {
            document.getElementById('superSecurityModal').classList.add('hidden');
        }

        function openEditProductModal(p) {
            document.getElementById('edit_product_id').value = p.id;
            document.getElementById('edit_product_name').value = p.name;
            document.getElementById('edit_product_price').value = p.price;
            document.getElementById('edit_product_old_price').value = p.old_price || '';
            document.getElementById('edit_product_image').value = p.image || p.image_url || '';
            document.getElementById('edit_product_description').value = p.description || '';

            const catVal = p.category || 'All-Season Elixirs';
            document.getElementById('edit_cat_input').value = catVal;

            const editBtns = document.querySelectorAll('.edit-cat-btn');
            let matched = false;
            editBtns.forEach(btn => {
                btn.classList.remove('cat-pill-active');
                btn.classList.add('text-neutral-300');
                if (btn.innerText.includes('All-Season') && catVal === 'All-Season Elixirs') { btn.classList.add('cat-pill-active'); matched = true; }
                if (btn.innerText.includes('Summer') && catVal === 'Summer Collection') { btn.classList.add('cat-pill-active'); matched = true; }
                if (btn.innerText.includes('Winter') && catVal === 'Winter Collection') { btn.classList.add('cat-pill-active'); matched = true; }
            });

            if (!matched) {
                document.getElementById('edit_cat_custom').value = catVal;
                document.getElementById('edit_cat_custom').classList.remove('hidden');
            } else {
                document.getElementById('edit_cat_custom').classList.add('hidden');
            }

            document.getElementById('editProductModal').classList.remove('hidden');
        }

        function closeModals() {
            document.getElementById('editProductModal').classList.add('hidden');
        }

        function submitAjaxProductEdit(e) {
            e.preventDefault();
            const form = document.getElementById('ajaxEditProductForm');
            const formData = new FormData(form);
            const saveBtn = document.getElementById('saveBtn');

            saveBtn.innerText = 'Saving...';

            fetch('admin.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                saveBtn.innerText = 'Save & Continue';
                if (data.status === 'success') {
                    const p = data.product;

                    const titleEl = document.getElementById('product_title_' + p.id);
                    const priceEl = document.getElementById('product_price_' + p.id);
                    const catBadgeEl = document.getElementById('product_cat_badge_' + p.id);
                    const imgBoxEl = document.getElementById('product_img_box_' + p.id);

                    if (titleEl) titleEl.innerText = p.name;
                    if (priceEl) priceEl.innerText = '$' + parseFloat(p.price).toFixed(2);
                    if (catBadgeEl) catBadgeEl.innerText = p.category;

                    if (imgBoxEl) {
                        if (p.image) {
                            imgBoxEl.innerHTML = `<img src="${p.image}" class="w-full h-full object-cover">`;
                        } else {
                            imgBoxEl.innerHTML = `<span class="text-xs">🌿</span>`;
                        }
                    }

                    const toast = document.getElementById('ajaxToast');
                    toast.classList.remove('hidden');
                    setTimeout(() => toast.classList.add('hidden'), 2000);

                    closeModals();
                }
            })
            .catch(err => {
                saveBtn.innerText = 'Save & Continue';
                console.error(err);
            });
        }

        // Chart
        const ctx = document.getElementById('sylvaChart')?.getContext('2d');
        if (ctx) {
            const gradientGold = ctx.createLinearGradient(0, 0, 0, 200);
            gradientGold.addColorStop(0, 'rgba(212, 175, 55, 0.35)');
            gradientGold.addColorStop(1, 'rgba(212, 175, 55, 0)');

            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
                    datasets: [{
                        label: 'Sales Revenue ($)',
                        data: [1200, 1900, 1500, 2800, 2200, 3500, 3000, 4200, 3800, 4800, 5200, 6100],
                        borderColor: '#d4af37',
                        borderWidth: 2,
                        backgroundColor: gradientGold,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 2,
                        pointBackgroundColor: '#d4af37'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { display: false }, ticks: { color: '#888', font: { size: 10 } } },
                        y: { grid: { color: 'rgba(212, 175, 55, 0.08)' }, ticks: { color: '#888', font: { size: 10 } } }
                    }
                }
            });
        }
    </script>

    <?php 
    $footer_file = file_exists(__DIR__ . '/footer.php') ? __DIR__ . '/footer.php' : __DIR__ . '/../footer.php';
    if (file_exists($footer_file)) { include $footer_file; }
    ?>
</body>
</html>