<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db_path = __DIR__ . '/config/db.php';
if (!file_exists($db_path)) { $db_path = __DIR__ . '/../config/db.php'; }
if (file_exists($db_path)) { require_once $db_path; }

// Fetch dynamic offers from DB
try {
    $offers_stmt = $pdo->query("SELECT * FROM products WHERE old_price IS NOT NULL AND old_price > price ORDER BY id DESC");
    $offers_list = $offers_stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $offers_list = [];
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exclusive Offers & 3D Showcase | AURA & SCENT</title>

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
        body { background-color: #0b130e; color: #e8ece9; font-family: 'Plus Jakarta Sans', sans-serif; overflow-x: hidden; }
        .sylva-plate {
            background: rgba(18, 31, 23, 0.85);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(212, 175, 55, 0.25);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.7);
        }
        .gold-glow-text {
            color: #d4af37;
            text-shadow: 0 0 20px rgba(212, 175, 55, 0.45);
        }
        .price-card-glass {
            background: linear-gradient(135deg, rgba(212, 175, 55, 0.08) 0%, rgba(18, 31, 23, 0.85) 100%);
            border: 1px solid rgba(212, 175, 55, 0.25);
        }
    </style>
</head>
<body class="relative selection:bg-sylva-gold selection:text-black">

    <!-- Background Animation -->
    <?php 
    $bg_file = __DIR__ . '/bg-animation.php';
    if (!file_exists($bg_file)) { $bg_file = __DIR__ . '/../bg-animation.php'; }
    if (file_exists($bg_file)) { include $bg_file; }
    ?>

    <!-- Header Include -->
    <?php include __DIR__ . '/header.php'; ?>

    <!-- 3D Direct Video Popup Modal (Full Vertical Reel Display) -->
    <div id="videoModal" class="fixed inset-0 z-[9999] bg-black/90 backdrop-blur-xl flex items-center justify-center p-3 sm:p-6 transition-all duration-300">
        <div class="sylva-plate rounded-[2.5rem] p-3 sm:p-5 w-full max-w-[360px] sm:max-w-[420px] relative border border-sylva-gold/40 shadow-[0_0_50px_rgba(212,175,55,0.2)] flex flex-col items-center my-auto">
            
            <!-- Premium Floating Close Button -->
            <button onclick="closeVideoModal()" class="absolute -top-3 -right-2 bg-sylva-gold text-black hover:bg-white font-extrabold text-xs px-4 py-2 rounded-full shadow-2xl transition-all cursor-pointer flex items-center gap-1.5 z-50">
                <span>✕ Close</span>
            </button>

            <div class="text-center mb-2.5 pt-1">
                <span class="text-[9px] font-bold uppercase tracking-[0.25em] text-sylva-gold block mb-0.5">Botanical Showcase</span>
                <h3 class="font-serif italic text-lg sm:text-xl text-sylva-light">Exclusive Elixir Experience</h3>
            </div>

            <!-- Full Vertical Video Frame (True Aspect 9:16 - No Clipping) -->
            <div class="relative w-full aspect-[9/16] h-[72vh] sm:h-[78vh] max-h-[720px] rounded-2xl overflow-hidden bg-black border border-sylva-accent/40 shadow-2xl flex items-center justify-center">
                <video id="heroVideo" loop muted playsinline controls class="w-full h-full object-contain rounded-2xl bg-black">
                    <source src="images/videos/perfume.mp4" type="video/mp4">
                    <source src="images\videos\perfume.mp4" type="video/mp4">
                    Your browser does not support the video tag.
                </video>
            </div>
        </div>
    </div>

    <main class="pt-32 pb-24 px-4 sm:px-8 max-w-7xl mx-auto relative z-10">

        <!-- Page Hero -->
        <section class="text-center max-w-3xl mx-auto mb-16">
            <span class="inline-block px-4 py-1.5 rounded-full border border-sylva-gold/30 bg-sylva-card/80 text-sylva-gold text-xs font-bold uppercase tracking-[0.2em] mb-4">
                ✨ Curated Promotional Selections
            </span>
            <h1 class="font-serif text-4xl sm:text-6xl font-light text-sylva-light mb-4">
                Limited <span class="italic text-sylva-gold">Elixir Offers</span>
            </h1>
            <p class="text-neutral-300 text-sm sm:text-base font-light leading-relaxed">
                Indulge in handcrafted botanical extractions with exclusive promotional rates and limited-time price drops.
            </p>
            <button onclick="openVideoModal()" class="mt-6 inline-flex items-center gap-2 sylva-plate border border-sylva-gold/40 text-sylva-gold px-6 py-3 rounded-full text-xs font-bold uppercase tracking-widest hover:bg-sylva-gold hover:text-black transition-all cursor-pointer shadow-lg">
                <span>▶ Play 3D Showcase Video</span>
            </button>
        </section>

        <!-- Offers Grid -->
        <section>
            <div class="flex items-center justify-between border-b border-sylva-accent/30 pb-4 mb-10">
                <h2 class="font-serif text-2xl sm:text-3xl italic text-sylva-light">Active Promotional Extractions</h2>
                <span class="text-xs text-sylva-gold font-mono uppercase tracking-widest">Special Rates Applied</span>
            </div>

            <?php if (!empty($offers_list)): ?>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <?php foreach ($offers_list as $item): 
                        $discount = round((($item['old_price'] - $item['price']) / $item['old_price']) * 100);
                        $item_img = !empty($item['image']) ? $item['image'] : $item['image_url'];
                    ?>
                        <div class="sylva-plate rounded-3xl p-6 flex flex-col justify-between relative group hover:border-sylva-gold/50 transition-all shadow-2xl">
                            
                            <!-- Premium Floating Badge -->
                            <div class="absolute top-8 right-8 z-10 bg-sylva-gold text-black text-[10px] font-black px-3.5 py-1.5 rounded-full shadow-lg shadow-sylva-gold/20 uppercase tracking-widest border border-yellow-200/50 flex items-center gap-1">
                                <span>⚡ SAVE <?= $discount ?>%</span>
                            </div>

                            <div>
                                <div class="relative aspect-square rounded-2xl overflow-hidden mb-6 border border-sylva-accent/30 bg-sylva-base flex items-center justify-center">
                                    <?php if (!empty($item_img)): ?>
                                        <img src="<?= htmlspecialchars($item_img) ?>" 
                                             alt="<?= htmlspecialchars($item['name']) ?>" 
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                                    <?php else: ?>
                                        <div class="text-center p-4">
                                            <span class="text-3xl block mb-1">🌿</span>
                                            <span class="text-[10px] text-sylva-gold/70 font-mono uppercase tracking-widest block">No Image Set</span>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <h3 class="font-serif text-xl font-normal text-sylva-light mb-2 group-hover:text-sylva-gold transition-colors">
                                    <?= htmlspecialchars($item['name']) ?>
                                </h3>
                                <p class="text-neutral-400 text-xs font-light leading-relaxed mb-6 line-clamp-2">
                                    <?= htmlspecialchars($item['description'] ?? 'Botanical organic extract with deep earthy notes.') ?>
                                </p>
                            </div>

                            <div>
                                <!-- Ultra-Luxurious Price Showcase -->
                                <div class="price-card-glass mb-6 p-4 rounded-2xl flex items-center justify-between shadow-inner">
                                    <div class="flex flex-col">
                                        <span class="text-[9px] uppercase tracking-[0.2em] text-sylva-gold font-bold mb-0.5">Special Price</span>
                                        <div class="flex items-baseline gap-1">
                                            <span class="font-serif text-3xl font-normal gold-glow-text">$<?= number_format($item['price'], 2) ?></span>
                                            <span class="text-[10px] text-sylva-gold/80 font-mono">USD</span>
                                        </div>
                                    </div>

                                    <div class="flex flex-col items-end">
                                        <span class="text-[9px] uppercase tracking-wider text-neutral-400 block mb-1">Original</span>
                                        <span class="text-neutral-500 line-through text-xs font-semibold bg-black/40 px-2 py-0.5 rounded-md border border-neutral-700/50">
                                            $<?= number_format($item['old_price'], 2) ?>
                                        </span>
                                    </div>
                                </div>

                                <button onclick="addToCart(event, <?= $item['id'] ?>)" class="w-full text-center bg-sylva-gold text-black py-3.5 rounded-full font-extrabold text-[11px] uppercase tracking-[0.15em] hover:bg-white transition-all shadow-lg hover:shadow-sylva-gold/20 cursor-pointer transform active:scale-95 flex items-center justify-center gap-2">
                                    <span>+ Add Offer to Cart</span>
                                </button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="sylva-plate p-12 rounded-3xl text-center text-neutral-400">
                    <p class="font-serif italic text-lg mb-2">No Active Promotional Offers at this moment.</p>
                    <p class="text-xs">Check back soon or visit our main shop to explore all fragrances.</p>
                </div>
            <?php endif; ?>
        </section>

    </main>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-8 left-1/2 -translate-x-1/2 z-50 hidden sylva-plate bg-sylva-gold text-black px-8 py-3.5 rounded-full font-extrabold text-xs uppercase tracking-widest shadow-2xl border border-sylva-gold transition-all">
        ✨ Offer added to your bag!
    </div>

    <script>
        // إغلاق المودال وإيقاف الفيديو
        function closeVideoModal() {
            const vid = document.getElementById('heroVideo');
            if (vid) vid.pause();
            document.getElementById('videoModal').classList.add('hidden');
        }

        // فتح المودال وتشغيل الفيديو
        function openVideoModal() {
            const vid = document.getElementById('heroVideo');
            document.getElementById('videoModal').classList.remove('hidden');
            if (vid) {
                vid.play().catch(function(e) { console.log('Playback error', e); });
            }
        }

        // تشغيل الفيديو أوتوماتيك عند فتح الصفحة
        document.addEventListener("DOMContentLoaded", function () {
            const vid = document.getElementById('heroVideo');
            if (vid) {
                vid.play().catch(function(e) { console.log('Autoplay handled:', e); });
            }
        });

        // Add Offer directly to Cart via AJAX
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

    <!-- Footer & AI Widget -->
    <?php include __DIR__ . '/footer.php'; ?>
    <?php include __DIR__ . '/ai-widget.php'; ?>

</body>
</html>