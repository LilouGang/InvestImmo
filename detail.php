<?php
session_start();
$base = '';
require 'includes/db.php';

// On cherche par 'slug' et plus par 'id'
if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    header('Location: projets.php');
    exit();
}

$slug_projet = htmlspecialchars($_GET['slug']);

// Récupération des infos du projet par son SLUG
$stmt = $pdo->prepare("
    SELECT p.*, u.nom as nom_agent, u.email as email_agent, u.telephone as telephone_agent 
    FROM projets p 
    LEFT JOIN utilisateurs u ON p.id_utilisateur = u.id 
    WHERE p.slug = ?
");
$stmt->execute([$slug_projet]);
$projet = $stmt->fetch();

if (!$projet) {
    header('Location: projets.php');
    exit();
}

// On récupère l'ID réel pour que le reste de la page (photos, étapes...) continue de fonctionner !
$id_projet = $projet['id'];

// Récupération des étapes
$stmtEtapes = $pdo->prepare("SELECT * FROM projet_etapes WHERE id_projet = ? ORDER BY ordre ASC, id ASC");
$stmtEtapes->execute([$id_projet]);
$etapes = $stmtEtapes->fetchAll();

$prix_actuel = $projet['prix'];
$prix_futur = null;
foreach ($etapes as $etape) {
    if ($etape['statut'] == 'Actuel') $prix_actuel = $etape['prix'];
    if ($etape['statut'] == 'Futur') $prix_futur = $etape['prix'];
}

// Récupération des plans et photos
$stmtPlans = $pdo->prepare("SELECT * FROM projet_plans WHERE id_projet = ?");
$stmtPlans->execute([$id_projet]);
$plans = $stmtPlans->fetchAll();

$stmtPhotos = $pdo->prepare("SELECT * FROM projet_photos WHERE id_projet = ? ORDER BY id ASC");
$stmtPhotos->execute([$projet['id']]);
$photos = $stmtPhotos->fetchAll();
$nbPhotos = count($photos);

$imgPrincipale = $nbPhotos > 0 ? $photos[0]['chemin_photo'] : 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=80';
$img2 = $nbPhotos > 1 ? $photos[1]['chemin_photo'] : 'https://storage.googleapis.com/uxpilot-auth.appspot.com/491ad0bc58-d9c29cbc63a582d47421.png';
$img3 = $nbPhotos > 2 ? $photos[2]['chemin_photo'] : 'https://storage.googleapis.com/uxpilot-auth.appspot.com/e53e5da3a2-a7cf5513a37af3d9284a.png';
$img4 = $nbPhotos > 3 ? $photos[3]['chemin_photo'] : 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=800&q=80';

// Tags uniques pour les filtres
$tags_existants = [];
foreach ($photos as $photo) {
    if (!empty($photo['tag']) && !in_array($photo['tag'], $tags_existants)) {
        $tags_existants[] = $photo['tag'];
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <base href="/">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($projet['titre']) ?> - AvenirImmo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'], serif: ['Playfair Display', 'serif'] },
                    colors: { primary: '#F97316', primaryHover: '#EA580C', dark: '#111827', textMain: '#374151', textMuted: '#6B7280', grayBorder: '#E5E7EB', olive: '#3e3d32', oliveLight: '#4d4c3f', accent: '#FF5A00' },
                    boxShadow: { 'card': '0 4px 6px -1px rgba(0, 0, 0, 0.05)', 'float': '0 10px 25px -5px rgba(0, 0, 0, 0.1)' }
                }
            }
        }
    </script>
    <style>
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        .gallery-scroll::-webkit-scrollbar, .hide-scrollbar::-webkit-scrollbar { display: none; }
        .gallery-scroll, .hide-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>
</head>
<body class="font-sans text-textMain bg-gray-50 antialiased relative">

    <?php include 'includes/header.php'; ?>

    <?php include 'detail/hero.php'; ?>
    <?php include 'detail/tabs_nav.php'; ?>

    <main class="py-12 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <div class="lg:col-span-2 space-y-12">
                    <?php include 'detail/tab_description.php'; ?>
                    <?php include 'detail/tab_tarifs.php'; ?>
                    <?php include 'detail/tab_plans.php'; ?>
                </div>

                <div class="lg:col-span-1" id="contact-agent">
                    <?php include 'detail/sidebar.php'; ?>
                </div>

            </div>
        </div>
    </main>

    <?php include 'detail/contact_cta.php'; ?>

    <div id="modal-galerie" class="hidden fixed inset-0 z-[100] bg-dark overflow-y-auto">
        <div class="sticky top-0 bg-dark/95 backdrop-blur-md z-50 px-6 py-4 flex justify-between items-center border-b border-gray-800">
            <h2 class="text-white font-bold text-xl"><i class="fa-solid fa-images text-primary mr-2"></i> Galerie du projet (<span id="modal-title-count"><?= $nbPhotos ?></span> photos)</h2>
            <button onclick="fermerModalGalerie()" class="text-gray-400 hover:text-white bg-gray-800 hover:bg-gray-700 w-10 h-10 rounded-full flex items-center justify-center transition-colors">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="columns-1 sm:columns-2 lg:columns-3 gap-6 space-y-6" id="galerie-container">
                <?php foreach($photos as $photo): ?>
                    <div class="galerie-item relative group break-inside-avoid rounded-xl overflow-hidden shadow-lg border border-gray-800 bg-gray-900" data-tag="<?= htmlspecialchars($photo['tag']) ?>">
                        <img src="<?= htmlspecialchars($photo['chemin_photo']) ?>" class="w-full object-cover">
                        <div class="absolute top-4 left-4 z-20 pointer-events-none flex items-center justify-center">
                            <div class="absolute inset-[-30px]" style="backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); mask-image: radial-gradient(ellipse at center, black 0%, transparent 70%); -webkit-mask-image: radial-gradient(ellipse at center, black 0%, transparent 70%);"></div>
                            <span class="relative z-10 text-white text-sm font-bold tracking-wide">
                                <?= htmlspecialchars($photo['tag']) ?>
                            </span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    <?php include 'detail/scripts.php'; ?>

</body>
</html>