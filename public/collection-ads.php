<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// جلب كافة الفيديوهات تلقائياً من الفولدر
$video_dir = __DIR__ . '/images/videos/';
$web_video_dir = 'images/videos/';
$videos = [];

if (is_dir($video_dir)) {
    $files = scandir($video_dir);
    foreach ($files as $file) {
        $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (in_array($ext, ['mp4', 'webm', 'ogg', 'mov'])) {
            $videos[] = $web_video_dir . $file;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Collection Ads | AURA & SCENT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        sylva: { base: '#0b130e', card: '#121f17', accent: '#2d4a36', gold: '#d4af37', light: '#e8ece9' }
                    },
                    fontFamily: { serif: ['Newsreader', 'serif'], sans: ['Plus Jakarta Sans', 'sans-serif'] }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Newsreader:ital,wght@0,300;1,300&family=Plus+Jakarta+Sans:wght@300;400;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #0b130e; color: #e8ece9; font-family: 'Plus Jakarta Sans', sans-serif; }
        .sylva-plate {
            background: rgba(18, 31, 23, 0.75);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(212, 175, 55, 0.25);
            box-shadow: 0 20px 40px rgba(0,0,0,0.6);
        }
    </style>
</head>
<body class="relative selection:bg-sylva-gold selection:text-black">

    <!-- Background Animation -->
    <?php 
    $bg_file = __DIR__ . '/bg-animation.php';
    if (file_exists($bg_file)) { include $bg_file; }
    ?>

    <!-- Header Include -->
    <?php include __DIR__ . '/header.php'; ?>

    <main class="pt-32 pb-24 px-4 sm:px-8 max-w-7xl mx-auto relative z-10">

        <!-- Page Header -->
        <section class="text-center max-w-3xl mx-auto mb-16">
            <span class="inline-block px-4 py-1.5 rounded-full border border-sylva-gold/30 bg-sylva-card/80 text-sylva-gold text-xs font-bold uppercase tracking-[0.2em] mb-4">
                🎬 Botanical Cinema
            </span>
            <h1 class="font-serif text-4xl sm:text-6xl font-light text-sylva-light mb-4">
                Collection <span class="italic text-sylva-gold">Campaign Ads</span>
            </h1>
            <p class="text-neutral-300 text-sm sm:text-base font-light leading-relaxed">
                Click on any campaign below to expand and watch the full uncropped cinematic experience.
            </p>
        </section>

        <!-- Video Ads Grid Gallery -->
        <section>
            <?php if (!empty($videos)): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                    <?php foreach ($videos as $index => $vid_path): ?>
                        <div onclick="openLightbox('<?= htmlspecialchars($vid_path) ?>')" class="sylva-plate rounded-3xl p-3 flex flex-col justify-between group hover:border-sylva-gold/80 transition-all duration-300 cursor-pointer shadow-xl relative overflow-hidden">
                            
                            <!-- Video Thumbnail Box -->
                            <div class="relative w-full aspect-[9/16] rounded-2xl overflow-hidden bg-black border border-sylva-accent/40 shadow-inner">
                                <video preload="metadata" class="w-full h-full object-cover rounded-2xl pointer-events-none group-hover:scale-105 transition-transform duration-500">
                                    <source src="<?= htmlspecialchars($vid_path) ?>#t=0.5" type="video/mp4">
                                </video>
                                
                                <!-- Play Icon Overlay -->
                                <div class="absolute inset-0 bg-black/40 group-hover:bg-black/20 transition-all flex items-center justify-center">
                                    <div class="w-12 h-12 rounded-full bg-sylva-gold/90 text-black flex items-center justify-center shadow-2xl transform group-hover:scale-110 transition-transform">
                                        <span class="text-lg ml-0.5">▶</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center justify-between px-2 pt-3 pb-1">
                                <div>
                                    <span class="text-[9px] font-bold uppercase tracking-[0.2em] text-sylva-gold block">Campaign #<?= sprintf('%02d', $index + 1) ?></span>
                                    <h3 class="font-serif italic text-sm text-sylva-light">AURA Film</h3>
                                </div>
                                <span class="text-[10px] bg-sylva-gold/10 border border-sylva-gold/30 text-sylva-gold px-2.5 py-0.5 rounded-full uppercase tracking-wider font-bold">
                                    Expand ⤢
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="sylva-plate p-12 rounded-3xl text-center text-neutral-400">
                    <p class="font-serif italic text-lg mb-2">No videos found in the library.</p>
                    <p class="text-xs">Please add your .mp4 files into <code class="text-sylva-gold">images/videos/</code> folder.</p>
                </div>
            <?php endif; ?>
        </section>

    </main>

    <!-- Fullscreen Video Lightbox Modal (Full Uncropped Display) -->
    <div id="videoLightbox" class="fixed inset-0 z-[99999] bg-black/95 backdrop-blur-2xl hidden flex flex-col items-center justify-center p-4 transition-all">
        
        <!-- Close Button -->
        <button onclick="closeLightbox()" class="absolute top-6 right-6 bg-sylva-gold text-black hover:bg-white font-extrabold text-xs px-6 py-3 rounded-full shadow-2xl transition-all cursor-pointer z-50">
            ✕ Close Video
        </button>

        <!-- Uncropped Video Container -->
        <div class="relative max-w-5xl max-h-[85vh] w-full flex items-center justify-center">
            <video id="lightboxPlayer" controls autoplay playsinline class="max-w-full max-h-[85vh] object-contain rounded-2xl border border-sylva-gold/40 shadow-2xl bg-black">
                <source id="lightboxSource" src="" type="video/mp4">
            </video>
        </div>
    </div>

    <script>
        function openLightbox(videoSrc) {
            const modal = document.getElementById('videoLightbox');
            const player = document.getElementById('lightboxPlayer');
            const source = document.getElementById('lightboxSource');

            source.src = videoSrc;
            player.load();
            modal.classList.remove('hidden');
            player.play().catch(e => console.log('Autoplay error:', e));
        }

        function closeLightbox() {
            const modal = document.getElementById('videoLightbox');
            const player = document.getElementById('lightboxPlayer');

            player.pause();
            modal.classList.add('hidden');
        }
    </script>

    <!-- Footer & AI Widget -->
    <?php include __DIR__ . '/footer.php'; ?>
    <?php include __DIR__ . '/ai-widget.php'; ?>

</body>
</html>