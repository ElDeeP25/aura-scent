<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/config/db.php';

// قائمة الأدمنز المسموح لهم
$admin_emails = [
    "y.yousefahmed26112001@gmail.com",
    "kholoudsaied@gmail.com",
    "nahlaamer2004@gmail.com",
    "rizkmariem8@gmail.com"
];

$is_admin = false;

if (isset($_SESSION['user_id'])) {
    try {
        $stmt = $pdo->prepare("SELECT name, email, role FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user_data) {
            $_SESSION['user_name'] = $user_data['name'] ?? 'User';
            $_SESSION['user_email'] = $user_data['email'] ?? '';
            
            $user_email_clean = strtolower(trim($user_data['email'] ?? ''));
            $user_role_clean = strtolower(trim($user_data['role'] ?? ''));
            $admin_emails_clean = array_map(function($e) { return strtolower(trim($e)); }, $admin_emails);
            
            if (in_array($user_email_clean, $admin_emails_clean) || $user_role_clean === 'admin') {
                $is_admin = true;
                $_SESSION['user_role'] = 'admin';
            }
        }
    } catch (Exception $e) {}
}

try {
    $featured_products = $pdo->query("SELECT * FROM products ORDER BY id DESC LIMIT 8")->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $featured_products = [];
}

// تجهيز قائمة العطور السلايدر للـ Hero (فقط التي تحتوي على صور)
$hero_products = array_filter($featured_products, function($p) {
    return !empty($p['image']) || !empty($p['image_url']);
});
$hero_products = array_values($hero_products);

