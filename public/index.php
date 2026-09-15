<?php
session_start();
require_once __DIR__ . '/config/db.php';

// قائمة الأدمنز الـ 4 الرسمية (كلها حروف صغيرة وبدون مسافات)
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
    $featured_products = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 4")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $featured_products = [];
}

$cart_count = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
?>
<!DOCTYPE html>
<html lang="en" class="dark scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AURA & SCENT — Luxury Fragrance House</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%23070708'/><text x='50%' y='68%' font-family='serif' font-size='65' font-weight='bold' fill='%23d4af37' text-anchor='middle'>A</text></svg>">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: { brand: { 50: '#fdfbf7', 900: '#0a0a0c', gold: '#d4af37', 'gold-light': '#f3e5ab' } },
                    fontFamily: { serif: ['Playfair Display', 'serif'], sans: ['Plus Jakarta Sans', 'sans-serif'] }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,900;1,400&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #070708; color: #fdfbf7; font-family: 'Plus Jakarta Sans', sans-serif; overflow-x: hidden; }
        .hero-glow { background: radial-gradient(circle at 50% 30%, rgba(212, 175, 55, 0.12) 0%, rgba(7, 7, 8, 0) 70%); }
        .glass-card { background: rgba(18, 18, 20, 0.6); backdrop-filter: blur(20px); border: 1px solid rgba(255, 255, 255, 0.05); }
    </style>
