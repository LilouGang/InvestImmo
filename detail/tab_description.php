<div id="tab-description" class="tab-content active bg-white p-8 rounded-2xl shadow-card border border-grayBorder">
    <h2 class="text-2xl font-serif font-bold text-dark mb-6">À propos du projet</h2>
    <div class="prose prose-orange max-w-none text-textMain leading-relaxed">
        <?= html_entity_decode($projet['description']) ?>
    </div>
    
    <h3 class="text-xl font-semibold text-dark mt-10 mb-6 border-b pb-2">Caractéristiques détaillées</h3>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-y-4 gap-x-8 text-sm">
        <?php if(!empty($projet['annee_construction'])): ?>
        <div class="flex items-center gap-3"><div class="w-8 h-8 rounded bg-gray-100 flex items-center justify-center text-gray-500"><i class="fa-regular fa-calendar"></i></div><div><span class="block text-textMuted text-xs">Année</span><span class="font-medium text-dark"><?= htmlspecialchars($projet['annee_construction']) ?></span></div></div>
        <?php endif; ?>

        <?php if(!empty($projet['etage']) && $projet['etage'] > 0): ?>
        <div class="flex items-center gap-3"><div class="w-8 h-8 rounded bg-gray-100 flex items-center justify-center text-gray-500"><i class="fa-solid fa-stairs"></i></div><div><span class="block text-textMuted text-xs">Étage</span><span class="font-medium text-dark"><?= htmlspecialchars($projet['etage']) ?>e</span></div></div>
        <?php endif; ?>

        <?php if(!empty($projet['type_chauffage'])): ?>
        <div class="flex items-center gap-3"><div class="w-8 h-8 rounded bg-gray-100 flex items-center justify-center text-gray-500"><i class="fa-solid fa-temperature-half"></i></div><div><span class="block text-textMuted text-xs">Chauffage</span><span class="font-medium text-dark"><?= htmlspecialchars($projet['type_chauffage']) ?></span></div></div>
        <?php endif; ?>

        <?php if(!empty($projet['dpe'])): ?>
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded bg-gray-100 flex items-center justify-center text-gray-500"><i class="fa-solid fa-leaf"></i></div>
            <div><span class="block text-textMuted text-xs">DPE</span>
                <span class="inline-block px-2 py-0.5 rounded text-white font-bold text-xs bg-<?= $projet['dpe'] == 'A' || $projet['dpe'] == 'B' ? 'green-500' : ($projet['dpe'] == 'C' || $projet['dpe'] == 'D' ? 'yellow-500' : 'red-500') ?>">Classe <?= htmlspecialchars($projet['dpe']) ?></span>
            </div>
        </div>
        <?php endif; ?>

        <?php if(!empty($projet['parking']) && $projet['parking'] > 0): ?>
        <div class="flex items-center gap-3"><div class="w-8 h-8 rounded bg-gray-100 flex items-center justify-center text-gray-500"><i class="fa-solid fa-square-parking"></i></div><div><span class="block text-textMuted text-xs">Parking</span><span class="font-medium text-dark"><?= htmlspecialchars($projet['parking']) ?> place(s)</span></div></div>
        <?php endif; ?>

        <?php if(!empty($projet['ascenseur'])): ?>
        <div class="flex items-center gap-3"><div class="w-8 h-8 rounded bg-primary/10 flex items-center justify-center text-primary"><i class="fa-solid fa-check"></i></div><span class="font-medium text-dark">Ascenseur</span></div>
        <?php endif; ?>
        
        <?php if(!empty($projet['balcon'])): ?>
        <div class="flex items-center gap-3"><div class="w-8 h-8 rounded bg-primary/10 flex items-center justify-center text-primary"><i class="fa-solid fa-check"></i></div><span class="font-medium text-dark">Balcon / Terrasse</span></div>
        <?php endif; ?>
        
        <?php if(!empty($projet['cave'])): ?>
        <div class="flex items-center gap-3"><div class="w-8 h-8 rounded bg-primary/10 flex items-center justify-center text-primary"><i class="fa-solid fa-check"></i></div><span class="font-medium text-dark">Cave</span></div>
        <?php endif; ?>
    </div>
</div>