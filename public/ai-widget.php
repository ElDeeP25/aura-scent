<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$user_name = $_SESSION['user_name'] ?? $_SESSION['name'] ?? '';

$db_path = __DIR__ . '/config/db.php';
if (!file_exists($db_path)) { $db_path = __DIR__ . '/../config/db.php'; }
if (file_exists($db_path)) { require_once $db_path; }

// Fetch dynamic products context from Database
$dynamic_products = [];
if (isset($pdo)) {
    try {
        $stmt = $pdo->query("SELECT name, price, old_price, category, season, description FROM products ORDER BY price DESC");
        $dynamic_products = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $dynamic_products = [];
    }
}
?>

<!-- Floating AI Advisor Button -->
<button type="button" onclick="openAIBotWindow()" class="fixed bottom-6 right-6 z-[9999] sylva-plate bg-sylva-card/90 border border-sylva-gold/40 text-sylva-gold px-5 py-3 rounded-full font-bold text-xs uppercase tracking-widest flex items-center gap-2 shadow-2xl hover:bg-sylva-gold hover:text-black transition-all transform hover:scale-105 cursor-pointer">
    <span class="w-2.5 h-2.5 rounded-full bg-sylva-gold animate-ping"></span>
    <span>✨ AI Concierge</span>
</button>

<!-- AI Chat Widget Window -->
<div id="ai-chat-widget" class="fixed bottom-24 right-6 z-[9999] w-[92%] sm:w-[420px] hidden sylva-plate rounded-3xl p-5 shadow-2xl border border-sylva-gold/30 backdrop-blur-2xl">
    <!-- Header -->
    <div class="flex items-center justify-between pb-3 border-b border-sylva-accent/40 mb-3">
        <div class="flex items-center gap-2.5">
            <div class="relative">
                <span class="w-3 h-3 rounded-full bg-sylva-gold block"></span>
                <span class="w-3 h-3 rounded-full bg-sylva-gold absolute top-0 left-0 animate-ping"></span>
            </div>
            <div>
                <h4 class="font-serif italic text-base text-sylva-light leading-none">Aura & Scent AI Concierge</h4>
                <span class="text-[9px] text-sylva-gold/80 font-mono tracking-wider uppercase">Live Catalog Knowledge</span>
            </div>
        </div>
        <button type="button" onclick="openAIBotWindow()" class="text-neutral-400 hover:text-white text-xl font-bold px-2 cursor-pointer transition-colors">&times;</button>
    </div>

    <!-- Chat Messages Box -->
    <div id="widgetChatBox" class="h-80 overflow-y-auto space-y-3 pr-1 text-xs scroll-smooth">
        <div class="flex items-start gap-2">
            <div class="w-7 h-7 rounded-full bg-sylva-card border border-sylva-gold/40 flex items-center justify-center text-sylva-gold text-[10px] font-serif italic shrink-0 shadow-md">AI</div>
            <div class="bg-sylva-card/90 border border-sylva-accent/30 p-3.5 rounded-2xl text-neutral-200 leading-relaxed font-light shadow-md">
                Welcome <?= !empty($user_name) ? '<b>' . htmlspecialchars($user_name) . '</b>' : '' ?>! ✨ Ask me about our highest-priced perfumes, best recommendations, or delivery details.
            </div>
        </div>
    </div>

    <!-- Quick Suggestion Chips -->
    <div class="mt-3 pt-2 overflow-x-auto flex gap-1.5 pb-1 text-[10px]">
        <button type="button" onclick="sendBotQuickQuery('Which one has the highest price?')" class="bg-sylva-card/80 border border-sylva-gold/20 text-sylva-gold px-2.5 py-1 rounded-full whitespace-nowrap hover:bg-sylva-gold hover:text-black transition-all cursor-pointer">💎 Most Expensive</button>
        <button type="button" onclick="sendBotQuickQuery('What is your best perfume?')" class="bg-sylva-card/80 border border-sylva-gold/20 text-sylva-gold px-2.5 py-1 rounded-full whitespace-nowrap hover:bg-sylva-gold hover:text-black transition-all cursor-pointer">✨ Best Seller</button>
        <button type="button" onclick="sendBotQuickQuery('Show cheapest options')" class="bg-sylva-card/80 border border-sylva-gold/20 text-sylva-gold px-2.5 py-1 rounded-full whitespace-nowrap hover:bg-sylva-gold hover:text-black transition-all cursor-pointer">🏷️ Best Value</button>
    </div>

    <!-- Input Field -->
    <div class="mt-2 pt-2 border-t border-sylva-accent/30 flex gap-2">
        <input type="text" id="widgetUserInput" placeholder="Ask about prices, notes, or delivery..." 
            class="flex-1 bg-sylva-base/90 border border-sylva-accent/50 px-4 py-2.5 rounded-full text-xs text-white focus:border-sylva-gold outline-none transition-colors">
        <button type="button" onclick="handleBotSend()" class="bg-sylva-gold text-black px-4 py-2.5 rounded-full font-extrabold text-[10px] uppercase tracking-wider hover:bg-white transition-all cursor-pointer shadow-md">
            Send
        </button>
    </div>
