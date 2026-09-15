document.addEventListener("DOMContentLoaded", function () {
    const chatBox = document.getElementById('chatBox');
    const userInput = document.getElementById('userInput');

    if (!userInput) return;

    userInput.addEventListener('keypress', function (e) {
        if (e.key === 'Enter') handleSendMessage();
    });

    window.handleSendMessage = function() {
        const text = userInput.value.trim();
        if (!text) return;

        const userHTML = `
            <div class="flex items-start gap-3 justify-end">
                <div class="bg-[#d4af37] text-black p-4 rounded-2xl text-xs md:text-sm font-semibold max-w-[85%] leading-relaxed shadow-lg">
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

            if (q.includes('hello') || q.includes('hi') || q.includes('أهلاً') || q.includes('مرحبا')) {
                botReply = "Hello! It's a pleasure to assist you. Are you looking for a specific fragrance note like warm vanilla, deep oud, or fresh florals today?";
            } 
            else if (q.includes('how are you') || q.includes('كيفك') || q.includes('اخبارك')) {
                botReply = "I am operating at peak performance, fully immersed in the world of high-end perfumery! How can I curate a magnificent scent for you today?";
            } 
            else if (q.includes('oud') || q.includes('smoke') || q.includes('عود') || q.includes('خشب')) {
                botReply = "<b>Top Recommendation: Midnight Velvet Oud ($180.00)</b><br><br>The rich, dark notes of smoky oud combined with mysterious leather and dark rose match your exact desire for a powerful, commanding presence.";
            } 
            else if (q.includes('vanilla') || q.includes('amber') || q.includes('فانيلا') || q.includes('عنبر')) {
                botReply = "<b>Top Recommendation: Royal Amber Elixir ($145.00)</b><br><br>An opulent masterpiece of warm amber, golden spices, and rich vanilla designed to wrap you in luxurious warmth and sophistication.";
            } 
            else if (q.includes('floral') || q.includes('fresh') || q.includes('jasmine') || q.includes('زهور') || q.includes('منعش')) {
                botReply = "<b>Top Recommendation: Celestial Blossom ($120.00)</b><br><br>A luminous composition featuring white jasmine, sparkling bergamot, and delicate white musk, perfect for a bright, elegant impression.";
            } 
            else {
                botReply = `That is an interesting perspective! Based on your input ("${escapeHtml(text)}"), I recommend checking our exclusive Shop collections, or you can ask me about specific notes like oud, amber, or vanilla.`;
            }

            const botHTML = `
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-[#d4af37]/20 border border-[#d4af37]/40 flex items-center justify-center text-[#d4af37] text-xs font-bold shrink-0">AI</div>
                    <div class="bg-neutral-900 border border-neutral-800 p-4 rounded-2xl text-xs md:text-sm text-neutral-200 max-w-[85%] leading-relaxed space-y-2">
                        ${botReply}
                    </div>
                </div>
            `;
            chatBox.insertAdjacentHTML('beforeend', botHTML);
            chatBox.scrollTop = chatBox.scrollHeight;
        }, 600);
    };

    function escapeHtml(string) {
        return string.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
    }
});