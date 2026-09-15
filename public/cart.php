<?php
session_start();
require_once __DIR__ . '/config/db.php';

try {
    $db_products = $pdo->query("SELECT * FROM products")->fetchAll(PDO::FETCH_ASSOC);
    $products = [];
    foreach ($db_products as $prod) { $products[$prod['id']] = $prod; }
} catch (Exception $e) { $products = []; }

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

// إضافة منتج بالسلة (يدعم AJAX + العادي)
if (isset($_GET['add'])) {
    $is_ajax = isset($_GET['ajax']);

    if (!isset($_SESSION['user_id'])) {
        if ($is_ajax) {
            header('Content-Type: application/json');
            echo json_encode(['status' => 'unauthorized']);
            exit;
        } else {
            header("Location: auth.php");
            exit;
        }
    }

    $id = (int)$_GET['add'];
    if (isset($products[$id])) {
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity'] += 1;
        } else {
            $_SESSION['cart'][$id] = [
                'name' => $products[$id]['name'],
                'price' => $products[$id]['price'],
                'image' => $products[$id]['image'],
                'quantity' => 1
            ];
        }
    }

    $cart_count = array_sum(array_column($_SESSION['cart'], 'quantity'));

    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success', 'cart_count' => $cart_count]);
        exit;
    } else {
        $redirect_url = $_SERVER['HTTP_REFERER'] ?? 'shop.php';
        header("Location: " . $redirect_url);
        exit;
    }
}

// حذف منتج من السلة
if (isset($_GET['remove'])) {
    $id = (int)$_GET['remove'];
    unset($_SESSION['cart'][$id]);
    header("Location: cart.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    if (!empty($_SESSION['cart'])) {
        $_SESSION['last_order'] = [
            'order_id' => 'AS-' . rand(100000, 999999),
            'items' => $_SESSION['cart'],
            'payment_method' => $_POST['payment_method'] ?? 'visa',
            'phone' => $_POST['phone'] ?? '',
            'address' => $_POST['address'] ?? '',
            'total' => array_sum(array_map(function($i){ return $i['price'] * $i['quantity']; }, $_SESSION['cart']))
        ];
        $_SESSION['cart'] = [];
        header("Location: checkout_success.php");
        exit;
    }
}

