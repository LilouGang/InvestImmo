<?php
// On s'assure d'avoir les données du compte qui a publié le projet (pour servir de repli)
if (!isset($utilisateur_projet) && isset($pdo) && !empty($projet['id_utilisateur'])) {
    // AJOUT: On sélectionne aussi l'email
    $stmtU = $pdo->prepare("SELECT prenom, nom, telephone, email FROM utilisateurs WHERE id = ?");
    $stmtU->execute([$projet['id_utilisateur']]);
    $utilisateur_projet = $stmtU->fetch(PDO::FETCH_ASSOC);
}

// 1. Détermination du nom à afficher (Priorité projet > Compte global)
$affiche_prenom = !empty($projet['contact_prenom']) ? $projet['contact_prenom'] : ($utilisateur_projet['prenom'] ?? '');
$affiche_nom = !empty($projet['contact_nom']) ? $projet['contact_nom'] : ($utilisateur_projet['nom'] ?? '');
$affiche_nom_complet = trim($affiche_prenom . ' ' . $affiche_nom);

if (empty($affiche_nom_complet)) {
    $affiche_nom_complet = 'Un de nos conseillers';
}

// 2. Détermination des coordonnées à afficher (Téléphone & Email)
$affiche_telephone = !empty($projet['contact_telephone']) ? $projet['contact_telephone'] : ($utilisateur_projet['telephone'] ?? '');
$affiche_email = !empty($projet['contact_email']) ? $projet['contact_email'] : ($utilisateur_projet['email'] ?? '');
$affiche_tel_display = trim(chunk_split(str_replace([' ', '.', '-'], '', $affiche_telephone), 2, ' '));
?>

<div class="sticky top-40 space-y-6">
    <div class="bg-white p-6 rounded-2xl shadow-float border border-primary/20 relative overflow-hidden">
        <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-bl-full -z-10"></div>
        
        <h3 class="text-xl font-bold text-dark mb-2">Intéressé par ce projet ?</h3>
        <p class="text-sm text-textMuted mb-6">Bloquez le prix actuel ou planifiez une visite de chantier pour vous projeter.</p>
        
        <div class="space-y-3">
            <a href="<?= isset($base) ? $base : '' ?>contact.php?projet_id=<?= $projet['id'] ?>&sujet=Projet_<?= urlencode($projet['titre'] ?? 'AvenirImmo') ?>" class="w-full bg-primary hover:bg-primaryHover text-white py-3.5 px-4 rounded-xl font-medium transition-colors shadow-sm flex items-center justify-center gap-2">
                <i class="fa-regular fa-envelope"></i> Être contacté
            </a>
        </div>
    
        <div class="mt-6 pt-6 border-t border-gray-100 flex items-center justify-between gap-2">
            
            <div class="flex items-center gap-3 overflow-hidden">
                <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 border border-gray-200 shadow-sm flex-shrink-0">
                    <i class="fa-solid fa-user text-xl"></i>
                </div>
                <div class="truncate">
                    <p class="text-sm font-bold text-dark truncate" title="<?= htmlspecialchars($affiche_nom_complet) ?>"><?= htmlspecialchars($affiche_nom_complet) ?></p>
                    <p class="text-xs text-textMuted truncate">Responsable du projet</p>
                </div>
            </div>
            
            <div class="flex items-center gap-2 flex-shrink-0">
                <?php if (!empty($affiche_telephone)): ?>
                <a href="tel:<?= str_replace(' ', '', htmlspecialchars($affiche_telephone)) ?>" title="<?= htmlspecialchars($affiche_tel_display) ?>" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-gray-50 flex items-center justify-center text-dark hover:bg-primary hover:text-white transition-colors border border-gray-100 shadow-sm">
                    <i class="fa-solid fa-phone text-sm"></i>
                </a>
                <?php endif; ?>

                <?php if (!empty($affiche_email)): ?>
                <a href="mailto:<?= htmlspecialchars($affiche_email) ?>" title="Envoyer un email" class="w-9 h-9 sm:w-10 sm:h-10 rounded-full bg-gray-50 flex items-center justify-center text-dark hover:bg-primary hover:text-white transition-colors border border-gray-100 shadow-sm">
                    <i class="fa-solid fa-envelope text-sm"></i>
                </a>
                <?php endif; ?>
            </div>
            
        </div>
    </div>

    <div class="bg-white p-6 rounded-2xl shadow-card border border-grayBorder">
        <h4 class="font-bold text-dark mb-4 border-b border-gray-100 pb-2">Informations pratiques</h4>
        <ul class="space-y-4 text-sm">
            <li class="flex items-start gap-3">
                <i class="fa-solid fa-map-pin text-gray-400 mt-1 w-4"></i>
                <div><span class="block font-medium text-dark">Localisation</span><span class="text-textMuted"><?= htmlspecialchars($projet['ville'] ?? '') ?></span></div>
            </li>
            <li class="flex items-start gap-3">
                <i class="fa-solid fa-file-invoice-dollar text-gray-400 mt-1 w-4"></i>
                <div>
                    <span class="block font-medium text-dark">Frais de notaire</span>
                    <span class="text-textMuted">
                        <?= !empty($projet['frais_notaire']) ? htmlspecialchars($projet['frais_notaire']) : 'À calculer' ?>
                    </span>
                </div>
            </li>
        </ul>
    </div>
</div>