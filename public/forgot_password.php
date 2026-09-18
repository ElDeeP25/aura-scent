<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$db_path = __DIR__ . '/config/db.php';
if (!file_exists($db_path)) { $db_path = __DIR__ . '/../config/db.php'; }
require_once $db_path;

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = strtolower(trim($_POST['email'] ?? ''));

    if (!empty($email)) {
        try {
            $stmt = $pdo->prepare("SELECT id, name FROM users WHERE LOWER(email) = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user) {
                // توليد كود الـ OTP
                $otp = rand(100000, 999999);
                $expires_at = date('Y-m-d H:i:s', strtotime('+15 minutes'));

                // تحديث قاعدة البيانات
                $update_stmt = $pdo->prepare("UPDATE users SET reset_code = ?, reset_expires_at = ? WHERE id = ?");
                $update_stmt->execute([$otp, $expires_at, $user['id']]);

                // حفظ بيانات الجلسة للاختبار المحلي الفوري
                $_SESSION['reset_email'] = $email;
                $_SESSION['dev_otp'] = $otp;

                header("Location: verify_otp.php");
                exit;
            } else {
                $error = "No account found with this email address.";
            }
        } catch (Exception $e) {
            $error = "Database error. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | AURA & SCENT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { sylva: { base: '#0b130e', card: '#121f17', gold: '#d4af37' } } } } }
    </script>
</head>
<body class="bg-sylva-base text-white min-h-screen flex items-center justify-center p-4 antialiased">
    <div class="bg-sylva-card/90 border border-sylva-gold/30 p-8 rounded-3xl max-w-md w-full shadow-2xl backdrop-blur-xl">
        <h2 class="font-serif text-2xl italic text-sylva-gold text-center mb-2">Forgot Password</h2>
        <p class="text-neutral-400 text-xs text-center mb-6">Enter your registered email to receive a 6-digit verification code.</p>

        <?php if ($error): ?>
            <div class="bg-red-950/50 border border-red-500/40 text-red-400 p-3 rounded-xl text-xs text-center mb-4"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-4 text-xs">
            <div>
                <label class="block uppercase font-bold text-neutral-400 mb-1">Email Address</label>
                <input type="email" name="email" required placeholder="name@example.com" class="w-full bg-sylva-base border border-sylva-gold/30 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-sylva-gold">
            </div>
            <button type="submit" class="w-full bg-sylva-gold text-black font-extrabold py-3.5 rounded-full uppercase tracking-wider hover:bg-white transition-all shadow-lg cursor-pointer">
                Generate Verification Code ⚡
            </button>
            <div class="text-center pt-2">
                <a href="auth.php" class="text-xs text-neutral-400 hover:text-sylva-gold underline">Back to Login</a>
            </div>
        </form>
    </div>
</body>
</html>