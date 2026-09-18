<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db_path = __DIR__ . '/config/db.php';
if (!file_exists($db_path)) {
    $db_path = __DIR__ . '/../config/db.php';
}
require_once $db_path;

$error = '';
$success = '';
if (!empty($_SESSION['login_msg'])) {
    $success = $_SESSION['login_msg'];
    unset($_SESSION['login_msg']);
}

$mode = $_GET['mode'] ?? 'login';

// قائمة إيميلات الأدمن لتحديد التوجيه التلقائي
$admin_emails = [
    "y.yousefahmed26112001@gmail.com",
    "kholoudsaied@gmail.com",
    "nahlaamer2004@gmail.com",
    "rizkmariem8@gmail.com",
    "zaftt@gmail.com"
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ================= LOGIN LOGIC =================
    if ($action === 'login') {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (!empty($email) && !empty($password)) {
            try {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
                $stmt->execute([$email]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && password_verify($password, $user['password'])) {
                    $user_name = !empty($user['name']) ? $user['name'] : 'User';
                    $user_email_clean = strtolower(trim($user['email']));
                    $admin_emails_clean = array_map(function($e) { return strtolower(trim($e)); }, $admin_emails);

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['user_name'] = $user_name;
                    $_SESSION['user_email'] = $user['email'];
                    
                    if (in_array($user_email_clean, $admin_emails_clean) || strtolower($user['role'] ?? '') === 'admin') {
                        $_SESSION['user_role'] = 'admin';
                        header("Location: admin.php");
                    } else {
                        $_SESSION['user_role'] = 'customer';
                        header("Location: index.php");
                    }
                    exit;
                } else {
                    $error = "Invalid email or password.";
                }
            } catch (Exception $e) {
                $error = "Something went wrong. Please try again.";
            }
        }
    }

    // ================= REGISTER LOGIC =================
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
                    $stmt = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
                    $stmt->execute([$name, $email, $hashed_password]);

                    $new_id = $pdo->lastInsertId();

                    $_SESSION['user_id'] = $new_id;
                    $_SESSION['user_name'] = $name;
                    $_SESSION['user_email'] = $email;
                    $_SESSION['user_role'] = 'customer';

                    header("Location: index.php");
                    exit;
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
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authentication — AURA & SCENT | Sylva Edition</title>
    
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%230f1d13'/><text x='50%' y='68%' font-family='serif' font-size='65' font-weight='bold' fill='%23d4af37' text-anchor='middle'>A</text></svg>">
    
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        sylva: {
                            base: '#0b130e',
                            card: '#121f17',
                            accent: '#2d4a36',
                            gold: '#d4af37',
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
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(212, 175, 55, 0.18);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.5);
        }
    </style>
