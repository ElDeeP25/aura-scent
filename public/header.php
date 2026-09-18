<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$is_admin = false;
$user_display_name = $_SESSION['user_name'] ?? $_SESSION['name'] ?? '';

$db_path = __DIR__ . '/config/db.php';
if (!file_exists($db_path)) { $db_path = __DIR__ . '/../config/db.php'; }
if (!file_exists($db_path)) { $db_path = __DIR__ . '/../../config/db.php'; }
if (file_exists($db_path)) { require_once $db_path; }

$admin_emails = [
    "y.yousefahmed26112001@gmail.com",
    "kholoudsaied@gmail.com",
    "nahlaamer2004@gmail.com",
    "rizkmariem8@gmail.com"
];

if (isset($_SESSION['user_id'])) {
    if (isset($pdo)) {
        try {
            $stmt = $pdo->prepare("SELECT name, email, role FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user_data) {
                $user_display_name = $user_data['name'] ?? 'Admin';
                $_SESSION['user_name'] = $user_display_name;

                $user_email_clean = strtolower(trim($user_data['email'] ?? ''));
                $user_role = strtolower(trim($user_data['role'] ?? ''));
                $admin_emails_clean = array_map(function($e) { return strtolower(trim($e)); }, $admin_emails);

                if (in_array($user_email_clean, $admin_emails_clean) || $user_role === 'admin') {
                    $is_admin = true;
                }
            }
        } catch (Exception $e) {}
    }

    if (!$is_admin && isset($_SESSION['user_email'])) {
        $session_email = strtolower(trim($_SESSION['user_email']));
        $admin_emails_clean = array_map(function($e) { return strtolower(trim($e)); }, $admin_emails);
        if (in_array($session_email, $admin_emails_clean) || ($_SESSION['user_role'] ?? '') === 'admin') {
            $is_admin = true;
        }
    }
}

if (empty($user_display_name) && isset($_SESSION['user_id'])) {
    $user_display_name = 'Admin';
}

$cart_count = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
$current_page = basename($_SERVER['PHP_SELF']);
$nav_prefix = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../' : '';
?>

