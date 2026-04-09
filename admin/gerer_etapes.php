<?php
session_start();
require '../includes/db.php';

// Vérification de sécurité
if (!isset($_SESSION['utilisateur_id']) || !isset($_GET['id'])) {
    header("Location: ../projets.php");
    exit();
}

$id_projet = (int)$_GET['id'];
$id_utilisateur = $_SESSION['utilisateur_id'];

// Vérification du propriétaire et récupération de la surface pour les calculs
$stmt = $pdo->prepare("SELECT id, titre, slug, surface FROM projets WHERE id = ? AND id_utilisateur = ?");
$stmt->execute([$id_projet, $id_utilisateur]);
$projet = $stmt->fetch();

if (!$projet) {
    header("Location: ../projets.php");
    exit();
}

// ---------------------------------------------------------
// 1. SUPPRESSION D'UNE ÉTAPE
// ---------------------------------------------------------
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['etape_id'])) {
    $delStmt = $pdo->prepare("DELETE FROM projet_etapes WHERE id = ? AND id_projet = ?");
    $delStmt->execute([(int)$_GET['etape_id'], $id_projet]);
    header("Location: gerer_etapes.php?id=" . $id_projet . "&success=deleted");
    exit();
}

// ---------------------------------------------------------
// 2. GESTION DES FORMULAIRES POST (Ajout, Modif, Ordre)
// ---------------------------------------------------------
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    // -- AJOUT --
    if (isset($_POST['action']) && $_POST['action'] == 'add') {
        $titre = htmlspecialchars($_POST['titre']);
        $statut = htmlspecialchars($_POST['statut']);
        // Nettoyage des espaces ajoutés par le JS pour l'affichage
        $prix = (int)str_replace(' ', '', $_POST['prix']);
        $prix_m2 = (int)str_replace(' ', '', $_POST['prix_m2'] ?? 0);
        $date_dispo = htmlspecialchars($_POST['date_dispo']);
        $description = htmlspecialchars($_POST['description']);
        $inclus = htmlspecialchars($_POST['inclus']);
        $non_inclus = htmlspecialchars($_POST['non_inclus']);

        // On le met à la fin de l'ordre actuel
        $stmtOrdre = $pdo->prepare("SELECT MAX(ordre) FROM projet_etapes WHERE id_projet = ?");
        $stmtOrdre->execute([$id_projet]);
        $maxOrdre = (int)$stmtOrdre->fetchColumn();

        $insert = $pdo->prepare("INSERT INTO projet_etapes (id_projet, titre, statut, prix, prix_m2, date_dispo, description, inclus, non_inclus, ordre) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $insert->execute([$id_projet, $titre, $statut, $prix, $prix_m2, $date_dispo, $description, $inclus, $non_inclus, $maxOrdre + 1]);
        
        header("Location: gerer_etapes.php?id=" . $id_projet . "&success=added");
        exit();
    }
    
    // -- MODIFICATION --
    if (isset($_POST['action']) && $_POST['action'] == 'edit') {
        $id_etape = (int)$_POST['etape_id'];
        $titre = htmlspecialchars($_POST['titre']);
        $statut = htmlspecialchars($_POST['statut']);
        $prix = (int)str_replace(' ', '', $_POST['prix']);
        $prix_m2 = (int)str_replace(' ', '', $_POST['prix_m2'] ?? 0);
        $date_dispo = htmlspecialchars($_POST['date_dispo']);
        $description = htmlspecialchars($_POST['description']);
        $inclus = htmlspecialchars($_POST['inclus']);
        $non_inclus = htmlspecialchars($_POST['non_inclus']);

        $update = $pdo->prepare("UPDATE projet_etapes SET titre=?, statut=?, prix=?, prix_m2=?, date_dispo=?, description=?, inclus=?, non_inclus=? WHERE id=? AND id_projet=?");
        $update->execute([$titre, $statut, $prix, $prix_m2, $date_dispo, $description, $inclus, $non_inclus, $id_etape, $id_projet]);
        
        header("Location: gerer_etapes.php?id=" . $id_projet . "&success=edited");
        exit();
    }

    // -- SAUVEGARDE DE L'ORDRE --
    if (isset($_POST['action']) && $_POST['action'] == 'reorder') {
        if(isset($_POST['etape_ordre']) && is_array($_POST['etape_ordre'])) {
            foreach($_POST['etape_ordre'] as $index => $id_etape) {
                $update = $pdo->prepare("UPDATE projet_etapes SET ordre = ? WHERE id = ? AND id_projet = ?");
                $update->execute([$index, (int)$id_etape, $id_projet]);
            }
        }
        // Redirection vers la page publique du projet via son slug
        header("Location: ../" . $projet['slug']);
        exit();
    }
}

