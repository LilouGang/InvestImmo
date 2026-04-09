<?php 
session_start(); 
require 'includes/db.php';

// Valeurs par défaut (Si on clique sur l'onglet contact général)
$contact_adresse = "923 Rue du 19 Mars 1962<br>24150 Lalinde, France";
$contact_tel = "06 25 55 43 93";
$contact_email = "agenc.etna@gmail.com";
$contact_titre = "Notre Agence";
$contact_desc = "Venez nous rencontrer pour discuter de votre projet immobilier autour d'un café.";

// Si on vient depuis le bouton "Être contacté" d'un projet
if (isset($_GET['projet_id'])) {
    $stmt = $pdo->prepare("SELECT titre, contact_prenom, contact_nom, contact_telephone, contact_email, adresse_complete, code_postal, ville FROM projets WHERE id = ?");
    $stmt->execute([(int)$_GET['projet_id']]);
    $projet_contact = $stmt->fetch();

    if ($projet_contact) {
        $nom_complet = trim(($projet_contact['contact_prenom'] ?? '') . ' ' . ($projet_contact['contact_nom'] ?? ''));
        $contact_titre = !empty($nom_complet) ? "Contact : " . htmlspecialchars($nom_complet) : "Responsable du projet";
        $contact_desc = "Responsable du bien : " . htmlspecialchars($projet_contact['titre']);

        if (!empty($projet_contact['contact_telephone'])) $contact_tel = $projet_contact['contact_telephone'];
        if (!empty($projet_contact['contact_email'])) $contact_email = $projet_contact['contact_email'];
        
        if (!empty($projet_contact['adresse_complete'])) {
            $contact_adresse = htmlspecialchars($projet_contact['adresse_complete']) . "<br>" . htmlspecialchars($projet_contact['code_postal']) . " " . htmlspecialchars($projet_contact['ville']);
        }
    }
}
$contact_tel_display = trim(chunk_split(str_replace([' ', '.', '-'], '', $contact_tel), 2, ' '));
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contactez-nous - AvenirImmo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: { sans: ['Inter', 'sans-serif'], serif: ['Playfair Display', 'serif'] },
                    colors: { primary: '#F97316', primaryHover: '#EA580C', dark: '#111827', textMain: '#374151', textMuted: '#6B7280', grayBorder: '#E5E7EB', grayLight: '#F9FAFB' },
                    boxShadow: { 'card': '0 4px 6px -1px rgba(0, 0, 0, 0.05)', 'float': '0 10px 25px -5px rgba(0, 0, 0, 0.1)' }
                }
            }
        }
    </script>
