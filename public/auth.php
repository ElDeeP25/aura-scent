<?php
session_start();
require_once __DIR__ . '/config/db.php';

$error = '';
$success = '';
$mode = $_GET['mode'] ?? 'login';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'login') {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (!empty($email) && !empty($password)) {
            try {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['password'])) {
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user['name'] ?? 'User';
                    header("Location: index.php");
                    exit;
                } else {
                    $error = "Invalid email or password.";
                }
            } catch (Exception $e) {
                $error = "Something went wrong. Please try again.";
            }
        }
    }

    if ($action === 'register') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (!empty($name) && !empty($email) && !empty($password)) {
            try {
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $error = "Email is already registered!";
                    $mode = 'register';
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)")->execute([$name, $email, $hashed_password]);
                    $success = "Account created successfully! You can now sign in.";
                    $mode = 'login';
                }
            } catch (Exception $e) {
                $error = "Registration failed. Please try again.";
                $mode = 'register';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication — AURA & SCENT</title>
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
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between selection:bg-brand-gold selection:text-black">

    <header class="fixed top-0 left-0 w-full z-50 glass-card border-b-0">
        <div class="max-w-7xl mx-auto px-4 sm:px-8 h-20 sm:h-24 flex items-center justify-between">
            <a href="index.php" class="font-serif text-lg sm:text-2xl font-bold tracking-[0.2em] sm:tracking-[0.25em] text-white">
                AURA <span class="text-brand-gold">&</span> SCENT
            </a>
            <nav class="hidden md:flex items-center gap-8 text-xs font-bold tracking-[0.2em] uppercase text-neutral-400">
                <a href="index.php" class="hover:text-brand-gold transition-colors">Home</a>
                <a href="shop.php" class="hover:text-brand-gold transition-colors">Shop</a>
                <a href="auth.php" class="text-brand-gold">Login</a>
            </nav>
            <button onclick="toggleMobileMenu()" class="md:hidden text-white p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"/></svg>
            </button>
        </div>
        <div id="mobile-menu" class="hidden md:hidden glass-card border-t border-white/5 px-6 py-6 space-y-4">
            <a href="index.php" class="block text-neutral-300 text-sm font-bold uppercase tracking-widest hover:text-brand-gold">Home</a>
            <a href="shop.php" class="block text-neutral-300 text-sm font-bold uppercase tracking-widest hover:text-brand-gold">Shop</a>
            <a href="auth.php" class="block text-brand-gold text-sm font-bold uppercase tracking-widest">Login</a>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center pt-32 pb-16 px-4 sm:px-6">
        <div class="w-full max-w-md glass-card p-6 sm:p-10 rounded-3xl shadow-2xl">
            
            <?php if (!empty($error)): ?>
                <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-xs text-center font-medium"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="mb-6 p-4 rounded-xl bg-green-500/10 border border-green-500/20 text-green-400 text-xs text-center font-medium"><?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <div id="login-box" class="<?= $mode === 'register' ? 'hidden' : '' ?>">
                <h1 class="font-serif text-2xl sm:text-3xl text-white text-center mb-2">Sign In</h1>
                <p class="text-[10px] sm:text-xs text-neutral-400 uppercase tracking-widest text-center mb-8 font-light">Access Your Account</p>

                <form action="auth.php" method="POST" class="space-y-4 sm:space-y-5">
                    <input type="hidden" name="action" value="login">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 mb-2">Email Address</label>
                        <input type="email" name="email" required placeholder="name@example.com" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-brand-gold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 mb-2">Password</label>
                        <input type="password" name="password" required placeholder="••••••••" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-brand-gold">
                    </div>
                    <button type="submit" class="w-full bg-brand-gold text-black py-3.5 sm:py-4 rounded-full font-bold text-xs uppercase tracking-[0.2em] hover:bg-white transition-all shadow-xl mt-4">
                        Sign In
                    </button>
                </form>

                <p class="text-xs text-neutral-500 text-center mt-6 uppercase tracking-wider font-light">
                    Don't have an account? 
                    <button type="button" onclick="toggleAuth('register')" class="text-brand-gold font-bold hover:underline cursor-pointer">SIGN UP</button>
                </p>
            </div>

            <div id="register-box" class="<?= $mode === 'register' ? '' : 'hidden' ?>">
                <h1 class="font-serif text-2xl sm:text-3xl text-white text-center mb-2">Create Account</h1>
                <p class="text-[10px] sm:text-xs text-neutral-400 uppercase tracking-widest text-center mb-8 font-light">Join AURA & SCENT</p>

                <form action="auth.php" method="POST" class="space-y-4 sm:space-y-5">
                    <input type="hidden" name="action" value="register">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 mb-2">Full Name</label>
                        <input type="text" name="name" required placeholder="John Doe" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-brand-gold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 mb-2">Email Address</label>
                        <input type="email" name="email" required placeholder="name@example.com" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-brand-gold">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 mb-2">Password</label>
                        <input type="password" name="password" required placeholder="••••••••" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-3 text-xs text-white focus:outline-none focus:border-brand-gold">
                    </div>
                    <button type="submit" class="w-full bg-brand-gold text-black py-3.5 sm:py-4 rounded-full font-bold text-xs uppercase tracking-[0.2em] hover:bg-white transition-all shadow-xl mt-4">
                        Sign Up
                    </button>
                </form>

                <p class="text-xs text-neutral-500 text-center mt-6 uppercase tracking-wider font-light">
                    Already have an account? 
                    <button type="button" onclick="toggleAuth('login')" class="text-brand-gold font-bold hover:underline cursor-pointer">SIGN IN</button>
                </p>
            </div>

        </div>
    </main>

    <script>
        function toggleMobileMenu() { document.getElementById('mobile-menu').classList.toggle('hidden'); }
        function toggleAuth(type) {
            document.getElementById('login-box').classList.toggle('hidden', type === 'register');
            document.getElementById('register-box').classList.toggle('hidden', type !== 'register');
        }
    </script>
</body>
</html>