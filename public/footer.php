<!-- Luxury Botanical Footer -->
<footer class="relative z-10 border-t border-sylva-accent/40 bg-sylva-base/80 backdrop-blur-xl pt-16 pb-12 px-6 mt-20">
    <div class="max-w-7xl mx-auto grid grid-cols-1 md:grid-cols-12 gap-12 pb-12 border-b border-sylva-accent/30">
        
        <!-- Brand Info -->
        <div class="md:col-span-4 space-y-4">
            <a href="index.php" class="font-serif italic text-2xl text-sylva-light tracking-wider flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-sylva-gold inline-block animate-pulse"></span>
                Aura <span class="text-sylva-gold font-normal">&</span> Scent
            </a>
            <p class="text-xs text-neutral-400 font-light leading-relaxed max-w-sm">
                Crafting luxury botanical extracts, organic perfumes, and rare fragrant elixirs rooted in nature's purest essence.
            </p>
            <div class="flex items-center gap-4 text-xs text-sylva-gold pt-2">
                <span class="px-3 py-1 rounded-full border border-sylva-gold/30 bg-sylva-card/50">🌿 100% Organic</span>
                <span class="px-3 py-1 rounded-full border border-sylva-gold/30 bg-sylva-card/50">✨ Cruelty Free</span>
            </div>
        </div>

        <!-- Quick Links -->
        <div class="md:col-span-2 space-y-3">
            <h4 class="font-serif text-sm text-sylva-gold italic">Navigation</h4>
            <ul class="space-y-2 text-xs text-neutral-300 font-light">
                <li><a href="index.php" class="hover:text-sylva-gold transition-colors">Home</a></li>
                <li><a href="shop.php" class="hover:text-sylva-gold transition-colors">Fragrance Shop</a></li>
                <li><a href="about.php" class="hover:text-sylva-gold transition-colors">About Our Atelier</a></li>
                <li><a href="cart.php" class="hover:text-sylva-gold transition-colors">Shopping Cart</a></li>
            </ul>
        </div>

        <!-- Customer Care -->
        <div class="md:col-span-3 space-y-3">
            <h4 class="font-serif text-sm text-sylva-gold italic">Botanical Care</h4>
            <ul class="space-y-2 text-xs text-neutral-300 font-light">
                <li><span>Shipping & Royal Delivery</span></li>
                <li><span>Organic Scent Guarantee</span></li>
                <li><span>Contact: support@aurascent.com</span></li>
                <li><span>Location: Cairo, Egypt</span></li>
            </ul>
        </div>

        <!-- Newsletter -->
        <div class="md:col-span-3 space-y-3">
            <h4 class="font-serif text-sm text-sylva-gold italic">Private Club</h4>
            <p class="text-xs text-neutral-400 font-light">Subscribe to receive private elixir drops & botanical scent guides.</p>
            <form onsubmit="event.preventDefault(); alert('Thank you for joining our private botanical list!');" class="flex gap-2 pt-1">
                <input type="email" placeholder="Enter your email" required class="w-full bg-sylva-card border border-sylva-accent/60 px-3.5 py-2 rounded-xl text-xs text-white focus:border-sylva-gold outline-none">
                <button type="submit" class="bg-sylva-gold text-black px-4 py-2 rounded-xl text-[10px] font-bold uppercase tracking-wider hover:bg-white transition-all">Join</button>
            </form>
        </div>

    </div>

    <!-- Copyright -->
    <div class="max-w-7xl mx-auto pt-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-neutral-400">
        <p class="font-serif italic text-neutral-300">AURA & SCENT — Sylva Living Green Edition</p>
        <p>&copy; <?= date('Y') ?> All Rights Reserved. Mastered with Botanical Elegance.</p>
    </div>
</footer>