</head>
<body class="font-sans text-textMain bg-gray-50 antialiased flex flex-col min-h-screen">

    <?php include 'includes/header.php'; ?>

    <main class="flex-grow py-12 md:py-20 flex items-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:items-start">
                
                <div class="lg:col-span-1 flex flex-col gap-3">
                    
                    <div class="bg-white p-6 rounded-2xl shadow-card border border-grayBorder transition-transform hover:-translate-y-1">
                        <div class="flex items-center gap-4 mb-3">
                            <div class="w-12 h-12 bg-orange-50 text-primary rounded-xl flex items-center justify-center text-xl flex-shrink-0">
                                <i class="fa-solid fa-location-dot"></i>
                            </div>
                            <h3 class="text-lg font-bold text-dark"><?= $contact_titre ?></h3>
                        </div>
                        <p class="text-sm text-textMuted leading-relaxed mb-3"><?= $contact_desc ?></p>
                        <p class="text-sm text-dark font-medium"><?= $contact_adresse ?></p>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-card border border-grayBorder transition-transform hover:-translate-y-1">
                        <div class="flex items-center gap-4 mb-3">
                            <div class="w-12 h-12 bg-orange-50 text-primary rounded-xl flex items-center justify-center text-xl flex-shrink-0">
                                <i class="fa-solid fa-phone"></i>
                            </div>
                            <h3 class="text-lg font-bold text-dark">Téléphone</h3>
                        </div>
                        <p class="text-sm text-textMuted leading-relaxed mb-3">Nos conseillers sont disponibles du Lundi au Vendredi, de 9h à 18h.</p>
                        <a href="tel:<?= str_replace(' ', '', $contact_tel) ?>" class="text-base font-bold text-primary hover:text-primaryHover transition-colors"><?= htmlspecialchars($contact_tel_display) ?></a>
                    </div>

                    <div class="bg-white p-6 rounded-2xl shadow-card border border-grayBorder transition-transform hover:-translate-y-1">
                        <div class="flex items-center gap-4 mb-3">
                            <div class="w-12 h-12 bg-orange-50 text-primary rounded-xl flex items-center justify-center text-xl flex-shrink-0">
                                <i class="fa-solid fa-envelope"></i>
                            </div>
                            <h3 class="text-lg font-bold text-dark">Email</h3>
                        </div>
                        <p class="text-sm text-textMuted leading-relaxed mb-3">Envoyez-nous vos documents ou questions, nous répondons sous 24h.</p>
                        <a href="mailto:<?= htmlspecialchars($contact_email) ?>" class="text-base font-bold text-primary hover:text-primaryHover transition-colors break-all"><?= htmlspecialchars($contact_email) ?></a>
                    </div>

                </div>

                <div class="lg:col-span-2 bg-white p-8 rounded-2xl shadow-float border border-grayBorder relative">
                    <h2 class="text-2xl font-bold text-dark mb-6">Envoyez-nous un message</h2>
                    
                    <?php
                    // Récupération UNIQUEMENT des paramètres de l'URL
                    $sujet_pre_rempli = isset($_GET['sujet']) ? htmlspecialchars($_GET['sujet']) : '';
                    $message_pre_rempli = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : '';
                    ?>
                    
                    <form id="ajax-contact-form">
                        <input type="hidden" name="_subject" value="Nouveau message depuis la page Contact !">
                        <input type="hidden" name="_captcha" value="false">
                        <input type="hidden" name="_template" value="table">
                    
                        <div class="space-y-5">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-dark mb-1">Prénom <span class="text-red-500">*</span></label>
                                    <input type="text" name="Prenom" required class="w-full px-4 py-2.5 border border-grayBorder rounded-lg focus:ring-2 focus:ring-primary/50 outline-none bg-grayLight focus:bg-white transition-colors">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-dark mb-1">Nom <span class="text-red-500">*</span></label>
                                    <input type="text" name="Nom" required class="w-full px-4 py-2.5 border border-grayBorder rounded-lg focus:ring-2 focus:ring-primary/50 outline-none bg-grayLight focus:bg-white transition-colors">
                                </div>
                            </div>
                        
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                                <div>
                                    <label class="block text-sm font-medium text-dark mb-1">Email <span class="text-red-500">*</span></label>
                                    <input type="email" name="Email" required class="w-full px-4 py-2.5 border border-grayBorder rounded-lg focus:ring-2 focus:ring-primary/50 outline-none bg-grayLight focus:bg-white transition-colors">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-dark mb-1">Téléphone</label>
                                    <input type="tel" name="Telephone" class="w-full px-4 py-2.5 border border-grayBorder rounded-lg focus:ring-2 focus:ring-primary/50 outline-none bg-grayLight focus:bg-white transition-colors">
                                </div>
                            </div>
                
                            <div>
                                <label class="block text-sm font-medium text-dark mb-1">Sujet de votre demande <span class="text-red-500">*</span></label>
                                <input type="text" name="Sujet" value="<?= $sujet_pre_rempli ?>" required class="w-full px-4 py-2.5 border border-grayBorder rounded-lg focus:ring-2 focus:ring-primary/50 outline-none bg-grayLight focus:bg-white transition-colors">
                            </div>
                        
                            <div>
                                <label class="block text-sm font-medium text-dark mb-1">Votre Message <span class="text-red-500">*</span></label>
                                <textarea name="Message" required rows="4" class="w-full px-4 py-2.5 border border-grayBorder rounded-lg focus:ring-2 focus:ring-primary/50 outline-none bg-grayLight focus:bg-white transition-colors"><?= $message_pre_rempli ?></textarea>
                            </div>
                        </div>
                    
                        <div class="mt-6">
                            <button type="submit" id="submit-btn" class="w-full bg-primary hover:bg-primaryHover text-white py-3.5 rounded-lg font-bold transition-colors shadow-md flex items-center justify-center gap-2">
                                Envoyer le message <i class="fa-solid fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                
                    <div id="form-success" class="hidden flex-col items-center justify-center text-center py-12 absolute inset-0 bg-white rounded-2xl z-10">
                        <div class="w-20 h-20 bg-green-500 rounded-full flex items-center justify-center text-white text-4xl mb-6 shadow-lg">
                            <i class="fa-solid fa-check"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-dark mb-2">Message envoyé avec succès !</h3>
                        <p class="text-textMuted text-lg">Notre équipe vous recontactera dans les plus brefs délais.</p>
                        <button onclick="window.location.reload();" class="mt-8 px-6 py-2 bg-gray-100 hover:bg-gray-200 text-dark rounded-lg font-medium transition-colors">
                            Envoyer un autre message
                        </button>
                    </div>
                </div>
                
                <script>
                document.getElementById('ajax-contact-form').addEventListener('submit', function(e) {
                    e.preventDefault(); 
                    const form = this;
                    const btn = document.getElementById('submit-btn');
                    
                    const originalText = btn.innerHTML;
                    btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Envoi en cours...';
                    btn.disabled = true;
                    btn.classList.add('opacity-75', 'cursor-not-allowed');
                
                    const formData = new FormData(form);
                
                    fetch('sauvegarder_contact.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        return fetch('https://formsubmit.co/ajax/agence.etna@gmail.com', {
                            method: 'POST',
                            body: formData
                        });
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success || data.ok) { // Vérifie .success ou .ok en fonction du retour de l'API
                            document.getElementById('form-success').classList.remove('hidden');
                            document.getElementById('form-success').classList.add('flex');
                            form.classList.add('invisible');
                        } else {
                            throw new Error('Erreur API');
                        }
                    })
                    .catch(error => {
                        console.error(error);
                        btn.innerHTML = 'Erreur. Réessayer';
                        btn.disabled = false;
                        btn.classList.remove('opacity-75', 'cursor-not-allowed');
                        alert('Une erreur est survenue lors de l\'envoi du message.');
                    });
                });
                </script>

            </div>
        </div>
    </main>

    <?php include 'includes/footer.php'; ?>

</body>
</html>