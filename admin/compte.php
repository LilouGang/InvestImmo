<?php
session_start();
require '../includes/db.php';

// Vérification de sécurité
if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: ../connexion.php");
    exit();
}

$message = '';

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $nom = htmlspecialchars(trim($_POST['nom']));
    $telephone = preg_replace('/[^0-9]/', '', $_POST['telephone']); 
    $nouveau_mdp = $_POST['nouveau_mdp'] ?? ''; // On récupère le nouveau mot de passe

    try {
        if (!empty($nouveau_mdp)) {
            // Si l'utilisateur veut changer de mot de passe
            if (strlen($nouveau_mdp) < 8) {
                throw new Exception("Le nouveau mot de passe doit faire au moins 8 caractères.");
            }
            $hash = password_hash($nouveau_mdp, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE utilisateurs SET prenom = ?, nom = ?, telephone = ?, mot_de_passe = ? WHERE id = ?");
            $stmt->execute([$prenom, $nom, $telephone, $hash, $_SESSION['utilisateur_id']]);
        } else {
            // S'il laisse le champ vide, on met à jour uniquement le reste
            $stmt = $pdo->prepare("UPDATE utilisateurs SET prenom = ?, nom = ?, telephone = ? WHERE id = ?");
            $stmt->execute([$prenom, $nom, $telephone, $_SESSION['utilisateur_id']]);
        }
        $message = '<div class="bg-green-50 text-green-600 border border-green-200 p-4 rounded-xl font-bold mb-6 flex items-center gap-2"><i class="fa-solid fa-check-circle"></i> Vos informations ont été mises à jour avec succès.</div>';
    } catch (Exception $e) {
        $message = '<div class="bg-red-50 text-red-500 border border-red-200 p-4 rounded-xl font-bold mb-6 flex items-center gap-2"><i class="fa-solid fa-triangle-exclamation"></i> Erreur lors de la mise à jour : ' . $e->getMessage() . '</div>';
    }
}

// Récupération des informations actuelles
$stmtUser = $pdo->prepare("SELECT prenom, nom, telephone, email FROM utilisateurs WHERE id = ?");
$stmtUser->execute([$_SESSION['utilisateur_id']]);
$utilisateur = $stmtUser->fetch(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Compte - AvenirImmo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: { extend: { colors: { primary: '#F97316', primaryHover: '#EA580C', dark: '#111827', grayBorder: '#E5E7EB' } } }
        }
    </script>
</head>
<body class="bg-gray-50 flex flex-col min-h-screen">

    <?php $base = '../'; include '../includes/header.php'; ?>

    <main class="flex-grow py-12 px-4 sm:px-6 lg:px-8 max-w-3xl mx-auto w-full">
        
        <div class="mb-8 flex items-center gap-4">
            <a href="../projets.php" class="w-10 h-10 bg-white border border-grayBorder rounded-full flex items-center justify-center text-gray-500 hover:text-primary hover:border-primary transition-colors shadow-sm">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-dark">Mon Profil</h1>
                <p class="text-gray-500 mt-1">Gérez vos informations personnelles et vos coordonnées de contact.</p>
            </div>
        </div>

        <?= $message ?>

        <div class="bg-white rounded-2xl shadow-sm border border-grayBorder overflow-hidden p-6 sm:p-8">
            <form action="" method="POST" class="space-y-6">
                
                <h2 class="text-xl font-bold mb-6 flex items-center gap-2 text-dark border-b pb-3">
                    <i class="fa-solid fa-user-pen text-primary"></i> Mes coordonnées par défaut
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Prénom</label>
                        <input type="text" name="prenom" value="<?= htmlspecialchars($utilisateur['prenom'] ?? '') ?>" class="w-full px-4 py-3 border border-grayBorder rounded-lg focus:ring-2 focus:ring-primary/50 outline-none transition-colors">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom</label>
                        <input type="text" name="nom" value="<?= htmlspecialchars($utilisateur['nom'] ?? '') ?>" class="w-full px-4 py-3 border border-grayBorder rounded-lg focus:ring-2 focus:ring-primary/50 outline-none transition-colors">
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Numéro de téléphone</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-phone text-gray-400"></i>
                        </div>
                        <input type="tel" name="telephone" value="<?= htmlspecialchars($utilisateur['telephone'] ?? '') ?>" maxlength="15" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="0612345678" class="w-full pl-11 pr-4 py-3 border border-grayBorder rounded-lg focus:ring-2 focus:ring-primary/50 outline-none transition-colors">
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Adresse Email (Identifiant)</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-envelope text-gray-400"></i>
                        </div>
                        <input type="email" value="<?= htmlspecialchars($utilisateur['email'] ?? '') ?>" disabled class="w-full pl-11 pr-4 py-3 border border-grayBorder bg-gray-50 text-gray-500 rounded-lg cursor-not-allowed">
                    </div>
                </div>
                
                <h2 class="text-xl font-bold mb-6 mt-8 flex items-center gap-2 text-dark border-b pb-3">
                    <i class="fa-solid fa-lock text-primary"></i> Sécurité
                </h2>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nouveau mot de passe</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fa-solid fa-key text-gray-400"></i>
                        </div>
                        <input type="password" name="nouveau_mdp" placeholder="Laisser vide pour ne pas modifier (min. 8 caractères)" class="w-full pl-11 pr-4 py-3 border border-grayBorder rounded-lg focus:ring-2 focus:ring-primary/50 outline-none transition-colors">
                    </div>
                </div>

                <div class="pt-8 mt-4 flex justify-end">
                    <button type="submit" class="w-full sm:w-auto px-8 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primaryHover transition-colors shadow-lg flex items-center justify-center gap-2">
                        <i class="fa-solid fa-save"></i> Enregistrer les modifications
                    </button>
                </div>

            </form>
        </div>
    </main>

</body>
</html>