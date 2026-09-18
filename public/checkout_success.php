<?php
session_start();

if (!isset($_SESSION['last_order'])) {
    header("Location: index.php");
    exit;
}

$order = $_SESSION['last_order'];
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmed — AURA & SCENT | Sylva Edition</title>

    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%230f1d13'/><text x='50%' y='68%' font-family='serif' font-size='65' font-weight='bold' fill='%23d4af37' text-anchor='middle'>A</text></svg>">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        sylva: {
                            base: '#0b130e',      /* أخضر زيتوني داكن */
                            card: '#121f17',      /* بطاقات سيلفا */
                            accent: '#2d4a36',    /* أخضر نباتي */
                            gold: '#d4af37',      /* ذهبي فاخر */
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
            background: rgba(18, 31, 23, 0.65);
            backdrop-filter: blur(25px);
            -webkit-backdrop-filter: blur(25px);
            border: 1px solid rgba(212, 175, 55, 0.18);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 selection:bg-sylva-gold selection:text-black antialiased">

    <div class="sylva-plate max-w-xl w-full p-8 sm:p-12 rounded-3xl text-center space-y-6 shadow-2xl relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-72 h-72 bg-sylva-gold/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="w-16 h-16 bg-sylva-card border border-sylva-gold/40 text-sylva-gold rounded-full flex items-center justify-center mx-auto text-2xl font-bold shadow-lg shadow-sylva-gold/10">
            ✓
        </div>

        <div>
            <span class="text-[10px] uppercase tracking-[0.25em] text-sylva-gold font-bold block mb-1">Botanical Acquisition Confirmed</span>
            <h1 class="font-serif text-3xl sm:text-4xl text-sylva-light font-light italic">Payment Confirmed</h1>
        </div>
        
        <p class="text-xs text-neutral-400 uppercase tracking-widest font-light">Thank you for your order with AURA & SCENT</p>

        <div class="bg-sylva-base/80 rounded-2xl p-6 border border-sylva-accent/40 space-y-4 text-left text-xs">
            <div class="flex justify-between border-b border-sylva-accent/30 pb-3">
                <span class="text-neutral-400">Order Reference</span>
                <span class="font-mono text-sylva-gold font-bold"><?= htmlspecialchars($order['order_id']) ?></span>
            </div>
            <div class="flex justify-between border-b border-sylva-accent/30 pb-3">
                <span class="text-neutral-400">Payment Method</span>
                <span class="uppercase font-bold text-white"><?= htmlspecialchars($order['payment_method']) ?></span>
            </div>
            <div class="flex justify-between border-b border-sylva-accent/30 pb-3">
                <span class="text-neutral-400">Shipping Address</span>
                <span class="text-white text-right font-light"><?= htmlspecialchars($order['address']) ?></span>
            </div>
            <div class="flex justify-between pt-2 text-base font-serif">
                <span class="text-white">Total Amount Paid</span>
                <span class="text-sylva-gold font-bold">$<?= number_format($order['total'], 2) ?></span>
            </div>
        </div>

        <div class="pt-4">
            <a href="index.php" class="inline-block w-full bg-sylva-gold text-black py-4 rounded-full font-extrabold text-xs uppercase tracking-[0.2em] hover:bg-white transition-all shadow-lg shadow-sylva-gold/20 cursor-pointer">
                Back to Home Page
            </a>
        </div>
    </div>

</body>
</html>