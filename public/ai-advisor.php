<?php
session_start();
require_once __DIR__ . '/config/db.php';
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AI Scent Advisor | AURA & SCENT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: { colors: { gold: { DEFAULT: '#d4af37', light: '#f3e5ab' } } }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
   
    <style>
        body { font-family: 'Plus Jakarta Sans', sans-serif; background-color: #050505; color: #ffffff; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #0a0a0a; }
        ::-webkit-scrollbar-thumb { background: #262626; border-radius: 3px; }
    </style>
</head>
<body class="bg-[#050505] text-white min-h-screen selection:bg-gold selection:text-black">

    <header class="fixed top-0 left-0 w-full z-50 bg-[#050505]/85 backdrop-blur-xl border-b border-gold/15">
        <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
            <a href="index.php" class="text-xl font-extrabold tracking-[0.25em] bg-gradient-to-r from-white via-neutral-200 to-gold-light bg-clip-text text-transparent">
                AURA & SCENT
            </a>
            <nav class="hidden md:flex items-center gap-8 text-xs font-bold tracking-[0.2em] uppercase text-neutral-400">
                <a href="index.php" class="hover:text-gold transition-colors">Home</a>
                <a href="shop.php" class="hover:text-gold transition-colors">Shop</a>
                <a href="ai-advisor.php" class="flex items-center gap-2 px-4 py-2 rounded-full border border-gold bg-gold/10 text-gold-light">✨ AI Advisor</a>
                <a href="cart.php" class="hover:text-gold transition-colors">Cart</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="logout.php" class="text-red-500 hover:text-red-400">Logout</a>
                <?php else: ?>
                    <a href="auth.php" class="hover:text-gold">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="pt-32 pb-16 px-4 max-w-4xl mx-auto">
        <div class="bg-[#0e0e0e] border border-gold/30 rounded-3xl p-6 md:p-10 shadow-2xl relative overflow-hidden">
            <div class="absolute -top-24 -right-24 w-72 h-72 bg-gold/5 rounded-full blur-3xl pointer-events-none"></div>

            <div class="text-center max-w-xl mx-auto mb-8">
                <div class="inline-flex p-3 rounded-2xl bg-gold/10 border border-gold/30 text-gold mb-4">
                    <svg class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09zM18.259 8.715L18 9.75l-.259-1.035a3.375 3.375 0 00-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 002.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 002.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 00-2.456 2.456z"/></svg>
                </div>
                <h1 class="text-3xl font-black uppercase tracking-wider mb-3 text-gold-light">AI Scent Matchmaker</h1>
                <p class="text-neutral-400 text-xs md:text-sm tracking-wide">Tell us your mood, greeting, or preferred notes, and our AI concierge will assist you.</p>
            </div>

            <div id="chatBox" class="bg-black/80 border border-neutral-800 rounded-2xl p-4 md:p-6 h-96 overflow-y-auto mb-6 flex flex-col gap-4">
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-gold/20 border border-gold/40 flex items-center justify-center text-gold text-xs font-bold shrink-0">AI</div>
                    <div class="bg-neutral-900 border border-neutral-800 p-4 rounded-2xl text-xs md:text-sm text-neutral-200 max-w-[85%] leading-relaxed">
                        Hello! I am your master perfumer AI. How can I help you find your signature fragrance today? You can say hello, ask how I am, or mention notes like oud, vanilla, or amber.
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <input type="text" id="userInput" placeholder="Type a message (e.g., hello, how are you, or I want vanilla)..." 
                    class="flex-1 bg-black border border-neutral-800 px-6 py-4 rounded-full text-xs md:text-sm text-white focus:border-gold outline-none transition-all">
                <button onclick="handleSendMessage()" 
                    class="bg-gold text-black px-8 py-4 rounded-full font-extrabold text-xs uppercase tracking-widest hover:bg-gold-light transition-all shrink-0">
                    Send
                </button>
            </div>
        </div>
    </main>

    <script>
        const chatBox = document.getElementById('chatBox');
        const userInput = document.getElementById('userInput');

        userInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') handleSendMessage();
        });

        function handleSendMessage() {
            const text = userInput.value.trim();
            if (!text) return;

            const userHTML = `
                <div class="flex items-start gap-3 justify-end">
                    <div class="bg-gold text-black p-4 rounded-2xl text-xs md:text-sm font-semibold max-w-[85%] leading-relaxed shadow-lg">
                        ${escapeHtml(text)}
                    </div>
                    <div class="w-8 h-8 rounded-full bg-neutral-800 border border-neutral-700 flex items-center justify-center text-neutral-300 text-xs font-bold shrink-0">YOU</div>
                </div>
            `;
            chatBox.insertAdjacentHTML('beforeend', userHTML);
            userInput.value = '';
            chatBox.scrollTop = chatBox.scrollHeight;

            setTimeout(() => {
                const q = text.toLowerCase();
                let botReply = "";

                // تحليل ذكي ومتكامل للنصوص والأسئلة والتحيات
                if (q.includes('hello') || q.includes('hi') || q.includes('hey') || q.includes('أهلاً') || q.includes('سلام') || q.includes('مرحبا')) {
                    botReply = "Hello! It's a pleasure to assist you. Are you looking for a specific fragrance note like warm vanilla, deep oud, or fresh florals today?";
                } 
                else if (q.includes('how are you') || q.includes('كيفك') || q.includes('اخبارك') || q.includes(' عامل ايه')) {
                    botReply = "I am operating at peak performance, fully immersed in the world of high-end perfumery! How can I curate a magnificent scent for you today?";
                } 
                else if (q.includes('oud') || q.includes('smoke') || q.includes('dark') || q.includes('wood') || q.includes('خشب') || q.includes('عود') || q.includes('مدخن')) {
                    botReply = "<b>Top Recommendation: Midnight Velvet Oud ($180.00)</b><br><br>The rich, dark notes of smoky oud combined with mysterious leather and dark rose match your exact desire for a powerful, commanding presence.";
                } 
                else if (q.includes('vanilla') || q.includes('amber') || q.includes('sweet') || q.includes('warm') || q.includes('فانيلا') || q.includes('عنبر') || q.includes('دافئ')) {
                    botReply = "<b>Top Recommendation: Royal Amber Elixir ($145.00)</b><br><br>An opulent masterpiece of warm amber, golden spices, and rich vanilla designed to wrap you in luxurious warmth and sophistication.";
                } 
                else if (q.includes('floral') || q.includes('fresh') || q.includes('jasmine') || q.includes('زهور') || q.includes('فواح') || q.includes('منعش')) {
                    botReply = "<b>Top Recommendation: Celestial Blossom ($120.00)</b><br><br>A luminous composition featuring white jasmine, sparkling bergamot, and delicate white musk, perfect for a bright, elegant impression.";
                } 
                else if (q.includes('tobacco') || q.includes('honey') || q.includes('تبغ')) {
                    botReply = "<b>Top Recommendation: Golden Tobacco Scent ($165.00)</b><br><br>Deep tobacco leaves laced with golden honey and warm woods, offering a bold and hypnotic signature trail.";
                } 
                else {
                    botReply = `That is an interesting perspective! Based on your input ("${escapeHtml(text)}"), I recommend checking our exclusive Shop collections, or you can ask me about specific notes like oud, amber, or vanilla to get a tailored recommendation.`;
                }

                const botHTML = `
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-gold/20 border border-gold/40 flex items-center justify-center text-gold text-xs font-bold shrink-0">AI</div>
                        <div class="bg-neutral-900 border border-neutral-800 p-4 rounded-2xl text-xs md:text-sm text-neutral-200 max-w-[85%] leading-relaxed space-y-2">
                            ${botReply}
                        </div>
                    </div>
                `;
                chatBox.insertAdjacentHTML('beforeend', botHTML);
                chatBox.scrollTop = chatBox.scrollHeight;
            }, 600);
        }

        function escapeHtml(string) {
            return string.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
        }
    </script>
     <script src="assets/js/ai-chat.js"></script>
</body>
</html>