<?php
session_start();
require 'includes/db.php';

$message = '';

if (isset($_SESSION['utilisateur_id'])) {
    header("Location: index.php");
    exit();
}

// --- RÉCUPÉRATION DE L'ADMINISTRATEUR ---
$stmtAdmin = $pdo->query("SELECT id, email FROM utilisateurs LIMIT 1");
$admin = $stmtAdmin->fetch(PDO::FETCH_ASSOC);

// --- NOUVEAU : GESTION AJAX POUR LE MOT DE PASSE OUBLIÉ ---
// Ce bloc ne s'exécute que lorsqu'on clique sur le bouton "M'envoyer le lien"
if (isset($_POST['action']) && $_POST['action'] === 'ajax_reset') {
    header('Content-Type: application/json');
    if ($admin) {
        $token = bin2hex(random_bytes(32));
        // SÉCURITÉ : Le lien expire dans 15 minutes maximum
        $expiration = date('Y-m-d H:i:s', strtotime('+15 minutes'));
        
        $stmtUpdate = $pdo->prepare("UPDATE utilisateurs SET token_reset = ?, token_expiration = ? WHERE id = ?");
        $stmtUpdate->execute([$token, $expiration, $admin['id']]);

        $lien_reset = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/reinitialiser_mdp.php?token=" . $token;
        
        echo json_encode(['success' => true, 'lien' => $lien_reset, 'email' => $admin['email']]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit(); // On arrête l'exécution ici pour ne pas afficher le HTML lors de l'appel AJAX
}

// --- GESTION DE LA CONNEXION CLASSIQUE ---
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = htmlspecialchars($_POST['email']);
    $password = $_POST['mot_de_passe'];

    $stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(); 

    if ($user && password_verify($password, $user['mot_de_passe'])) {
        $_SESSION['utilisateur_id'] = $user['id'];
        $_SESSION['utilisateur_nom'] = $user['nom'];
        header("Location: index.php");
        exit();
    } else {
        $message = "<div class='bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative mb-4 text-sm font-medium'>Email ou mot de passe incorrect.</div>";
    }
}

// Masquage de l'email pour l'affichage
$email_masque = 'Email non trouvé';
if ($admin) {
    $parts = explode('@', $admin['email']);
    $nom_email = $parts[0];
    $domaine = $parts[1] ?? '';
    $lettres_visibles = min(2, strlen($nom_email));
    $email_masque = substr($nom_email, 0, $lettres_visibles) . '****@' . $domaine;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - AvenirImmo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = { theme: { extend: { colors: { primary: '#F97316', primaryHover: '#EA580C', dark: '#111827', textMain: '#374151', textMuted: '#6B7280', grayBorder: '#E5E7EB' } } } }
    </script>
</head>
<body class="font-sans text-textMain bg-gray-50 antialiased flex flex-col min-h-screen">

    <header class="bg-white border-b border-grayBorder p-4 text-center shadow-sm">
        <div class="inline-flex items-center gap-2">
            <i class="fa-solid fa-house-chimney text-primary text-2xl"></i>
            <span class="font-bold text-xl tracking-tight text-dark">Avenir<span class="text-primary">Immo</span></span>
        </div>
    </header>

    <main class="flex-grow flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full">
            <div class="bg-white p-8 rounded-2xl shadow-lg border border-grayBorder mb-6">
                <div class="text-center mb-8">
                    <h2 class="text-3xl font-bold text-dark mb-2">Bon retour !</h2>
                    <p class="text-textMuted text-sm">Connectez-vous pour gérer vos projets.</p>
                </div>

                <?= $message ?>

                <form action="connexion.php" method="POST" class="space-y-5 mb-8">
                    <input type="hidden" name="action" value="login">
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1">Adresse Email</label>
                        <input type="email" name="email" required class="w-full px-4 py-3 border border-grayBorder rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-colors" placeholder="jean@exemple.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1">Mot de passe</label>
                        <input type="password" name="mot_de_passe" required class="w-full px-4 py-3 border border-grayBorder rounded-lg focus:ring-2 focus:ring-primary/50 focus:border-primary outline-none transition-colors" placeholder="••••••••">
                    </div>
                    <button type="submit" class="w-full bg-dark hover:bg-gray-800 text-white py-3 px-4 rounded-xl font-bold transition-transform shadow-md">
                        Se connecter
                    </button>
                </form>

                <div class="relative flex py-2 items-center">
                    <div class="flex-grow border-t border-gray-200"></div>
                    <span class="flex-shrink-0 mx-4 text-gray-400 text-sm">Mot de passe perdu ?</span>
                    <div class="flex-grow border-t border-gray-200"></div>
                </div>

                <form id="ajax-reset-form" class="mt-6 text-center bg-gray-50 p-4 rounded-xl border border-gray-100">
                    <input type="hidden" name="action" value="ajax_reset">
                    <p class="text-xs text-gray-500 mb-3 leading-relaxed">
                        Un lien de réinitialisation (valable 15 min) sera envoyé à :<br>
                        <strong class="text-dark bg-white px-2 py-1 rounded border inline-block mt-1"><?= $email_masque ?></strong>
                    </p>
                    <button type="submit" id="btn-reset" class="text-sm font-medium text-primary hover:text-primaryHover transition-colors flex items-center justify-center gap-2 w-full">
                        <i class="fa-solid fa-paper-plane"></i> M'envoyer le lien sécurisé
                    </button>
                </form>
            </div>
        </div>
    </main>

    <script>
        document.getElementById('ajax-reset-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('btn-reset');
            btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Création du lien...';
            btn.disabled = true;

            // 1. On demande à PHP de générer le jeton et le lien
            fetch('connexion.php', {
                method: 'POST',
                body: new FormData(this)
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    btn.innerHTML = '<i class="fa-solid fa-paper-plane fa-spin"></i> Envoi de l\'email...';
                    
                    // 2. On utilise FormSubmit pour envoyer le mail avec le lien
                    const mailData = new FormData();
                    mailData.append('_subject', 'Votre lien de réinitialisation (AvenirImmo)');
                    mailData.append('Message', "Bonjour,\n\nVoici votre lien sécurisé pour changer de mot de passe. Il est valable 15 minutes :\n\n" + data.lien);
                    mailData.append('_captcha', 'false');

                    fetch('https://formsubmit.co/ajax/' + data.email, {
                        method: 'POST',
                        headers: { 'Accept': 'application/json' },
                        body: mailData
                    })
                    .then(response => response.json())
                    .then(fsData => {
                        btn.innerHTML = '<i class="fa-solid fa-check"></i> Email envoyé !';
                        btn.classList.replace('text-primary', 'text-green-600');
                        btn.classList.replace('hover:text-primaryHover', 'hover:text-green-700');
                    })
                    .catch(error => {
                        btn.innerHTML = '<i class="fa-solid fa-triangle-exclamation"></i> Erreur lors de l\'envoi';
                    });
                }
            });
        });
    </script>
</body>
</html>