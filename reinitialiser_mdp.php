<?php
session_start();
require 'includes/db.php';

$message = '';
$token_valide = false;
$admin_id = null;

if (isset($_GET['token']) && !empty($_GET['token'])) {
    $token = $_GET['token'];
    
    // On vérifie que le token existe et n'est pas expiré
    $stmt = $pdo->prepare("SELECT id FROM utilisateurs WHERE token_reset = ? AND token_expiration > NOW()");
    $stmt->execute([$token]);
    $admin = $stmt->fetch();

    if ($admin) {
        $token_valide = true;
        $admin_id = $admin['id'];
    } else {
        $message = '<div class="bg-red-50 text-red-600 border border-red-200 p-4 rounded-xl font-bold mb-6 text-sm text-center">Ce lien de sécurité est invalide ou a expiré.</div>';
    }
} else {
    header("Location: connexion.php");
    exit();
}

// Si le formulaire du nouveau mot de passe est soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $token_valide) {
    $nouveau_mdp = $_POST['nouveau_mdp'];

    if (strlen($nouveau_mdp) < 8) {
        $message = '<div class="bg-red-50 text-red-600 border border-red-200 p-4 rounded-xl font-bold mb-6 text-sm">Le mot de passe doit faire au moins 8 caractères.</div>';
    } else {
        // Hachage du mot de passe
        $hash = password_hash($nouveau_mdp, PASSWORD_DEFAULT);

        // Mise à jour ET destruction du token
        $stmtUpdate = $pdo->prepare("UPDATE utilisateurs SET mot_de_passe = ?, token_reset = NULL, token_expiration = NULL WHERE id = ?");
        $stmtUpdate->execute([$hash, $admin_id]);

        $token_valide = false; 
        $message = '<div class="bg-green-50 text-green-700 border border-green-200 p-6 rounded-xl font-bold text-center">
            <i class="fa-solid fa-circle-check text-3xl mb-2 block"></i>
            Votre mot de passe a été modifié avec succès !<br><br>
            <a href="connexion.php" class="inline-block mt-2 px-6 py-2 bg-dark text-white rounded-lg hover:bg-gray-800 transition-colors">Retour à la connexion</a>
        </div>';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Créer un nouveau mot de passe</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>tailwind.config={theme:{extend:{colors:{primary:'#F97316', dark:'#111827', grayBorder:'#E5E7EB'}}}}</script>
</head>
<body class="bg-gray-50 flex items-center justify-center min-h-screen p-4">

    <div class="bg-white p-8 rounded-2xl shadow-lg border border-grayBorder w-full max-w-md">
        <div class="text-center mb-6">
            <i class="fa-solid fa-lock-open text-primary text-4xl mb-3"></i>
            <h1 class="text-2xl font-bold text-dark">Nouveau mot de passe</h1>
        </div>

        <?= $message ?>

        <?php if ($token_valide): ?>
        <form method="POST" class="space-y-5">
            <div>
                <label class="block text-sm font-medium text-dark mb-1">Entrez votre nouveau mot de passe</label>
                <input type="password" name="nouveau_mdp" required placeholder="Minimum 8 caractères" class="w-full px-4 py-3 border border-grayBorder rounded-lg focus:ring-2 focus:ring-primary/50 outline-none">
            </div>
            
            <button type="submit" class="w-full py-3 bg-primary text-white font-bold rounded-xl hover:bg-orange-600 transition-colors">
                Enregistrer le mot de passe
            </button>
        </form>
        <?php endif; ?>
    </div>

</body>
</html>