// Récupération des étapes existantes triées par ORDRE
$etapes = $pdo->prepare("SELECT * FROM projet_etapes WHERE id_projet = ? ORDER BY ordre ASC, id ASC");
$etapes->execute([$id_projet]);
$liste_etapes = $etapes->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
    <title>Gérer les étapes - AvenirImmo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        tailwind.config = { theme: { extend: { colors: { primary: '#F97316', primaryHover: '#EA580C', dark: '#111827', grayBorder: '#E5E7EB' } } } }
    </script>
</head>
<body class="bg-gray-50 min-h-screen pb-20">
    <div class="max-w-5xl mx-auto py-6 sm:py-10 px-4">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-grayBorder">
            <div>
                <a href="../<?= htmlspecialchars($projet['slug']) ?>" class="text-sm text-gray-500 hover:text-primary mb-2 inline-block"><i class="fa-solid fa-arrow-left mr-2"></i>Retour au projet</a>
                <h1 class="text-xl sm:text-2xl font-bold text-dark">Étapes de : <span class="text-primary"><?= htmlspecialchars($projet['titre']) ?></span></h1>
            </div>
            <button onclick="document.getElementById('form-ajout').classList.toggle('hidden')" class="w-full sm:w-auto bg-dark hover:bg-black text-white px-5 py-2.5 rounded-lg font-medium transition-colors text-center shadow-sm">
                <i class="fa-solid fa-plus mr-2"></i> Ajouter une étape
            </button>
        </div>

        <?php if(isset($_GET['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center font-medium">
                <i class="fa-solid fa-circle-check mr-2"></i>
                <?php 
                    if($_GET['success'] == 'added') echo "L'étape a été ajoutée avec succès.";
                    if($_GET['success'] == 'edited') echo "L'étape a été modifiée avec succès.";
                    if($_GET['success'] == 'deleted') echo "L'étape a été supprimée.";
                    if($_GET['success'] == 'reordered') echo "L'ordre a été sauvegardé avec succès.";
                ?>
            </div>
        <?php endif; ?>

        <div id="form-ajout" class="hidden bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-primary/20 mb-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-primary/5 rounded-bl-full -z-10"></div>
            <h2 class="text-lg sm:text-xl font-bold mb-6 text-dark border-b pb-3"><i class="fa-solid fa-layer-group text-primary mr-2"></i>Nouvelle étape</h2>
            
            <form method="POST" action="gerer_etapes.php?id=<?= $id_projet ?>">
                <input type="hidden" name="action" value="add">
                
                <input type="hidden" id="project_surface" value="<?= (float)$projet['surface'] ?>">

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5 mb-5 form-grid-calc">
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium mb-1">Titre de l'étape <span class="text-red-500">*</span></label>
                        <input type="text" name="titre" required class="w-full border border-grayBorder rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary/50 outline-none" placeholder="Ex: Clos Couvert">
                    </div>
                    <div class="lg:col-span-1">
                        <label class="block text-sm font-medium mb-1">Statut</label>
                        <select name="statut" required class="w-full border border-grayBorder rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary/50 outline-none bg-white">
                            <option value="Actuel">Actuel</option>
                            <option value="Futur">Futur</option>
                            <option value="Terminé">Terminé</option>
                        </select>
                    </div>
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium mb-1">Date dispo</label>
                        <input type="text" name="date_dispo" class="w-full border border-grayBorder rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary/50 outline-none" placeholder="Ex: T3 2025">
                    </div>
                    
                    <div class="lg:col-span-2 md:col-start-1">
                        <label class="block text-sm font-medium mb-1">Prix de l'étape (€) <span class="text-red-500">*</span></label>
                        <input type="text" name="prix" required class="e-prix font-bold text-dark w-full border border-grayBorder rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary/50 outline-none" placeholder="Ex: 250 000" oninput="formatPrice(this); calcPrixM2(this)">
                    </div>
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium mb-1 text-gray-500">Prix au m² (€/m²) <i class="fa-solid fa-calculator ml-1"></i></label>
                        <input type="text" name="prix_m2" class="e-prix-m2 w-full border border-gray-200 bg-gray-50 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary/50 outline-none text-gray-600" placeholder="Ex: 3 500" oninput="formatPrice(this); calcPrixTotal(this)">
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium mb-1">Description courte</label>
                    <textarea name="description" rows="2" class="w-full border border-grayBorder rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary/50 outline-none"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                    <div>
                        <label class="block text-sm font-medium mb-1">Inclus <span class="text-xs text-gray-500">(1 élément par ligne)</span></label>
                        <textarea name="inclus" rows="4" class="w-full border border-grayBorder rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary/50 outline-none bg-green-50/30" placeholder="Ex:\nMurs extérieurs\nToiture neuve"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Non Inclus / Reste à charge <span class="text-xs text-gray-500">(1 par ligne)</span></label>
                        <textarea name="non_inclus" rows="4" class="w-full border border-grayBorder rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary/50 outline-none bg-red-50/30" placeholder="Ex:\nPeinture intérieure\nCuisine"></textarea>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-gray-100">
                    <button type="button" onclick="document.getElementById('form-ajout').classList.add('hidden')" class="w-full sm:w-auto px-5 py-2.5 border border-grayBorder rounded-lg hover:bg-gray-50 transition-colors font-medium text-gray-700">Annuler</button>
                    <button type="submit" class="w-full sm:w-auto bg-primary hover:bg-primaryHover text-white px-6 py-2.5 rounded-lg font-bold transition-colors shadow-sm flex items-center justify-center gap-2"><i class="fa-solid fa-check"></i> Créer l'étape</button>
                </div>
            </form>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-grayBorder">
            <h2 class="text-lg sm:text-xl font-bold mb-2">Ordre des étapes</h2>
            <p class="text-sm text-gray-500 mb-6"><i class="fa-solid fa-circle-info mr-1 text-primary"></i> Glissez-déposez les blocs pour changer l'ordre d'affichage de vos tarifs.</p>

            <?php if(count($liste_etapes) == 0): ?>
                <div class="text-center py-10 text-gray-500 bg-gray-50 rounded-xl border border-dashed border-gray-300">
                    <i class="fa-solid fa-list-ul text-3xl mb-3 text-gray-300"></i><br>
                    Aucune étape pour ce projet. Ajoutez-en une !
                </div>
            <?php else: ?>
                <form id="form-reorganisation" method="POST" action="gerer_etapes.php?id=<?= $id_projet ?>">
                    <input type="hidden" name="action" value="reorder">
                    
                    <ul id="etapes-list" class="space-y-4">
                        <?php foreach($liste_etapes as $index => $etape): ?>
                            <li class="bg-white border border-grayBorder rounded-xl p-3 sm:p-4 shadow-sm flex items-center gap-3 sm:gap-4 cursor-grab active:cursor-grabbing group hover:border-primary/50 hover:shadow-md transition-all text-left">
    
                                <input type="hidden" name="etape_ordre[]" value="<?= $etape['id'] ?>">
                                
                                <div class="text-gray-300 group-hover:text-primary transition-colors px-1 sm:px-2 flex-shrink-0">
                                    <i class="fa-solid fa-grip-vertical text-lg"></i>
                                </div>
                                
                                <div class="etape-number w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-orange-50 text-primary flex items-center justify-center font-bold flex-shrink-0 text-sm sm:text-base border border-primary/20">
                                    <?= $index + 1 ?>
                                </div>
                                
                                <div class="flex-grow overflow-hidden">
                                    <div class="flex items-center gap-2 mb-1">
                                        <h4 class="font-bold text-dark text-sm sm:text-base truncate"><?= htmlspecialchars($etape['titre']) ?></h4>
                                        <span class="text-[9px] sm:text-[10px] uppercase font-bold px-1.5 py-0.5 rounded flex-shrink-0 <?= $etape['statut'] == 'Actuel' ? 'bg-orange-100 text-orange-700' : 'bg-gray-100 text-gray-600' ?>"><?= htmlspecialchars($etape['statut']) ?></span>
                                    </div>
                                    <p class="text-xs sm:text-sm text-gray-500 font-medium truncate">
                                        <span class="text-dark font-bold"><?= number_format($etape['prix'], 0, ',', ' ') ?> €</span> 
                                        <?= !empty($etape['prix_m2']) ? '<span class="mx-1 text-gray-300">•</span> ' . number_format($etape['prix_m2'], 0, ',', ' ') . ' €/m²' : '' ?>
                                        <span class="mx-1 text-gray-300">•</span> <?= htmlspecialchars($etape['date_dispo']) ?>
                                    </p>
                                </div>
                                
                                <div class="flex items-center gap-1 sm:gap-2 flex-shrink-0">
                                    <button type="button" onclick="openEditModal(<?= htmlspecialchars(json_encode($etape, JSON_HEX_APOS | JSON_HEX_QUOT), ENT_QUOTES, 'UTF-8') ?>)" class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-colors"><i class="fa-solid fa-pen text-xs sm:text-base"></i></button>
                                    <a href="gerer_etapes.php?id=<?= $id_projet ?>&action=delete&etape_id=<?= $etape['id'] ?>" onclick="return confirm('Voulez-vous vraiment supprimer l\'étape <?= htmlspecialchars(addslashes($etape['titre'])) ?> ?');" class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition-colors"><i class="fa-solid fa-trash text-xs sm:text-base"></i></a>
                                </div>
                            </li>
                        <?php endforeach; ?>
                    </ul>

                    <button type="submit" class="mt-8 w-full bg-dark text-white py-3.5 rounded-xl font-bold hover:bg-black transition-colors shadow-md flex items-center justify-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Sauvegarder le nouvel ordre
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div id="editModal" class="hidden fixed inset-0 bg-dark/70 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-3xl max-h-[90vh] overflow-y-auto">
            <div class="p-4 sm:p-6 border-b border-grayBorder flex justify-between items-center sticky top-0 bg-white z-10">
                <h2 class="text-lg sm:text-xl font-bold text-dark"><i class="fa-solid fa-pen text-blue-500 mr-2"></i>Modifier l'étape</h2>
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="text-gray-400 hover:text-dark w-8 h-8 rounded-full hover:bg-gray-100 flex items-center justify-center transition-colors">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            
            <form method="POST" action="gerer_etapes.php?id=<?= $id_projet ?>" class="p-4 sm:p-6">
                <input type="hidden" name="action" value="edit">
                <input type="hidden" name="etape_id" id="edit_id">
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-5 mb-5 form-grid-calc">
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium mb-1">Titre de l'étape <span class="text-red-500">*</span></label>
                        <input type="text" name="titre" id="edit_titre" required class="w-full border border-grayBorder rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary/50 outline-none">
                    </div>
                    <div class="lg:col-span-1">
                        <label class="block text-sm font-medium mb-1">Statut</label>
                        <select name="statut" id="edit_statut" required class="w-full border border-grayBorder rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary/50 outline-none bg-white">
                            <option value="Actuel">Actuel</option>
                            <option value="Futur">Futur</option>
                            <option value="Terminé">Terminé</option>
                        </select>
                    </div>
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium mb-1">Date de dispo</label>
                        <input type="text" name="date_dispo" id="edit_date_dispo" class="w-full border border-grayBorder rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary/50 outline-none">
                    </div>

                    <div class="lg:col-span-2 md:col-start-1">
                        <label class="block text-sm font-medium mb-1">Prix de l'étape (€) <span class="text-red-500">*</span></label>
                        <input type="text" name="prix" id="edit_prix" required class="e-prix font-bold text-dark w-full border border-grayBorder rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary/50 outline-none" oninput="formatPrice(this); calcPrixM2(this)">
                    </div>
                    <div class="lg:col-span-2">
                        <label class="block text-sm font-medium mb-1 text-gray-500">Prix au m² (€/m²) <i class="fa-solid fa-calculator ml-1"></i></label>
                        <input type="text" name="prix_m2" id="edit_prix_m2" class="e-prix-m2 w-full border border-gray-200 bg-gray-50 rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary/50 outline-none text-gray-600" oninput="formatPrice(this); calcPrixTotal(this)">
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-sm font-medium mb-1">Description</label>
                    <textarea name="description" id="edit_description" rows="2" class="w-full border border-grayBorder rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary/50 outline-none"></textarea>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">
                    <div>
                        <label class="block text-sm font-medium mb-1">Inclus</label>
                        <textarea name="inclus" id="edit_inclus" rows="4" class="w-full border border-grayBorder rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary/50 outline-none bg-green-50/30"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Non Inclus / Reste à charge</label>
                        <textarea name="non_inclus" id="edit_non_inclus" rows="4" class="w-full border border-grayBorder rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary/50 outline-none bg-red-50/30"></textarea>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-3 pt-4 border-t border-grayBorder">
                    <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="w-full sm:w-auto px-5 py-2.5 border border-grayBorder rounded-lg hover:bg-gray-50 transition-colors font-medium text-gray-700">Annuler</button>
                    <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-bold transition-colors shadow-sm flex items-center justify-center gap-2"><i class="fa-solid fa-save"></i> Enregistrer les modifications</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var el = document.getElementById('etapes-list');
            if(el) {
                Sortable.create(el, {
                    animation: 150,
                    ghostClass: 'opacity-50',
                    handle: 'li', 
                    onEnd: function () {
                        document.querySelectorAll('.etape-number').forEach((numEl, index) => {
                            numEl.innerText = index + 1;
                        });
                    }
                });
            }
        });

        // Formater les nombres avec des espaces (ex: 150 000)
        function formatPrice(input) {
            let val = input.value.replace(/\D/g, ''); 
            input.value = val.replace(/\B(?=(\d{3})+(?!\d))/g, " "); 
        }

        // Calculer le prix au m² à partir du prix global
        function calcPrixM2(prixInput) {
            let surf = parseFloat(document.getElementById('project_surface').value) || 0;
            let prix = parseInt(prixInput.value.replace(/\s/g, '')) || 0;
            let container = prixInput.closest('.form-grid-calc');
            let pm2Input = container.querySelector('.e-prix-m2');
            
            if(surf > 0 && pm2Input && prix > 0) {
                let pm2 = Math.round(prix / surf);
                pm2Input.value = pm2.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
            } else if (pm2Input && prix === 0) {
                pm2Input.value = '';
            }
        }

        // Calculer le prix global à partir du prix au m²
        function calcPrixTotal(pm2Input) {
            let surf = parseFloat(document.getElementById('project_surface').value) || 0;
            let pm2 = parseInt(pm2Input.value.replace(/\s/g, '')) || 0;
            let container = pm2Input.closest('.form-grid-calc');
            let prixInput = container.querySelector('.e-prix');
            
            if(surf > 0 && prixInput && pm2 > 0) {
                let prix = Math.round(pm2 * surf);
                prixInput.value = prix.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
            } else if (prixInput && pm2 === 0) {
                prixInput.value = '';
            }
        }

        function decodeHtml(html) {
            if (!html) return '';
            var txt = document.createElement("textarea");
            txt.innerHTML = html;
            return txt.value;
        }

        function openEditModal(etape) {
            document.getElementById('edit_id').value = etape.id;
            
            // On utilise decodeHtml() pour tous les champs texte
            document.getElementById('edit_titre').value = decodeHtml(etape.titre);
            document.getElementById('edit_statut').value = etape.statut;
            document.getElementById('edit_date_dispo').value = decodeHtml(etape.date_dispo);
            document.getElementById('edit_description').value = decodeHtml(etape.description);
            document.getElementById('edit_inclus').value = decodeHtml(etape.inclus);
            document.getElementById('edit_non_inclus').value = decodeHtml(etape.non_inclus);
            
            // On insère le prix en y ajoutant les espaces pour la lisibilité
            document.getElementById('edit_prix').value = (etape.prix || '').toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
            document.getElementById('edit_prix_m2').value = (etape.prix_m2 || '').toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
            
            document.getElementById('editModal').classList.remove('hidden');
        }
    </script>
</body>
</html>