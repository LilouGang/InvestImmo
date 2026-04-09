<?php
session_start();
require '../includes/db.php';

// Vérification de sécurité
if (!isset($_SESSION['utilisateur_id'])) {
    header("Location: ../connexion.php");
    exit();
}

// Récupération de tous les messages
$stmt = $pdo->query("SELECT * FROM messages_contact ORDER BY date_envoi DESC");
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boîte de réception - AvenirImmo</title>
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

    <main class="flex-grow py-10 px-4 sm:px-6 lg:px-8 max-w-4xl mx-auto w-full">
        
        <div class="mb-8 flex items-center justify-between border-b border-gray-200 pb-4">
            <div>
                <h1 class="text-2xl font-bold text-dark">Boîte de réception</h1>
                <p class="text-sm text-gray-500 mt-1"><?= count($messages) ?> demande(s) de contact</p>
            </div>
            <a href="../projets.php" class="px-4 py-2 bg-white border border-grayBorder rounded-lg text-sm text-gray-700 font-medium hover:bg-gray-50 transition-colors shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Retour
            </a>
        </div>

        <?php if(count($messages) > 0): ?>
            <div class="space-y-4">
                <?php foreach($messages as $msg): 
                    // Formatage de la date (Ex: 12 Oct 2023 à 14:30)
                    $dateObj = new DateTime($msg['date_envoi']);
                    $fmt = new IntlDateFormatter('fr_FR', IntlDateFormatter::MEDIUM, IntlDateFormatter::SHORT);
                    $dateFmt = $fmt->format($dateObj);
                    
                    // Formatage du nom et des initiales
                    $prenom = htmlspecialchars($msg['prenom']);
                    $nom = htmlspecialchars($msg['nom']);
                    $nomComplet = trim($prenom . ' ' . $nom);
                    
                    $initialePrenom = !empty($prenom) ? substr($prenom, 0, 1) : '';
                    $initialeNom = !empty($nom) ? substr($nom, 0, 1) : '';
                    $initiales = strtoupper($initialePrenom . $initialeNom);
                    if(empty($initiales)) $initiales = '?';
                ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 hover:border-primary/40 transition-colors group">
                    
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex items-start gap-3">
                            <div class="w-10 h-10 rounded-full bg-orange-50 text-primary flex items-center justify-center text-sm font-bold flex-shrink-0 mt-0.5">
                                <?= $initiales ?>
                            </div>
                            <div>
                                <h3 class="font-bold text-dark text-base flex items-center gap-2">
                                    <?= $nomComplet ?>
                                </h3>
                                
                                <div class="flex flex-wrap items-center gap-3 mt-1 text-xs text-gray-500 font-medium">
                                    <a href="mailto:<?= htmlspecialchars($msg['email']) ?>" class="hover:text-primary transition-colors flex items-center gap-1.5">
                                        <i class="fa-solid fa-envelope"></i> <?= htmlspecialchars($msg['email']) ?>
                                    </a>
                                    <?php if(!empty($msg['telephone'])): ?>
                                        <span class="text-gray-300">•</span>
                                        <a href="tel:<?= htmlspecialchars($msg['telephone']) ?>" class="hover:text-primary transition-colors flex items-center gap-1.5">
                                            <i class="fa-solid fa-phone"></i> <?= htmlspecialchars($msg['telephone']) ?>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-xs text-gray-400 whitespace-nowrap pl-4 flex-shrink-0">
                            <?= $dateFmt ?>
                        </div>
                    </div>

                    <div class="ml-13">
                        <h4 class="text-sm font-bold text-dark mb-1">
                            <span class="text-gray-400 font-normal mr-1">Sujet :</span> <?= htmlspecialchars($msg['sujet']) ?>
                        </h4>
                        <p class="text-sm text-gray-600 whitespace-pre-wrap leading-relaxed bg-gray-50 p-3 rounded-lg border border-gray-100"><?= htmlspecialchars($msg['message']) ?></p>
                    </div>

                </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-16 bg-white rounded-xl border border-grayBorder shadow-sm">
                <i class="fa-solid fa-inbox text-4xl text-gray-200 mb-3"></i>
                <h3 class="text-lg font-bold text-dark">Votre boîte est vide</h3>
                <p class="text-sm text-gray-500 mt-1">Vous n'avez pas encore reçu de messages.</p>
            </div>
        <?php endif; ?>

    </main>

</body>
</html>