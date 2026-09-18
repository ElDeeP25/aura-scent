<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

$db_path = __DIR__ . '/config/db.php';
if (!file_exists($db_path)) { $db_path = __DIR__ . '/../config/db.php'; }
require_once $db_path;

$email = $_SESSION['reset_email'] ?? '';
$verified = $_SESSION['otp_verified'] ?? false;

if (empty($email) || !$verified) {
    header("Location: forgot_password.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (strlen($password) < 6) {
        $error = "Password must be at least 6 characters long.";
    } elseif ($password !== $confirm_password) {
        $error = "Passwords do not match!";
    } else {
        try {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            
            $stmt = $pdo->prepare("UPDATE users SET password = ?, reset_code = NULL, reset_expires_at = NULL WHERE LOWER(email) = ?");
            $stmt->execute([$hashed, $email]);

            unset($_SESSION['reset_email']);
            unset($_SESSION['otp_verified']);
            unset($_SESSION['dev_otp']);

            $_SESSION['login_msg'] = "Password updated successfully! Please login.";
            header("Location: auth.php");
            exit;
        } catch (Exception $e) {
            $error = "Failed to update password. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set New Password | AURA & SCENT</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { sylva: { base: '#0b130e', card: '#121f17', gold: '#d4af37' } } } } }
    </script>
</head>
<body class="bg-sylva-base text-white min-h-screen flex items-center justify-center p-4 antialiased">
    <div class="bg-sylva-card/90 border border-sylva-gold/30 p-8 rounded-3xl max-w-md w-full shadow-2xl backdrop-blur-xl">
        <h2 class="font-serif text-2xl italic text-sylva-gold text-center mb-2">Set New Password</h2>
        <p class="text-neutral-400 text-xs text-center mb-6">Create a new secure password for your account.</p>

        <?php if ($error): ?>
            <div class="bg-red-950/50 border border-red-500/40 text-red-400 p-3 rounded-xl text-xs text-center mb-4"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" class="space-y-4 text-xs">
            <div>
                <label class="block uppercase font-bold text-neutral-400 mb-1">New Password</label>
                <input type="password" name="password" required placeholder="••••••••" class="w-full bg-sylva-base border border-sylva-gold/30 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-sylva-gold">
            </div>
            <div>
                <label class="block uppercase font-bold text-neutral-400 mb-1">Confirm Password</label>
                <input type="password" name="confirm_password" required placeholder="••••••••" class="w-full bg-sylva-base border border-sylva-gold/30 rounded-xl px-4 py-3 text-white focus:outline-none focus:border-sylva-gold">
            </div>
            <button type="submit" class="w-full bg-sylva-gold text-black font-extrabold py-3.5 rounded-full uppercase tracking-wider hover:bg-white transition-all shadow-lg cursor-pointer">
                Save & Update Password 🔑
            </button>
        </form>
    </div>
</body>
</html>