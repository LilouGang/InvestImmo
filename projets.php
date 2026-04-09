<?php
session_start();
require 'includes/db.php';

// --- RÉCUPÉRATION DES VILLES EXISTANTES POUR LE FILTRE ---
$stmtVilles = $pdo->query("SELECT DISTINCT ville FROM projets WHERE ville IS NOT NULL AND ville != '' ORDER BY ville");
$villes_disponibles = $stmtVilles->fetchAll(PDO::FETCH_COLUMN);

// --- GESTION DES FILTRES AVANCÉS ---
$where = [];
$params = [];

if (!empty($_GET['search'])) {
    $where[] = "(p.titre LIKE ? OR p.description LIKE ?)";
    $params[] = '%' . $_GET['search'] . '%';
    $params[] = '%' . $_GET['search'] . '%';
}
if (!empty($_GET['ville'])) {
    $where[] = "p.ville = ?";
    $params[] = $_GET['ville'];
}
if (!empty($_GET['surface_min'])) {
    $where[] = "p.surface >= ?";
    $params[] = (int)$_GET['surface_min'];
}

$sql = "SELECT p.*, 
        (SELECT chemin_photo FROM projet_photos pp WHERE pp.id_projet = p.id ORDER BY id ASC LIMIT 1) as image_principale,
        COALESCE((SELECT prix FROM projet_etapes pe WHERE pe.id_projet = p.id AND pe.statut = 'Actuel' LIMIT 1), 0) as prix_reel 
        FROM projets p";

if (count($where) > 0) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

// Filtre sur le prix réel calculé
if (!empty($_GET['budget_max'])) {
    $sql .= (count($where) > 0 ? " HAVING " : " HAVING ") . "prix_reel <= ? AND prix_reel > 0";
    $params[] = (int)$_GET['budget_max'];
}

