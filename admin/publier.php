<?php
session_start();
require '../includes/db.php';

// Vérification de sécurité
if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: ../connexion.php");
    exit();
}

// -------------------------------------------------------------
// TRAITEMENT AJAX DE LA SOUMISSION DU FORMULAIRE COMPLET
// -------------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ajax_submit'])) {
    header('Content-Type: application/json');
    
    try {
        $pdo->beginTransaction();

        $titre = htmlspecialchars($_POST['titre']);
        $description = htmlspecialchars($_POST['description']);
        $ville = htmlspecialchars($_POST['ville']);
        $adresse_complete = htmlspecialchars($_POST['adresse_complete']);
        $code_postal = htmlspecialchars($_POST['code_postal']);
        $latitude = !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null;
        $longitude = !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null;
        
        $surface = (int)$_POST['surface'];
        $pieces = (int)$_POST['pieces'];
        $chambres = (int)$_POST['chambres'];
        $exterieur = htmlspecialchars($_POST['exterieur']);
        $etat_bien = htmlspecialchars($_POST['etat_bien']);
        $avancement = (int)$_POST['avancement'];
        $statut_commercial = htmlspecialchars($_POST['statut_commercial'] ?? 'Disponible');
        $id_utilisateur = $_SESSION['utilisateur_id'];
        
        // Nouveaux champs de contact
        $contact_prenom = htmlspecialchars($_POST['contact_prenom']);
        $contact_nom = htmlspecialchars($_POST['contact_nom']);
        $contact_telephone = htmlspecialchars($_POST['contact_telephone']);
        
        $titre = htmlspecialchars($_POST['titre'] ?? '');

        // --- GÉNÉRATION DU SLUG ---
        function creerSlug($chaine) {
            $chaine = preg_replace('~[^\pL\d]+~u', '-', $chaine);
            $chaine = iconv('utf-8', 'us-ascii//TRANSLIT', $chaine);
            $chaine = preg_replace('~[^-\w]+~', '', $chaine);
            $chaine = trim($chaine, '-');
            $chaine = preg_replace('~-+~', '-', $chaine);
            return strtolower($chaine);
        }

        $slug_base = creerSlug($titre);
        $slug = $slug_base;
        $compteur = 1;
        while (true) {
            $stmtCheck = $pdo->prepare("SELECT id FROM projets WHERE slug = ?");
            $stmtCheck->execute([$slug]);
            if (!$stmtCheck->fetch()) break;
            $slug = $slug_base . '-' . $compteur;
            $compteur++;
        }

        // 1. CRÉATION DU PROJET
        $stmtProj = $pdo->prepare("INSERT INTO projets (
            slug, id_utilisateur, contact_prenom, contact_nom, contact_telephone, contact_email,
            titre, type_bien, ancien_usage, description, etat_bien, avancement, dispositif_fiscal, frais_notaire,
            adresse_complete, code_postal, ville, latitude, longitude, prix_m2_secteur,
            surface, surface_carrez, pieces, chambres, salles_de_bain, wc, etage, nombre_etages,
            ext_jardin, surf_jardin, ext_terrasse, surf_terrasse, ext_balcon, surf_balcon, ext_loggia, surf_loggia,
            has_parking, parking_places, has_garage, garage_places, cave, ascenseur, acces_pmr,
            type_chauffage, dpe, ges
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmtProj->execute([
            $slug, $id_utilisateur, $_POST['contact_prenom'] ?? '', $_POST['contact_nom'] ?? '', $_POST['contact_telephone'] ?? '', $_POST['contact_email'] ?? '',
            $_POST['titre'] ?? '', $_POST['type_bien'] ?? 'Appartement', $_POST['ancien_usage'] ?? '', $_POST['description'] ?? '', $_POST['etat_bien'] ?? '', (int)($_POST['avancement'] ?? 0), $_POST['dispositif_fiscal'] ?? '', $_POST['frais_notaire'] ?? '',
            $_POST['adresse_complete'] ?? '', $_POST['code_postal'] ?? '', $_POST['ville'] ?? '', !empty($_POST['latitude']) ? (float)$_POST['latitude'] : null, !empty($_POST['longitude']) ? (float)$_POST['longitude'] : null, !empty($_POST['prix_m2_secteur']) ? (int)$_POST['prix_m2_secteur'] : null,
            (float)($_POST['surface'] ?? 0), !empty($_POST['surface_carrez']) ? (float)$_POST['surface_carrez'] : null, (int)($_POST['pieces'] ?? 1), (int)($_POST['chambres'] ?? 0), (int)($_POST['salles_de_bain'] ?? 0), (int)($_POST['wc'] ?? 0), $_POST['etage'] ?? null, (int)($_POST['nombre_etages'] ?? 0),
            (int)($_POST['ext_jardin'] ?? 0), (int)($_POST['surf_jardin'] ?? 0), (int)($_POST['ext_terrasse'] ?? 0), (int)($_POST['surf_terrasse'] ?? 0), (int)($_POST['ext_balcon'] ?? 0), (int)($_POST['surf_balcon'] ?? 0), (int)($_POST['ext_loggia'] ?? 0), (int)($_POST['surf_loggia'] ?? 0),
            (int)($_POST['has_parking'] ?? 0), (int)($_POST['parking_places'] ?? 0), (int)($_POST['has_garage'] ?? 0), (int)($_POST['garage_places'] ?? 0), (int)($_POST['cave'] ?? 0), (int)($_POST['ascenseur'] ?? 0), (int)($_POST['acces_pmr'] ?? 0),
            $_POST['type_chauffage'] ?? '', $_POST['dpe'] ?? null, $_POST['ges'] ?? null
        ]);
        $id_projet = $pdo->lastInsertId();

        // 1.Bis ENREGISTREMENT DES LOTS (IMMEUBLE)
        if (($_POST['type_bien'] ?? '') === 'Immeuble' && isset($_POST['lot_nom'])) {
            $stmtLot = $pdo->prepare("INSERT INTO projet_logements (id_projet, nom_lot, type_logement, etage, prix, dispositif_fiscal, frais_notaire, surface, surface_carrez, pieces, chambres, salles_de_bain, wc, dpe, ges, cave, parking_places, garage_places, ext_balcon, surf_balcon, ext_terrasse, surf_terrasse, ext_jardin, surf_jardin, ext_loggia, surf_loggia, type_chauffage) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            
            foreach ($_POST['lot_nom'] as $i => $nom) {
                $stmtLot->execute([
                    $id_projet, 
                    htmlspecialchars($nom), 
                    htmlspecialchars($_POST['lot_type'][$i]), 
                    htmlspecialchars($_POST['lot_etage'][$i] ?? ''), 
                    !empty($_POST['lot_prix'][$i]) ? (int)$_POST['lot_prix'][$i] : null,
                    htmlspecialchars($_POST['lot_fiscal'][$i] ?? ''),
                    htmlspecialchars($_POST['lot_notaire'][$i] ?? ''),
                    (float)($_POST['lot_surf'][$i] ?? 0), 
                    (float)($_POST['lot_carrez'][$i] ?? 0), 
                    (int)($_POST['lot_pieces'][$i] ?? 1),
                    (int)($_POST['lot_chambres'][$i] ?? 0),
                    (int)($_POST['lot_sdb'][$i] ?? 0),
                    (int)($_POST['lot_wc'][$i] ?? 0),
                    htmlspecialchars($_POST['lot_dpe'][$i] ?? ''),
                    htmlspecialchars($_POST['lot_ges'][$i] ?? ''),
                    (int)($_POST['lot_cave'][$i] ?? 0),
                    (int)($_POST['lot_parking'][$i] ?? 0),
                    (int)($_POST['lot_garage'][$i] ?? 0),
                    (int)($_POST['lot_ext_balcon'][$i] ?? 0),
                    (int)($_POST['lot_surf_balcon'][$i] ?? 0),
                    (int)($_POST['lot_ext_terrasse'][$i] ?? 0),
                    (int)($_POST['lot_surf_terrasse'][$i] ?? 0),
                    (int)($_POST['lot_ext_jardin'][$i] ?? 0),
                    (int)($_POST['lot_surf_jardin'][$i] ?? 0),
                    (int)($_POST['lot_ext_loggia'][$i] ?? 0),
                    (int)($_POST['lot_surf_loggia'][$i] ?? 0),
                    htmlspecialchars($_POST['lot_chauffage'][$i] ?? '')
                ]);
            }
        }

        // 2. ENREGISTREMENT DES PHOTOS
        if (isset($_FILES['photos'])) {
            $stmtPhoto = $pdo->prepare("INSERT INTO projet_photos (id_projet, chemin_photo, tag, ordre) VALUES (?, ?, ?, ?)");
            foreach ($_FILES['photos']['tmp_name'] as $index => $tmp_name) {
                if ($_FILES['photos']['error'][$index] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($_FILES['photos']['name'][$index], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp'])) {
                        $new_name = uniqid('photo_') . '.' . $ext;
                        $dest = "../uploads/" . $new_name;
                        if (move_uploaded_file($tmp_name, $dest)) {
                            $tag = htmlspecialchars($_POST['photo_tags'][$index] ?? 'Galerie');
                            $stmtPhoto->execute([$id_projet, "uploads/" . $new_name, $tag, $index]);
                        }
                    }
                }
            }
        }

        // 3. ENREGISTREMENT DES ÉTAPES
        if (isset($_POST['etape_titre'])) {
            $stmtEtape = $pdo->prepare("INSERT INTO projet_etapes (id_projet, titre, statut, prix, prix_m2, date_dispo, description, inclus, non_inclus, ordre) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            foreach ($_POST['etape_titre'] as $index => $titre_etape) {
                $statut = htmlspecialchars($_POST['etape_statut'][$index]);
                $prix = (int)$_POST['etape_prix'][$index];
                $prix_m2 = (int)$_POST['etape_prix_m2'][$index]; // <-- NOUVEAU
                $date_dispo = htmlspecialchars($_POST['etape_date'][$index]);
                $desc = htmlspecialchars($_POST['etape_desc'][$index]);
                $inclus = htmlspecialchars($_POST['etape_inclus'][$index]);
                $non_inclus = htmlspecialchars($_POST['etape_noninclus'][$index]);
                $stmtEtape->execute([$id_projet, htmlspecialchars($titre_etape), $statut, $prix, $prix_m2, $date_dispo, $desc, $inclus, $non_inclus, $index]);
            }
        }

        // 4. VR & PLANS
        if (!empty($_POST['visite_vr'])) {
            $pdo->prepare("UPDATE projets SET visite_virtuelle_url = ? WHERE id = ?")->execute([htmlspecialchars($_POST['visite_vr']), $id_projet]);
        }

        if (isset($_FILES['plans'])) {
            $stmtPlan = $pdo->prepare("INSERT INTO projet_plans (id_projet, titre, fichier_url, taille_ko, ordre) VALUES (?, ?, ?, ?, ?)");
            foreach ($_FILES['plans']['tmp_name'] as $index => $tmp_name) {
                if ($_FILES['plans']['error'][$index] === UPLOAD_ERR_OK) {
                    $ext = strtolower(pathinfo($_FILES['plans']['name'][$index], PATHINFO_EXTENSION));
                    if ($ext === 'pdf') {
                        $new_name = uniqid('plan_') . '.pdf';
                        $dest = "../uploads/" . $new_name;
                        if (move_uploaded_file($tmp_name, $dest)) {
                            $titre_plan = htmlspecialchars($_POST['plan_titres'][$index] ?? 'Plan');
                            $taille_ko = round($_FILES['plans']['size'][$index] / 1024);
                            $stmtPlan->execute([$id_projet, $titre_plan, "uploads/" . $new_name, $taille_ko, $index]);
                        }
                    }
                }
            }
        }

        $pdo->commit();
        echo json_encode(['success' => true, 'redirect' => "../detail.php?id=" . $id_projet]);
        exit();

    } catch (Exception $e) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit();
    }
}

