<?php
session_start();
require '../includes/db.php';

// Vérification de sécurité
if (!isset($_SESSION['utilisateur_id'])) {
    header('Location: ../connexion.php');
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: ../projets.php');
    exit();
}

$id_projet = (int)$_GET['id'];

// -------------------------------------------------------------
// TRAITEMENT AJAX DE LA SOUMISSION DU FORMULAIRE (MODIFICATION)
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_submit'])) {
    header('Content-Type: application/json');
    try {
        $pdo->beginTransaction();
        
        function creerSlug($chaine) {
            $chaine = preg_replace('~[^\pL\d]+~u', '-', $chaine);
            $chaine = iconv('utf-8', 'us-ascii//TRANSLIT', $chaine);
            $chaine = preg_replace('~[^-\w]+~', '', $chaine);
            $chaine = trim($chaine, '-');
            $chaine = preg_replace('~-+~', '-', $chaine);
            return strtolower($chaine);
        }

        $titre = htmlspecialchars($_POST['titre'] ?? '');
        $slug_base = creerSlug($titre);
        $slug = $slug_base;
        $compteur = 1;
        while (true) {
            $stmtCheck = $pdo->prepare("SELECT id FROM projets WHERE slug = ? AND id != ?");
            $stmtCheck->execute([$slug, $id_projet]);
            if (!$stmtCheck->fetch()) break;
            $slug = $slug_base . '-' . $compteur;
            $compteur++;
        }
        
        // 1. Mise à jour des informations générales
        $stmt = $pdo->prepare("
            UPDATE projets 
            SET slug=?, titre=?, type_bien=?, ancien_usage=?, description=?, etat_bien=?, avancement=?, dispositif_fiscal=?, frais_notaire=?,
                adresse_complete=?, code_postal=?, ville=?, latitude=?, longitude=?, prix_m2_secteur=?,
                surface=?, surface_carrez=?, pieces=?, chambres=?, salles_de_bain=?, wc=?, etage=?, nombre_etages=?,
                ext_jardin=?, surf_jardin=?, ext_terrasse=?, surf_terrasse=?, ext_balcon=?, surf_balcon=?, ext_loggia=?, surf_loggia=?,
                has_parking=?, parking_places=?, has_garage=?, garage_places=?, cave=?, ascenseur=?, acces_pmr=?,
                type_chauffage=?, dpe=?, ges=?, visite_virtuelle_url=?,
                contact_prenom=?, contact_nom=?, contact_telephone=?, contact_email=?, statut_commercial=?
            WHERE id=? AND id_utilisateur=?
        ");
        
        // Traitement des champs multiples (Chauffage)
        $type_chauffage = isset($_POST['type_chauffage']) ? htmlspecialchars($_POST['type_chauffage']) : '';

        $stmt->execute([
            $slug, 
            $titre, 
            htmlspecialchars($_POST['type_bien'] ?? 'Appartement'), 
            htmlspecialchars($_POST['ancien_usage'] ?? ''), 
            $_POST['description'] ?? '', 
            htmlspecialchars($_POST['etat_bien'] ?? ''), 
            (int)($_POST['avancement'] ?? 0), 
            htmlspecialchars($_POST['dispositif_fiscal'] ?? ''), 
            htmlspecialchars($_POST['frais_notaire'] ?? ''),
            htmlspecialchars($_POST['adresse_complete'] ?? ''), 
            htmlspecialchars($_POST['code_postal'] ?? ''), 
            htmlspecialchars($_POST['ville'] ?? ''), 
            !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null, 
            !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null, 
            !empty($_POST['prix_m2_secteur']) ? (int)$_POST['prix_m2_secteur'] : null,
            (float)($_POST['surface'] ?? 0), 
            !empty($_POST['surface_carrez']) ? (float)$_POST['surface_carrez'] : null, 
            (int)($_POST['pieces'] ?? 1), 
            (int)($_POST['chambres'] ?? 0), 
            (int)($_POST['salles_de_bain'] ?? 0), 
            (int)($_POST['wc'] ?? 0), 
            htmlspecialchars($_POST['etage'] ?? ''), 
            (int)($_POST['nombre_etages'] ?? 0),
            (int)($_POST['ext_jardin'] ?? 0), (int)($_POST['surf_jardin'] ?? 0), 
            (int)($_POST['ext_terrasse'] ?? 0), (int)($_POST['surf_terrasse'] ?? 0), 
            (int)($_POST['ext_balcon'] ?? 0), (int)($_POST['surf_balcon'] ?? 0), 
            (int)($_POST['ext_loggia'] ?? 0), (int)($_POST['surf_loggia'] ?? 0),
            (int)($_POST['has_parking'] ?? 0), (int)($_POST['parking_places'] ?? 0), 
            (int)($_POST['has_garage'] ?? 0), (int)($_POST['garage_places'] ?? 0), 
            (int)($_POST['cave'] ?? 0), 
            (int)($_POST['ascenseur'] ?? 0), 
            (int)($_POST['acces_pmr'] ?? 0),
            $type_chauffage, 
            $_POST['dpe'] ?? null, 
            $_POST['ges'] ?? null, 
            htmlspecialchars($_POST['visite_virtuelle_url'] ?? ''),
            htmlspecialchars($_POST['contact_prenom'] ?? ''),
            htmlspecialchars($_POST['contact_nom'] ?? ''),
            htmlspecialchars($_POST['contact_telephone'] ?? ''),
            htmlspecialchars($_POST['contact_email'] ?? ''),
            htmlspecialchars($_POST['statut_commercial'] ?? 'Disponible'),
            $id_projet, 
            $_SESSION['utilisateur_id']
        ]);

        // 2. Gestion de la galerie photos
        $stmtOld = $pdo->prepare("SELECT id, chemin_photo FROM projet_photos WHERE id_projet = ?");
        $stmtOld->execute([$id_projet]);
        $oldPhotos = $stmtOld->fetchAll(PDO::FETCH_ASSOC);
        $oldPhotoIds = array_column($oldPhotos, 'id');
        $oldPhotoPaths = array_column($oldPhotos, 'chemin_photo', 'id');
        
        $keptPhotoIds = $_POST['existing_ids'] ?? [];
        $deletedIds = array_diff($oldPhotoIds, $keptPhotoIds);
        
        if (!empty($deletedIds)) {
            $in = str_repeat('?,', count($deletedIds) - 1) . '?';
            $stmtDel = $pdo->prepare("DELETE FROM projet_photos WHERE id IN ($in)");
            $stmtDel->execute(array_values($deletedIds));
            
            foreach ($deletedIds as $delId) {
                $path = "../" . $oldPhotoPaths[$delId];
                if (file_exists($path) && is_file($path)) unlink($path);
            }
        }

        $orderItems = $_POST['photo_order'] ?? []; 
        $tags = $_POST['photo_tags'] ?? [];
        $isExistingArr = $_POST['photo_is_existing'] ?? [];
        
        $stmtUpdateExisting = $pdo->prepare("UPDATE projet_photos SET tag = ?, ordre = ? WHERE id = ?");
        $stmtInsertNew = $pdo->prepare("INSERT INTO projet_photos (id_projet, chemin_photo, tag, ordre) VALUES (?, ?, ?, ?)");
        
        $newFileIndex = 0;
        
        foreach ($orderItems as $i => $itemId) {
            $tag = htmlspecialchars($tags[$i] ?? '');
            $isExisting = $isExistingArr[$i];
            
            if ($isExisting == 1) {
                $stmtUpdateExisting->execute([$tag, $i, $itemId]);
            } else {
                if (isset($_FILES['new_photos']['tmp_name'][$newFileIndex]) && $_FILES['new_photos']['error'][$newFileIndex] === UPLOAD_ERR_OK) {
                    $tmp_name = $_FILES['new_photos']['tmp_name'][$newFileIndex];
                    $ext = strtolower(pathinfo($_FILES['new_photos']['name'][$newFileIndex], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                        $new_name = uniqid('photo_') . '.' . $ext;
                        $dest = "../uploads/" . $new_name;
                        if (move_uploaded_file($tmp_name, $dest)) {
                            $stmtInsertNew->execute([$id_projet, "uploads/" . $new_name, $tag, $i]);
                        }
                    }
                    $newFileIndex++;
                }
            }
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'redirect' => "../" . $slug]);
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit();
    }
}