$subtotal = 0;
foreach ($_SESSION['cart'] as $item) { $subtotal += $item['price'] * $item['quantity']; }
$cart_count = array_sum(array_column($_SESSION['cart'], 'quantity'));
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout & Payment — AURA & SCENT</title>

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
        body { background-color: #070708; color: #fdfbf7; font-family: 'Plus Jakarta Sans', sans-serif; overflow-x: hidden; }
        .glass-card { background: rgba(18, 18, 20, 0.6); backdrop-filter: blur(25px); border: 1px solid rgba(255, 255, 255, 0.08); }
        .payment-radio:checked + label { border-color: #d4af37; background: rgba(212, 175, 55, 0.12); box-shadow: 0 0 15px rgba(212, 175, 55, 0.2); }
    </style>
</head>
<body class="selection:bg-brand-gold selection:text-black">

    <header class="fixed top-0 left-0 w-full z-50 glass-card border-b-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-8 h-20 sm:h-24 flex items-center justify-between">
            <a href="index.php" class="font-serif text-lg sm:text-2xl font-bold tracking-[0.2em] sm:tracking-[0.25em] text-white">
                AURA <span class="text-brand-gold">&</span> SCENT
            </a>
            <a href="shop.php" class="text-xs font-semibold tracking-widest uppercase text-neutral-400 hover:text-white">← Return to Shop</a>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-8 pt-32 sm:pt-36 pb-20">
        <?php if (empty($_SESSION['cart'])): ?>
            <div class="glass-card text-center py-20 px-6 rounded-3xl max-w-xl mx-auto">
                <h1 class="font-serif text-2xl sm:text-3xl text-white mb-4">Your Shopping Bag is Empty</h1>
                <p class="text-neutral-400 text-xs uppercase tracking-widest mb-8 font-light">Explore our luxury collection to add items.</p>
                <a href="shop.php" class="inline-block bg-brand-gold text-black font-bold text-xs uppercase tracking-[0.2em] px-8 py-4 rounded-full hover:bg-white transition-all">
                    Explore Editions
                </a>
            </div>
        <?php else: ?>
            <h1 class="font-serif text-3xl sm:text-4xl text-white mb-8 sm:mb-12">Checkout & Payment</h1>
            
            <form action="cart.php" method="POST" class="grid grid-cols-1 lg:grid-cols-3 gap-8 sm:gap-12">
                <div class="lg:col-span-2 space-y-8">
                    
                    <div class="space-y-4">
                        <h2 class="text-xs font-bold uppercase tracking-widest text-brand-gold">1. Selected Products</h2>
                        <?php foreach ($_SESSION['cart'] as $id => $item): ?>
                            <div class="glass-card p-4 sm:p-5 rounded-2xl flex items-center justify-between gap-4">
                                <div class="flex items-center gap-4">
                                    <img src="<?= htmlspecialchars($item['image']) ?>" class="w-16 h-16 object-cover rounded-xl bg-neutral-900">
                                    <div>
                                        <h3 class="font-serif text-base text-white mb-1"><?= htmlspecialchars($item['name']) ?></h3>
                                        <p class="text-brand-gold text-xs font-serif">$<?= number_format($item['price'], 2) ?> × <?= $item['quantity'] ?></p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-6">
                                    <span class="font-bold text-sm text-white">$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                                    <a href="cart.php?remove=<?= $id ?>" class="text-red-400 hover:text-red-300 text-xs font-semibold uppercase tracking-widest">Remove</a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="glass-card p-6 sm:p-8 rounded-3xl space-y-4">
                        <h2 class="text-xs font-bold uppercase tracking-widest text-brand-gold">2. Shipping Address</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 mb-2">Full Name</label>
                                <input type="text" name="name" required value="<?= htmlspecialchars($_SESSION['user_name'] ?? '') ?>" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-brand-gold">
                            </div>
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 mb-2">Phone Number</label>
                                <input type="text" name="phone" required placeholder="01012345678" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-brand-gold">
                            </div>
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 mb-2">Delivery Address</label>
                            <input type="text" name="address" required placeholder="Street Name, Building / Villa, City" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-brand-gold">
                        </div>
                    </div>

                    <div class="glass-card p-6 sm:p-8 rounded-3xl space-y-6">
                        <h2 class="text-xs font-bold uppercase tracking-widest text-brand-gold">3. Select Payment Method</h2>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                            <div>
                                <input type="radio" id="pay_visa" name="payment_method" value="visa" class="hidden payment-radio" checked onclick="switchPayment('visa')">
                                <label for="pay_visa" class="glass-card p-4 rounded-2xl border border-white/10 flex flex-col items-center justify-center text-center cursor-pointer transition-all hover:border-brand-gold/50 h-full">
                                    <div class="flex items-center gap-1.5 mb-2">
                                        <span class="bg-blue-600 text-white font-black text-[9px] italic px-1.5 py-0.5 rounded tracking-tighter">VISA</span>
                                        <div class="flex -space-x-1">
                                            <span class="w-3.5 h-3.5 bg-red-500 rounded-full inline-block opacity-90"></span>
                                            <span class="w-3.5 h-3.5 bg-amber-400 rounded-full inline-block opacity-90"></span>
                                        </div>
                                    </div>
                                    <span class="text-xs font-bold text-white">Credit Card</span>
                                    <span class="text-[9px] text-neutral-400">Visa / Mastercard</span>
                                </label>
                            </div>

                            <div>
                                <input type="radio" id="pay_vodafone" name="payment_method" value="vodafone" class="hidden payment-radio" onclick="switchPayment('vodafone')">
                                <label for="pay_vodafone" class="glass-card p-4 rounded-2xl border border-white/10 flex flex-col items-center justify-center text-center cursor-pointer transition-all hover:border-brand-gold/50 h-full">
                                    <div class="w-6 h-6 bg-red-600 rounded-full flex items-center justify-center font-bold text-white text-[11px] mb-2">V</div>
                                    <span class="text-xs font-bold text-white">Vodafone Cash</span>
                                    <span class="text-[9px] text-neutral-400">Smart Wallet</span>
                                </label>
                            </div>

                            <div>
                                <input type="radio" id="pay_fawry" name="payment_method" value="fawry" class="hidden payment-radio" onclick="switchPayment('fawry')">
                                <label for="pay_fawry" class="glass-card p-4 rounded-2xl border border-white/10 flex flex-col items-center justify-center text-center cursor-pointer transition-all hover:border-brand-gold/50 h-full">
                                    <div class="bg-yellow-400 text-black px-2 py-0.5 rounded font-black text-[10px] tracking-tighter mb-2">FAWRY</div>
                                    <span class="text-xs font-bold text-white">Fawry</span>
                                    <span class="text-[9px] text-neutral-400">Pay at Store</span>
                                </label>
                            </div>

                            <div>
                                <input type="radio" id="pay_paypal" name="payment_method" value="paypal" class="hidden payment-radio" onclick="switchPayment('paypal')">
                                <label for="pay_paypal" class="glass-card p-4 rounded-2xl border border-white/10 flex flex-col items-center justify-center text-center cursor-pointer transition-all hover:border-brand-gold/50 h-full">
                                    <div class="font-black text-blue-400 text-sm italic tracking-tighter mb-2">Pay<span class="text-blue-200">Pal</span></div>
                                    <span class="text-xs font-bold text-white">PayPal</span>
                                    <span class="text-[9px] text-neutral-400">Global Account</span>
                                </label>
                            </div>

                            <div>
                                <input type="radio" id="pay_payoneer" name="payment_method" value="payoneer" class="hidden payment-radio" onclick="switchPayment('payoneer')">
                                <label for="pay_payoneer" class="glass-card p-4 rounded-2xl border border-white/10 flex flex-col items-center justify-center text-center cursor-pointer transition-all hover:border-brand-gold/50 h-full">
                                    <div class="flex items-center gap-1 mb-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-gradient-to-r from-red-500 via-yellow-500 to-green-500"></span>
                                        <span class="font-black text-white text-[11px] tracking-tight">Payoneer</span>
                                    </div>
                                    <span class="text-xs font-bold text-white">Payoneer</span>
                                    <span class="text-[9px] text-neutral-400">Direct Balance</span>
                                </label>
                            </div>

                            <div>
                                <input type="radio" id="pay_binance" name="payment_method" value="binance" class="hidden payment-radio" onclick="switchPayment('binance')">
                                <label for="pay_binance" class="glass-card p-4 rounded-2xl border border-white/10 flex flex-col items-center justify-center text-center cursor-pointer transition-all hover:border-brand-gold/50 h-full">
                                    <div class="flex items-center gap-1 text-yellow-400 font-bold text-[11px] mb-2"><span class="text-xs">❖</span> BINANCE</div>
                                    <span class="text-xs font-bold text-white">Binance Pay</span>
                                    <span class="text-[9px] text-neutral-400">Crypto (USDT/BTC)</span>
                                </label>
                            </div>
                        </div>

                        <div id="payment-fields" class="pt-4 border-t border-white/10">
                            <div id="field-visa" class="space-y-4">
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 mb-2">Card Number</label>
                                    <input type="text" placeholder="4000 1234 5678 9010" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-brand-gold">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 mb-2">Expiry Date</label>
                                        <input type="text" placeholder="MM/YY" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-brand-gold">
                                    </div>
                                    <div>
                                        <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 mb-2">CVV Security Code</label>
                                        <input type="password" placeholder="123" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-brand-gold">
                                    </div>
                                </div>
                            </div>

                            <div id="field-vodafone" class="hidden space-y-3">
                                <p class="text-xs text-neutral-300">Transfer total amount to Vodafone Wallet Number: <strong class="text-brand-gold font-mono">01099998877</strong></p>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 mb-2">Sender Wallet Number</label>
                                    <input type="text" placeholder="010XXXXXXXX" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-brand-gold">
                                </div>
                            </div>

                            <div id="field-fawry" class="hidden space-y-2 text-xs text-neutral-300">
                                <p>A Fawry reference code will be generated upon order completion. You can pay using this code at any Fawry retail point across Egypt.</p>
                            </div>

                            <div id="field-paypal" class="hidden space-y-2 text-xs text-neutral-300">
                                <p>You will be redirected to PayPal securely to complete your payment.</p>
                            </div>

                            <div id="field-payoneer" class="hidden space-y-3 text-xs text-neutral-300">
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 mb-2">Payoneer Email Address</label>
                                    <input type="email" placeholder="your-email@payoneer.com" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-brand-gold">
                                </div>
                            </div>

                            <div id="field-binance" class="hidden space-y-3 text-xs text-neutral-300">
                                <p>Binance Pay PayID: <strong class="text-yellow-400 font-mono">289384920</strong></p>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 mb-2">Binance Transaction ID (TxID)</label>
                                    <input type="text" placeholder="Enter TxID after transfer" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-brand-gold">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="glass-card p-6 sm:p-8 rounded-3xl h-fit space-y-6">
                    <h3 class="font-serif text-xl text-white border-b border-white/5 pb-4">Order Summary</h3>
                    
                    <div class="flex justify-between text-xs text-neutral-400">
                        <span>Items Subtotal</span>
                        <span class="text-white font-serif">$<?= number_format($subtotal, 2) ?></span>
                    </div>
                    <div class="flex justify-between text-xs text-neutral-400">
                        <span>Delivery</span>
                        <span class="text-brand-gold">Free</span>
                    </div>

                    <div class="flex justify-between text-base border-t border-white/5 pt-4 font-serif">
                        <span class="text-white">Total Amount</span>
                        <span class="text-brand-gold">$<?= number_format($subtotal, 2) ?></span>
                    </div>

                    <button type="submit" name="place_order" class="w-full bg-brand-gold text-black py-4 rounded-full font-bold text-xs uppercase tracking-[0.2em] hover:bg-white transition-all shadow-xl">
                        Confirm & Pay Now
                    </button>
                    
                    <div class="flex items-center justify-center gap-3 text-[10px] text-neutral-500 uppercase tracking-widest pt-2">
                        <span>🔒 256-Bit Encrypted Payment</span>
                    </div>
                </div>
            </form>
        <?php endif; ?>
    </main>

    <script>
        function switchPayment(type) {
            const fields = ['visa', 'vodafone', 'fawry', 'paypal', 'payoneer', 'binance'];
            fields.forEach(f => { document.getElementById('field-' + f).classList.add('hidden'); });
            document.getElementById('field-' + type).classList.remove('hidden');
        }
    </script>
</body>
</html>