// === NOUVEAU ===
// Récupération des données du compte utilisateur pour préremplir l'étape 1
$stmtUser = $pdo->prepare("SELECT prenom, nom, telephone, email FROM utilisateurs WHERE id = ?");
$stmtUser->execute([$_SESSION['utilisateur_id']]);
$utilisateur = $stmtUser->fetch(PDO::FETCH_ASSOC);
// =================
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un projet - AvenirImmo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>tailwind.config = { theme: { extend: { colors: { primary: '#F97316', primaryHover: '#EA580C', dark: '#111827', grayBorder: '#E5E7EB' } } } }</script>
    <style>
        .form-step { display: none; }
        .form-step.active { display: block; animation: fadeIn 0.3s ease-in-out; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
    </style>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen pb-20">

    <?php $base = '../'; include '../includes/header.php'; ?>

    <main class="flex-grow py-8 px-4 sm:px-6 lg:px-8 max-w-5xl mx-auto w-full">
        
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-dark mb-2">Publier un nouveau bien</h1>
            <p class="text-gray-500">Suivez les 4 étapes pour créer un projet complet et détaillé.</p>
        </div>

        <div class="mb-12 px-4 max-w-3xl mx-auto relative">
            <div class="absolute top-5 left-[12.5%] right-[12.5%] h-1 bg-gray-200 z-0 rounded-full"></div>
            <div id="progress-bar" class="absolute top-5 left-[12.5%] h-1 bg-primary z-0 transition-all duration-300 rounded-full" style="width: 0%;"></div>
        
            <div class="relative flex justify-between items-start z-10">
                <div class="flex flex-col items-center w-1/4">
                    <div class="step-indicator w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-bold text-sm shadow-md transition-colors"><i class="fa-solid fa-info"></i></div>
                    <span class="text-xs font-medium text-gray-500 mt-2 text-center">Informations</span>
                </div>
                <div class="flex flex-col items-center w-1/4">
                    <div class="step-indicator w-10 h-10 rounded-full bg-white border-2 border-grayBorder text-gray-400 flex items-center justify-center font-bold text-sm transition-colors"><i class="fa-solid fa-images"></i></div>
                    <span class="text-xs font-medium text-gray-500 mt-2 text-center">Photos</span>
                </div>
                <div class="flex flex-col items-center w-1/4">
                    <div class="step-indicator w-10 h-10 rounded-full bg-white border-2 border-grayBorder text-gray-400 flex items-center justify-center font-bold text-sm transition-colors"><i class="fa-solid fa-list-check"></i></div>
                    <span class="text-xs font-medium text-gray-500 mt-2 text-center">Étapes</span>
                </div>
                <div class="flex flex-col items-center w-1/4">
                    <div class="step-indicator w-10 h-10 rounded-full bg-white border-2 border-grayBorder text-gray-400 flex items-center justify-center font-bold text-sm transition-colors"><i class="fa-solid fa-vr-cardboard"></i></div>
                    <span class="text-xs font-medium text-gray-500 mt-2 text-center">Médias 3D</span>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-card border border-grayBorder overflow-hidden p-6 sm:p-8">
            <form id="wizard-form">
                
                <?php include 'publier/step1_infos.php'; ?>
                <?php include 'publier/step2_photos.php'; ?>
                <?php include 'publier/step3_etapes.php'; ?>
                <?php include 'publier/step4_medias.php'; ?>

                <div class="pt-8 border-t border-grayBorder mt-8 flex justify-between">
                    <button type="button" id="btn-prev" class="hidden px-6 py-3 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Retour
                    </button>
                    <button type="button" id="btn-next" class="ml-auto px-8 py-3 bg-dark text-white font-bold rounded-xl hover:bg-black transition-colors shadow-sm">
                        Suivant <i class="fa-solid fa-arrow-right ml-2"></i>
                    </button>
                    <button type="submit" id="btn-submit" class="hidden ml-auto px-8 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primaryHover transition-colors shadow-lg flex items-center gap-2">
                        <i class="fa-solid fa-cloud-arrow-up"></i> Mettre en ligne
                    </button>
                </div>

            </form>
        </div>
    </main>

    <?php include 'publier/scripts.php'; ?>

</body>
</html>