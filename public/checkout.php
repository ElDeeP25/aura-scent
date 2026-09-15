<?php
session_start();
require_once __DIR__ . '/config/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: auth.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch cart items
$stmt = $pdo->prepare("SELECT c.*, p.name, p.price, p.image_url FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Secure Checkout | AURA & SCENT</title>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --bg-ivory: #fcfbf9;
            --card-white: #ffffff;
            --gold-primary: #c5a059;
            --text-main: #111111;
            --text-muted: #777777;
            --border-light: #eae6df;
            --transition-smooth: all 0.4s ease;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Tajawal', sans-serif; background-color: var(--bg-ivory); color: var(--text-main); text-align: left; direction: ltr; }

        header { background: #fff; border-bottom: 1px solid var(--border-light); padding: 20px 50px; display: flex; justify-content: space-between; align-items: center; }
        .brand-logo { font-size: 20px; font-weight: 700; color: var(--text-main); letter-spacing: 3px; text-decoration: none; text-transform: uppercase; }
        .back-link { font-size: 13px; font-weight: 600; color: var(--text-muted); text-decoration: none; transition: var(--transition-smooth); }
        .back-link:hover { color: var(--gold-primary); }

        .checkout-container { max-width: 1150px; margin: 50px auto; padding: 0 20px; display: grid; grid-template-columns: 1.3fr 0.9fr; gap: 40px; }
        @media(max-width: 900px) { .checkout-container { grid-template-columns: 1fr; } }

        .checkout-form-box, .order-summary-box { background: var(--card-white); border: 1px solid var(--border-light); border-radius: 12px; padding: 30px; }
        
        h2 { font-size: 18px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 25px; }
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; font-size: 12px; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 8px; color: var(--text-muted); }
        .form-group input, .form-group textarea { width: 100%; padding: 12px 15px; border: 1px solid var(--border-light); border-radius: 6px; font-family: 'Tajawal'; font-size: 14px; transition: var(--transition-smooth); text-align: left; }
        .form-group input:focus, .form-group textarea:focus { border-color: var(--gold-primary); outline: none; }

        .payment-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px; margin-bottom: 20px; }
        @media(max-width: 500px) { .payment-grid { grid-template-columns: 1fr; } }

        .payment-option { border: 1px solid var(--border-light); padding: 12px; border-radius: 8px; text-align: center; cursor: pointer; font-size: 12px; font-weight: 600; transition: var(--transition-smooth); display: flex; align-items: center; justify-content: center; gap: 6px; }
        .payment-option input { accent-color: var(--gold-primary); }
        .payment-option.active { border-color: var(--gold-primary); background: rgba(197, 160, 89, 0.04); }

        .payment-details-box { background: #faf9f6; border: 1px solid var(--border-light); border-radius: 8px; padding: 20px; margin-bottom: 20px; display: none; animation: fadeIn 0.3s ease; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(-5px); } to { opacity: 1; transform: translateY(0); } }

        .card-row { display: grid; grid-template-columns: 2fr 1fr; gap: 15px; }

        .summary-item { display: flex; justify-content: space-between; font-size: 14px; margin-bottom: 12px; color: var(--text-muted); }
        .summary-total { display: flex; justify-content: space-between; font-size: 16px; font-weight: 700; color: var(--text-main); border-top: 1px solid var(--border-light); padding-top: 15px; margin-top: 15px; }
        
        .btn-submit { display: block; width: 100%; background: var(--text-main); color: #fff; border: none; padding: 15px; border-radius: 6px; font-weight: 700; font-size: 13px; letter-spacing: 1px; text-transform: uppercase; cursor: pointer; transition: var(--transition-smooth); margin-top: 25px; text-align: center; text-decoration: none; }
        .btn-submit:hover { background: var(--gold-primary); }

        .alert-error { background: #fdf2f2; border: 1px solid #f5c6cb; color: #721c24; padding: 12px; border-radius: 6px; font-size: 13px; margin-bottom: 20px; }

        .success-box { text-align: center; padding: 60px 30px; background: #fff; border: 1px solid var(--border-light); border-radius: 12px; max-width: 600px; margin: 80px auto; }
        .success-box h3 { font-size: 22px; color: var(--gold-primary); margin-bottom: 15px; }
        .success-box p { color: var(--text-muted); font-size: 14px; margin-bottom: 30px; line-height: 1.6; }
    </style>
</head>
<body>

    <header>
        <a href="index.php" class="brand-logo">AURA & SCENT</a>
        <a href="cart.php" class="back-link">← Back to Cart</a>
    </header>

    <?php if (!empty($success_msg)): ?>
        <div class="success-box">
            <h3>Order Placed Successfully</h3>
            <p><?= htmlspecialchars($success_msg) ?></p>
            <a href="index.php" class="btn-submit" style="display: inline-block; width: auto; padding: 14px 45px;">Return to Store</a>
        </div>
    <?php else: ?>
        <div class="checkout-container">
            <div class="checkout-form-box">
                <h2>Shipping & Payment Details</h2>

                <?php if (!empty($error_msg)): ?>
                    <div class="alert-error"><?= htmlspecialchars($error_msg) ?></div>
                <?php endif; ?>

                <form method="POST" id="checkoutForm">
                    <div class="form-group">
                        <label>Delivery Address</label>
                        <textarea name="address" rows="3" placeholder="Street name, building number, apartment, city" required><?= htmlspecialchars($_POST['address'] ?? '') ?></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Phone Number</label>
                        <input type="text" name="phone" placeholder="010xxxxxxxx" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Payment Method</label>
                        <div class="payment-grid">
                            <label class="payment-option active" id="opt_cod" onclick="selectPayment('cod')">
                                <input type="radio" name="payment_method" value="cod" checked> Cash on Delivery
                            </label>
                            <label class="payment-option" id="opt_card" onclick="selectPayment('card')">
                                <input type="radio" name="payment_method" value="card"> Credit / Debit Card
                            </label>
                            <label class="payment-option" id="opt_instapay" onclick="selectPayment('instapay')">
                                <input type="radio" name="payment_method" value="instapay"> InstaPay
                            </label>
                            <label class="payment-option" id="opt_vodafone" onclick="selectPayment('vodafone_cash')">
                                <input type="radio" name="payment_method" value="vodafone_cash"> Vodafone Cash
                            </label>
                            <label class="payment-option" id="opt_etisalat" onclick="selectPayment('etisalat_cash')">
                                <input type="radio" name="payment_method" value="etisalat_cash"> Etisalat Cash
                            </label>
                            <label class="payment-option" id="opt_orange" onclick="selectPayment('orange_cash')">
                                <input type="radio" name="payment_method" value="orange_cash"> Orange Cash
                            </label>
                            <label class="payment-option" id="opt_we" onclick="selectPayment('we_cash')">
                                <input type="radio" name="payment_method" value="we_cash"> WE Pay
                            </label>
                        </div>
                    </div>

                    <!-- Credit Card Box -->
                    <div class="payment-details-box" id="box_card">
                        <div class="form-group" style="margin-bottom: 12px;">
                            <label>Card Number</label>
                            <input type="text" name="card_number" placeholder="4111 2222 3333 4444" maxlength="19">
                        </div>
                        <div class="card-row">
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>Expiry Date</label>
                                <input type="text" name="card_expiry" placeholder="MM/YY" maxlength="5">
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label>CVV Code</label>
                                <input type="password" name="card_cvv" placeholder="123" maxlength="4">
                            </div>
                        </div>
                    </div>

                    <!-- InstaPay Box -->
                    <div class="payment-details-box" id="box_instapay">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>InstaPay Username (IPN)</label>
                            <input type="text" name="instapay_username" placeholder="username@instapay">
                            <small style="color: var(--text-muted); font-size: 11px; display: block; margin-top: 5px;">Transfer total amount to: <strong>aurascent@instapay</strong></small>
                        </div>
                    </div>

                    <!-- Mobile Wallets Box -->
                    <div class="payment-details-box" id="box_wallet">
                        <div class="form-group" style="margin-bottom: 0;">
                            <label>Wallet Mobile Number</label>
                            <input type="text" name="wallet_number" placeholder="01xxxxxxxx" maxlength="11">
                            <small style="color: var(--text-muted); font-size: 11px; display: block; margin-top: 5px;">Transfer to store wallet: <strong>01012345678</strong></small>
                        </div>
                    </div>

                    <button type="submit" class="btn-submit" id="submitBtn">Place Order ($<?= number_format($total, 2) ?>)</button>
                </form>
            </div>

            <div class="order-summary-box">
                <h2>Order Summary</h2>
                <div style="max-height: 250px; overflow-y: auto; margin-bottom: 20px; padding-right: 5px;">
                    <?php foreach ($cart_items as $item): ?>
                        <div style="display: flex; justify-content: space-between; align-items: center; font-size: 13px; margin-bottom: 12px;">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <img src="<?= htmlspecialchars($item['image_url']) ?>" style="width: 45px; height: 45px; object-fit: cover; border-radius: 6px;">
                                <div>
                                    <div style="font-weight: 600;"><?= htmlspecialchars($item['name']) ?></div>
                                    <div style="color: var(--text-muted); font-size: 11px;">Qty: <?= $item['quantity'] ?></div>
                                </div>
                            </div>
                            <span style="font-weight: 600;">$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="summary-item">
                    <span>Subtotal</span>
                    <span>$<?= number_format($subtotal, 2) ?></span>
                </div>
                <div class="summary-item">
                    <span>Shipping Fee</span>
                    <span>$<?= number_format($shipping, 2) ?></span>
                </div>
                <div class="summary-item" id="gatewayFeeRow" style="display: none;">
                    <span>Card Gateway Fee (2.5%)</span>
                    <span id="gatewayFeeVal">$0.00</span>
                </div>
                <div class="summary-total">
                    <span>Total Amount</span>
                    <span id="totalAmountVal" style="color: var(--gold-primary);">$<?= number_format($subtotal + $shipping, 2) ?></span>
                </div>
            </div>
        </div>

        <script>
            const subtotal = <?= $subtotal ?>;
            const shipping = <?= $shipping ?>;

            function selectPayment(method) {
                document.querySelectorAll('.payment-option').forEach(opt => opt.classList.remove('active'));
                document.querySelectorAll('.payment-details-box').forEach(box => box.style.display = 'none');

                document.getElementById('opt_' + getOptionId(method)).classList.add('active');
                const radio = document.querySelector(`input[value="${method}"]`);
                if(radio) radio.checked = true;

                let gatewayFee = 0;
                if(method === 'card') {
                    document.getElementById('box_card').style.display = 'block';
                    gatewayFee = subtotal * 0.025;
                } else if(method === 'instapay') {
                    document.getElementById('box_instapay').style.display = 'block';
                } else if(['vodafone_cash', 'orange_cash', 'etisalat_cash', 'we_cash'].includes(method)) {
                    document.getElementById('box_wallet').style.display = 'block';
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
    <?php endif; ?>

</body>
</html>