$cart_count = isset($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AURA & SCENT | Sylva Living Green Edition</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%230f1d13'/><text x='50%' y='68%' font-family='serif' font-size='65' font-weight='bold' fill='%23d4af37' text-anchor='middle'>A</text></svg>">
    
    <!-- Tailwind CSS & Fonts -->
    <script src="https://cdn.tailwindcss.com"></script>
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
                            emerald: '#386641',
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
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,200..800;1,6..72,200..800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            background-color: #0b130e;
            color: #e8ece9;
            font-family: 'Plus Jakarta Sans', sans-serif;
            overflow-x: hidden;
        }

        .sylva-plate {
            background: rgba(18, 31, 23, 0.55);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(212, 175, 55, 0.15);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.4);
        }

        .sylva-plate-hover:hover {
            border-color: rgba(212, 175, 55, 0.4);
            transform: translateY(-4px);
            transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
        }

        .hero-slide {
            transition: opacity 0.8s ease-in-out;
        }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #0b130e; }
        ::-webkit-scrollbar-thumb { background: #2d4a36; border-radius: 3px; }
    </style>
</head>
<body class="relative selection:bg-sylva-gold selection:text-black">

    <!-- WebGL Background -->
    <?php 
    $bg_file = __DIR__ . '/bg-animation.php';
    if (!file_exists($bg_file)) { $bg_file = __DIR__ . '/../bg-animation.php'; }
    if (file_exists($bg_file)) { include $bg_file; }
    ?>

    <!-- Floating Navigation Bar -->
    <?php include __DIR__ . '/header.php'; ?>

    <!-- Hero Section -->
    <section class="min-h-screen relative z-10 flex items-center pt-32 pb-20 px-6 max-w-7xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center w-full">
            <div class="lg:col-span-7 space-y-8">
                <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-sylva-gold/30 bg-sylva-card/60 backdrop-blur-md">
                    <span class="text-[11px] uppercase tracking-[0.25em] text-sylva-gold font-medium">Botanical Organic Extraction</span>
                </div>

                <h1 class="text-5xl sm:text-7xl font-serif font-light leading-[1.08] text-sylva-light tracking-tight">
                    Living Green <br>
                    <span class="italic text-sylva-gold">Botanical Elegance</span>
                </h1>

                <p class="text-neutral-300 text-sm sm:text-base font-light max-w-xl leading-relaxed">
                    Immerse your senses in handcrafted botanical fragrances extracted from rare flora, aged mosses, and organic elixirs designed to leave an unforgettable, natural aura.
                </p>

                <div class="flex flex-wrap items-center gap-5 pt-4">
                    <a href="shop.php" class="bg-sylva-gold text-black font-extrabold text-xs uppercase tracking-[0.2em] px-8 py-4 rounded-full shadow-lg shadow-sylva-gold/20 hover:bg-white transition-all transform hover:-translate-y-1">
                        Explore Collection
                    </a>
                    <a href="#featured" class="sylva-plate text-sylva-light font-bold text-xs uppercase tracking-[0.2em] px-8 py-4 rounded-full hover:border-sylva-gold transition-all">
                        Discover Notes
                    </a>
                </div>
            </div>

            <!-- Auto-rotating Interactive Hero Card -->
            <div class="lg:col-span-5 relative">
                <div class="sylva-plate rounded-3xl p-8 relative overflow-hidden group h-[520px] flex flex-col justify-between">
                    <div class="absolute inset-0 bg-gradient-to-tr from-sylva-emerald/30 via-transparent to-sylva-gold/10 opacity-60 z-10 pointer-events-none"></div>
                    
                    <?php if (!empty($hero_products)): ?>
                        <div class="relative w-full h-[400px] overflow-hidden rounded-2xl">
                            <?php foreach ($hero_products as $idx => $hp): 
                                $hp_img = !empty($hp['image']) ? $hp['image'] : $hp['image_url'];
                            ?>
                                <div class="hero-slide absolute inset-0 <?= $idx === 0 ? 'opacity-100 z-10' : 'opacity-0 z-0' ?>" data-index="<?= $idx ?>">
                                    <img src="<?= htmlspecialchars($hp_img) ?>" 
                                         alt="<?= htmlspecialchars($hp['name']) ?>" 
                                         class="w-full h-full object-cover rounded-2xl filter group-hover:scale-105 transition-transform duration-700 ease-out">
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Dynamic Title Banner -->
                        <div class="relative z-20 sylva-plate p-4 rounded-2xl flex items-center justify-between mt-4">
                            <div>
                                <span class="text-[9px] font-bold uppercase tracking-[0.2em] text-sylva-gold block">Featured Elixir</span>
                                <h3 id="hero-title" class="font-serif text-lg italic text-white transition-all"><?= htmlspecialchars($hero_products[0]['name']) ?></h3>
                            </div>
                            <span id="hero-price" class="text-sylva-gold font-bold text-xs">$<?= number_format($hero_products[0]['price'], 2) ?></span>
                        </div>
                    <?php else: ?>
                        <div class="w-full h-full bg-sylva-card/80 rounded-2xl flex flex-col items-center justify-center border border-sylva-accent/40">
                            <span class="text-5xl mb-3">🌿</span>
                            <span class="text-xs text-sylva-gold font-mono uppercase tracking-widest">No Featured Products Set</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- Featured Section -->
    <section id="featured" class="relative z-10 py-24 px-6 max-w-7xl mx-auto border-t border-sylva-accent/30">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-16 gap-6">
            <div>
                <span class="text-xs uppercase tracking-[0.3em] text-sylva-gold font-bold block mb-2">Curated Blends</span>
                <h2 class="text-3xl sm:text-5xl font-serif font-light">Organic High Perfumery</h2>
            </div>
            <a href="shop.php" class="text-xs uppercase tracking-[0.2em] font-bold text-sylva-gold hover:text-white transition-colors flex items-center gap-2">
                View All Fragrances &rarr;
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <?php if (!empty($featured_products)): ?>
                <?php foreach ($featured_products as $product): 
                    $p_img = !empty($product['image']) ? $product['image'] : (!empty($product['image_url']) ? $product['image_url'] : '');
                ?>
                    <div class="sylva-plate sylva-plate-hover rounded-3xl p-5 flex flex-col justify-between group">
                        <div>
                            <div class="relative overflow-hidden rounded-2xl mb-5 aspect-square bg-sylva-card flex items-center justify-center">
                                <?php if (!empty($p_img)): ?>
                                    <img src="<?= htmlspecialchars($p_img) ?>" 
                                         alt="<?= htmlspecialchars($product['name']) ?>" 
                                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                <?php else: ?>
                                    <div class="text-center p-4">
                                        <span class="text-3xl block mb-1">🌿</span>
                                        <span class="text-[10px] text-sylva-gold/70 font-mono uppercase tracking-widest block">No Image Set</span>
                                    </div>
                                <?php endif; ?>

                                <span class="absolute top-3 right-3 bg-black/60 backdrop-blur-md text-sylva-gold text-xs font-bold px-3 py-1 rounded-full border border-sylva-gold/20">
                                    $<?= number_format($product['price'], 2) ?>
                                </span>
                            </div>

                            <h3 class="font-serif text-xl font-normal text-sylva-light mb-2 group-hover:text-sylva-gold transition-colors">
                                <?= htmlspecialchars($product['name']) ?>
                            </h3>
                            <p class="text-neutral-400 text-xs font-light line-clamp-2 mb-6">
                                <?= htmlspecialchars($product['description'] ?? 'Botanical organic extract with deep earthy notes.') ?>
                            </p>
                        </div>

                        <a href="shop.php" class="w-full text-center bg-sylva-accent/40 border border-sylva-gold/20 text-sylva-gold py-3 rounded-xl font-extrabold text-[10px] uppercase tracking-[0.2em] hover:bg-sylva-gold hover:text-black transition-all">
                            Explore Fragrance
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-12 text-neutral-400">
                    No botanical fragrances available at the moment.
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Auto-Carousel Script for Hero Card -->
    <script>
        const heroProductsData = <?= json_encode($hero_products) ?>;
        const slides = document.querySelectorAll('.hero-slide');
        const heroTitle = document.getElementById('hero-title');
        const heroPrice = document.getElementById('hero-price');
        let currentHeroIndex = 0;

        if (slides.length > 1) {
            setInterval(() => {
                slides[currentHeroIndex].classList.remove('opacity-100', 'z-10');
                slides[currentHeroIndex].classList.add('opacity-0', 'z-0');

                currentHeroIndex = (currentHeroIndex + 1) % slides.length;

                slides[currentHeroIndex].classList.remove('opacity-0', 'z-0');
                slides[currentHeroIndex].classList.add('opacity-100', 'z-10');

                if (heroProductsData[currentHeroIndex]) {
                    if (heroTitle) heroTitle.innerText = heroProductsData[currentHeroIndex].name;
                    if (heroPrice) heroPrice.innerText = '$' + parseFloat(heroProductsData[currentHeroIndex].price).toFixed(2);
                }
            }, 4000); // تتغير الصورة كل 4 ثوانٍ تلقائياً
        }
    </script>

    <!-- Includes -->
    <?php include __DIR__ . '/footer.php'; ?>
    <?php include __DIR__ . '/ai-widget.php'; ?>
</body>
</html>