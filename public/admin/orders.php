<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلبات العملاء | لوحة التحكم — Sylva Edition</title>
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
                            gold: '#d4af37',      /* ذهبي راقي */
                            light: '#e8ece9'
                        }
                    },
                    fontFamily: {
                        tajawal: ['Tajawal', 'sans-serif'],
                        serif: ['Newsreader', 'Georgia', 'serif']
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&family=Newsreader:ital,wght@0,300;1,400&display=swap" rel="stylesheet">
    
    <style>
        body { background-color: #0b130e; color: #e8ece9; font-family: 'Tajawal', sans-serif; overflow-x: hidden; }
        
        .sylva-plate {
            background: rgba(18, 31, 23, 0.65);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(212, 175, 55, 0.18);
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.4);
        }
    </style>
</head>
<body class="selection:bg-sylva-gold selection:text-black antialiased min-h-screen">

    <!-- Header Navigation -->
    <header class="fixed top-6 left-1/2 -translate-x-1/2 z-50 w-[92%] max-w-6xl">
        <div class="sylva-plate rounded-full px-6 py-4 flex items-center justify-between">
            <a href="../index.php" class="font-serif italic text-xl sm:text-2xl text-sylva-light tracking-wider flex items-center gap-2">
                <span class="w-3 h-3 rounded-full bg-sylva-gold inline-block animate-pulse"></span>
                Aura <span class="text-sylva-gold font-normal">&</span> Scent
            </a>
            
            <nav class="hidden md:flex items-center gap-8 text-xs font-bold tracking-wider text-neutral-300">
                <a href="dashboard.php" class="hover:text-sylva-gold transition-colors">إدارة المنتجات</a>
                <a href="orders.php" class="text-sylva-gold border-b-2 border-sylva-gold pb-1">طلبات العملاء</a>
                <a href="../index.php" class="hover:text-sylva-gold transition-colors">الذهاب للمتجر ↗</a>
            </nav>

            <a href="../index.php" class="sylva-plate px-5 py-2 rounded-full text-xs font-bold text-sylva-gold hover:bg-sylva-gold hover:text-black transition-all">
                المتجر
            </a>
        </div>
    </header>

    <!-- Main Content Container -->
    <main class="max-w-6xl mx-auto px-4 sm:px-8 pt-36 pb-20">
        
        <!-- Orders Section -->
        <div class="sylva-plate rounded-3xl p-6 sm:p-8 shadow-2xl overflow-hidden">
            <div class="flex items-center justify-between mb-6 pb-4 border-b border-sylva-accent/40">
                <h2 class="text-xl font-bold text-sylva-light flex items-center gap-2">
                    <span>📋</span> قائمة طلبات العملاء الحالية
                </h2>
                <span class="text-xs font-bold bg-sylva-accent/40 text-sylva-gold border border-sylva-gold/20 px-3.5 py-1.5 rounded-full">
                    تحديث مباشر
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-right text-xs">
                    <thead>
                        <tr class="border-b border-sylva-accent/40 text-sylva-gold font-bold">
                            <th class="p-4">رقم الطلب</th>
                            <th class="p-4">اسم العميل</th>
                            <th class="p-4">العنوان</th>
                            <th class="p-4">الهاتف</th>
                            <th class="p-4 text-center">الحالة</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-sylva-accent/20">
                        <tr class="hover:bg-sylva-card/50 transition-colors">
                            <td class="p-4 font-mono font-bold text-sylva-gold">#1001</td>
                            <td class="p-4 font-bold text-white">أحمد محمد</td>
                            <td class="p-4 text-neutral-300">القاهرة، مصر الجديدة</td>
                            <td class="p-4 text-neutral-400 font-mono">01012345678</td>
                            <td class="p-4 text-center">
                                <span class="bg-sylva-accent/60 text-sylva-gold border border-sylva-gold/30 px-3 py-1 rounded-full font-bold text-[11px] inline-block">
                                    قيد التجهيز
                                </span>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

</body>
</html>