</head>
<body class="selection:bg-brand-gold selection:text-black antialiased">

    <!-- Header / Navbar -->
    <header class="fixed top-0 left-0 w-full z-50 glass-card border-b-0 border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-8 h-20 sm:h-24 flex items-center justify-between">
            <a href="index.php" class="font-serif text-lg sm:text-2xl font-bold tracking-[0.2em] sm:tracking-[0.25em] text-white">
                AURA <span class="text-brand-gold">&</span> SCENT
            </a>
            
            <nav class="hidden md:flex items-center gap-8 text-[11px] font-semibold tracking-[0.2em] uppercase text-neutral-400">
                <a href="index.php" class="text-brand-gold">Home</a>
                <a href="shop.php" class="hover:text-white transition-colors">Shop</a>

                <?php if (isset($_SESSION['user_id'])): ?>
                    <?php if ($is_admin): ?>
                        <a href="admin.php" class="text-brand-gold font-bold hover:text-brand-gold-light">Dashboard</a>
                    <?php else: ?>
                        <a href="cart.php" class="hover:text-brand-gold transition-colors flex items-center gap-2 text-white">
                            <span>Cart</span>
                            <span id="cart-badge-desktop" class="bg-brand-gold text-black font-extrabold text-[10px] px-2 py-0.5 rounded-full"><?= $cart_count ?></span>
                        </a>
                    <?php endif; ?>
                    <a href="logout.php" class="text-red-400 hover:text-red-300">Logout</a>
                <?php else: ?>
                    <a href="cart.php" class="hover:text-brand-gold transition-colors flex items-center gap-2 text-white">
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
            <a href="index.php" class="block text-brand-gold text-sm font-bold uppercase tracking-widest">Home</a>
            <a href="shop.php" class="block text-neutral-300 text-sm font-bold uppercase tracking-widest hover:text-brand-gold">Shop</a>
            
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

    <!-- Hero Section -->
    <section class="relative min-h-screen flex items-center justify-center pt-28 pb-16 hero-glow px-4 sm:px-6">
        <div class="max-w-4xl mx-auto text-center z-10">
            <span class="text-brand-gold text-[10px] sm:text-xs font-semibold tracking-[0.3em] sm:tracking-[0.4em] uppercase block mb-4 sm:mb-6">Exquisite Craftsmanship</span>
            <h1 class="font-serif text-4xl sm:text-6xl md:text-8xl font-normal tracking-tight text-white mb-6 sm:mb-8 leading-tight">
                The Essence of <br><span class="italic font-light text-brand-gold-light">Pure Luxury.</span>
            </h1>
            <p class="text-neutral-400 text-xs sm:text-sm md:text-base max-w-xl mx-auto mb-8 sm:mb-12 font-light leading-relaxed">
                Immerse your senses in our handcrafted collection of rare, timeless fragrances designed to leave an unforgettable aura.
            </p>
            <div class="flex justify-center">
                <a href="shop.php" class="w-full sm:w-auto bg-brand-gold text-black font-bold text-xs uppercase tracking-[0.2em] px-8 sm:px-10 py-4 sm:py-5 rounded-full hover:bg-white transition-all">
                    Explore Collection
                </a>
            </div>
        </div>
    </section>

    <!-- Featured Products Grid -->
    <section class="max-w-7xl mx-auto px-4 sm:px-8 py-16 sm:py-24 border-t border-white/5">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-end mb-10 sm:mb-16 gap-4">
            <div>
                <span class="text-brand-gold text-[10px] font-bold tracking-[0.3em] uppercase block mb-2">Handpicked</span>
                <h2 class="font-serif text-2xl sm:text-4xl text-white">Featured Fragrances</h2>
            </div>
            <a href="shop.php" class="text-xs font-semibold tracking-[0.2em] text-neutral-400 hover:text-brand-gold transition-colors">View All →</a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
            <?php foreach ($featured_products as $p): ?>
                <div class="glass-card rounded-2xl p-4 sm:p-6 transition-all group flex flex-col justify-between">
                    <div>
                        <div class="w-full h-56 sm:h-72 rounded-xl overflow-hidden mb-4 sm:mb-6 bg-neutral-900/50 relative">
                            <img src="<?= htmlspecialchars(!empty($p['image']) ? $p['image'] : 'https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?q=80&w=800&auto=format&fit=crop') ?>" alt="<?= htmlspecialchars($p['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        </div>
                        <h3 class="font-serif text-lg sm:text-xl text-white mb-2"><?= htmlspecialchars($p['name']) ?></h3>
                        <p class="text-neutral-400 text-xs font-light line-clamp-2 mb-4 leading-relaxed"><?= htmlspecialchars($p['description']) ?></p>
                    </div>
                    <div class="flex items-center justify-between pt-4 border-t border-white/5">
                        <span class="font-serif text-base sm:text-lg text-brand-gold">$<?= number_format($p['price'], 2) ?></span>
                        <button onclick="addToCart(event, <?= $p['id'] ?>)" class="text-[10px] font-bold uppercase tracking-widest text-white hover:text-brand-gold cursor-pointer transition-colors">
                            + Add to Cart
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- AI Advisor Button -->
    <div class="fixed bottom-6 right-4 sm:right-8 z-50">
        <button onclick="toggleAIChat()" class="glass-card text-white px-4 sm:px-6 py-3 sm:py-4 rounded-full shadow-2xl flex items-center gap-2 sm:gap-3 cursor-pointer">
            <span class="w-2.5 h-2.5 rounded-full bg-brand-gold animate-pulse"></span>
            <span class="text-[10px] sm:text-xs font-bold uppercase tracking-[0.2em]">AI Advisor</span>
        </button>

        <div id="ai-chat-box" class="hidden absolute bottom-16 right-0 w-[300px] sm:w-[360px] glass-card rounded-3xl shadow-2xl overflow-hidden border border-white/10">
            <div class="p-4 border-b border-white/5 flex justify-between items-center bg-black/40">
                <span class="font-serif text-xs sm:text-sm text-brand-gold">AI Scent Advisor</span>
                <button onclick="toggleAIChat()" class="text-neutral-400 hover:text-white text-xs">✕</button>
            </div>
            <div id="ai-messages" class="p-4 h-60 sm:h-72 overflow-y-auto space-y-3 text-xs font-light">
                <div class="bg-white/5 p-3 rounded-2xl border border-white/5 text-neutral-300">
                    Welcome to Aura & Scent! Describe your mood or notes, and I'll find your perfect match.
                </div>
            </div>
            <div class="p-3 border-t border-white/5 bg-black/40 flex gap-2">
                <input type="text" id="ai-input" placeholder="Ask recommendations..." class="flex-grow bg-white/5 border border-white/10 rounded-full px-3 py-1.5 text-xs text-white focus:outline-none">
                <button onclick="sendAIMessage()" class="bg-brand-gold text-black font-bold text-[10px] uppercase px-3 py-1.5 rounded-full">Send</button>
            </div>
        </div>
    </div>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-50 hidden bg-brand-gold text-black px-6 py-3 rounded-full font-bold text-xs uppercase tracking-widest shadow-2xl transition-all border border-brand-gold-light">
        ✨ Fragrance added to your bag!
    </div>

    <script>
        function toggleMobileMenu() { document.getElementById('mobile-menu').classList.toggle('hidden'); }
        function toggleAIChat() { document.getElementById('ai-chat-box').classList.toggle('hidden'); }

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

        function sendAIMessage() {
            const input = document.getElementById('ai-input');
            const messages = document.getElementById('ai-messages');
            if (!input.value.trim()) return;

            messages.innerHTML += `<div class="bg-brand-gold/10 border border-brand-gold/20 p-2.5 rounded-2xl text-brand-gold ml-auto max-w-[85%]">${input.value}</div>`;
            input.value = '';
            messages.scrollTop = messages.scrollHeight;

            setTimeout(() => {
                messages.innerHTML += `<div class="bg-white/5 p-2.5 rounded-2xl border border-white/5 text-neutral-300 max-w-[85%]">I recommend exploring our signature collection!</div>`;
                messages.scrollTop = messages.scrollHeight;
            }, 700);
        }
    </script>
</body>
</html>