<!-- Floating Premium Navigation Bar -->
<header class="fixed top-5 left-1/2 -translate-x-1/2 z-50 w-[94%] max-w-7xl">
    <div class="sylva-plate rounded-full px-6 py-3.5 flex items-center justify-between border border-sylva-gold/25 shadow-2xl backdrop-blur-2xl bg-sylva-base/85 transition-all">
        
        <!-- Logo -->
        <a href="<?= $nav_prefix ?>index.php" class="font-serif italic text-xl sm:text-2xl text-sylva-light tracking-wider flex items-center gap-2.5 group">
            <span class="w-3.5 h-3.5 rounded-full bg-sylva-gold inline-block animate-pulse group-hover:scale-125 transition-transform shadow-lg shadow-sylva-gold/50"></span>
            <span class="group-hover:text-sylva-gold transition-colors">Aura <span class="text-sylva-gold font-normal">&</span> Scent</span>
        </a>
        
        <!-- Desktop Navigation Links -->
        <nav class="hidden md:flex items-center gap-6 text-[11px] font-bold tracking-[0.18em] uppercase">
            <a href="<?= $nav_prefix ?>index.php" class="px-3 py-1.5 rounded-full transition-all duration-300 <?= $current_page == 'index.php' ? 'text-black bg-sylva-gold font-extrabold shadow-md' : 'text-neutral-300 hover:text-sylva-gold hover:bg-sylva-gold/10' ?>">Home</a>
            <a href="<?= $nav_prefix ?>shop.php" class="px-3 py-1.5 rounded-full transition-all duration-300 <?= $current_page == 'shop.php' ? 'text-black bg-sylva-gold font-extrabold shadow-md' : 'text-neutral-300 hover:text-sylva-gold hover:bg-sylva-gold/10' ?>">Shop</a>
            <a href="<?= $nav_prefix ?>about.php" class="px-3 py-1.5 rounded-full transition-all duration-300 <?= $current_page == 'about.php' ? 'text-black bg-sylva-gold font-extrabold shadow-md' : 'text-neutral-300 hover:text-sylva-gold hover:bg-sylva-gold/10' ?>">About Us</a>
            <a href="<?= $nav_prefix ?>offer.php" class="px-3.5 py-1.5 rounded-full transition-all duration-300 border border-sylva-gold/30 <?= ($current_page == 'offer.php' || $current_page == 'offers.php') ? 'text-black bg-sylva-gold font-extrabold shadow-lg' : 'text-sylva-gold hover:bg-sylva-gold hover:text-black' ?> flex items-center gap-1.5">
                <span>✨ Offers</span>
            </a>
            <a href="<?= $nav_prefix ?>collection-ads.php" class="px-3.5 py-1.5 rounded-full transition-all duration-300 border border-sylva-gold/30 <?= $current_page == 'collection-ads.php' ? 'text-black bg-sylva-gold font-extrabold shadow-lg' : 'text-neutral-300 hover:text-sylva-gold hover:bg-sylva-gold/10' ?> flex items-center gap-1.5">
                <span>🎬 Collection Ads</span>
            </a>
        </nav>

        <!-- Right Side User & Controls Container -->
        <div class="hidden md:flex items-center gap-4">
            <?php if (isset($_SESSION['user_id'])): ?>
                
                <!-- Clear & Styled User Pill -->
                <div class="flex items-center gap-2 px-4 py-1.5 rounded-full bg-sylva-card/90 border border-sylva-gold/40 text-sylva-gold shadow-lg backdrop-blur-md">
                    <span class="text-xs">👤</span>
                    <span class="font-serif italic text-sm font-semibold text-white tracking-wide">
                        <?= htmlspecialchars($user_display_name ?: 'Admin') ?>
                    </span>
                    <?php if ($is_admin): ?>
                        <span class="text-[9px] bg-sylva-gold text-black px-2 py-0.5 rounded-full font-sans font-black tracking-widest uppercase">Admin</span>
                    <?php endif; ?>
                </div>

                <?php if ($is_admin): ?>
                    <a href="<?= $nav_prefix ?>admin.php" class="bg-sylva-gold hover:bg-white text-black px-4 py-2 rounded-full font-extrabold text-[10px] uppercase tracking-widest transition-all shadow-lg hover:scale-105 active:scale-95">
                        Dashboard ⚙️
                    </a>
                <?php else: ?>
                    <a href="<?= $nav_prefix ?>cart.php" class="relative p-2.5 rounded-full bg-sylva-card border border-sylva-gold/30 text-sylva-gold hover:border-sylva-gold transition-all">
                        <span class="text-sm">🛍️</span>
                        <span id="cart-badge-desktop" class="absolute -top-1 -right-1 bg-sylva-gold text-black rounded-full text-[10px] w-5 h-5 flex items-center justify-center font-extrabold shadow-md border border-black"><?= $cart_count ?></span>
                    </a>
                <?php endif; ?>

                <a href="<?= $nav_prefix ?>logout.php" class="text-[10px] uppercase font-bold tracking-widest text-red-400 hover:text-red-200 px-3.5 py-1.5 rounded-full border border-red-500/30 hover:bg-red-900/30 transition-all">Logout</a>

            <?php else: ?>
                <a href="<?= $nav_prefix ?>cart.php" class="relative p-2.5 rounded-full bg-sylva-card border border-sylva-gold/30 text-sylva-gold hover:border-sylva-gold transition-all">
                    <span class="text-sm">🛍️</span>
                    <span id="cart-badge-desktop" class="absolute -top-1 -right-1 bg-sylva-gold text-black rounded-full text-[10px] w-5 h-5 flex items-center justify-center font-extrabold shadow-md border border-black"><?= $cart_count ?></span>
                </a>
                <a href="<?= $nav_prefix ?>auth.php" class="bg-sylva-gold hover:bg-white text-black px-6 py-2 rounded-full text-[10px] uppercase font-extrabold tracking-widest transition-all shadow-lg hover:scale-105">Sign In</a>
            <?php endif; ?>
        </div>

        <!-- Mobile Toggle Button -->
        <button onclick="toggleMobileMenu()" class="md:hidden text-sylva-gold p-2 rounded-full hover:bg-sylva-gold/10 focus:outline-none transition-all cursor-pointer">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
        </button>
    </div>

    <!-- Mobile Navigation Menu Dropdown -->
    <div id="mobile-menu" class="hidden md:hidden sylva-plate rounded-3xl mt-3 p-6 space-y-3.5 border border-sylva-gold/30 shadow-2xl backdrop-blur-2xl text-xs text-center">
        <?php if (isset($_SESSION['user_id'])): ?>
            <div class="pb-3 border-b border-sylva-accent/40 flex items-center justify-center gap-2">
                <span class="text-sylva-gold text-sm">👤</span>
                <span class="font-serif italic text-base text-white font-bold"><?= htmlspecialchars($user_display_name ?: 'Admin') ?></span>
                <?php if ($is_admin): ?>
                    <span class="text-[9px] bg-sylva-gold text-black px-2 py-0.5 rounded-full font-bold">Admin</span>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <a href="<?= $nav_prefix ?>index.php" class="block font-bold uppercase tracking-widest py-2 rounded-xl transition-all <?= $current_page == 'index.php' ? 'bg-sylva-gold text-black' : 'text-neutral-300 hover:text-sylva-gold' ?>">Home</a>
        <a href="<?= $nav_prefix ?>shop.php" class="block font-bold uppercase tracking-widest py-2 rounded-xl transition-all <?= $current_page == 'shop.php' ? 'bg-sylva-gold text-black' : 'text-neutral-300 hover:text-sylva-gold' ?>">Shop</a>
        <a href="<?= $nav_prefix ?>about.php" class="block font-bold uppercase tracking-widest py-2 rounded-xl transition-all <?= $current_page == 'about.php' ? 'bg-sylva-gold text-black' : 'text-neutral-300 hover:text-sylva-gold' ?>">About Us</a>
        <a href="<?= $nav_prefix ?>offer.php" class="block font-bold uppercase tracking-widest py-2 rounded-xl transition-all border border-sylva-gold/30 <?= ($current_page == 'offer.php' || $current_page == 'offers.php') ? 'bg-sylva-gold text-black' : 'text-sylva-gold' ?>">✨ Offers</a>
        
        <!-- رابط الصفحة الجديدة بداخل الموبايل -->
        <a href="<?= $nav_prefix ?>collection-ads.php" class="block font-bold uppercase tracking-widest py-2 rounded-xl transition-all border border-sylva-gold/30 <?= $current_page == 'collection-ads.php' ? 'bg-sylva-gold text-black' : 'text-sylva-gold bg-sylva-accent/30' ?>">
            🎬 Collection Ads
        </a>

        <?php if (isset($_SESSION['user_id'])): ?>
            <?php if ($is_admin): ?>
                <a href="<?= $nav_prefix ?>admin.php" class="block bg-sylva-gold text-black font-extrabold uppercase tracking-widest py-2.5 rounded-xl mt-2">Dashboard ⚙️</a>
            <?php else: ?>
                <a href="<?= $nav_prefix ?>cart.php" class="block bg-sylva-accent/40 text-sylva-gold font-bold uppercase tracking-widest py-2.5 rounded-xl">Cart (<?= $cart_count ?>)</a>
            <?php endif; ?>
            <a href="<?= $nav_prefix ?>logout.php" class="block text-red-400 font-bold uppercase tracking-widest py-2">Logout</a>
        <?php else: ?>
            <a href="<?= $nav_prefix ?>cart.php" class="block text-neutral-300 font-bold uppercase tracking-widest py-2">Cart (<?= $cart_count ?>)</a>
            <a href="<?= $nav_prefix ?>auth.php" class="block bg-sylva-gold text-black font-extrabold uppercase tracking-widest py-3 rounded-full mt-2">Sign In</a>
        <?php endif; ?>
    </div>
</header>

<script>
    function toggleMobileMenu() {
        const menu = document.getElementById('mobile-menu');
        if (menu) {
            menu.classList.toggle('hidden');
        }
    }
</script>

<?php 
$bg_file = __DIR__ . '/bg-animation.php';
if (!file_exists($bg_file)) { $bg_file = __DIR__ . '/../bg-animation.php'; }
if (file_exists($bg_file)) { include $bg_file; }
?>