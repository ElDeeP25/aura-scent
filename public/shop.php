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

$is_admin = false;

if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT email FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user_data && !empty($user_data['email'])) {
            $user_email_clean = strtolower(trim($user_data['email']));
            $admin_emails_clean = array_map('strtolower', array_map('trim', $admin_emails));
            if (in_array($user_email_clean, $admin_emails_clean)) {
                $is_admin = true;
            }
        }
    } catch (Exception $e) {}
}

try {
    $products = $pdo->query("SELECT * FROM products ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $products = [];
}

$cart_count = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
?>
<!DOCTYPE html>
<html lang="en" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop — AURA & SCENT</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%23070708'/><text x='50%' y='68%' font-family='serif' font-size='65' font-weight='bold' fill='%23d4af37' text-anchor='middle'>A</text></svg>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: { brand: { gold: '#d4af37', 'gold-light': '#f3e5ab' } },
                    fontFamily: { serif: ['Playfair Display', 'serif'], sans: ['Plus Jakarta Sans', 'sans-serif'] }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,900;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #070708; color: #fdfbf7; font-family: 'Plus Jakarta Sans', sans-serif; overflow-x: hidden; }
        .glass-card { background: rgba(18, 18, 20, 0.6); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.05); }
    </style>
</head>
<body class="selection:bg-brand-gold selection:text-black antialiased">

    <header class="fixed top-0 left-0 w-full z-50 glass-card border-b-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-8 h-20 sm:h-24 flex items-center justify-between">
            <a href="index.php" class="font-serif text-lg sm:text-2xl font-bold tracking-[0.2em] sm:tracking-[0.25em] text-white">
                AURA <span class="text-brand-gold">&</span> SCENT
            </a>
            
            <nav class="hidden md:flex items-center gap-8 text-[11px] font-semibold tracking-[0.2em] uppercase text-neutral-400">
                <a href="index.php" class="hover:text-white transition-colors">Home</a>
                <a href="shop.php" class="text-brand-gold">Shop</a>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($is_admin): ?>
                        <a href="admin.php" class="text-brand-gold font-bold">Dashboard</a>
                    <?php else: ?>
                        <a href="cart.php" class="hover:text-white transition-colors flex items-center gap-2 text-white">
                            <span>Cart</span>
                            <span id="cart-badge-desktop" class="bg-brand-gold text-black font-extrabold text-[10px] px-2 py-0.5 rounded-full"><?= $cart_count ?></span>
                        </a>
                    <?php endif; ?>
                    <a href="logout.php" class="text-red-400 hover:text-red-300">Logout</a>
                <?php else: ?>
                    <a href="cart.php" class="hover:text-white transition-colors flex items-center gap-2 text-white">
                        <span>Cart</span>
                        <span id="cart-badge-desktop" class="bg-brand-gold text-black font-extrabold text-[10px] px-2 py-0.5 rounded-full"><?= $cart_count ?></span>
                    </a>
                    <a href="auth.php" class="bg-white/5 border border-white/10 text-white px-6 py-2.5 rounded-full hover:bg-brand-gold hover:text-black transition-all">Sign In</a>
                <?php endif; ?>
            </nav>

            <button onclick="toggleMobileMenu()" class="md:hidden text-white p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
            </button>
        </div>

        <div id="mobile-menu" class="hidden md:hidden glass-card border-t border-white/5 px-6 py-6 space-y-4">
            <a href="index.php" class="block text-neutral-300 text-sm font-bold uppercase tracking-widest hover:text-brand-gold">Home</a>
            <a href="shop.php" class="block text-brand-gold text-sm font-bold uppercase tracking-widest">Shop</a>
            <div class="pt-4 border-t border-white/10 flex flex-col gap-3">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($is_admin): ?>
                        <a href="admin.php" class="text-sm font-bold uppercase tracking-widest text-brand-gold">Dashboard</a>
                    <?php else: ?>
                        <a href="cart.php" class="flex justify-between items-center text-sm font-bold uppercase tracking-widest text-white">
                            <span>Cart</span>
                            <span id="cart-badge-mobile" class="bg-brand-gold text-black text-xs font-bold px-2.5 py-1 rounded-full"><?= $cart_count ?></span>
                        </a>
                    <?php endif; ?>
                    <a href="logout.php" class="text-sm font-bold uppercase tracking-widest text-red-400">Logout</a>
                <?php else: ?>
                    <a href="cart.php" class="flex justify-between items-center text-sm font-bold uppercase tracking-widest text-white">
                        <span>Cart</span>
                        <span id="cart-badge-mobile" class="bg-brand-gold text-black text-xs font-bold px-2.5 py-1 rounded-full"><?= $cart_count ?></span>
                    </a>
                    <a href="auth.php" class="block text-center bg-brand-gold text-black py-3 rounded-full font-bold text-xs uppercase tracking-widest">Sign In</a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <section class="pt-32 sm:pt-40 pb-10 sm:pb-12 px-4 sm:px-8 text-center border-b border-white/5">
        <span class="text-brand-gold text-[10px] sm:text-xs font-semibold tracking-[0.3em] uppercase block mb-2">Fragrance House</span>
        <h1 class="font-serif text-3xl sm:text-5xl text-white">Our Products</h1>
    </section>

    <section class="max-w-7xl mx-auto px-4 sm:px-8 py-12 sm:py-16">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            <?php foreach ($products as $p): ?>
                <div class="glass-card rounded-2xl p-4 sm:p-6 transition-all group flex flex-col justify-between">
                    <div>
                        <div class="w-full h-60 sm:h-72 rounded-xl overflow-hidden mb-4 sm:mb-6 bg-neutral-900/50">
                            <img src="<?= htmlspecialchars(!empty($p['image']) ? $p['image'] : 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?q=80&w=800&auto=format&fit=crop') ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        </div>
                        <h3 class="font-serif text-xl text-white mb-2"><?= htmlspecialchars($p['name']) ?></h3>
                        <p class="text-neutral-400 text-xs font-light line-clamp-3 mb-6 leading-relaxed"><?= htmlspecialchars($p['description']) ?></p>
                    </div>
                    <div class="flex items-center justify-between pt-4 border-t border-white/5">
                        <span class="font-serif text-lg text-brand-gold">$<?= number_format($p['price'], 2) ?></span>
                        <button onclick="addToCart(event, <?= $p['id'] ?>)" class="bg-brand-gold text-black font-bold text-[10px] uppercase tracking-[0.2em] px-5 py-2.5 rounded-full hover:bg-white transition-all cursor-pointer">
                            + Add to Cart
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 hidden bg-brand-gold text-black px-6 py-3 rounded-full font-bold text-xs uppercase tracking-widest shadow-2xl transition-all border border-brand-gold-light">
        ✨ Fragrance added to your bag!
    </div>

    <script>
        function toggleMobileMenu() { document.getElementById('mobile-menu').classList.toggle('hidden'); }
        
        function addToCart(e, productId) {
            e.preventDefault();
            fetch('cart.php?add=' + productId + '&ajax=1')
            .then(res => res.json())
            .then(data => {
                if (data.status === 'unauthorized') {
                    window.location.href = 'auth.php';
                    return;
                }
                if (data.status === 'success') {
                    const badgeDesktop = document.getElementById('cart-badge-desktop');
                    const badgeMobile = document.getElementById('cart-badge-mobile');
                    if (badgeDesktop) badgeDesktop.innerText = data.cart_count;
                    if (badgeMobile) badgeMobile.innerText = data.cart_count;

                    const toast = document.getElementById('toast');
                    toast.classList.remove('hidden');
                    setTimeout(() => { toast.classList.add('hidden'); }, 2500);
                }
            })
            .catch(err => console.error(err));
        }
    </script>
</body>
</html>