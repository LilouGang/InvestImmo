<?php
session_start();
require '../includes/db.php';

if (!isset($_SESSION['utilisateur_id']) || !isset($_GET['id'])) {
    header("Location: ../projets.php");
    exit();
}

$id_projet = (int)$_GET['id'];
$id_utilisateur = $_SESSION['utilisateur_id'];

$stmt = $pdo->prepare("SELECT id, titre, slug, visite_virtuelle_url FROM projets WHERE id = ? AND id_utilisateur = ?");
$stmt->execute([$id_projet, $id_utilisateur]);
$projet = $stmt->fetch();

if (!$projet) {
    header("Location: ../projets.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    
    if (isset($_POST['action']) && $_POST['action'] == 'update_visite') {
        $url = htmlspecialchars($_POST['visite_url']);
        $updateStmt = $pdo->prepare("UPDATE projets SET visite_virtuelle_url = ? WHERE id = ?");
        $updateStmt->execute([$url, $id_projet]);
        header("Location: gerer_plansetvisite.php?id=" . $id_projet . "&success=visite_updated");
        exit();
    }

    if (isset($_POST['action']) && $_POST['action'] == 'add_plan' && isset($_FILES['plan_fichier'])) {
        $titre_plan = htmlspecialchars($_POST['titre_plan']);
        $file = $_FILES['plan_fichier'];
        
        if ($file['error'] == 0) {
            $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            if ($ext == 'pdf') {
                // On sépare le nom en base de données du chemin physique
                $chemin_bdd = 'uploads/plan_' . uniqid() . '.pdf';
                $dest = '../' . $chemin_bdd; // Le vrai chemin de destination vu qu'on est dans /admin
                
                if (move_uploaded_file($file['tmp_name'], $dest)) {
                    $taille_ko = round($file['size'] / 1024); 
                    $insert = $pdo->prepare("INSERT INTO projet_plans (id_projet, titre, fichier_url, taille_ko) VALUES (?, ?, ?, ?)");
                    // On enregistre proprement "uploads/plan_xxx.pdf" en BDD
                    $insert->execute([$id_projet, $titre_plan, $chemin_bdd, $taille_ko]);
                    header("Location: gerer_plansetvisite.php?id=" . $id_projet . "&success=plan_added");
                    exit();
                } else {
                    header("Location: gerer_plansetvisite.php?id=" . $id_projet . "&error=upload_failed");
                    exit();
                }
            } else {
                header("Location: gerer_plansetvisite.php?id=" . $id_projet . "&error=not_pdf");
                exit();
            }
        }
    }

    if (isset($_POST['action']) && $_POST['action'] == 'edit_plan') {
        $plan_id = (int)$_POST['plan_id'];
        $nouveau_titre = htmlspecialchars($_POST['titre_plan']);
        
        $update = $pdo->prepare("UPDATE projet_plans SET titre = ? WHERE id = ? AND id_projet = ?");
        $update->execute([$nouveau_titre, $plan_id, $id_projet]);
        
        header("Location: gerer_plansetvisite.php?id=" . $id_projet . "&success=plan_edited");
        exit();
    }
}

if (isset($_GET['action']) && $_GET['action'] == 'delete_plan' && isset($_GET['plan_id'])) {
    $plan_id = (int)$_GET['plan_id'];
    
    $stmtPlan = $pdo->prepare("SELECT fichier_url FROM projet_plans WHERE id = ? AND id_projet = ?");
    $stmtPlan->execute([$plan_id, $id_projet]);
    $plan_a_supprimer = $stmtPlan->fetch();
    
    if ($plan_a_supprimer) {
        // On construit le bon chemin vers le fichier (ltrim enlève les anciens "../" s'il y en a eu par erreur)
        $chemin_physique = "../" . ltrim($plan_a_supprimer['fichier_url'], '../');
        
        // On détruit le fichier du serveur s'il existe
        if (file_exists($chemin_physique) && is_file($chemin_physique)) {
            unlink($chemin_physique);
        }
        
        // On détruit la ligne en base de données
        $pdo->prepare("DELETE FROM projet_plans WHERE id = ?")->execute([$plan_id]);
        header("Location: gerer_plansetvisite.php?id=" . $id_projet . "&success=plan_deleted");
        exit();
    }
}

$stmtPlans = $pdo->prepare("SELECT * FROM projet_plans WHERE id_projet = ? ORDER BY id DESC");
$stmtPlans->execute([$id_projet]);
$liste_plans = $stmtPlans->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plans & Visite 3D - AvenirImmo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = { theme: { extend: { colors: { primary: '#F97316', primaryHover: '#EA580C', dark: '#111827', grayBorder: '#E5E7EB' } } } }
    </script>
</head>
<body class="bg-gray-50 min-h-screen pb-20">
    <div class="max-w-5xl mx-auto py-6 sm:py-10 px-4">
        
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-grayBorder">
            <div>
                <a href="../<?= $projet['slug'] ?>" class="text-sm text-textMuted hover:text-primary mb-2 inline-block"><i class="fa-solid fa-arrow-left mr-2"></i>Retour au projet</a>
                <h1 class="text-xl sm:text-2xl font-bold text-dark">Médias interactifs de : <span class="text-primary"><?= htmlspecialchars($projet['titre']) ?></span></h1>
            </div>
            <button onclick="document.getElementById('form-ajout-plan').classList.toggle('hidden')" class="w-full sm:w-auto bg-dark hover:bg-black text-white px-5 py-2.5 rounded-lg font-medium transition-colors text-center">
                <i class="fa-solid fa-plus mr-2"></i> Ajouter un plan
            </button>
        </div>

        <?php if(isset($_GET['success'])): ?>
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                <i class="fa-solid fa-circle-check mr-2"></i>
                <?php 
                    if($_GET['success'] == 'visite_updated') echo "Le lien de la visite virtuelle a été mis à jour.";
                    if($_GET['success'] == 'plan_added') echo "Le plan a été mis en ligne avec succès.";
                    if($_GET['success'] == 'plan_edited') echo "Le nom du plan a été modifié avec succès.";
                    if($_GET['success'] == 'plan_deleted') echo "Le plan a été supprimé définitivement.";
                ?>
            </div>
        <?php endif; ?>
        <?php if(isset($_GET['error'])): ?>
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                <i class="fa-solid fa-triangle-exclamation mr-2"></i>
                <?php 
                    if($_GET['error'] == 'not_pdf') echo "Le fichier doit obligatoirement être au format PDF.";
                    if($_GET['error'] == 'upload_failed') echo "Une erreur est survenue lors de l'enregistrement du fichier.";
                ?>
            </div>
        <?php endif; ?>

        <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-grayBorder mb-8">
            <h2 class="text-lg sm:text-xl font-bold mb-4 text-dark border-b pb-3 flex items-center gap-2">
                <i class="fa-solid fa-vr-cardboard text-blue-500"></i> Visite virtuelle 3D
            </h2>
            <form action="gerer_plansetvisite.php?id=<?= $id_projet ?>" method="POST" class="flex flex-col sm:flex-row gap-3">
                <input type="hidden" name="action" value="update_visite">
                <input type="url" name="visite_url" value="<?= htmlspecialchars($projet['visite_virtuelle_url']) ?>" placeholder="Ex: https://my.matterport.com/show/?m=..." class="flex-grow border border-grayBorder rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-blue-500/50 outline-none w-full">
                <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-6 py-2.5 rounded-lg font-medium transition-colors shadow-sm whitespace-nowrap text-center">Mettre à jour</button>
            </form>
            <p class="text-xs text-gray-500 mt-3"><i class="fa-solid fa-circle-info mr-1"></i>Laissez le champ vide si vous n'avez pas de visite 3D pour ce bien.</p>
        </div>

        <div id="form-ajout-plan" class="hidden bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-primary/20 mb-8 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-20 h-20 bg-primary/5 rounded-bl-full -z-10"></div>
            <h2 class="text-lg sm:text-xl font-bold mb-6 text-dark border-b pb-3">Nouveau plan d'architecte</h2>
            <form action="gerer_plansetvisite.php?id=<?= $id_projet ?>" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="action" value="add_plan">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
                    <div>
                        <label class="block text-sm font-medium mb-1">Titre du plan <span class="text-red-500">*</span></label>
                        <input type="text" name="titre_plan" required class="w-full border border-grayBorder rounded-lg px-4 py-2 focus:ring-2 focus:ring-primary/50 outline-none" placeholder="Ex: Plan RDC - Côté Sud">
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-1">Fichier PDF <span class="text-red-500">*</span></label>
                        <input type="file" name="plan_fichier" accept="application/pdf" required class="w-full bg-white border border-grayBorder rounded-lg px-3 py-1.5 cursor-pointer file:mr-4 file:py-1 file:px-3 file:rounded file:border-0 file:text-sm file:font-medium file:bg-primary/10 file:text-primary hover:file:bg-primary/20 transition-colors">
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-3 mt-6">
                    <button type="button" onclick="document.getElementById('form-ajout-plan').classList.add('hidden')" class="w-full sm:w-auto px-5 py-2 border border-grayBorder rounded-lg hover:bg-gray-50 transition-colors">Annuler</button>
                    <button type="submit" class="w-full sm:w-auto bg-primary hover:bg-primaryHover text-white px-6 py-2 rounded-lg font-medium transition-colors shadow-sm flex justify-center items-center"><i class="fa-solid fa-upload mr-2"></i> Mettre en ligne</button>
                </div>
            </form>
        </div>

        <div class="bg-white p-4 sm:p-6 rounded-2xl shadow-sm border border-grayBorder">
            <h2 class="text-lg sm:text-xl font-bold mb-6 text-dark border-b pb-3 flex items-center gap-2">
                <i class="fa-solid fa-file-pdf text-red-500"></i> Plans actuels (<?= count($liste_plans) ?>)
            </h2>
            
            <?php if(count($liste_plans) > 0): ?>
                <ul class="space-y-4">
                    <?php foreach($liste_plans as $plan): ?>
                        <li class="bg-white border border-grayBorder rounded-xl p-3 shadow-sm flex items-center gap-3 group hover:border-primary/40 transition-colors text-left">
    
                            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg bg-red-50 flex items-center justify-center text-red-500 flex-shrink-0 border border-red-100">
                                <i class="fa-solid fa-file-pdf text-xl sm:text-2xl"></i>
                            </div>
                            
                            <div class="flex-grow overflow-hidden">
                                <h4 class="font-bold text-dark text-sm sm:text-lg truncate"><?= htmlspecialchars($plan['titre']) ?></h4>
                                <p class="text-xs text-gray-500 font-medium">PDF <span class="mx-1">•</span> <?= round($plan['taille_ko'] / 1024, 2) ?> MB</p>
                            </div>
                            
                            <div class="flex items-center gap-1 sm:gap-2 flex-shrink-0">
                                <a href="<?= htmlspecialchars($plan['fichier_url']) ?>" target="_blank" class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-gray-50 text-gray-600 flex items-center justify-center hover:bg-gray-200 hover:text-dark transition-colors tooltip" title="Voir le PDF">
                                    <i class="fa-solid fa-eye text-xs sm:text-base"></i>
                                </a>
                                <button type="button" onclick='openEditModal(<?= json_encode($plan, JSON_HEX_APOS | JSON_HEX_QUOT) ?>)' class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-colors tooltip" title="Renommer">
                                    <i class="fa-solid fa-pen text-xs sm:text-base"></i>
                                </button>
                                <a href="gerer_plansetvisite.php?id=<?= $id_projet ?>&action=delete_plan&plan_id=<?= $plan['id'] ?>" onclick="return confirm('Voulez-vous vraiment supprimer le plan <?= htmlspecialchars(addslashes($plan['titre'])) ?> ?');" class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition-colors tooltip" title="Supprimer">
                                    <i class="fa-solid fa-trash text-xs sm:text-base"></i>
                                </a>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <div class="text-center py-8 text-gray-500 bg-gray-50 rounded-lg border border-dashed border-gray-300">
                    Aucun plan PDF n'a été ajouté pour le moment.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div id="editModal" class="hidden fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 backdrop-blur-sm">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg mx-4">
            <div class="p-4 sm:p-6 border-b border-grayBorder flex justify-between items-center">
                <h2 class="text-lg sm:text-xl font-bold text-dark">Renommer le plan</h2>
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="text-gray-400 hover:text-dark text-2xl leading-none">&times;</button>
            </div>
            <form method="POST" action="gerer_plansetvisite.php?id=<?= $id_projet ?>" class="p-4 sm:p-6">
                <input type="hidden" name="action" value="edit_plan">
                <input type="hidden" name="plan_id" id="edit_id">
                
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-1">Nouveau titre du plan</label>
                    <input type="text" name="titre_plan" id="edit_titre" required class="w-full border border-grayBorder rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500/50 outline-none">
                </div>

                <div class="flex flex-col sm:flex-row justify-end gap-3">
                    <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="w-full sm:w-auto px-5 py-2 border border-grayBorder rounded-lg hover:bg-gray-50 transition-colors">Annuler</button>
                    <button type="submit" class="w-full sm:w-auto bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium transition-colors shadow-sm">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Fonction pour pré-remplir et ouvrir la modale de modification
        function openEditModal(plan) {
            document.getElementById('edit_id').value = plan.id;
            document.getElementById('edit_titre').value = plan.titre;
            document.getElementById('editModal').classList.remove('hidden');
        }
    </script>
</body>
</html>