</head>
<body class="min-h-screen flex flex-col justify-between selection:bg-sylva-gold selection:text-black antialiased">

    <!-- Floating Navigation Bar -->
    <?php include __DIR__ . '/header.php'; ?>

    <!-- Main Auth Container -->
    <main class="flex-grow flex items-center justify-center pt-36 pb-16 px-4 sm:px-6">
        <div class="w-full max-w-md sylva-plate p-8 sm:p-10 rounded-3xl shadow-2xl">
            
            <?php if (!empty($error)): ?>
                <div class="mb-6 p-4 rounded-2xl bg-red-950/40 border border-red-500/30 text-red-400 text-xs text-center font-bold uppercase tracking-wider">⚠️ <?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            <?php if (!empty($success)): ?>
                <div class="mb-6 p-4 rounded-2xl bg-emerald-950/40 border border-emerald-500/30 text-emerald-400 text-xs text-center font-bold uppercase tracking-wider">✨ <?= htmlspecialchars($success) ?></div>
            <?php endif; ?>

            <!-- LOGIN BOX -->
            <div id="login-box" class="<?= $mode === 'register' ? 'hidden' : '' ?>">
                <h1 class="font-serif text-3xl sm:text-4xl text-sylva-light font-light italic text-center mb-2">Sign In</h1>
                <p class="text-[10px] sm:text-xs text-neutral-400 uppercase tracking-widest text-center mb-8 font-light">Access Your Botanical Sanctuary</p>

                <form action="auth.php" method="POST" class="space-y-5">
                    <input type="hidden" name="action" value="login">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 mb-2">Email Address</label>
                        <input type="email" name="email" required placeholder="name@example.com" class="w-full bg-sylva-base/80 border border-sylva-accent/50 rounded-xl px-4 py-3.5 text-xs text-white focus:outline-none focus:border-sylva-gold transition-colors">
                    </div>
                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400">Password</label>
                            <a href="forgot_password.php" class="text-[10px] font-bold text-sylva-gold hover:underline uppercase tracking-wider">Forgot Password?</a>
                        </div>
                        <input type="password" name="password" required placeholder="••••••••" class="w-full bg-sylva-base/80 border border-sylva-accent/50 rounded-xl px-4 py-3.5 text-xs text-white focus:outline-none focus:border-sylva-gold transition-colors">
                    </div>
                    <button type="submit" class="w-full bg-sylva-gold text-black py-4 rounded-full font-extrabold text-xs uppercase tracking-[0.2em] hover:bg-white transition-all shadow-lg shadow-sylva-gold/20 mt-4 cursor-pointer">
                        Sign In
                    </button>
                </form>

                <p class="text-xs text-neutral-400 text-center mt-8 uppercase tracking-wider font-light">
                    Don't have an account? 
                    <button type="button" onclick="toggleAuth('register')" class="text-sylva-gold font-bold hover:underline cursor-pointer ml-1">SIGN UP</button>
                </p>
            </div>

            <!-- REGISTER BOX -->
            <div id="register-box" class="<?= $mode === 'register' ? '' : 'hidden' ?>">
                <h1 class="font-serif text-3xl sm:text-4xl text-sylva-light font-light italic text-center mb-2">Create Account</h1>
                <p class="text-[10px] sm:text-xs text-neutral-400 uppercase tracking-widest text-center mb-8 font-light">Join AURA & SCENT Botanical Club</p>

                <form action="auth.php" method="POST" class="space-y-5">
                    <input type="hidden" name="action" value="register">
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 mb-2">Full Name</label>
                        <input type="text" name="name" required placeholder="John Doe" class="w-full bg-sylva-base/80 border border-sylva-accent/50 rounded-xl px-4 py-3.5 text-xs text-white focus:outline-none focus:border-sylva-gold transition-colors">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 mb-2">Email Address</label>
                        <input type="email" name="email" required placeholder="name@example.com" class="w-full bg-sylva-base/80 border border-sylva-accent/50 rounded-xl px-4 py-3.5 text-xs text-white focus:outline-none focus:border-sylva-gold transition-colors">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold uppercase tracking-[0.2em] text-neutral-400 mb-2">Password</label>
                        <input type="password" name="password" required placeholder="••••••••" class="w-full bg-sylva-base/80 border border-sylva-accent/50 rounded-xl px-4 py-3.5 text-xs text-white focus:outline-none focus:border-sylva-gold transition-colors">
                    </div>
                    <button type="submit" class="w-full bg-sylva-gold text-black py-4 rounded-full font-extrabold text-xs uppercase tracking-[0.2em] hover:bg-white transition-all shadow-lg shadow-sylva-gold/20 mt-4 cursor-pointer">
                        Sign Up
                    </button>
                </form>

                <p class="text-xs text-neutral-400 text-center mt-8 uppercase tracking-wider font-light">
                    Already have an account? 
                    <button type="button" onclick="toggleAuth('login')" class="text-sylva-gold font-bold hover:underline cursor-pointer ml-1">SIGN IN</button>
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

    <?php include __DIR__ . '/footer.php'; ?>
    <?php include __DIR__ . '/ai-widget.php'; ?>

    <!-- تضمين الخلفية المتحركة -->
    <?php 
    $bg_file = __DIR__ . '/bg-animation.php';
    if (!file_exists($bg_file)) { $bg_file = __DIR__ . '/../bg-animation.php'; }
    if (file_exists($bg_file)) { include $bg_file; }
    ?>
</body>
</html>