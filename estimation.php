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
    <title>Demander une estimation - AvenirImmo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'] },
                    colors: { primary: '#F97316', primaryHover: '#EA580C', dark: '#111827', grayLight: '#F9FAFB', grayBorder: '#E5E7EB', textMain: '#374151', textMuted: '#6B7280' },
                    boxShadow: { 'card': '0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03)', 'float': '0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1)' }
                }
            }
        }
    </script>
    <style>
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: #d1d5db; border-radius: 4px; }
        ::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
        
        .custom-radio:checked + div { border-color: #F97316; background-color: #FFF7ED; }
        .custom-radio:checked + div .radio-icon { color: #F97316; }
        .form-step { display: none; }
        .form-step.active { display: block; }
    </style>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
</head>
<body class="font-sans text-textMain bg-gray-50 antialiased">

    <?php include 'includes/header.php'; ?>

    <main class="py-10 md:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <?php include 'estimation/hero.php'; ?>
            <?php include 'estimation/form.php'; ?>
            <?php include 'estimation/success.php'; ?>
            <?php include 'estimation/trust.php'; ?>

        </div>
    </main>

    <?php include 'includes/footer.php'; ?>
    <?php include 'estimation/scripts.php'; ?>

</body>
</html>