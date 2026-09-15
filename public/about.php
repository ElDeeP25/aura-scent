<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | AURA & SCENT</title>
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="favicon.png">
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
        .bg-animation {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            overflow: hidden; z-index: -1; pointer-events: none;
        }
        .orb {
            position: absolute;
            border-radius: 50%;
            filter: blur(120px);
            opacity: 0.15;
            animation: floatOrb 15s infinite alternate ease-in-out;
        }
        .orb-1 { width: 500px; height: 500px; background: #d4af37; top: -10%; left: -10%; }
        .orb-2 { width: 600px; height: 600px; background: #8a6f1d; bottom: -15%; right: -10%; animation-delay: -5s; }
        .orb-3 { width: 400px; height: 400px; background: #c5a059; top: 40%; left: 30%; animation-delay: -10s; }
        @keyframes floatOrb {
            0% { transform: translate(0, 0) scale(1); }
            100% { transform: translate(60px, 70px) scale(1.15); }
        }
    </style>
</head>
<body class="min-h-screen text-white relative selection:bg-gold selection:text-black">

    <!-- Background Animation Layer -->
    <div class="bg-animation">
        <div class="orb orb-1"></div>
        <div class="orb orb-2"></div>
        <div class="orb orb-3"></div>
    </div>

    <!-- Header -->
    <header class="fixed top-0 left-0 w-full z-50 bg-[#050505]/75 backdrop-blur-xl border-b border-gold/20 shadow-2xl">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="index.php" class="text-xl font-extrabold tracking-[0.25em] bg-gradient-to-r from-white via-neutral-200 to-gold-light bg-clip-text text-transparent">
                AURA & SCENT
            </a>
            <nav class="hidden md:flex items-center gap-8 text-xs font-bold tracking-[0.2em] uppercase text-neutral-300">
                <a href="index.php" class="hover:text-gold transition-colors">Home</a>
                <a href="shop.php" class="hover:text-gold transition-colors">Collection</a>
                <a href="about.php" class="text-gold hover:text-gold-light transition-colors">About Us</a>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="admin.php" class="hover:text-gold transition-colors">Dashboard</a>
                    <a href="logout.php" class="text-red-400 hover:text-red-300">Logout</a>
                <?php else: ?>
                    <a href="auth.php" class="hover:text-gold transition-colors">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-6 pt-36 pb-24 relative z-10">
        
        <!-- Hero Section inside About -->
        <div class="text-center max-w-3xl mx-auto mb-20">
            <span class="text-gold uppercase tracking-[0.3em] text-xs font-black block mb-3">Our Heritage & Vision</span>
            <h1 class="text-4xl md:text-6xl font-black uppercase tracking-wider mb-6 bg-gradient-to-r from-white via-neutral-100 to-gold-light bg-clip-text text-transparent">
                Crafting Timeless Aura
            </h1>
            <p class="text-neutral-400 text-sm md:text-base leading-relaxed">
                Founded with a deep passion for elite perfumery, <span class="text-white font-bold">AURA & SCENT</span> bridges the art of traditional fragrance composition with modern elegance. Every bottle tells a unique story of luxury, capturing raw emotions and unforgettable memories.
            </p>
        </div>

        <!-- Vision & Values Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-24">
            <div class="bg-[#0a0a0a]/80 backdrop-blur-xl border border-neutral-800 rounded-3xl p-8 hover:border-gold/40 transition-all shadow-xl">
                <span class="text-gold uppercase tracking-[0.2em] text-xs font-extrabold block mb-2">01 / Our Vision</span>
                <h3 class="text-2xl font-bold uppercase tracking-wide mb-4">Redefining Luxury</h3>
                <p class="text-neutral-400 text-xs md:text-sm leading-relaxed">
                    We aim to transcend ordinary boundaries by sourcing the rarest botanical extracts and blending them with precision. Our goal is to provide a signature scent for every individual seeking distinct sophistication.
                </p>
            </div>
            <div class="bg-[#0a0a0a]/80 backdrop-blur-xl border border-neutral-800 rounded-3xl p-8 hover:border-gold/40 transition-all shadow-xl">
                <span class="text-gold uppercase tracking-[0.2em] text-xs font-extrabold block mb-2">02 / Exceptional Quality</span>
                <h3 class="text-2xl font-bold uppercase tracking-wide mb-4">Uncompromising Standards</h3>
                <p class="text-neutral-400 text-xs md:text-sm leading-relaxed">
                    From meticulous formulation down to the hand-finished crystal bottles, quality is embedded in our DNA. We ensure long-lasting depth and rich aromatic layers in every masterpiece we release.
                </p>
            </div>
        </div>

        <!-- The Team Section -->
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="text-gold uppercase tracking-[0.3em] text-xs font-black block mb-3">The Masterminds</span>
            <h2 class="text-3xl md:text-4xl font-black uppercase tracking-wider mb-4">Meet Our Visionary Team</h2>
            <p class="text-neutral-400 text-xs md:text-sm tracking-wide">The dedicated minds and creators bringing AURA & SCENT to life.</p>
        </div>

        <!-- Team Grid (4 Members) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            
            <!-- Member 1 -->
            <div class="bg-[#0a0a0a]/80 backdrop-blur-xl border border-neutral-800 rounded-3xl p-6 text-center group hover:border-gold/40 transition-all shadow-xl">
                <div class="w-28 h-28 mx-auto rounded-full overflow-hidden bg-neutral-900 border-2 border-gold/30 mb-6 flex items-center justify-center text-gold font-black text-2xl group-hover:scale-105 transition-transform">
                    YE
                </div>
                <h3 class="font-bold text-lg text-white mb-1">Yousef Eldeep</h3>
                <span class="text-gold text-xs font-bold tracking-widest uppercase block mb-3">Project Lead</span>
                <p class="text-neutral-400 text-xs leading-relaxed">Driving the architectural vision and overall strategy of the platform.</p>
            </div>

            <!-- Member 2 -->
            <div class="bg-[#0a0a0a]/80 backdrop-blur-xl border border-neutral-800 rounded-3xl p-6 text-center group hover:border-gold/40 transition-all shadow-xl">
                <div class="w-28 h-28 mx-auto rounded-full overflow-hidden bg-neutral-900 border-2 border-gold/30 mb-6 flex items-center justify-center text-gold font-black text-2xl group-hover:scale-105 transition-transform">
                    KM
                </div>
                <h3 class="font-bold text-lg text-white mb-1">Kholoud Mohamed</h3>
                <span class="text-gold text-xs font-bold tracking-widest uppercase block mb-3">Core Developer</span>
                <p class="text-neutral-400 text-xs leading-relaxed">Specialized in backend logic, database integrity, and system performance.</p>
            </div>

            <!-- Member 3 -->
            <div class="bg-[#0a0a0a]/80 backdrop-blur-xl border border-neutral-800 rounded-3xl p-6 text-center group hover:border-gold/40 transition-all shadow-xl">
                <div class="w-28 h-28 mx-auto rounded-full overflow-hidden bg-neutral-900 border-2 border-gold/30 mb-6 flex items-center justify-center text-gold font-black text-2xl group-hover:scale-105 transition-transform">
                    NF
                </div>
                <h3 class="font-bold text-lg text-white mb-1">Nahla Fouad</h3>
                <span class="text-gold text-xs font-bold tracking-widest uppercase block mb-3">UI / UX Designer</span>
                <p class="text-neutral-400 text-xs leading-relaxed">Crafting the luxurious aesthetic and smooth user journey across the interface.</p>
            </div>

            <!-- Member 4 -->
            <div class="bg-[#0a0a0a]/80 backdrop-blur-xl border border-neutral-800 rounded-3xl p-6 text-center group hover:border-gold/40 transition-all shadow-xl">
                <div class="w-28 h-28 mx-auto rounded-full overflow-hidden bg-neutral-900 border-2 border-gold/30 mb-6 flex items-center justify-center text-gold font-black text-2xl group-hover:scale-105 transition-transform">
                    MH
                </div>
                <h3 class="font-bold text-lg text-white mb-1">Mariam Hafez</h3>
                <span class="text-gold text-xs font-bold tracking-widest uppercase block mb-3">Quality & Operations</span>
                <p class="text-neutral-400 text-xs leading-relaxed">Ensuring seamless deployment, content management, and testing workflows.</p>
            </div>

        </div>

    </main>

    <!-- Full Luxury Footer -->
    <footer class="border-t border-neutral-800/80 bg-[#060606]/90 backdrop-blur-xl pt-16 pb-12 relative z-10 text-neutral-400 text-xs">
        <div class="max-w-7xl mx-auto px-6 grid grid-cols-1 md:grid-cols-3 gap-12 mb-12">
            <!-- About Us Column -->
            <div>
                <h4 class="text-white font-extrabold text-sm uppercase tracking-[0.2em] mb-4">About Aura & Scent</h4>
                <p class="leading-relaxed text-neutral-400">
                    We curate the world's most exclusive and luxurious ingredients to bottle pure emotions and timeless elegance. Elevate your presence with our signature fragrances.
                </p>
            </div>
            <!-- Quick Links Column -->
            <div>
                <h4 class="text-white font-extrabold text-sm uppercase tracking-[0.2em] mb-4">Quick Links</h4>
                <ul class="space-y-2 uppercase tracking-wider font-medium">
                    <li><a href="index.php" class="hover:text-gold transition-colors">Home</a></li>
                    <li><a href="shop.php" class="hover:text-gold transition-colors">Collection</a></li>
                    <li><a href="about.php" class="hover:text-gold transition-colors">About Us</a></li>
                    <li><a href="admin.php" class="hover:text-gold transition-colors">Admin Dashboard</a></li>
                </ul>
            </div>
            <!-- Contact Info Column -->
            <div>
                <h4 class="text-white font-extrabold text-sm uppercase tracking-[0.2em] mb-4">Contact Us</h4>
                <ul class="space-y-3">
                    <li class="flex items-center gap-2">
                        <span class="text-gold font-bold">Email:</span> support@aurascent.com
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-gold font-bold">Phone:</span> +1 (555) 389-4922
                    </li>
                    <li class="flex items-center gap-2">
                        <span class="text-gold font-bold">Location:</span> 5th Avenue, Luxury District, NY
                    </li>
                </ul>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-6 pt-8 border-t border-neutral-800/80 text-center uppercase tracking-widest text-[10px] text-neutral-500">
            <p>&copy; <?= date('Y') ?> AURA & SCENT. All Rights Reserved.</p>
        </div>
    </footer>

</body>
</html>