</div>

<script>
    let activeUserName = "<?= htmlspecialchars($user_name) ?>";
    const catalogProducts = <?= json_encode($dynamic_products) ?>;

    function openAIBotWindow() {
        const widget = document.getElementById('ai-chat-widget');
        if (widget) {
            widget.classList.toggle('hidden');
        }
    }

    function sendBotQuickQuery(queryText) {
        const input = document.getElementById('widgetUserInput');
        if (input) {
            input.value = queryText;
            handleBotSend();
        }
    }

    document.getElementById('widgetUserInput')?.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            handleBotSend();
        }
    });

    function handleBotSend() {
        const input = document.getElementById('widgetUserInput');
        const chatBox = document.getElementById('widgetChatBox');
        if (!input || !chatBox) return;

        const text = input.value.trim();
        if (!text) return;

        // User Message
        const userHTML = `
            <div class="flex items-start gap-2 justify-end">
                <div class="bg-sylva-gold text-black p-3 rounded-2xl text-xs font-bold leading-relaxed max-w-[82%] shadow-md">
                    ${escapeBotHtml(text)}
                </div>
            </div>
        `;
        chatBox.insertAdjacentHTML('beforeend', userHTML);
        input.value = '';
        chatBox.scrollTop = chatBox.scrollHeight;

        // Thinking Indicator
        const thinkingId = 'thinking_' + Date.now();
        const thinkingHTML = `
            <div id="${thinkingId}" class="flex items-start gap-2">
                <div class="w-7 h-7 rounded-full bg-sylva-card border border-sylva-gold/40 flex items-center justify-center text-sylva-gold text-[10px] font-serif italic shrink-0">AI</div>
                <div class="bg-sylva-card/90 border border-sylva-accent/30 p-3 rounded-2xl text-neutral-400 italic text-xs animate-pulse">
                    Analyzing catalog database...
                </div>
            </div>
        `;
        chatBox.insertAdjacentHTML('beforeend', thinkingHTML);
        chatBox.scrollTop = chatBox.scrollHeight;

        // Process Intelligence Response
        setTimeout(() => {
            const thinkingEl = document.getElementById(thinkingId);
            if (thinkingEl) thinkingEl.remove();

            const reply = processSmartCatalogQuery(text);

            const aiHTML = `
                <div class="flex items-start gap-2">
                    <div class="w-7 h-7 rounded-full bg-sylva-card border border-sylva-gold/40 flex items-center justify-center text-sylva-gold text-[10px] font-serif italic shrink-0 shadow-md">AI</div>
                    <div class="bg-sylva-card/90 border border-sylva-accent/30 p-3.5 rounded-2xl text-neutral-200 leading-relaxed font-light max-w-[85%] shadow-md">
                        ${reply}
                    </div>
                </div>
            `;
            chatBox.insertAdjacentHTML('beforeend', aiHTML);
            chatBox.scrollTop = chatBox.scrollHeight;
        }, 300);
    }

    function processSmartCatalogQuery(rawInput) {
        const q = rawInput.toLowerCase().trim();

        // 1. Highest Price Query
        if (q.includes('high price') || q.includes('highest') || q.includes('expensive') || q.includes('most price') || q.includes('top price')) {
            if (catalogProducts.length > 0) {
                let sorted = [...catalogProducts].sort((a, b) => parseFloat(b.price) - parseFloat(a.price));
                let top = sorted[0];
                return `💎 <b>Our Most Exclusive & Highest-Priced Fragrance:</b><br><br>
                    • <b>${top.name}</b> — <span class="text-sylva-gold font-bold">$${parseFloat(top.price).toFixed(2)}</span><br>
                    <i>${top.description || 'Masterpiece botanical extraction.'}</i><br><br>
                    You can find it available in our <a href="shop.php" class="text-sylva-gold underline">Shop</a> collection!`;
            }
        }

        // 2. Lowest Price Query
        if (q.includes('low price') || q.includes('cheapest') || q.includes('best price') || q.includes('budget') || q.includes('least price')) {
            if (catalogProducts.length > 0) {
                let sorted = [...catalogProducts].sort((a, b) => parseFloat(a.price) - parseFloat(b.price));
                let lowest = sorted[0];
                return `🏷️ <b>Our Most Affordable Selection:</b><br><br>
                    • <b>${lowest.name}</b> — <span class="text-sylva-gold font-bold">$${parseFloat(lowest.price).toFixed(2)}</span><br>
                    <i>${lowest.description || 'Great entry extraction.'}</i>`;
            }
        }

        // 3. Best Fragrance / Recommendation
        if (q.includes('best') || q.includes('recommend') || q.includes('top') || q.includes('popular')) {
            if (catalogProducts.length > 0) {
                let featured = catalogProducts.slice(0, 2);
                let listStr = featured.map(p => `• <b>${p.name}</b> ($${p.price})`).join('<br>');
                return `✨ <b>Our Highly Recommended Fragrances:</b><br><br>${listStr}<br><br>Both offer magnificent projection and long-lasting scent notes!`;
            }
        }

        // 4. Summer Query
        if (q.includes('summer') || q.includes('fresh') || q.includes('citrus')) {
            let items = catalogProducts.filter(p => (p.season || '').toLowerCase() === 'summer');
            if (items.length > 0) {
                let listStr = items.map(p => `• <b>${p.name}</b> ($${p.price})`).join('<br>');
                return `☀️ <b>Summer Fresh Selections:</b><br><br>${listStr}`;
            }
        }

        // 5. Winter Query
        if (q.includes('winter') || q.includes('oud') || q.includes('amber')) {
            let items = catalogProducts.filter(p => (p.season || '').toLowerCase() === 'winter');
            if (items.length > 0) {
                let listStr = items.map(p => `• <b>${p.name}</b> ($${p.price})`).join('<br>');
                return `❄️ <b>Winter & Evening Selections:</b><br><br>${listStr}`;
            }
        }

        // 6. Shipping & Delivery
        if (q.includes('shipping') || q.includes('delivery') || q.includes('ship')) {
            return `🚚 <b>Shipping Details:</b><br>
                • <b>Delivery Time:</b> 2 to 4 business days.<br>
                • <b>Free Shipping:</b> On orders over $150.`;
        }

        // Default Fallback Response
        if (catalogProducts.length > 0) {
            let sample = catalogProducts[0];
            return `I am here to guide you! Explore our featured fragrance <b>${sample.name}</b> ($${sample.price}) or ask me specifically about prices, best sellers, or shipping!`;
        }

        return `Welcome! Explore our artisanal collections directly on the <a href="shop.php" class="text-sylva-gold underline">Shop</a> page!`;
    }

    function escapeBotHtml(string) {
        return string.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
    }
</script>