<!DOCTYPE html>
<html lang="ar" dir="rtl" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تأكيد الطلب | Aura & Scent — Sylva Edition</title>
    
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
                        tajawal: ['Tajawal', 'sans-serif'],
                        serif: ['Newsreader', 'Georgia', 'serif']
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700;900&family=Newsreader:ital,wght@0,300;1,400&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">

    <style>
        body { background-color: #0b130e; color: #e8ece9; font-family: 'Tajawal', sans-serif; overflow-x: hidden; }
        
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

    <div class="sylva-plate max-w-lg w-full p-8 sm:p-12 rounded-3xl text-center space-y-6 shadow-2xl relative overflow-hidden">
        <div class="absolute -top-24 -right-24 w-72 h-72 bg-sylva-gold/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="w-20 h-20 bg-sylva-card border border-sylva-gold/40 text-sylva-gold rounded-full flex items-center justify-center mx-auto text-3xl font-bold shadow-lg shadow-sylva-gold/10 animate-bounce">
            🎉
        </div>

        <div>
            <span class="text-xs uppercase tracking-[0.25em] text-sylva-gold font-bold block mb-2">تم تأكيد الحجز النباتي</span>
            <h1 class="text-3xl sm:text-4xl font-bold text-sylva-gold leading-tight">تم استلام طلبك بنجاح!</h1>
        </div>

        <p class="text-neutral-300 text-xs sm:text-sm leading-relaxed font-light">
            شكراً لاختيارك <span class="text-white font-bold">Aura & Scent</span>. جاري تجهيز عطرك الفاخر واستخلاصه من أندر النوتات العطرية وشحنه إليك قريباً.
        </p>

        <div class="pt-6">
            <a href="index.php" class="inline-block w-full bg-sylva-gold text-black py-4 rounded-full font-extrabold text-xs uppercase tracking-widest hover:bg-white transition-all shadow-lg shadow-sylva-gold/20 cursor-pointer">
                العودة للرئيسية
            </a>
        </div>
    </div>

</body>
</html>