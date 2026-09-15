<?php
session_start();

if (!isset($_SESSION['last_order'])) {
    header("Location: index.php");
    exit;
}

$order = $_SESSION['last_order'];
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed — AURA & SCENT</title>

    <!-- Favicon Icon (Gold SVG) -->
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%23070708'/><text x='50%' y='68%' font-family='serif' font-size='65' font-weight='bold' fill='%23d4af37' text-anchor='middle'>A</text></svg>">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: { brand: { gold: '#d4af37', 'gold-light': '#f3e5ab' } },
                    fontFamily: { serif: ['Playfair Display', 'serif'], sans: ['Plus Jakarta Sans', 'sans-serif'] }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;0,900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { background-color: #070708; color: #fdfbf7; font-family: 'Plus Jakarta Sans', sans-serif; }
        .glass-card { background: rgba(18, 18, 20, 0.6); backdrop-filter: blur(25px); border: 1px solid rgba(255, 255, 255, 0.08); }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4">

    <div class="glass-card max-w-xl w-full p-8 sm:p-12 rounded-3xl text-center space-y-6">
        <div class="w-16 h-16 bg-brand-gold/10 border border-brand-gold/30 text-brand-gold rounded-full flex items-center justify-center mx-auto text-2xl font-bold">
            ✓
        </div>

        <h1 class="font-serif text-3xl sm:text-4xl text-white">Payment Confirmed</h1>
        <p class="text-xs text-neutral-400 uppercase tracking-widest font-light">Thank you for your order with AURA & SCENT</p>

        <div class="bg-white/5 rounded-2xl p-6 border border-white/5 space-y-4 text-left text-xs">
            <div class="flex justify-between border-b border-white/5 pb-3">
                <span class="text-neutral-400">Order Reference</span>
                <span class="font-mono text-brand-gold font-bold"><?= htmlspecialchars($order['order_id']) ?></span>
            </div>
            <div class="flex justify-between border-b border-white/5 pb-3">
                <span class="text-neutral-400">Payment Method</span>
                <span class="uppercase font-bold text-white"><?= htmlspecialchars($order['payment_method']) ?></span>
            </div>
            <div class="flex justify-between border-b border-white/5 pb-3">
                <span class="text-neutral-400">Shipping Address</span>
                <span class="text-white text-right font-light"><?= htmlspecialchars($order['address']) ?></span>
            </div>
            <div class="flex justify-between pt-2 text-sm font-serif">
                <span class="text-white">Total Amount Paid</span>
                <span class="text-brand-gold">$<?= number_format($order['total'], 2) ?></span>
            </div>
        </div>

        <div class="pt-4">
            <a href="index.php" class="inline-block w-full bg-brand-gold text-black py-4 rounded-full font-bold text-xs uppercase tracking-[0.2em] hover:bg-white transition-all">
                Back to Home Page
            </a>
        </div>
    </div>

</body>
</html>