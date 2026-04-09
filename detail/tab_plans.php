<div id="tab-plans" class="tab-content bg-white p-6 sm:p-8 rounded-2xl shadow-card border border-grayBorder">
    <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
        <h2 class="text-xl sm:text-2xl font-serif font-bold text-dark">Plans & Visite Virtuelle</h2>
        <?php if(isset($_SESSION['utilisateur_id']) && $_SESSION['utilisateur_id'] == $projet['id_utilisateur']): ?>
            <a href="admin/gerer_plansetvisite.php?id=<?= $projet['id'] ?>" class="bg-primary text-white hover:bg-primaryHover px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-pen"></i> Modifier les plans
            </a>
        <?php endif; ?>
    </div>
    
    <?php if(!empty($projet['visite_virtuelle_url'])): ?>
    <div class="mb-10">
        <h3 class="text-lg font-semibold mb-4">Visite 3D du projet</h3>
        
        <div class="hidden lg:block aspect-video bg-gray-200 rounded-xl overflow-hidden shadow-inner">
            <iframe src="<?= htmlspecialchars($projet['visite_virtuelle_url']) ?>" width="100%" height="100%" frameborder="0" allowfullscreen allow="xr-spatial-tracking"></iframe>
        </div>

        <div class="block lg:hidden relative aspect-video bg-gray-200 rounded-xl overflow-hidden shadow-inner group">
            <iframe src="<?= htmlspecialchars($projet['visite_virtuelle_url']) ?>" width="100%" height="100%" frameborder="0" class="pointer-events-none opacity-80 group-hover:opacity-100 transition-opacity"></iframe>
            
            <a href="<?= htmlspecialchars($projet['visite_virtuelle_url']) ?>" target="_blank" rel="noopener noreferrer" class="absolute inset-0 z-10 flex flex-col items-center justify-center bg-black/10 hover:bg-black/30 transition-colors cursor-pointer">
            </a>
        </div>
    </div>
    <?php else: ?>
        <div class="mb-10 p-6 bg-gray-50 rounded-xl border border-grayBorder text-center text-gray-500">
            <i class="fa-solid fa-vr-cardboard text-3xl mb-2 text-gray-300"></i>
            <p>La visite virtuelle n'est pas encore disponible pour ce bien.</p>
        </div>
    <?php endif; ?>

    <div>
        <h3 class="text-lg font-semibold mb-4">Plans d'architecte</h3>
        <?php if(count($plans) > 0): ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <?php foreach($plans as $plan): ?>
                    <a href="<?= htmlspecialchars($plan['fichier_url']) ?>" target="_blank" class="border border-grayBorder rounded-xl p-4 flex flex-col items-center justify-center gap-3 hover:border-primary hover:bg-orange-50/10 cursor-pointer transition-colors group">
                        <i class="fa-regular fa-file-pdf text-4xl text-gray-400 group-hover:text-primary transition-colors"></i>
                        <span class="font-medium text-dark text-center"><?= htmlspecialchars($plan['titre']) ?></span>
                        <span class="text-xs text-textMuted px-3 py-1 bg-gray-100 rounded-full">PDF - <?= round($plan['taille_ko'] / 1024, 1) ?> MB</span>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <p class="text-gray-500">Aucun plan n'a été publié pour ce projet.</p>
        <?php endif; ?>
    </div>
</div>