// Récupération des données du projet
$stmt = $pdo->prepare("SELECT * FROM projets WHERE id = ?");
$stmt->execute([$id_projet]);
$projet = $stmt->fetch();

if (!$projet) {
    header('Location: ../projets.php');
    exit();
}

// Photos existantes
$stmtPhotos = $pdo->prepare("SELECT * FROM projet_photos WHERE id_projet = ? ORDER BY ordre ASC");
$stmtPhotos->execute([$id_projet]);
$existingPhotos = $stmtPhotos->fetchAll(PDO::FETCH_ASSOC);

// Préparation des tableaux pour les cases à cocher multiples
$chauffages_actuels = array_map('trim', explode(',', $projet['type_chauffage'] ?? ''));

// =========================================================================
// AJOUT ICI : Récupération des données pour préremplir le bloc contact
// =========================================================================
$stmtUser = $pdo->prepare("SELECT nom, prenom, telephone, email FROM utilisateurs WHERE id = ?");
$stmtUser->execute([$_SESSION['utilisateur_id']]);
$utilisateur = $stmtUser->fetch(PDO::FETCH_ASSOC);

$val_prenom = !empty($projet['contact_prenom']) ? $projet['contact_prenom'] : ($utilisateur['prenom'] ?? '');
$val_nom = !empty($projet['contact_nom']) ? $projet['contact_nom'] : ($utilisateur['nom'] ?? '');
$val_telephone = !empty($projet['contact_telephone']) ? $projet['contact_telephone'] : ($utilisateur['telephone'] ?? '');
$val_email = !empty($projet['contact_email']) ? $projet['contact_email'] : ($utilisateur['email'] ?? '');
// =========================================================================

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le projet - AvenirImmo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <link href="https://cdn.quilljs.com/1.3.7/quill.snow.css" rel="stylesheet">
    <script src="https://cdn.quilljs.com/1.3.7/quill.min.js"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: '#F97316', primaryHover: '#EA580C', dark: '#111827', grayBorder: '#E5E7EB' } } }
        }
    </script>
    <style>
        .dpe-radio:checked + label { transform: scale(1.15); border: 2px solid #111827; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2); z-index: 10; }
        .dpe-label { transition: all 0.2s; color: white; font-weight: bold; display: flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem; cursor: pointer; border-radius: 0.375rem; }
    </style>
</head>
<body class="bg-gray-50 text-gray-800 font-sans antialiased pb-20">

    <nav class="bg-dark text-white p-4 shadow-md sticky top-0 z-50">
        <div class="max-w-5xl mx-auto flex justify-between items-center">
            <a href="../<?= htmlspecialchars($projet['slug']) ?>" class="text-white hover:text-primary transition-colors flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Retour
            </a>
            <span class="font-bold">Modifier le bien</span>
            <button type="button" onclick="document.getElementById('modifier-form').dispatchEvent(new Event('submit'))" class="bg-primary hover:bg-primaryHover text-white px-4 py-2 rounded-lg font-bold text-sm transition-colors shadow-sm">
                Enregistrer
            </button>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto py-8 px-4 sm:px-6">
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-dark">Modification : <?= htmlspecialchars($projet['titre']) ?></h1>
            <p class="text-gray-500 mt-2">Mettez à jour les caractéristiques complètes du bien.</p>
        </div>
        
        <div id="submit-error" class="hidden bg-red-50 text-red-500 border border-red-200 p-4 rounded-xl font-bold mb-6"></div>

        <form id="modifier-form" class="space-y-8">
            
            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <div class="flex flex-col md:flex-row gap-6 mb-6 pb-6 border-b">
                    <div class="w-full md:w-1/3">
                        <label class="block text-sm font-bold text-dark mb-2">Statut commercial</label>
                        <select name="statut_commercial" class="w-full border border-grayBorder rounded-lg p-2.5 focus:ring-primary outline-none font-bold text-lg bg-gray-50">
                            <option value="Disponible" <?= $projet['statut_commercial'] === 'Disponible' ? 'selected' : '' ?>>🟢 Disponible</option>
                            <option value="Réservé" <?= $projet['statut_commercial'] === 'Réservé' ? 'selected' : '' ?>>🟠 Réservé</option>
                            <option value="Vendu" <?= $projet['statut_commercial'] === 'Vendu' ? 'selected' : '' ?>>🔴 Vendu</option>
                        </select>
                    </div>
                </div>
                
                <h2 class="text-lg font-bold mb-4 flex items-center gap-2 text-dark"><i class="fa-solid fa-address-card text-primary"></i> Contact du bien</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div>
                        <label class="block text-sm font-medium mb-1">Prénom <span class="text-red-500">*</span></label>
                        <input type="text" name="contact_prenom" value="<?= htmlspecialchars($val_prenom) ?>" required class="w-full border border-grayBorder rounded-lg p-2.5 outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Nom <span class="text-red-500">*</span></label>
                        <input type="text" name="contact_nom" value="<?= htmlspecialchars($val_nom) ?>" required class="w-full border border-grayBorder rounded-lg p-2.5 outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Téléphone</label>
                        <input type="tel" name="contact_telephone" value="<?= htmlspecialchars($val_telephone) ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="15" class="w-full border border-grayBorder rounded-lg p-2.5 outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="contact_email" value="<?= htmlspecialchars($val_email) ?>" required class="w-full border border-grayBorder rounded-lg p-2.5 outline-none focus:border-primary">
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h2 class="text-lg font-bold mb-4 border-b pb-2 text-dark">L'identité du projet</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium mb-1">Titre de l'annonce <span class="text-red-500">*</span></label>
                        <input type="text" name="titre" value="<?= htmlspecialchars($projet['titre']) ?>" required class="w-full border border-grayBorder rounded-lg p-2.5 outline-none focus:border-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Type de bien <span class="text-red-500">*</span></label>
                        <select name="type_bien" id="type_bien" required class="w-full border border-grayBorder rounded-lg p-2.5 outline-none bg-white font-bold text-primary">
                            <option value="Appartement" <?= $projet['type_bien'] === 'Appartement' ? 'selected' : '' ?>>Appartement</option>
                            <option value="Maison" <?= $projet['type_bien'] === 'Maison' ? 'selected' : '' ?>>Maison / Villa</option>
                            <option value="Loft" <?= $projet['type_bien'] === 'Loft' ? 'selected' : '' ?>>Loft / Atelier</option>
                            <option value="Local commercial" <?= $projet['type_bien'] === 'Local commercial' ? 'selected' : '' ?>>Local commercial</option>
                            <option value="Immeuble" <?= $projet['type_bien'] === 'Immeuble' ? 'selected' : '' ?>>🏢 Immeuble complet</option>
                        </select>
                    </div>
                    
                    <div class="md:col-span-3">
                        <label class="block text-sm font-medium mb-1">Description <span class="text-red-500">*</span></label>
                        <div class="border border-grayBorder rounded-lg overflow-hidden">
                            <div id="editor-container" class="h-64 bg-white"><?= html_entity_decode($projet['description'] ?? '') ?></div>
                        </div>
                        <input type="hidden" name="description" id="hidden-description">
                    </div>
                    
                    <div id="bloc_ancien_usage" class="<?= $projet['type_bien'] === 'Loft' ? '' : 'hidden' ?> md:col-span-3">
                        <label class="block text-sm font-medium mb-1 text-primary">Ancien usage (Loft)</label>
                        <input type="text" name="ancien_usage" value="<?= htmlspecialchars($projet['ancien_usage'] ?? '') ?>" class="w-full px-4 py-2 border border-primary/50 bg-orange-50 rounded-lg outline-none">
                    </div>

                    <div class="md:col-span-3 bg-gray-50 p-5 rounded-xl border border-gray-200">
                        <div class="flex justify-between items-end mb-2">
                            <label class="block text-sm font-bold text-dark">Avancement des travaux</label>
                            <span class="text-primary font-extrabold text-xl"><span id="val_avancement"><?= (int)$projet['avancement'] ?></span>%</span>
                        </div>
                        
                        <div class="relative w-full h-3 bg-gray-200 rounded-full mt-3 shadow-inner">
                            <div id="progress_avancement" class="absolute top-0 left-0 h-full rounded-full transition-all duration-150 pointer-events-none" style="width: <?= (int)$projet['avancement'] ?>%; background-color: hsl(<?= (int)$projet['avancement'] * 1.2 ?>, 85%, 45%);"></div>
                            
                            <div id="thumb_avancement" class="absolute top-1/2 -translate-y-1/2 w-5 h-5 bg-white border-2 border-gray-400 rounded-full shadow pointer-events-none z-10 transition-all duration-150" style="left: calc(<?= (int)$projet['avancement'] ?>% - 10px); border-color: hsl(<?= (int)$projet['avancement'] * 1.2 ?>, 85%, 45%);"></div>
                            
                            <input type="range" name="avancement" min="0" max="100" value="<?= (int)$projet['avancement'] ?>" step="5" class="absolute top-0 left-0 w-full h-full opacity-0 cursor-pointer z-20" oninput="
                                document.getElementById('val_avancement').innerText = this.value;
                                document.getElementById('progress_avancement').style.width = this.value + '%';
                                document.getElementById('progress_avancement').style.backgroundColor = 'hsl(' + Math.round(this.value * 1.2) + ', 85%, 45%)';
                                document.getElementById('thumb_avancement').style.left = 'calc(' + this.value + '% - 10px)';
                                document.getElementById('thumb_avancement').style.borderColor = 'hsl(' + Math.round(this.value * 1.2) + ', 85%, 45%)';
                            ">
                        </div>
                        <div class="flex justify-between text-[10px] text-gray-400 mt-3 font-bold uppercase tracking-widest"><span>Projet (0%)</span><span>Hors d'eau</span><span>Livré (100%)</span></div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium mb-1">État du bien</label>
                        <select name="etat_bien" class="w-full border border-grayBorder rounded-lg p-2.5 outline-none">
                            <?php $etats = ["Neuf / Sur plan (VEFA)", "Plateau brut à aménager", "Plateau viabilisé", "À rénover intégralement", "À rafraîchir", "Rénové à neuf (Clé en main)"];
                            foreach($etats as $e): ?>
                                <option value="<?= $e ?>" <?= $projet['etat_bien'] === $e ? 'selected' : '' ?>><?= $e ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Dispositif Fiscal</label>
                        <select name="dispositif_fiscal" class="w-full border border-grayBorder rounded-lg p-2.5 outline-none">
                            <option value="">Aucun</option>
                            <?php $fisc = ["Déficit Foncier", "Loi Pinel", "Loi Malraux", "Loi Denormandie", "LMNP", "LMP", "Monuments Historiques", "Éligible PTZ"];
                            foreach($fisc as $f): ?>
                                <option value="<?= $f ?>" <?= $projet['dispositif_fiscal'] === $f ? 'selected' : '' ?>><?= $f ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Frais de notaire</label>
                        <select name="frais_notaire" class="w-full border border-grayBorder rounded-lg p-2.5 outline-none">
                            <option value="">Non précisé</option>
                            <option value="Frais réduits (2 à 3%)" <?= $projet['frais_notaire'] === 'Frais réduits (2 à 3%)' ? 'selected' : '' ?>>Frais réduits (2 à 3%)</option>
                            <option value="Frais classiques (7 à 8%)" <?= $projet['frais_notaire'] === 'Frais classiques (7 à 8%)' ? 'selected' : '' ?>>Frais classiques (7 à 8%)</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h3 class="text-lg font-bold text-dark border-b pb-2 mb-4">Localisation & Marché</h3>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <label class="block text-sm font-medium mb-1">Adresse complète</label>
                        <input type="text" id="adresse-input" name="adresse_complete" value="<?= htmlspecialchars($projet['adresse_complete'] ?? '') ?>" class="w-full px-4 py-2 border border-grayBorder rounded-lg outline-none" placeholder="Rechercher une adresse..." autocomplete="off">
                        <div class="grid grid-cols-2 gap-4">
                            <input type="text" id="cp-input" name="code_postal" value="<?= htmlspecialchars($projet['code_postal'] ?? '') ?>" class="w-full px-4 py-2 border border-grayBorder bg-white rounded-lg focus:border-primary outline-none" placeholder="Code postal">
                            <input type="text" id="ville-input" name="ville" value="<?= htmlspecialchars($projet['ville'] ?? '') ?>" class="w-full px-4 py-2 border border-grayBorder bg-white rounded-lg focus:border-primary outline-none" placeholder="Ville">
                        </div>
                        <input type="hidden" id="lat-input" name="latitude" value="<?= $projet['latitude'] ?? '' ?>">
                        <input type="hidden" id="lng-input" name="longitude" value="<?= $projet['longitude'] ?? '' ?>">
                        
                        <div class="mt-4 p-4 bg-blue-50 border border-blue-100 rounded-xl">
                            <label class="block text-sm font-bold text-blue-900 mb-1">Prix moyen du secteur (€/m²)</label>
                            <input type="text" name="prix_m2_secteur" value="<?= htmlspecialchars($projet['prix_m2_secteur'] ?? '') ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-1/2 px-4 py-2 border border-blue-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div class="relative h-full min-h-[250px] rounded-xl border border-grayBorder overflow-hidden z-0">
                        <div id="map" class="absolute inset-0 z-0"></div>
                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-full z-10 pointer-events-none drop-shadow-lg"><i class="fa-solid fa-location-dot text-4xl text-primary"></i></div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h3 class="text-lg font-bold text-dark border-b pb-2 mb-4">Surfaces et Agencement</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
                    <div><label class="block text-sm font-medium mb-1">Surface Hab. totale</label><input type="text" name="surface" value="<?= htmlspecialchars($projet['surface']) ?>" class="w-full px-4 py-2 border border-grayBorder rounded-lg outline-none"></div>
                    <div><label class="block text-sm font-medium mb-1">Surface Carrez</label><input type="text" name="surface_carrez" value="<?= htmlspecialchars($projet['surface_carrez'] ?? '') ?>" class="w-full px-4 py-2 border border-grayBorder rounded-lg outline-none"></div>
                    <div><label class="block text-sm font-medium mb-1">Pièces</label><input type="text" name="pieces" value="<?= htmlspecialchars($projet['pieces']) ?>" class="w-full px-4 py-2 border border-grayBorder rounded-lg outline-none"></div>
                    <div><label class="block text-sm font-medium mb-1">Chambres</label><input type="text" name="chambres" value="<?= htmlspecialchars($projet['chambres']) ?>" class="w-full px-4 py-2 border border-grayBorder rounded-lg outline-none"></div>
                    
                    <div><label class="block text-sm font-medium mb-1">Salles de bain</label><input type="text" name="salles_de_bain" value="<?= htmlspecialchars($projet['salles_de_bain'] ?? '') ?>" class="w-full px-4 py-2 border border-grayBorder rounded-lg outline-none"></div>
                    <div><label class="block text-sm font-medium mb-1">Toilettes (WC)</label><input type="text" name="wc" value="<?= htmlspecialchars($projet['wc'] ?? '') ?>" class="w-full px-4 py-2 border border-grayBorder rounded-lg outline-none"></div>
                    <div><label class="block text-sm font-medium mb-1">Étage du bien</label><input type="text" name="etage" value="<?= htmlspecialchars($projet['etage']) ?>" class="w-full px-4 py-2 border border-grayBorder rounded-lg outline-none"></div>
                    <div><label class="block text-sm font-medium mb-1">Nb total d'étages</label><input type="text" name="nombre_etages" value="<?= htmlspecialchars($projet['nombre_etages'] ?? '') ?>" class="w-full px-4 py-2 border border-grayBorder rounded-lg outline-none"></div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-100">
                        <h4 class="font-bold text-dark mb-4"><i class="fa-solid fa-tree text-green-500 mr-2"></i>Espaces Extérieurs</h4>
                        <div class="space-y-3">
                            <div class="flex flex-col gap-2">
                                <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="ext_jardin" id="ext_jardin" value="1" <?= $projet['ext_jardin'] ? 'checked' : '' ?> class="w-4 h-4 accent-primary ext-trigger"><span class="text-sm font-medium">Jardin</span></label>
                                <div class="ext-surface <?= $projet['ext_jardin'] ? '' : 'hidden' ?> pl-6"><input type="text" name="surf_jardin" value="<?= $projet['surf_jardin'] ?: '' ?>" placeholder="Surface m²" class="w-32 px-3 py-1 border border-grayBorder rounded text-sm outline-none"></div>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="ext_terrasse" id="ext_terrasse" value="1" <?= $projet['ext_terrasse'] ? 'checked' : '' ?> class="w-4 h-4 accent-primary ext-trigger"><span class="text-sm font-medium">Terrasse</span></label>
                                <div class="ext-surface <?= $projet['ext_terrasse'] ? '' : 'hidden' ?> pl-6"><input type="text" name="surf_terrasse" value="<?= $projet['surf_terrasse'] ?: '' ?>" placeholder="Surface m²" class="w-32 px-3 py-1 border border-grayBorder rounded text-sm outline-none"></div>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="ext_balcon" id="ext_balcon" value="1" <?= $projet['ext_balcon'] ? 'checked' : '' ?> class="w-4 h-4 accent-primary ext-trigger"><span class="text-sm font-medium">Balcon</span></label>
                                <div class="ext-surface <?= $projet['ext_balcon'] ? '' : 'hidden' ?> pl-6"><input type="text" name="surf_balcon" value="<?= $projet['surf_balcon'] ?: '' ?>" placeholder="Surface m²" class="w-32 px-3 py-1 border border-grayBorder rounded text-sm outline-none"></div>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="ext_loggia" id="ext_loggia" value="1" <?= $projet['ext_loggia'] ? 'checked' : '' ?> class="w-4 h-4 accent-primary ext-trigger"><span class="text-sm font-medium">Loggia</span></label>
                                <div class="ext-surface <?= $projet['ext_loggia'] ? '' : 'hidden' ?> pl-6"><input type="text" name="surf_loggia" value="<?= $projet['surf_loggia'] ?: '' ?>" placeholder="Surface m²" class="w-32 px-3 py-1 border border-grayBorder rounded text-sm outline-none"></div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gray-50 p-5 rounded-xl border border-gray-100">
                        <h4 class="font-bold text-dark mb-4"><i class="fa-solid fa-car text-gray-500 mr-2"></i>Annexes & Bâtiment</h4>
                        <div class="space-y-3">
                            <div class="flex flex-col gap-2">
                                <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="has_parking" id="has_parking" value="1" <?= $projet['has_parking'] ? 'checked' : '' ?> class="w-4 h-4 accent-primary ext-trigger"><span class="text-sm font-medium">Places de Parking</span></label>
                                <div class="ext-surface <?= $projet['has_parking'] ? '' : 'hidden' ?> pl-6"><input type="text" name="parking_places" value="<?= $projet['parking_places'] ?: '' ?>" placeholder="Combien ?" class="w-24 px-3 py-1 border border-grayBorder rounded text-sm outline-none"></div>
                            </div>
                            <div class="flex flex-col gap-2">
                                <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="has_garage" id="has_garage" value="1" <?= $projet['has_garage'] ? 'checked' : '' ?> class="w-4 h-4 accent-primary ext-trigger"><span class="text-sm font-medium">Garages / Box</span></label>
                                <div class="ext-surface <?= $projet['has_garage'] ? '' : 'hidden' ?> pl-6"><input type="text" name="garage_places" value="<?= $projet['garage_places'] ?: '' ?>" placeholder="Combien ?" class="w-24 px-3 py-1 border border-grayBorder rounded text-sm outline-none"></div>
                            </div>
                            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="cave" value="1" <?= $projet['cave'] ? 'checked' : '' ?> class="w-4 h-4 accent-primary"><span class="text-sm font-medium">Cave privative</span></label>
                            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="ascenseur" value="1" <?= $projet['ascenseur'] ? 'checked' : '' ?> class="w-4 h-4 accent-primary"><span class="text-sm font-medium">Ascenseur</span></label>
                            <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="acces_pmr" value="1" <?= $projet['acces_pmr'] ? 'checked' : '' ?> class="w-4 h-4 accent-primary"><span class="text-sm font-medium text-blue-600">Accès PMR</span></label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h3 class="text-lg font-bold text-dark border-b pb-2 mb-4">Énergie & Visite 3D</h3>
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-3">Modes de chauffage</label>
                    <div class="flex flex-wrap gap-4 bg-gray-50 p-4 rounded-xl border border-gray-200" id="chauffage_container">
                        <?php $chauf_options = ["Électrique", "Pompe à chaleur", "Gaz", "Climatisation réversible", "Poêle à bois/granulés", "Réseau urbain"];
                        foreach($chauf_options as $opt): ?>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" value="<?= $opt ?>" <?= in_array($opt, $chauffages_actuels) ? 'checked' : '' ?> class="chk-chauffage w-4 h-4 accent-primary">
                                <span class="text-sm"><?= $opt ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    <input type="hidden" name="type_chauffage" id="type_chauffage_hidden">
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <div>
                        <label class="block text-sm font-medium mb-2">Classe Énergétique (DPE)</label>
                        <div class="flex flex-wrap gap-1">
                            <?php $colors = ['A'=>'#32984b', 'B'=>'#33cc31', 'C'=>'#cbf000', 'D'=>'#ffff00', 'E'=>'#f0b000', 'F'=>'#eb680f', 'G'=>'#d21016'];
                            foreach($colors as $lettre => $hex): ?>
                                <input type="radio" name="dpe" id="dpe_<?= $lettre ?>" value="<?= $lettre ?>" <?= ($projet['dpe'] === $lettre) ? 'checked' : '' ?> class="hidden dpe-radio">
                                <label for="dpe_<?= $lettre ?>" class="dpe-label" style="background-color: <?= $hex ?>; color: <?= in_array($lettre, ['C','D']) ? '#000' : '#fff' ?>;"><?= $lettre ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Émissions Gaz (GES)</label>
                        <div class="flex flex-wrap gap-1">
                            <?php foreach($colors as $lettre => $hex): ?>
                                <input type="radio" name="ges" id="ges_<?= $lettre ?>" value="<?= $lettre ?>" <?= ($projet['ges'] === $lettre) ? 'checked' : '' ?> class="hidden dpe-radio">
                                <label for="ges_<?= $lettre ?>" class="dpe-label" style="background-color: <?= $hex ?>; color: <?= in_array($lettre, ['C','D']) ? '#000' : '#fff' ?>;"><?= $lettre ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-dark mb-2"><i class="fa-solid fa-vr-cardboard text-blue-500 mr-2"></i>Lien Visite Virtuelle (Matterport, etc.)</label>
                    <input type="url" name="visite_virtuelle_url" value="<?= htmlspecialchars($projet['visite_virtuelle_url'] ?? '') ?>" placeholder="https://" class="w-full px-4 py-2 border border-grayBorder rounded-lg focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>

            <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200">
                <h2 class="text-xl font-bold mb-4 flex items-center gap-2 text-dark border-b pb-3">
                    <i class="fa-solid fa-images text-primary"></i> Galerie Photos
                </h2>
                
                <div id="photo-dropzone" 
                    class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:bg-gray-100 transition-colors relative cursor-pointer" 
                    onclick="document.getElementById('photo-upload').click()">
                    <i class="fa-solid fa-cloud-arrow-up text-4xl text-gray-400 mb-3 pointer-events-none"></i>
                    <h3 class="font-bold text-dark pointer-events-none">Cliquez ou glissez vos nouvelles photos ici</h3>
                    <input type="file" id="photo-upload" multiple accept="image/*" class="hidden" onchange="handlePhotoSelect(event)">
                </div>

                <ul id="photos-list" class="space-y-3 mt-6"></ul>
            </div>
            
        </form>
    </main>

<script>
    // 1. Initialisation Quill pour la description
    var quill = new Quill('#editor-container', {
        theme: 'snow',
        modules: { toolbar: [ ['bold', 'italic', 'underline', 'strike'], [{'list':'ordered'}, {'list':'bullet'}], [{'header':[1,2,3,false]}], ['link','clean'] ] }
    });
    
    // 2. Initialisation Carte Leaflet (Copie exacte du comportement de "Publier")
    var lat = <?= !empty($projet['latitude']) ? $projet['latitude'] : 46.603354 ?>;
    var lng = <?= !empty($projet['longitude']) ? $projet['longitude'] : 1.888334 ?>;
    var zoom = <?= !empty($projet['latitude']) ? 17 : 5 ?>;
    
    var map = L.map('map', {zoomControl: false}).setView([lat, lng], zoom);
    L.control.zoom({ position: 'bottomright' }).addTo(map);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

    const addrInput = document.getElementById('adresse-input');
    const cpInput = document.getElementById('cp-input');
    const villeInput = document.getElementById('ville-input');
    const latInput = document.getElementById('lat-input');
    const lngInput = document.getElementById('lng-input');
    let typingTimer;
    let isMapMovingByUser = true;

    // 1. Quand on tape manuellement dans les champs de texte
    function searchAddress() {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => {
            let query = [addrInput.value, cpInput.value, villeInput.value].filter(Boolean).join(' ').trim();
            if(query.length > 4) {
                fetch(`https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(query)}&limit=1`)
                .then(res => res.json())
                .then(data => {
                    if(data.features && data.features.length > 0) {
                        let coords = data.features[0].geometry.coordinates;
                        let props = data.features[0].properties;
                        
                        isMapMovingByUser = false; 
                        map.flyTo([coords[1], coords[0]], 17, {duration: 1.5});
                        
                        cpInput.value = props.postcode || cpInput.value;
                        villeInput.value = props.city || villeInput.value;
                        latInput.value = coords[1];
                        lngInput.value = coords[0];
                    }
                });
            }
        }, 800);
    }

    addrInput.addEventListener('keyup', searchAddress);
    cpInput.addEventListener('keyup', searchAddress);
    villeInput.addEventListener('keyup', searchAddress);

    // 2. Quand on déplace visuellement la carte sous le pin orange !
    map.on('moveend', function() {
        if(!isMapMovingByUser) { isMapMovingByUser = true; return; }
        var center = map.getCenter();
        fetch(`https://api-adresse.data.gouv.fr/reverse/?lon=${center.lng}&lat=${center.lat}`)
        .then(res => res.json())
        .then(data => {
            if(data.features && data.features.length > 0) {
                let props = data.features[0].properties;
                addrInput.value = props.name || '';
                cpInput.value = props.postcode || '';
                villeInput.value = props.city || '';
                latInput.value = center.lat;
                lngInput.value = center.lng;
            }
        });
    });

    // Toggle Loft
    document.getElementById('type_bien').addEventListener('change', function() {
        document.getElementById('bloc_ancien_usage').classList.toggle('hidden', this.value !== 'Loft');
    });

    // Toggle surfaces exterieures et annexes
    document.querySelectorAll('.ext-trigger').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const containerSurface = this.closest('div').querySelector('.ext-surface');
            if(containerSurface) {
                if(this.checked) {
                    containerSurface.classList.remove('hidden');
                    containerSurface.querySelector('input').focus();
                } else {
                    containerSurface.classList.add('hidden');
                    containerSurface.querySelector('input').value = '';
                }
            }
        });
    });

    // 3. Gestion Galerie Photos
    let photosData = [];
    <?php foreach ($existingPhotos as $photo): ?>
    photosData.push({
        id: 'ex_<?= $photo['id'] ?>', db_id: <?= $photo['id'] ?>,
        url: '../<?= addslashes($photo['chemin_photo']) ?>',
        tag: '<?= addslashes($photo['tag']) === "Galerie" ? "" : addslashes($photo['tag']) ?>', 
        isExisting: true, hasError: false
    });
    <?php endforeach; ?>

    function renderPhotos() {
        const list = document.getElementById('photos-list');
        list.innerHTML = '';
        const tagsPredefinis = ["Façade", "Salon", "Séjour", "Cuisine", "Chambre", "Salle de bain", "Jardin", "Terrasse", "Piscine", "Garage", "Plan", "Autre"];

        photosData.forEach((p, index) => {
            let options = `<option value="" disabled ${p.tag === '' ? 'selected' : ''}>Choisir un tag...</option>`;
            options += tagsPredefinis.map(t => `<option value="${t}" ${p.tag === t ? 'selected' : ''}>${t}</option>`).join('');
            let borderClass = p.hasError ? 'border-red-500 bg-red-50' : 'border-grayBorder bg-white';

            list.innerHTML += `
                <li data-id="${p.id}" class="photo-item flex flex-col group">
                    <div class="flex items-center gap-3 p-3 border ${borderClass} rounded-xl shadow-sm cursor-grab hover:border-primary">
                        <i class="fa-solid fa-grip-vertical text-gray-300"></i>
                        <div class="w-6 text-center font-bold text-gray-400 text-sm">${index === 0 ? '★' : index}</div>
                        <img src="${p.url}" class="w-16 h-16 rounded object-cover border">
                        <div class="flex-grow">
                            <select id="select-${p.id}" onchange="updatePhotoTag('${p.id}', this.value)" class="w-full text-sm p-2 border rounded bg-gray-50 outline-none">${options}</select>
                        </div>
                        <button type="button" onclick="removePhoto('${p.id}')" class="w-10 h-10 bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white"><i class="fa-solid fa-trash"></i></button>
                    </div>
                </li>`;
        });
    }

    function handlePhotoSelect(e) { 
        Array.from(e.target.files).forEach(file => {
            photosData.push({ id: 'new_' + Date.now() + '_' + Math.random(), file: file, url: URL.createObjectURL(file), tag: '', isExisting: false, hasError: false });
        });
        e.target.value = '';
        renderPhotos(); 
    }

    function updatePhotoTag(id, val) { 
        let photo = photosData.find(p => p.id === id);
        if(photo) { photo.tag = val; photo.hasError = false; renderPhotos(); }
    }

    function removePhoto(id) { photosData = photosData.filter(p => p.id !== id); renderPhotos(); }

    document.addEventListener('DOMContentLoaded', () => {
        renderPhotos();
        Sortable.create(document.getElementById('photos-list'), { 
            animation: 150, handle: '.cursor-grab',
            onEnd: function() {
                const newOrderIds = Array.from(document.getElementById('photos-list').children).map(li => li.getAttribute('data-id'));
                photosData = newOrderIds.map(id => photosData.find(p => p.id === id)).filter(Boolean);
                renderPhotos(); 
            }
        });
    });

    // 4. Soumission AJAX
    document.getElementById('modifier-form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        // Mettre à jour description cachée
        document.getElementById('hidden-description').value = quill.root.innerHTML;

        // Récupérer les chauffages cochés
        let chauf = [];
        document.querySelectorAll('.chk-chauffage:checked').forEach(c => chauf.push(c.value));
        document.getElementById('type_chauffage_hidden').value = chauf.join(', ');

        const errorDiv = document.getElementById('submit-error');
        errorDiv.classList.add('hidden');

        let isValid = true;
        photosData.forEach(p => { if (!p.tag) { isValid = false; p.hasError = true; } });
        
        if (!isValid) {
            renderPhotos();
            errorDiv.innerText = "Veuillez définir un tag pour toutes les photos.";
            errorDiv.classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
            return;
        }

        let fd = new FormData(this);
        fd.append('ajax_submit', '1');
        
        photosData.forEach((p, index) => {
            fd.append('photo_order[]', p.isExisting ? p.db_id : p.id);
            fd.append('photo_tags[]', p.tag);
            fd.append('photo_is_existing[]', p.isExisting ? 1 : 0);
            if (p.isExisting) fd.append('existing_ids[]', p.db_id);
            else fd.append('new_photos[]', p.file);
        });

        fetch('modifier.php?id=<?= $id_projet ?>', { method: 'POST', body: fd })
        .then(res => res.json())
        .then(data => {
            if(data.success) { window.location.href = data.redirect; } 
            else {
                errorDiv.innerText = 'Erreur serveur : ' + data.error;
                errorDiv.classList.remove('hidden');
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }).catch(err => {
            errorDiv.innerText = 'Erreur réseau.';
            errorDiv.classList.remove('hidden');
            window.scrollTo({ top: 0, behavior: 'smooth' });
        });
    });
</script>

</body>
</html>