<?php
session_start();
require_once __DIR__ . '/config/db.php';

// قائمة الأدمنز الـ 4 الرسمية
$admin_emails = [
    "y.yousefahmed26112001@gmail.com",
    "kholoudsaied@gmail.com",
    "nahlaamer2004@gmail.com",
    "rizkmariem8@gmail.com"
]; 

if (!isset($_SESSION['user_id'])) {
    header("Location: auth.php");
    exit();
}

try {
    $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $current_user = $stmt->fetch(PDO::FETCH_ASSOC);

    $user_email_clean = strtolower(trim($current_user['email'] ?? ''));
    $admin_emails_clean = array_map('strtolower', array_map('trim', $admin_emails));

    if (!$current_user || !in_array($user_email_clean, $admin_emails_clean)) {
        header("Location: index.php");
        exit();
    }
} catch (Exception $e) {
    header("Location: index.php");
    exit();
}
// ==========================================
// ==========================================

$message = '';
$error = '';
$tab = $_GET['tab'] ?? 'overview';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add_product') {
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $image = trim($_POST['image'] ?? '');

        if (!empty($name) && $price > 0) {
            $stmt = $pdo->prepare("INSERT INTO products (name, description, price, image) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $description, $price, $image]);
            $message = "Product added successfully!";
        } else {
            $error = "Please fill in all required product fields.";
        }
    } elseif ($action === 'edit_product') {
        $id = intval($_POST['product_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $price = floatval($_POST['price'] ?? 0);
        $image = trim($_POST['image'] ?? '');

        if ($id > 0 && !empty($name)) {
            $stmt = $pdo->prepare("UPDATE products SET name = ?, description = ?, price = ?, image = ? WHERE id = ?");
            $stmt->execute([$name, $description, $price, $image, $id]);
            $message = "Product updated successfully!";
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
            if (!empty($new_password)) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE users SET name = ?, password = ? WHERE id = ?");
                $stmt->execute([$name, $hashed_password, $user_id]);
            } else {
                $stmt = $pdo->prepare("UPDATE users SET name = ? WHERE id = ?");
                $stmt->execute([$name, $user_id]);
            }
            $message = "User updated successfully!";
        }
    } elseif ($action === 'hash_all_passwords') {
        $stmt = $pdo->query("SELECT id, password FROM users");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $updated_count = 0;
        foreach ($users as $u) {
            $pwd = $u['password'];
            if (!empty($pwd) && substr($pwd, 0, 4) !== '$2y$') {
                $hashed = password_hash($pwd, PASSWORD_DEFAULT);
                $update_stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $update_stmt->execute([$hashed, $u['id']]);
                $updated_count++;
            }
        }
        $message = "Successfully hashed $updated_count user password(s)!";
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
<html lang="en" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | AURA & SCENT</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%23070708'/><text x='50%' y='68%' font-family='serif' font-size='65' font-weight='bold' fill='%23d4af37' text-anchor='middle'>A</text></svg>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: { colors: { gold: { DEFAULT: '#d4af37', light: '#f3e5ab', dark: '#aa8c2c' } } }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #030303; color: #ffffff; overflow-x: hidden; }
        .bg-animation { position: fixed; top: 0; left: 0; width: 100%; height: 100%; overflow: hidden; z-index: -1; pointer-events: none; }
        .orb { position: absolute; border-radius: 50%; filter: blur(100px); opacity: 0.15; animation: floatOrb 15s infinite alternate ease-in-out; }
        .orb-1 { width: 450px; height: 450px; background: #d4af37; top: -10%; left: -10%; }
        .orb-2 { width: 500px; height: 500px; background: #8a6f1d; bottom: -15%; right: -10%; animation-delay: -5s; }
        .orb-3 { width: 350px; height: 350px; background: #c5a059; top: 40%; left: 40%; animation-delay: -10s; }
        @keyframes floatOrb { 0% { transform: translate(0, 0) scale(1); } 100% { transform: translate(50px, 60px) scale(1.15); } }
    </style>
</head>
<body class="min-h-screen text-white relative selection:bg-gold selection:text-black antialiased">

    <div class="bg-animation">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <!-- Admin Header -->
    <header class="fixed top-0 left-0 w-full z-50 bg-[#050505]/80 backdrop-blur-xl border-b border-gold/20 shadow-2xl">
        <div class="max-w-7xl mx-auto px-4 sm:px-8 h-20 flex items-center justify-between">
            <a href="index.php" class="text-lg sm:text-xl font-extrabold tracking-[0.2em] sm:tracking-[0.25em] bg-gradient-to-r from-white via-neutral-200 to-gold-light bg-clip-text text-transparent">
                AURA & SCENT <span class="text-[10px] sm:text-xs text-gold ml-1 sm:ml-2 tracking-normal font-bold">ADMIN</span>
            </a>
            
            <nav class="hidden md:flex items-center gap-8 text-xs font-bold tracking-[0.2em] uppercase text-neutral-400">
                <a href="index.php" class="hover:text-gold transition-colors">Home</a>
                <a href="shop.php" class="hover:text-gold transition-colors">Shop</a>
                <a href="admin.php" class="text-gold">Dashboard</a>
                <a href="logout.php" class="text-red-400 hover:text-red-300">Logout</a>
            </nav>

            <button onclick="toggleMobileMenu()" class="md:hidden text-white p-2 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
            </button>
        </div>

        <div id="mobile-menu" class="hidden md:hidden bg-[#0a0a0a]/95 backdrop-blur-xl border-b border-gold/20 px-6 py-4 space-y-3 text-xs uppercase font-bold tracking-wider">
            <a href="index.php" class="block text-neutral-300 hover:text-gold">Home</a>
            <a href="shop.php" class="block text-neutral-300 hover:text-gold">Shop</a>
            <a href="admin.php" class="block text-gold">Dashboard</a>
            <a href="logout.php" class="block text-red-400">Logout</a>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-8 pt-28 sm:pt-36 pb-24 relative z-10">
        
        <div class="flex flex-col lg:flex-row lg:items-end justify-between mb-8 sm:mb-12 gap-6 border-b border-neutral-800/80 pb-6">
            <div>
                <h1 class="text-2xl sm:text-3xl font-black uppercase tracking-wider mb-2">Admin Control Center</h1>
                <p class="text-neutral-400 text-xs uppercase tracking-widest font-light">Manage store metrics, products, users, and security hashes.</p>
            </div>
            
            <div class="flex items-center gap-1.5 sm:gap-2 bg-[#0a0a0a]/90 backdrop-blur-md p-1.5 rounded-2xl border border-gold/20 self-start lg:self-auto overflow-x-auto max-w-full">
                <a href="admin.php?tab=overview" class="px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl font-extrabold text-[10px] sm:text-xs uppercase tracking-wider transition-all whitespace-nowrap <?= $tab === 'overview' ? 'bg-gold text-black shadow-lg shadow-gold/20' : 'text-neutral-400 hover:text-white' ?>">
                    Overview
                </a>
                <a href="admin.php?tab=products" class="px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl font-extrabold text-[10px] sm:text-xs uppercase tracking-wider transition-all whitespace-nowrap <?= $tab === 'products' ? 'bg-gold text-black shadow-lg shadow-gold/20' : 'text-neutral-400 hover:text-white' ?>">
                    Products (<?= count($products) ?>)
                </a>
                <a href="admin.php?tab=users" class="px-4 sm:px-5 py-2 sm:py-2.5 rounded-xl font-extrabold text-[10px] sm:text-xs uppercase tracking-wider transition-all whitespace-nowrap <?= $tab === 'users' ? 'bg-gold text-black shadow-lg shadow-gold/20' : 'text-neutral-400 hover:text-white' ?>">
                    Users (<?= count($users) ?>)
                </a>
            </div>
        </div>

        <?php if (!empty($message)): ?>
            <div class="mb-8 p-4 bg-emerald-950/50 border border-emerald-500/30 text-emerald-400 rounded-2xl text-xs uppercase tracking-wider font-bold backdrop-blur-md text-center">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <div class="mb-8 p-4 bg-red-950/50 border border-red-500/30 text-red-400 rounded-2xl text-xs uppercase tracking-wider font-bold backdrop-blur-md text-center">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <?php if ($tab === 'overview'): ?>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 sm:gap-6 mb-12">
                <div class="bg-[#0a0a0a]/80 backdrop-blur-xl border border-neutral-800 p-6 sm:p-8 rounded-3xl shadow-xl hover:border-gold/30 transition-all">
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-neutral-500 block mb-2">Total Products</span>
                    <span class="text-3xl sm:text-4xl font-black text-gold"><?= $total_products ?></span>
                </div>
                <div class="bg-[#0a0a0a]/80 backdrop-blur-xl border border-neutral-800 p-6 sm:p-8 rounded-3xl shadow-xl hover:border-gold/30 transition-all">
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-neutral-500 block mb-2">Registered Users</span>
                    <span class="text-3xl sm:text-4xl font-black text-gold"><?= $total_users ?></span>
                </div>
                <div class="bg-[#0a0a0a]/80 backdrop-blur-xl border border-neutral-800 p-6 sm:p-8 rounded-3xl shadow-xl hover:border-gold/30 transition-all">
                    <span class="text-xs font-bold uppercase tracking-[0.2em] text-neutral-500 block mb-2">Total Cart/Orders</span>
                    <span class="text-3xl sm:text-4xl font-black text-gold"><?= $total_orders ?></span>
                </div>
            </div>

            <div class="bg-[#0a0a0a]/80 backdrop-blur-xl border border-neutral-800 p-6 sm:p-8 rounded-3xl text-center shadow-xl max-w-2xl mx-auto">
                <h3 class="text-base sm:text-lg font-bold mb-2 uppercase tracking-wider">Quick Security Action</h3>
                <p class="text-neutral-400 text-xs mb-6 font-light">Ensure all user accounts have securely hashed passwords in the database.</p>
                <form method="POST">
                    <input type="hidden" name="action" value="hash_all_passwords">
                    <button type="submit" class="w-full sm:w-auto bg-gold text-black px-8 py-3.5 rounded-full font-extrabold text-xs uppercase tracking-widest hover:bg-gold-light transition-all shadow-lg shadow-gold/20 cursor-pointer">
                        Hash All Existing Passwords Now
                    </button>
                </form>
            </div>
        <?php endif; ?>

        <?php if ($tab === 'products'): ?>
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="bg-[#0a0a0a]/80 backdrop-blur-xl border border-neutral-800 p-6 sm:p-8 rounded-3xl h-fit shadow-xl">
                    <h2 class="text-base sm:text-lg font-bold mb-6 uppercase tracking-wider text-gold">Add New Perfume</h2>
                    <form method="POST" class="space-y-4">
                        <input type="hidden" name="action" value="add_product">
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-neutral-400 mb-2">Product Name</label>
                            <input type="text" name="name" required class="w-full bg-neutral-900/90 border border-neutral-800 rounded-xl px-4 py-3 text-xs focus:border-gold focus:outline-none transition-colors text-white">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-neutral-400 mb-2">Price ($ USD)</label>
                            <input type="number" step="0.01" name="price" required class="w-full bg-neutral-900/90 border border-neutral-800 rounded-xl px-4 py-3 text-xs focus:border-gold focus:outline-none transition-colors text-white">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-neutral-400 mb-2">Image URL</label>
                            <input type="text" name="image" placeholder="https://..." class="w-full bg-neutral-900/90 border border-neutral-800 rounded-xl px-4 py-3 text-xs focus:border-gold focus:outline-none transition-colors text-white">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-wider text-neutral-400 mb-2">Description</label>
                            <textarea name="description" rows="3" class="w-full bg-neutral-900/90 border border-neutral-800 rounded-xl px-4 py-3 text-xs focus:border-gold focus:outline-none transition-colors text-white resize-none"></textarea>
                        </div>
                        <button type="submit" class="w-full bg-gold text-black py-4 rounded-full font-extrabold text-xs uppercase tracking-widest hover:bg-gold-light transition-all shadow-lg shadow-gold/20 cursor-pointer">
                            Save Product
                        </button>
                    </form>
                </div>

                <div class="lg:col-span-2 bg-[#0a0a0a]/80 backdrop-blur-xl border border-neutral-800 rounded-3xl overflow-hidden shadow-xl">
                    <div class="p-6 border-b border-neutral-800">
                        <h2 class="text-base sm:text-lg font-bold uppercase tracking-wider">All Products (<?= count($products) ?>)</h2>
                    </div>
                    <div class="divide-y divide-neutral-800/80 max-h-[650px] overflow-y-auto">
                        <?php foreach ($products as $p): ?>
                            <div class="p-4 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-neutral-900/40 transition-colors">
                                <div class="flex items-center gap-4">
                                    <img src="<?= htmlspecialchars(!empty($p['image']) ? $p['image'] : 'https://images.unsplash.com/photo-1523293182086-7651a899d37f?q=80&w=600&auto=format&fit=crop') ?>" class="w-14 h-14 rounded-xl object-cover bg-neutral-900 border border-neutral-800 shrink-0">
                                    <div>
                                        <h4 class="font-bold text-sm sm:text-base text-white"><?= htmlspecialchars($p['name']) ?></h4>
                                        <span class="text-gold font-bold text-xs">$<?= number_format($p['price'], 2) ?></span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3 self-end sm:self-auto pt-2 sm:pt-0 border-t sm:border-t-0 border-white/5 w-full sm:w-auto justify-end">
                                    <button onclick="openEditProductModal(<?= htmlspecialchars(json_encode($p)) ?>)" class="bg-neutral-800/80 hover:bg-gold hover:text-black text-white px-4 py-2 rounded-xl text-xs font-extrabold uppercase tracking-wider transition-all cursor-pointer">Edit</button>
                                    <form method="POST" onsubmit="return confirm('Delete this product?');">
                                        <input type="hidden" name="action" value="delete_product">
                                        <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                        <button type="submit" class="bg-red-950/40 border border-red-500/30 text-red-400 hover:bg-red-600 hover:text-white px-4 py-2 rounded-xl text-xs font-extrabold uppercase tracking-wider transition-all cursor-pointer">Delete</button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($tab === 'users'): ?>
            <div class="bg-[#0a0a0a]/80 backdrop-blur-xl border border-neutral-800 rounded-3xl overflow-hidden shadow-xl">
                <div class="p-6 border-b border-neutral-800 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                    <h2 class="text-base sm:text-lg font-bold uppercase tracking-wider">Registered Users (<?= count($users) ?>)</h2>
                    <form method="POST">
                        <input type="hidden" name="action" value="hash_all_passwords">
                        <button type="submit" class="bg-gold text-black px-4 py-2 rounded-xl text-xs font-extrabold uppercase tracking-wider hover:bg-gold-light transition-all shadow-md shadow-gold/20 cursor-pointer">Hash All Passwords</button>
                    </form>
                </div>
                <div class="divide-y divide-neutral-800/80">
                    <?php foreach ($users as $u): ?>
                        <div class="p-4 sm:p-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div>
                                <h4 class="font-bold text-sm text-white"><?= htmlspecialchars($u['name'] ?? 'No Name') ?></h4>
                                <p class="text-neutral-400 text-xs"><?= htmlspecialchars($u['email'] ?? 'No Email') ?></p>
                            </div>
                            <button onclick="openEditUserModal(<?= htmlspecialchars(json_encode($u)) ?>)" class="self-end sm:self-auto bg-neutral-800/80 hover:bg-gold hover:text-black text-white px-4 py-2 rounded-xl text-xs font-extrabold uppercase tracking-wider transition-all cursor-pointer">Edit User</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

    </main>

    <!-- Modals -->
    <div id="editProductModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="bg-[#0e0e0e] border border-neutral-800 rounded-3xl p-6 sm:p-8 max-w-md w-full relative shadow-2xl">
            <h3 class="text-lg font-bold mb-6 uppercase tracking-wider text-gold">Edit Product</h3>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="edit_product">
                <input type="hidden" name="product_id" id="edit_product_id">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-neutral-400 mb-2">Product Name</label>
                    <input type="text" name="name" id="edit_product_name" required class="w-full bg-neutral-900 border border-neutral-800 rounded-xl px-4 py-3 text-xs focus:border-gold focus:outline-none text-white">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-neutral-400 mb-2">Price ($ USD)</label>
                    <input type="number" step="0.01" name="price" id="edit_product_price" required class="w-full bg-neutral-900 border border-neutral-800 rounded-xl px-4 py-3 text-xs focus:border-gold focus:outline-none text-white">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-neutral-400 mb-2">Image URL</label>
                    <input type="text" name="image" id="edit_product_image" class="w-full bg-neutral-900 border border-neutral-800 rounded-xl px-4 py-3 text-xs focus:border-gold focus:outline-none text-white">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-neutral-400 mb-2">Description</label>
                    <textarea name="description" id="edit_product_description" rows="3" class="w-full bg-neutral-900 border border-neutral-800 rounded-xl px-4 py-3 text-xs focus:border-gold focus:outline-none text-white resize-none"></textarea>
                </div>
                <div class="flex items-center gap-4 pt-2">
                    <button type="submit" class="flex-1 bg-gold text-black py-3 rounded-full font-extrabold text-xs uppercase tracking-widest hover:bg-gold-light transition-all cursor-pointer">Update</button>
                    <button type="button" onclick="closeModals()" class="flex-1 bg-neutral-800 text-white py-3 rounded-full font-extrabold text-xs uppercase tracking-widest hover:bg-neutral-700 transition-all cursor-pointer">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editUserModal" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-md hidden flex items-center justify-center p-4">
        <div class="bg-[#0e0e0e] border border-neutral-800 rounded-3xl p-6 sm:p-8 max-w-md w-full relative shadow-2xl">
            <h3 class="text-lg font-bold mb-6 uppercase tracking-wider text-gold">Edit User Details</h3>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="action" value="edit_user">
                <input type="hidden" name="user_id" id="edit_user_id">
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-neutral-400 mb-2">User Name</label>
                    <input type="text" name="name" id="edit_user_name" required class="w-full bg-neutral-900 border border-neutral-800 rounded-xl px-4 py-3 text-xs focus:border-gold focus:outline-none text-white">
                </div>
                <div>
                    <label class="block text-[10px] font-bold uppercase tracking-wider text-neutral-400 mb-2">New Password (Optional)</label>
                    <input type="password" name="password" placeholder="Leave blank to keep current password" class="w-full bg-neutral-900 border border-neutral-800 rounded-xl px-4 py-3 text-xs focus:border-gold focus:outline-none text-white">
                </div>
                <div class="flex items-center gap-4 pt-2">
                    <button type="submit" class="flex-1 bg-gold text-black py-3 rounded-full font-extrabold text-xs uppercase tracking-widest hover:bg-gold-light transition-all cursor-pointer">Update User</button>
                    <button type="button" onclick="closeModals()" class="flex-1 bg-neutral-800 text-white py-3 rounded-full font-extrabold text-xs uppercase tracking-widest hover:bg-neutral-700 transition-all cursor-pointer">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function toggleMobileMenu() { document.getElementById('mobile-menu').classList.toggle('hidden'); }
        function openEditProductModal(product) {
            document.getElementById('edit_product_id').value = product.id;
            document.getElementById('edit_product_name').value = product.name;
            document.getElementById('edit_product_price').value = product.price;
            document.getElementById('edit_product_image').value = product.image || '';
            document.getElementById('edit_product_description').value = product.description || '';
            document.getElementById('editProductModal').classList.remove('hidden');
        }
        function openEditUserModal(user) {
            document.getElementById('edit_user_id').value = user.id;
            document.getElementById('edit_user_name').value = user.name || '';
            document.getElementById('editUserModal').classList.remove('hidden');
        }
        function closeModals() {
            document.getElementById('editProductModal').classList.add('hidden');
            document.getElementById('editUserModal').classList.add('hidden');
        }
    </script>
</body>
</html>