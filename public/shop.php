<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db_path = __DIR__ . '/config/db.php';
if (!file_exists($db_path)) { $db_path = __DIR__ . '/../config/db.php'; }
if (file_exists($db_path)) { require_once $db_path; }

// Smart Function to render filtered products dynamically
function renderProductsGrid($pdo) {
    $sort = $_GET['sort'] ?? 'all';
    $category = $_GET['category'] ?? 'all';

    $query = "SELECT * FROM products WHERE 1=1";
    $params = [];

    // Smart Category Filter Logic
    if ($category !== 'all') {
        if ($category === 'summer') {
            $query .= " AND (LOWER(category) LIKE '%summer%' OR LOWER(season) = 'summer')";
        } elseif ($category === 'winter') {
            $query .= " AND (LOWER(category) LIKE '%winter%' OR LOWER(season) = 'winter')";
        } elseif ($category === 'all-season') {
            $query .= " AND (LOWER(category) LIKE '%all-season%' OR LOWER(season) = 'all' OR LOWER(category) LIKE '%all%')";
        } else {
            $query .= " AND LOWER(category) = :category";
            $params[':category'] = strtolower($category);
        }
    }

    // Sorting Engine
    switch ($sort) {
        case 'price_high':
            $query .= " ORDER BY price DESC";
            break;
        case 'price_low':
            $query .= " ORDER BY price ASC";
            break;
        case 'best_selling':
            $query .= " ORDER BY sales_count DESC, id DESC";
            break;
        default:
            $query .= " ORDER BY id DESC";
            break;
    }

    try {
        $stmt = $pdo->prepare($query);
        $stmt->execute($params);
        $items = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $items = [];
    }

    if (!empty($items)):
        foreach ($items as $item):
            $item_img = !empty($item['image']) ? $item['image'] : ($item['image_url'] ?? '');
            $item_cat = !empty($item['category']) ? $item['category'] : 'All-Season Elixirs';
    ?>
        <div class="sylva-plate rounded-3xl p-6 flex flex-col justify-between group hover:border-sylva-gold/50 transition-all shadow-xl hover:-translate-y-1">
            <div>
                <div class="relative aspect-square rounded-2xl overflow-hidden mb-6 border border-sylva-accent/30 bg-sylva-base flex items-center justify-center">
                    <span class="absolute top-4 left-4 z-10 bg-sylva-card/90 border border-sylva-gold/30 text-sylva-gold text-[10px] font-bold px-3 py-1 rounded-full uppercase tracking-wider backdrop-blur-md">
                        <?= htmlspecialchars($item_cat) ?>
                    </span>

                    <?php if (!empty($item_img)): ?>
                        <img src="<?= htmlspecialchars($item_img) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                    <?php else: ?>
                        <span class="text-4xl block">🌿</span>
                    <?php endif; ?>
                </div>

                <h3 class="font-serif text-xl font-normal text-sylva-light mb-2 group-hover:text-sylva-gold transition-colors">
                    <?= htmlspecialchars($item['name']) ?>
                </h3>
                <p class="text-neutral-400 text-xs font-light leading-relaxed mb-6 line-clamp-2">
                    <?= htmlspecialchars($item['description'] ?? 'Botanical artisan fragrance formulation with pure essential oils.') ?>
                </p>
            </div>

            <div>
                <div class="flex items-center justify-between mb-5 px-1">
                    <span class="font-serif text-2xl font-bold text-sylva-gold">$<?= number_format($item['price'], 2) ?></span>
                    <?php if (!empty($item['old_price']) && $item['old_price'] > $item['price']): ?>
                        <span class="text-neutral-500 line-through text-xs font-semibold">$<?= number_format($item['old_price'], 2) ?></span>
                    <?php endif; ?>
                </div>

                <button onclick="addToCart(event, <?= $item['id'] ?>)" class="w-full text-center bg-sylva-gold text-black py-3 rounded-full font-extrabold text-[11px] uppercase tracking-widest hover:bg-white transition-all shadow-lg cursor-pointer transform active:scale-95">
                    + Add To Cart
                </button>
            </div>
        </div>
    <?php 
        endforeach;
    else: 
    ?>
        <div class="col-span-full sylva-plate p-12 rounded-3xl text-center text-neutral-400">
            <p class="font-serif italic text-lg mb-2">No fragrances match your selected criteria.</p>
            <p class="text-xs">Try selecting another category to view the full gallery.</p>
        </div>
    <?php endif;
}

