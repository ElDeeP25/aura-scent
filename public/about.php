<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us — AURA & SCENT | Sylva Edition</title>
    
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%230f1d13'/><text x='50%' y='68%' font-family='serif' font-size='65' font-weight='bold' fill='%23d4af37' text-anchor='middle'>A</text></svg>">
    
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
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,opsz,wght@0,6..72,200..800;1,6..72,200..800&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <style>
        body { background-color: #0b130e; color: #e8ece9; font-family: 'Plus Jakarta Sans', sans-serif; overflow-x: hidden; }

        .sylva-plate {
            background: rgba(18, 31, 23, 0.65);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(212, 175, 55, 0.18);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4);
        }
    </style>
</head>
<body class="min-h-screen text-sylva-light relative selection:bg-sylva-gold selection:text-black antialiased">

    <!-- WebGL Animated Background -->
    <?php 
    $bg_file = __DIR__ . '/bg-animation.php';
    if (!file_exists($bg_file)) { $bg_file = __DIR__ . '/../bg-animation.php'; }
    if (file_exists($bg_file)) { include $bg_file; }
    ?>

    <!-- Floating Navigation Bar -->
    <?php include __DIR__ . '/header.php'; ?>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-6 pt-36 sm:pt-44 pb-24 relative z-10">
        
        <!-- Hero Section inside About -->
        <div class="text-center max-w-3xl mx-auto mb-20">
            <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full border border-sylva-gold/30 bg-sylva-card/60 backdrop-blur-md mb-4">
                <span class="text-[10px] uppercase tracking-[0.25em] text-sylva-gold font-medium">Our Heritage & Vision</span>
            </div>
            <h1 class="text-4xl sm:text-6xl font-serif font-light italic text-sylva-light tracking-tight mb-6">
                Crafting Timeless Aura
            </h1>
            <p class="text-neutral-300 text-sm sm:text-base leading-relaxed font-light">
                Founded with a deep passion for elite perfumery, <span class="text-sylva-gold font-semibold">AURA & SCENT</span> bridges the art of traditional botanical extraction with modern elegance. Every bottle tells a unique story of luxury, capturing raw organic emotions and unforgettable scents.
            </p>
        </div>

        <!-- Vision & Values Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-24">
            <div class="sylva-plate rounded-3xl p-8 sm:p-10 hover:border-sylva-gold/40 transition-all shadow-xl">
                <span class="text-sylva-gold uppercase tracking-[0.2em] text-xs font-extrabold block mb-2">01 / Our Vision</span>
                <h3 class="font-serif text-3xl font-light italic mb-4 text-white">Redefining Botanical Luxury</h3>
                <p class="text-neutral-400 text-xs sm:text-sm leading-relaxed font-light">
                    We aim to transcend ordinary boundaries by sourcing the rarest botanical extracts and blending them with precision. Our goal is to provide a signature scent for every individual seeking distinct sophistication.
                </p>
            </div>
            <div class="sylva-plate rounded-3xl p-8 sm:p-10 hover:border-sylva-gold/40 transition-all shadow-xl">
                <span class="text-sylva-gold uppercase tracking-[0.2em] text-xs font-extrabold block mb-2">02 / Exceptional Quality</span>
                <h3 class="font-serif text-3xl font-light italic mb-4 text-white">Uncompromising Standards</h3>
                <p class="text-neutral-400 text-xs sm:text-sm leading-relaxed font-light">
                    From meticulous formulation down to the hand-finished crystal bottles, quality is embedded in our DNA. We ensure long-lasting depth and rich aromatic layers in every masterpiece we release.
                </p>
            </div>
        </div>

        <!-- The Team Section -->
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="text-sylva-gold uppercase tracking-[0.3em] text-xs font-bold block mb-2">The Masterminds</span>
            <h2 class="font-serif text-3xl sm:text-5xl font-light italic text-sylva-light mb-4">Meet Our Visionary Team</h2>
            <p class="text-neutral-400 text-xs sm:text-sm font-light tracking-wide">The dedicated minds and creators bringing AURA & SCENT to life.</p>
        </div>

        <!-- Team Grid (5 Members) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl mx-auto">
            
            <!-- Member 1: Yousef -->
            <div class="sylva-plate rounded-3xl p-6 text-center group hover:border-sylva-gold/40 transition-all shadow-xl">
                <div class="w-24 h-24 mx-auto rounded-full overflow-hidden bg-sylva-card border-2 border-sylva-gold/40 mb-6 flex items-center justify-center text-sylva-gold font-serif italic text-3xl group-hover:scale-105 transition-transform shadow-lg shadow-sylva-gold/10">
                    YE
                </div>
                <h3 class="font-serif text-xl text-white mb-1">Yousef Eldeep</h3>
                <span class="text-sylva-gold text-[10px] font-bold tracking-[0.2em] uppercase block mb-3">Project Lead</span>
                <p class="text-neutral-400 text-xs font-light leading-relaxed">Driving the architectural vision and overall strategy of the platform.</p>
            </div>

            <!-- Member 2: Kholoud -->
            <div class="sylva-plate rounded-3xl p-6 text-center group hover:border-sylva-gold/40 transition-all shadow-xl">
                <div class="w-24 h-24 mx-auto rounded-full overflow-hidden bg-sylva-card border-2 border-sylva-gold/40 mb-6 flex items-center justify-center text-sylva-gold font-serif italic text-3xl group-hover:scale-105 transition-transform shadow-lg shadow-sylva-gold/10">
                    KM
                </div>
                <h3 class="font-serif text-xl text-white mb-1">Kholoud Said</h3>
                <span class="text-sylva-gold text-[10px] font-bold tracking-[0.2em] uppercase block mb-3">Core Developer</span>
                <p class="text-neutral-400 text-xs font-light leading-relaxed">Specialized in backend logic, database integrity, and system performance.</p>
            </div>

            <!-- Member 3: Nahla -->
            <div class="sylva-plate rounded-3xl p-6 text-center group hover:border-sylva-gold/40 transition-all shadow-xl">
                <div class="w-24 h-24 mx-auto rounded-full overflow-hidden bg-sylva-card border-2 border-sylva-gold/40 mb-6 flex items-center justify-center text-sylva-gold font-serif italic text-3xl group-hover:scale-105 transition-transform shadow-lg shadow-sylva-gold/10">
                    NF
                </div>
                <h3 class="font-serif text-xl text-white mb-1">Nahla Fouad</h3>
                <span class="text-sylva-gold text-[10px] font-bold tracking-[0.2em] uppercase block mb-3">UI / UX Designer</span>
                <p class="text-neutral-400 text-xs font-light leading-relaxed">Crafting the luxurious aesthetic and smooth user journey across the interface.</p>
            </div>

            <!-- Member 4: Mariam -->
            <div class="sylva-plate rounded-3xl p-6 text-center group hover:border-sylva-gold/40 transition-all shadow-xl">
                <div class="w-24 h-24 mx-auto rounded-full overflow-hidden bg-sylva-card border-2 border-sylva-gold/40 mb-6 flex items-center justify-center text-sylva-gold font-serif italic text-3xl group-hover:scale-105 transition-transform shadow-lg shadow-sylva-gold/10">
                    MH
                </div>
                <h3 class="font-serif text-xl text-white mb-1">Mariam Hafez</h3>
                <span class="text-sylva-gold text-[10px] font-bold tracking-[0.2em] uppercase block mb-3">Quality & Operations</span>
                <p class="text-neutral-400 text-xs font-light leading-relaxed">Ensuring seamless deployment, content management, and testing workflows.</p>
            </div>

            <!-- Member 5: Zainab -->
            <div class="sylva-plate rounded-3xl p-6 text-center group hover:border-sylva-gold/40 transition-all shadow-xl sm:col-span-2 lg:col-span-1">
                <div class="w-24 h-24 mx-auto rounded-full overflow-hidden bg-sylva-card border-2 border-sylva-gold/40 mb-6 flex items-center justify-center text-sylva-gold font-serif italic text-3xl group-hover:scale-105 transition-transform shadow-lg shadow-sylva-gold/10">
                    ZM
                </div>
                <h3 class="font-serif text-xl text-white mb-1">Zainab Mohamed</h3>
                <span class="text-sylva-gold text-[10px] font-bold tracking-[0.2em] uppercase block mb-3">Youssef's Fiancée</span>
                <p class="text-neutral-400 text-xs font-light leading-relaxed">Bringing endless joy, warmth, and love into Youssef's life every single day.</p>
            </div>

        </div>

    </main>

    <!-- Includes -->
    <?php include __DIR__ . '/footer.php'; ?>
    <?php include __DIR__ . '/ai-widget.php'; ?>
</body>
</html>