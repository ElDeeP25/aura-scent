<?php
session_start();
require_once __DIR__ . '/config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch cart items
$stmt = $pdo->prepare("SELECT c.*, p.name, p.price, p.image FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$stmt->execute([$user_id]);
$cart_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($cart_items)) {
    header('Location: cart.php');
    exit;
}

$subtotal = 0;
foreach ($cart_items as $item) {
    $subtotal += $item['price'] * $item['quantity'];
}
$shipping = 50.00;

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $address = trim($_POST['address'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $payment_method = $_POST['payment_method'] ?? 'cod';

    $gateway_fee = ($payment_method === 'card') ? ($subtotal * 0.025) : 0.00;
    $total = $subtotal + $shipping + $gateway_fee;

    if (empty($address) || empty($phone)) {
        $error_msg = 'Please enter your shipping address and phone number.';
    } else {
        if ($payment_method === 'card') {
            $card_number = trim($_POST['card_number'] ?? '');
            $card_expiry = trim($_POST['card_expiry'] ?? '');
            $card_cvv = trim($_POST['card_cvv'] ?? '');
            if (empty($card_number) || empty($card_expiry) || empty($card_cvv)) {
                $error_msg = 'Please complete your credit card details.';
            }
        } elseif (in_array($payment_method, ['vodafone_cash', 'orange_cash', 'etisalat_cash', 'we_cash'])) {
            $wallet_number = trim($_POST['wallet_number'] ?? '');
            if (empty($wallet_number)) {
                $error_msg = 'Please enter your mobile wallet number.';
            }
        } elseif ($payment_method === 'instapay') {
            $instapay_username = trim($_POST['instapay_username'] ?? '');
            if (empty($instapay_username)) {
                $error_msg = 'Please enter your InstaPay username.';
            }
        }

        if (empty($error_msg)) {
            // Clear cart after successful order placement
            $clear_stmt = $pdo->prepare("DELETE FROM cart WHERE user_id = ?");
            $clear_stmt->execute([$user_id]);

            $success_msg = 'Your order has been placed successfully! Thank you for shopping with AURA & SCENT.';
        }
    }
} else {
    $total = $subtotal + $shipping;
}
?>
<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Checkout — AURA & SCENT | Sylva Edition</title>
    
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

        .payment-option.active {
            border-color: #d4af37;
            background: rgba(45, 74, 54, 0.6);
            box-shadow: 0 0 15px rgba(212, 175, 55, 0.2);
        }
    </style>
</head>
<body class="selection:bg-sylva-gold selection:text-black antialiased min-h-screen">

    <!-- Header Navigation -->
    <?php include __DIR__ . '/header.php'; ?>

    <main class="pt-36 sm:pt-44 pb-20 max-w-7xl mx-auto px-4 sm:px-8">

        <?php if (!empty($success_msg)): ?>
            <div class="sylva-plate text-center py-16 px-6 rounded-3xl max-w-xl mx-auto shadow-2xl">
                <div class="w-16 h-16 bg-sylva-card border border-sylva-gold/40 text-sylva-gold rounded-full flex items-center justify-center mx-auto text-2xl font-bold mb-6">✓</div>
                <h3 class="font-serif text-3xl text-sylva-light font-light italic mb-4">Order Placed Successfully</h3>
                <p class="text-neutral-400 text-xs leading-relaxed mb-8 font-light"><?= htmlspecialchars($success_msg) ?></p>
                <a href="index.php" class="inline-block bg-sylva-gold text-black font-extrabold text-xs uppercase tracking-[0.2em] px-10 py-4 rounded-full hover:bg-white transition-all shadow-lg shadow-sylva-gold/20">
                    Return to Store
                </a>
            </div>
        <?php else: ?>
            <div class="mb-10 border-b border-sylva-accent/30 pb-6">
                <span class="text-xs uppercase tracking-[0.3em] text-sylva-gold font-bold block mb-2">Final Step</span>
                <h1 class="font-serif text-3xl sm:text-5xl font-light italic text-sylva-light">Shipping & Payment Details</h1>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 sm:gap-12">
                
                <!-- Form Box -->
                <div class="lg:col-span-7 sylva-plate p-6 sm:p-10 rounded-3xl shadow-xl">
                    <?php if (!empty($error_msg)): ?>
                        <div class="mb-6 p-4 rounded-2xl bg-red-950/40 border border-red-500/30 text-red-400 text-xs font-bold uppercase tracking-wider">
                            ⚠️ <?= htmlspecialchars($error_msg) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" id="checkoutForm" class="space-y-6">
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 mb-2">Delivery Address</label>
                            <textarea name="address" rows="3" placeholder="Street name, building number, apartment, city" required 
                                class="w-full bg-sylva-base/80 border border-sylva-accent/50 rounded-xl p-4 text-xs text-white focus:outline-none focus:border-sylva-gold transition-colors resize-none"><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 mb-2">Phone Number</label>
                            <input type="text" name="phone" placeholder="010xxxxxxxx" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required
                                class="w-full bg-sylva-base/80 border border-sylva-accent/50 rounded-xl px-4 py-3.5 text-xs text-white focus:outline-none focus:border-sylva-gold transition-colors">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 mb-3">Payment Method</label>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                <label class="payment-option active sylva-plate p-3.5 rounded-xl border border-sylva-accent/40 flex items-center gap-3 cursor-pointer text-xs font-bold text-white transition-all" id="opt_cod" onclick="selectPayment('cod')">
                                    <input type="radio" name="payment_method" value="cod" checked class="accent-sylva-gold"> Cash on Delivery
                                </label>
                                <label class="payment-option sylva-plate p-3.5 rounded-xl border border-sylva-accent/40 flex items-center gap-3 cursor-pointer text-xs font-bold text-white transition-all" id="opt_card" onclick="selectPayment('card')">
                                    <input type="radio" name="payment_method" value="card" class="accent-sylva-gold"> Credit / Debit Card
                                </label>
                                <label class="payment-option sylva-plate p-3.5 rounded-xl border border-sylva-accent/40 flex items-center gap-3 cursor-pointer text-xs font-bold text-white transition-all" id="opt_instapay" onclick="selectPayment('instapay')">
                                    <input type="radio" name="payment_method" value="instapay" class="accent-sylva-gold"> InstaPay
                                </label>
                                <label class="payment-option sylva-plate p-3.5 rounded-xl border border-sylva-accent/40 flex items-center gap-3 cursor-pointer text-xs font-bold text-white transition-all" id="opt_vodafone" onclick="selectPayment('vodafone_cash')">
                                    <input type="radio" name="payment_method" value="vodafone_cash" class="accent-sylva-gold"> Vodafone Cash
                                </label>
                                <label class="payment-option sylva-plate p-3.5 rounded-xl border border-sylva-accent/40 flex items-center gap-3 cursor-pointer text-xs font-bold text-white transition-all" id="opt_etisalat" onclick="selectPayment('etisalat_cash')">
                                    <input type="radio" name="payment_method" value="etisalat_cash" class="accent-sylva-gold"> Etisalat Cash
                                </label>
                                <label class="payment-option sylva-plate p-3.5 rounded-xl border border-sylva-accent/40 flex items-center gap-3 cursor-pointer text-xs font-bold text-white transition-all" id="opt_orange" onclick="selectPayment('orange_cash')">
                                    <input type="radio" name="payment_method" value="orange_cash" class="accent-sylva-gold"> Orange Cash
                                </label>
                                <label class="payment-option sylva-plate p-3.5 rounded-xl border border-sylva-accent/40 flex items-center gap-3 cursor-pointer text-xs font-bold text-white transition-all" id="opt_we" onclick="selectPayment('we_cash')">
                                    <input type="radio" name="payment_method" value="we_cash" class="accent-sylva-gold"> WE Pay
                                </label>
                            </div>
                        </div>

                        <!-- Credit Card Box -->
                        <div class="sylva-plate p-5 rounded-2xl hidden space-y-4" id="box_card">
                            <div>
                                <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 mb-2">Card Number</label>
                                <input type="text" name="card_number" placeholder="4111 2222 3333 4444" maxlength="19" class="w-full bg-sylva-base/80 border border-sylva-accent/50 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-sylva-gold">
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 mb-2">Expiry Date</label>
                                    <input type="text" name="card_expiry" placeholder="MM/YY" maxlength="5" class="w-full bg-sylva-base/80 border border-sylva-accent/50 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-sylva-gold">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 mb-2">CVV Code</label>
                                    <input type="password" name="card_cvv" placeholder="123" maxlength="4" class="w-full bg-sylva-base/80 border border-sylva-accent/50 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-sylva-gold">
                                </div>
                            </div>
                        </div>

                        <!-- InstaPay Box -->
                        <div class="sylva-plate p-5 rounded-2xl hidden" id="box_instapay">
                            <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 mb-2">InstaPay Username (IPN)</label>
                            <input type="text" name="instapay_username" placeholder="username@instapay" class="w-full bg-sylva-base/80 border border-sylva-accent/50 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-sylva-gold">
                            <small class="text-sylva-gold text-[10px] block mt-2 font-mono">Transfer total amount to: <strong>aurascent@instapay</strong></small>
                        </div>

                        <!-- Mobile Wallets Box -->
                        <div class="sylva-plate p-5 rounded-2xl hidden" id="box_wallet">
                            <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 mb-2">Wallet Mobile Number</label>
                            <input type="text" name="wallet_number" placeholder="01xxxxxxxx" maxlength="11" class="w-full bg-sylva-base/80 border border-sylva-accent/50 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-sylva-gold">
                            <small class="text-sylva-gold text-[10px] block mt-2 font-mono">Transfer to store wallet: <strong>01012345678</strong></small>
                        </div>

                        <button type="submit" class="w-full bg-sylva-gold text-black py-4 rounded-full font-extrabold text-xs uppercase tracking-[0.2em] hover:bg-white transition-all shadow-lg shadow-sylva-gold/20 cursor-pointer" id="submitBtn">
                            Place Order ($<?= number_format($total, 2) ?>)
                        </button>
                    </form>
                </div>

                <!-- Order Summary Side Card -->
                <div class="lg:col-span-5 sylva-plate p-6 sm:p-8 rounded-3xl h-fit shadow-xl space-y-6">
                    <h2 class="font-serif text-2xl font-light italic text-sylva-light border-b border-sylva-accent/30 pb-4">Order Summary</h2>
                    
                    <div class="max-h-60 overflow-y-auto space-y-4 pr-1">
                        <?php foreach ($cart_items as $item): ?>
                            <div class="flex justify-between items-center text-xs">
                                <div class="flex items-center gap-3">
                                    <img src="<?= htmlspecialchars(!empty($item['image']) ? $item['image'] : 'https://images.unsplash.com/photo-1523293182086-7651a899d37f?q=80&w=200') ?>" class="w-12 h-12 rounded-xl object-cover bg-sylva-base border border-sylva-accent/40">
                                    <div>
                                        <div class="font-serif text-sm text-white"><?= htmlspecialchars($item['name']) ?></div>
                                        <div class="text-neutral-400 text-[10px]">Qty: <?= $item['quantity'] ?></div>
                                    </div>
                                </div>
                                <span class="font-serif text-sm text-sylva-gold">$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="space-y-3 pt-4 border-t border-sylva-accent/30 text-xs text-neutral-400">
                        <div class="flex justify-between">
                            <span>Subtotal</span>
                            <span class="text-white font-serif">$<?= number_format($subtotal, 2) ?></span>
                        </div>
                        <div class="flex justify-between">
                            <span>Shipping Fee</span>
                            <span class="text-white font-serif">$<?= number_format($shipping, 2) ?></span>
                        </div>
                        <div class="flex justify-between text-sylva-gold font-bold" id="gatewayFeeRow" style="display: none;">
                            <span>Card Gateway Fee (2.5%)</span>
                            <span id="gatewayFeeVal">$0.00</span>
                        </div>
                        <div class="flex justify-between text-base font-serif text-white pt-3 border-t border-sylva-accent/30">
                            <span>Total Amount</span>
                            <span id="totalAmountVal" class="text-sylva-gold font-bold text-xl">$<?= number_format($subtotal + $shipping, 2) ?></span>
                        </div>
                    </div>
                </div>

            </div>
        <?php endif; ?>

    </main>

    <script>
        const subtotal = <?= $subtotal ?>;
        const shipping = <?= $shipping ?>;

        function selectPayment(method) {
            document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('active'));
            document.querySelectorAll('#box_card, #box_instapay, #box_wallet').forEach(box => box.classList.add('hidden'));

            document.getElementById('opt_' + getOptionId(method)).classList.add('active');
            const radio = document.querySelector(`input[value="${method}"]`);
            if(radio) radio.checked = true;

            let gatewayFee = 0;
            if(method === 'card') {
                document.getElementById('box_card').classList.remove('hidden');
                gatewayFee = subtotal * 0.025;
            } else if(method === 'instapay') {
                document.getElementById('box_instapay').classList.remove('hidden');
            } else if(['vodafone_cash', 'orange_cash', 'etisalat_cash', 'we_cash'].includes(method)) {
                document.getElementById('box_wallet').classList.remove('hidden');
            }

            const feeRow = document.getElementById('gatewayFeeRow');
            if(gatewayFee > 0) {
                feeRow.style.display = 'flex';
                document.getElementById('gatewayFeeVal').innerText = '$' + gatewayFee.toFixed(2);
            } else {
                feeRow.style.display = 'none';
            }

            const finalTotal = subtotal + shipping + gatewayFee;
            document.getElementById('totalAmountVal').innerText = '$' + finalTotal.toFixed(2);
            document.getElementById('submitBtn').innerText = 'Place Order ($' + finalTotal.toFixed(2) + ')';
        }

        function getOptionId(method) {
            if(method === 'cod') return 'cod';
            if(method === 'card') return 'card';
            if(method === 'instapay') return 'instapay';
            if(method === 'vodafone_cash') return 'vodafone';
            if(method === 'etisalat_cash') return 'etisalat';
            if(method === 'orange_cash') return 'orange';
            if(method === 'we_cash') return 'we';
            return 'cod';
        }
    </script>
<?php include __DIR__ . '/ai-widget.php'; ?>
<?php include __DIR__ . '/footer.php'; ?>
<?php include __DIR__ . '/ai-widget.php'; ?>
</body>
</html>