$sql .= " ORDER BY p.date_publication DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projets = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Catalogue des Projets - AvenirImmo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = { theme: { extend: { fontFamily: { sans: ['Inter', 'sans-serif'], serif: ['Playfair Display', 'serif'] }, colors: { primary: '#F97316', primaryHover: '#EA580C', dark: '#111827', textMain: '#374151', textMuted: '#6B7280', grayBorder: '#E5E7EB' }, boxShadow: { 'soft': '0 4px 20px rgba(0,0,0,0.05)', 'card': '0 4px 6px -1px rgba(0, 0, 0, 0.05)', 'float': '0 10px 25px -5px rgba(0, 0, 0, 0.1)' } } } }
    </script>
    <style>
        .filter-dropdown { appearance: none; background-image: url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%236B7280%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E"); background-repeat: no-repeat; background-position: right 0.7rem top 50%; background-size: 0.65rem auto; }
        .project-card:hover .project-image img { transform: scale(1.05); }
    </style>
</head>
<body class="font-sans text-textMain bg-gray-50 antialiased">

    <?php include 'includes/header.php'; ?>

    <section id="projects-header" class="bg-white border-b border-grayBorder py-12 md:py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center md:text-left mb-10">
                <h1 class="font-serif text-4xl md:text-5xl font-bold text-dark mb-4">Nos Projets Immobiliers</h1>
                <p class="text-lg text-textMuted max-w-2xl">Découvrez notre catalogue de biens en cours de rénovation. Trouvez le projet qui correspond à votre budget et vos envies.</p>
            </div>

            <form action="projets.php" method="GET" class="bg-white rounded-xl shadow-soft border border-gray-100 p-4 md:p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
                    
                    <div class="lg:col-span-2 relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i class="fa-solid fa-magnifying-glass text-gray-400"></i></div>
                        <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" class="block w-full pl-10 pr-3 py-3 border border-grayBorder rounded-lg focus:ring-primary focus:border-primary text-sm" placeholder="Mot-clé...">
                    </div>
                    
                    <div>
                        <select name="ville" onchange="this.form.submit()" class="block w-full py-3 px-4 border border-grayBorder rounded-lg focus:ring-primary focus:border-primary text-sm filter-dropdown bg-white cursor-pointer hover:border-primary/50 transition-colors">
                            <option value="">Toutes les villes</option>
                            <?php foreach($villes_disponibles as $v): ?>
                                <option value="<?= htmlspecialchars($v) ?>" <?= (isset($_GET['ville']) && $_GET['ville'] == $v) ? 'selected' : '' ?>><?= htmlspecialchars($v) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div>
                        <select name="budget_max" onchange="this.form.submit()" class="block w-full py-3 px-4 border border-grayBorder rounded-lg focus:ring-primary focus:border-primary text-sm filter-dropdown bg-white cursor-pointer hover:border-primary/50 transition-colors">
                            <option value="">Budget max</option>
                            <option value="150000" <?= (isset($_GET['budget_max']) && $_GET['budget_max'] == '150000') ? 'selected' : '' ?>>< 150 000 €</option>
                            <option value="250000" <?= (isset($_GET['budget_max']) && $_GET['budget_max'] == '250000') ? 'selected' : '' ?>>< 250 000 €</option>
                            <option value="400000" <?= (isset($_GET['budget_max']) && $_GET['budget_max'] == '400000') ? 'selected' : '' ?>>< 400 000 €</option>
                            <option value="600000" <?= (isset($_GET['budget_max']) && $_GET['budget_max'] == '600000') ? 'selected' : '' ?>>< 600 000 €</option>
                        </select>
                    </div>

                    <div>
                        <select name="surface_min" onchange="this.form.submit()" class="block w-full py-3 px-4 border border-grayBorder rounded-lg focus:ring-primary focus:border-primary text-sm filter-dropdown bg-white cursor-pointer hover:border-primary/50 transition-colors">
                            <option value="">Surface min</option>
                            <option value="30" <?= (isset($_GET['surface_min']) && $_GET['surface_min'] == '30') ? 'selected' : '' ?>>> 30 m²</option>
                            <option value="50" <?= (isset($_GET['surface_min']) && $_GET['surface_min'] == '50') ? 'selected' : '' ?>>> 50 m²</option>
                            <option value="100" <?= (isset($_GET['surface_min']) && $_GET['surface_min'] == '100') ? 'selected' : '' ?>>> 100 m²</option>
                        </select>
                    </div>

                    <div>
                        <a href="projets.php" class="w-full bg-primary hover:bg-primaryHover text-white py-2.5 px-4 rounded-lg font-medium transition-colors flex items-center justify-center gap-2 border border-grayBorder">
                            <i class="fa-solid fa-xmark text-white"></i> Réinitialiser
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </section>

    <main id="projects-list" class="py-12 md:py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <div class="flex justify-between items-center mb-8 gap-4">
                <h2 class="text-xl font-bold text-dark"><?= count($projets) ?> projets trouvés</h2>
            </div>

            <?php if (count($projets) > 0): ?>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                    
                    <?php foreach ($projets as $projet): 
                        // --- RÉCUPÉRATION DES PRIX DYNAMIQUES POUR CETTE CARTE ---
                        $stmtEtapes = $pdo->prepare("SELECT statut, prix FROM projet_etapes WHERE id_projet = ?");
                        $stmtEtapes->execute([$projet['id']]);
                        $etapes = $stmtEtapes->fetchAll();
                        
                        $prix_actuel = 0;
                        $prix_futur = null;
                        foreach ($etapes as $etape) {
                            if ($etape['statut'] == 'Actuel') $prix_actuel = $etape['prix'];
                            if ($etape['statut'] == 'Futur') $prix_futur = $etape['prix'];
                        }

                        // --- DESCRIPTION NETTOYÉE ---
                        $description_propre = strip_tags(html_entity_decode($projet['description'] ?? '', ENT_QUOTES, 'UTF-8'));

                        // --- LISTE DES CARACTÉRISTIQUES PRIORISÉES ---
                        $features = [];
                        
                        // 1. Type de bien
                        if (!empty($projet['type_bien'])) {
                            $icon = 'fa-building';
                            if (in_array($projet['type_bien'], ['Maison', 'Villa'])) $icon = 'fa-house';
                            if ($projet['type_bien'] == 'Loft') $icon = 'fa-industry';
                            if ($projet['type_bien'] == 'Local commercial') $icon = 'fa-store';
                            $features[] = '<i class="fa-solid ' . $icon . ' text-gray-400"></i> <span>' . htmlspecialchars($projet['type_bien']) . '</span>';
                        }
                        
                        // 2. Surface
                        if (!empty($projet['surface']) && $projet['surface'] > 0) {
                            $features[] = '<i class="fa-solid fa-ruler-combined text-gray-400"></i> <span>' . htmlspecialchars($projet['surface']) . ' m²</span>';
                        }
                        
                        // 3. Pièces / Chambres
                        if (!empty($projet['pieces']) && $projet['pieces'] > 0) {
                            $feat_piece = $projet['pieces'] . ' P.';
                            if (!empty($projet['chambres']) && $projet['chambres'] > 0) {
                                $feat_piece .= ' (' . $projet['chambres'] . ' Ch.)';
                            }
                            $features[] = '<i class="fa-solid fa-door-open text-gray-400"></i> <span>' . $feat_piece . '</span>';
                        }
                        
                        // 4. Extérieurs (Priorité au Jardin, sinon Terrasse, sinon Balcon)
                        if (!empty($projet['ext_jardin'])) {
                            $features[] = '<i class="fa-solid fa-tree text-green-500/70"></i> <span>Jardin</span>';
                        } elseif (!empty($projet['ext_terrasse'])) {
                            $features[] = '<i class="fa-solid fa-sun text-orange-400/70"></i> <span>Terrasse</span>';
                        } elseif (!empty($projet['ext_balcon'])) {
                            $features[] = '<i class="fa-solid fa-person-through-window text-gray-400"></i> <span>Balcon</span>';
                        }
                        
                        // 5. Stationnement
                        if (!empty($projet['has_garage'])) {
                            $features[] = '<i class="fa-solid fa-warehouse text-gray-400"></i> <span>Garage</span>';
                        } elseif (!empty($projet['has_parking'])) {
                            $features[] = '<i class="fa-solid fa-square-parking text-gray-400"></i> <span>Parking</span>';
                        }
                        
                        // 6. DPE
                        if (!empty($projet['dpe'])) {
                            $dpe = strtoupper($projet['dpe']);
                            $dpeColor = in_array($dpe, ['A','B']) ? 'text-green-500' : (in_array($dpe, ['C','D']) ? 'text-yellow-500' : 'text-red-500');
                            $features[] = '<i class="fa-solid fa-leaf ' . $dpeColor . '"></i> <span class="font-medium">DPE ' . htmlspecialchars($dpe) . '</span>';
                        }
                        
                        // On garde uniquement les 4 données les plus pertinentes pour un affichage propre
                        $display_features = array_slice($features, 0, 4);

                        // --- COULEUR DU BADGE (État du bien) ---
                        $badgeColor = 'bg-blue-100 text-blue-800 border border-blue-200'; // Défaut
                        $etat = $projet['etat_bien'] ?? '';
                        if (stripos($etat, 'Neuf') !== false || stripos($etat, 'VEFA') !== false) {
                            $badgeColor = 'bg-orange-100 text-orange-800 border border-orange-200';
                        } elseif (stripos($etat, 'Clé en main') !== false || stripos($etat, 'Rénové') !== false) {
                            $badgeColor = 'bg-green-100 text-green-800 border border-green-200';
                        } elseif (stripos($etat, 'rénover') !== false || stripos($etat, 'Plateau') !== false || stripos($etat, 'rafraîchir') !== false) {
                            $badgeColor = 'bg-gray-100 text-gray-800 border border-gray-200';
                        }
                    ?>
                        <div class="bg-white rounded-2xl overflow-hidden shadow-card border border-gray-100 project-card flex flex-col h-full transition-all hover:shadow-float">
                            <div class="relative h-64 overflow-hidden project-image group bg-gray-200">
                                <img src="<?= htmlspecialchars($projet['image_principale'] != 'default-house.jpg' ? $projet['image_url'] ?? $projet['image_principale'] : 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80') ?>" alt="Photo du bien" class="w-full h-full object-cover transition-transform duration-500">
                                
                                <div class="absolute top-4 left-4">
                                    <span class="bg-white text-dark text-xs font-bold px-3 py-1.5 rounded-md shadow flex items-center gap-1">
                                        <i class="fa-solid fa-location-dot text-primary"></i> <?= htmlspecialchars($projet['ville']) ?>
                                    </span>
                                </div>
                                <div class="absolute top-4 right-4">
                                    <span class="<?= $badgeColor ?> text-xs font-bold px-3 py-1.5 rounded-md shadow">
                                        <?= htmlspecialchars($etat) ?>
                                    </span>
                                </div>

                                <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                    <a href="<?= $projet['slug'] ?>" class="bg-white text-dark px-6 py-2 rounded-md font-medium text-sm hover:bg-primary hover:text-white transition-colors">
                                        Voir les détails
                                    </a>
                                </div>
                            </div>

                            <div class="p-6 flex-grow flex flex-col">
                                <h3 class="text-xl font-bold font-serif text-dark mb-2 leading-tight flex items-center gap-2 flex-wrap">
                                    <?= htmlspecialchars($projet['titre']) ?>
                                    <?php if(isset($projet['statut_commercial']) && in_array($projet['statut_commercial'], ['Réservé', 'Vendu'])): ?>
                                        <span class="text-[10px] font-sans font-bold px-2 py-1 rounded-md uppercase tracking-wider <?= $projet['statut_commercial'] == 'Vendu' ? 'bg-red-100 text-red-700 border border-red-200' : 'bg-orange-100 text-orange-700 border border-orange-200' ?>">
                                            <?= htmlspecialchars($projet['statut_commercial']) ?>
                                        </span>
                                    <?php endif; ?>
                                </h3>
                                
                                <p class="text-sm text-textMuted mb-4 line-clamp-2"><?= htmlspecialchars($description_propre) ?></p>
                                
                                <div class="flex flex-wrap gap-x-4 gap-y-2 text-sm text-textMain mb-6 pb-4 border-b border-gray-100">
                                    <?php foreach($display_features as $feature_html): ?>
                                        <div class="flex items-center gap-1.5">
                                            <?= $feature_html ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <div class="mt-auto">
                                    <div class="mb-4">
                                        <div class="flex justify-between text-xs mb-1">
                                            <span class="font-medium text-textMain">Avancement projet</span>
                                            <span class="font-bold text-primary"><?= (int)$projet['avancement'] ?>%</span>
                                        </div>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5">
                                            <div class="bg-primary h-1.5 rounded-full" style="width: <?= (int)$projet['avancement'] ?>%"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="bg-gray-50 rounded-lg p-3 border border-gray-100">
                                        <div class="text-xs text-textMuted mb-1">Prix selon finition :</div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-sm font-medium text-dark">En l'état actuel</span>
                                            <span class="text-lg font-bold text-primary"><?= $prix_actuel > 0 ? number_format($prix_actuel, 0, ',', ' ') . ' €' : 'Sur demande' ?></span>
                                        </div>
                                        <?php if($prix_futur): ?>
                                        <div class="flex justify-between items-center mt-1 pt-1 border-t border-gray-200">
                                            <span class="text-xs text-textMuted">Projet Clé en main</span>
                                            <span class="text-xs font-bold text-gray-500">~ <?= number_format($prix_futur, 0, ',', ' ') ?> €</span>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>

                </div>
            <?php else: ?>
                <div class="text-center py-12 bg-white rounded-xl border border-gray-200">
                    <i class="fa-solid fa-house-circle-xmark text-4xl text-gray-300 mb-3"></i>
                    <h3 class="text-lg font-medium text-dark">Aucun projet trouvé</h3>
                    <p class="text-textMuted mt-1">Essayez de modifier vos filtres de recherche.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

</body>
</html>