// Handle AJAX filter request
if (isset($_GET['ajax_filter'])) {
    renderProductsGrid($pdo);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Curated Fragrance Collection | AURA & SCENT</title>

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
        body { background-color: #0b130e; color: #e8ece9; font-family: 'Plus Jakarta Sans', sans-serif; }
        .sylva-plate {
            background: rgba(18, 31, 23, 0.75);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(212, 175, 55, 0.2);
            box-shadow: 0 30px 60px rgba(0, 0, 0, 0.5);
        }
        .filter-btn-active {
            background-color: #d4af37 !important;
            color: #000000 !important;
            font-weight: 800 !important;
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.4);
        }
    </style>
</head>
<body class="min-h-screen relative selection:bg-sylva-gold selection:text-black antialiased">

    <!-- Header Include -->
    <?php include __DIR__ . '/header.php'; ?>

    <main class="max-w-7xl mx-auto px-6 pt-36 sm:pt-44 pb-24 relative z-10">

        <!-- Header Title -->
        <div class="text-center max-w-2xl mx-auto mb-10">
            <span class="text-sylva-gold uppercase tracking-[0.3em] text-xs font-bold block mb-2">Botanical Collection</span>
            <h1 class="font-serif text-4xl sm:text-6xl font-light italic text-sylva-light mb-4">The Fragrance Gallery</h1>
            <p class="text-neutral-400 text-xs sm:text-sm font-light">Explore our hand-extracted organic elixirs and artisan perfumes.</p>
        </div>

        <!-- Sleek Interactive Category Pills Bar -->
        <div class="sylva-plate rounded-3xl p-4 mb-10 border border-sylva-gold/30 shadow-2xl flex flex-col md:flex-row items-center justify-between gap-4">
            
            <!-- Category Quick Selector Pills -->
            <div class="flex items-center gap-2 overflow-x-auto w-full md:w-auto pb-2 md:pb-0 scrollbar-none">
                <button onclick="setCategory('all', this)" class="category-btn filter-btn-active px-5 py-2.5 rounded-full text-xs uppercase tracking-wider transition-all border border-sylva-gold/30 whitespace-nowrap cursor-pointer">
                    ✨ All Extractions
                </button>
                <button onclick="setCategory('all-season', this)" class="category-btn text-neutral-300 hover:text-sylva-gold hover:bg-sylva-gold/10 px-5 py-2.5 rounded-full text-xs uppercase tracking-wider transition-all border border-sylva-gold/20 whitespace-nowrap cursor-pointer">
                    🌿 All-Season
                </button>
                <button onclick="setCategory('summer', this)" class="category-btn text-neutral-300 hover:text-sylva-gold hover:bg-sylva-gold/10 px-5 py-2.5 rounded-full text-xs uppercase tracking-wider transition-all border border-sylva-gold/20 whitespace-nowrap cursor-pointer">
                    ☀️ Summer
                </button>
                <button onclick="setCategory('winter', this)" class="category-btn text-neutral-300 hover:text-sylva-gold hover:bg-sylva-gold/10 px-5 py-2.5 rounded-full text-xs uppercase tracking-wider transition-all border border-sylva-gold/20 whitespace-nowrap cursor-pointer">
                    ❄️ Winter
                </button>
            </div>

            <!-- Sort Selection Option -->
            <div class="flex items-center gap-3 w-full md:w-auto justify-end">
                <span class="text-[10px] uppercase font-bold tracking-widest text-sylva-gold shrink-0">Sort By:</span>
                <select id="sortSelect" onchange="triggerFilter()" class="bg-sylva-card border border-sylva-gold/30 text-sylva-light text-xs px-4 py-2 rounded-full focus:outline-none focus:border-sylva-gold cursor-pointer">
                    <option value="all">Newest First</option>
                    <option value="best_selling">🔥 Best Selling</option>
                    <option value="price_high">💎 Price: High to Low</option>
                    <option value="price_low">🏷️ Price: Low to High</option>
                </select>
            </div>
        </div>

        <!-- Products Display Grid -->
        <div id="productsGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
            <?php renderProductsGrid($pdo); ?>
        </div>

    </main>

    <!-- Toast Notification -->
    <div id="toast" class="fixed bottom-8 left-1/2 -translate-x-1/2 z-50 hidden sylva-plate bg-sylva-gold text-black px-8 py-3.5 rounded-full font-extrabold text-xs uppercase tracking-widest shadow-2xl border border-sylva-gold transition-all">
        ✨ Fragrance added to your bag!
    </div>

    <script>
        let currentCategory = 'all';

        function setCategory(cat, btn) {
            currentCategory = cat;
            document.querySelectorAll('.category-btn').forEach(b => {
                b.classList.remove('filter-btn-active');
                b.classList.add('text-neutral-300');
            });
            btn.classList.add('filter-btn-active');
            btn.classList.remove('text-neutral-300');
            triggerFilter();
        }

        function triggerFilter() {
            const sort = document.getElementById('sortSelect').value;
            fetch(`shop.php?ajax_filter=1&category=${currentCategory}&sort=${sort}`)
                .then(res => res.text())
                .then(html => {
                    document.getElementById('productsGrid').innerHTML = html;
                })
                .catch(err => console.error(err));
        }

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
                        if (badgeDesktop) badgeDesktop.innerText = data.cart_count;

                        const toast = document.getElementById('toast');
                        toast.classList.remove('hidden');
                        setTimeout(() => { toast.classList.add('hidden'); }, 2500);
                    }
                })
                .catch(err => console.error(err));
        }
    </script>

    <!-- Footer & AI Widget Include -->
    <?php include __DIR__ . '/footer.php'; ?>
    <?php include __DIR__ . '/ai-widget.php'; ?>

</body>
</html>