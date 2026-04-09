<?php session_start(); ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <script>
        window.FontAwesomeConfig = { autoReplaceSvg: 'nest' };
    </script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Marchand de Biens | Estimation Immobilière</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:ital,wght@0,400;0,600;0,700;1,400&display=swap" rel="stylesheet">
    <script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: {
                    sans: ['Inter', 'sans-serif'],
                    serif: ['Playfair Display', 'serif'],
                },
                colors: { 
                    primary: '#F97316', 
                    primaryHover: '#EA580C', 
                    dark: '#111827', 
                    grayLight: '#F9FAFB', 
                    grayBorder: '#E5E7EB', 
                    textMain: '#374151', 
                    textMuted: '#6B7280' 
                }
            }
        }
    }
</script>
    <style>
        html { scroll-behavior: smooth; }
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
        details > summary { list-style: none; }
        details > summary::-webkit-details-marker { display: none; }
    </style>
</head>
<body class="font-sans bg-gray-50 text-textMain antialiased flex flex-col min-h-screen">

    <?php include 'includes/header.php'; ?>

    <main class="flex-grow">
        <?php include 'index/hero.php'; ?>
        <?php include 'index/estimation.php'; ?> 
        <?php include 'index/avantages.php'; ?>
        <?php include 'index/process.php'; ?>
        <?php include 'index/faq.php'; ?>
    </main>

    <?php include 'includes/footer.php'; ?>
    
    <?php include 'index/scripts.php'; ?>

</body>
</html>