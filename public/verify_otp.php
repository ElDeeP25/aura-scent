<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$db_path = __DIR__ . '/config/db.php';
if (!file_exists($db_path)) { $db_path = __DIR__ . '/../config/db.php'; }
require_once $db_path;

$email = $_SESSION['reset_email'] ?? '';
$dev_otp = $_SESSION['dev_otp'] ?? '';

if (empty($email)) { header("Location: forgot_password.php"); exit; }

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp'] ?? '');
    $otp = preg_replace('/\s+/', '', $otp); // تنظيف الكود من أي مسافات

    try {
        $stmt = $pdo->prepare("SELECT id, reset_code FROM users WHERE LOWER(email) = ?");
        $stmt->execute([strtolower($email)]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && !empty($user['reset_code'])) {
            $db_code = trim((string)$user['reset_code']);

            if ($db_code === $otp || (string)$dev_otp === $otp) {
                $_SESSION['otp_verified'] = true;
                header("Location: reset_password.php");
                exit;
            } else {
                $error = "Invalid verification code!";
            }
        } else {
            $error = "No reset request found for this account.";
        }
    } catch (Exception $e) {
        $error = "System verification error.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify Code | AURA & SCENT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { sylva: { base: '#0b130e', card: '#121f17', gold: '#d4af37' } } } } }
    </script>
</head>
<body class="bg-sylva-base text-white min-h-screen flex items-center justify-center p-4 antialiased relative">

    <!-- DEV OTP DISPLAY BADGE -->
    <?php if (!empty($dev_otp)): ?>
        <div class="fixed top-6 left-1/2 -translate-x-1/2 bg-sylva-gold text-black px-6 py-3 rounded-full font-bold text-xs uppercase tracking-widest shadow-2xl border border-white z-50">
            ⚡ Dev OTP Code: <span class="font-mono text-sm underline font-black"><?= htmlspecialchars($dev_otp) ?></span>
        </div>
    <?php endif; ?>

    <div class="bg-sylva-card/90 border border-sylva-gold/30 p-8 rounded-3xl max-w-md w-full shadow-2xl backdrop-blur-xl">
        <h2 class="font-serif text-2xl italic text-sylva-gold text-center mb-2">Verify Code</h2>
        <p class="text-neutral-400 text-xs text-center mb-6">Enter the 6-digit OTP code generated for <span class="text-sylva-gold font-mono"><?= htmlspecialchars($email) ?></span></p>

        <?php if ($error): ?>
            <div class="bg-red-950/50 border border-red-500/40 text-red-400 p-3 rounded-xl text-xs text-center mb-4"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-4 text-xs">
            <div>
                <label class="block uppercase font-bold text-neutral-400 mb-1">6-Digit OTP Code</label>
                <input type="text" name="otp" required maxlength="6" placeholder="123456" autocomplete="off" class="w-full bg-sylva-base border border-sylva-gold/30 rounded-xl px-4 py-3 text-center text-xl font-mono text-sylva-gold tracking-[0.5em] focus:outline-none focus:border-sylva-gold">
            </div>
            <button type="submit" class="w-full bg-sylva-gold text-black font-extrabold py-3.5 rounded-full uppercase tracking-wider hover:bg-white transition-all shadow-lg cursor-pointer">
                Verify & Continue ✨
            </button>
            <div class="text-center pt-2">
                <a href="forgot_password.php" class="text-xs text-neutral-400 hover:text-sylva-gold underline">Generate New Code</a>
            </div>
        </form>
    </div>
</body>
</html>