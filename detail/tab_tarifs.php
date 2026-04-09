<div id="tab-tarifs" class="tab-content bg-white p-8 rounded-2xl shadow-card border border-grayBorder">
    <div class="flex flex-wrap justify-between items-center gap-4 mb-6">
        <h2 class="text-2xl font-serif font-bold text-dark">Tarifs évolutifs</h2>
        <?php if(isset($_SESSION['utilisateur_id']) && $_SESSION['utilisateur_id'] == $projet['id_utilisateur']): ?>
            <a href="admin/gerer_etapes.php?id=<?= $projet['id'] ?>" class="bg-primary text-white hover:bg-primaryHover px-4 py-2 rounded-lg text-sm font-medium transition-colors shadow-sm flex items-center gap-2">
                <i class="fa-solid fa-pen"></i> Modifier les étapes
            </a>
        <?php endif; ?>
    </div>
    <p class="text-textMuted mb-8">Achetez au moment qui vous convient. Plus vous intervenez tôt dans le projet, plus le prix est attractif.</p>

    <div class="space-y-6">
        <?php if(count($etapes) > 0): ?>
            <?php foreach($etapes as $index => $etape): ?>
                <?php 
                $bgClass = 'bg-white hover:border-gray-400';
                $borderClass = 'border border-grayBorder';
                $badgeClass = 'bg-gray-100 text-gray-600';
                $priceClass = 'text-2xl font-bold text-dark';
                
                if($etape['statut'] == 'Terminé') {
                    $bgClass = 'bg-gray-50';
                    $badgeClass = 'bg-gray-200 text-gray-600';
                    $priceClass = 'text-2xl font-bold text-gray-400 line-through';
                } elseif($etape['statut'] == 'Actuel') {
                    $bgClass = 'bg-orange-50/30 transform scale-[1.02] shadow-sm';
                    $borderClass = 'border-2 border-primary';
                    $badgeClass = 'bg-primary text-white';
                    $priceClass = 'text-3xl font-bold text-primary';
                }

                // Décodage des entités HTML pour éviter l'affichage des &#039;
                $titre_etape = html_entity_decode($etape['titre'], ENT_QUOTES, 'UTF-8');
                $desc_etape = html_entity_decode($etape['description'], ENT_QUOTES, 'UTF-8');
                $statut_etape = html_entity_decode($etape['statut'], ENT_QUOTES, 'UTF-8');
                $date_dispo = html_entity_decode($etape['date_dispo'] ?? '', ENT_QUOTES, 'UTF-8');
                $inclus = html_entity_decode($etape['inclus'] ?? '', ENT_QUOTES, 'UTF-8');
                $non_inclus = html_entity_decode($etape['non_inclus'] ?? '', ENT_QUOTES, 'UTF-8');
                ?>

                <div class="<?= $borderClass ?> rounded-xl p-6 relative overflow-hidden <?= $bgClass ?> transition-all">
                    <div class="absolute top-0 right-0 <?= $badgeClass ?> px-3 py-1 text-xs font-bold rounded-bl-lg"><?= htmlspecialchars($statut_etape) ?></div>
                    
                    <div class="flex flex-col sm:flex-row justify-between gap-4">
                        <div class="flex-grow">
                            <h3 class="text-lg font-bold text-dark mb-1">Étape <?= $index + 1 ?> : <?= htmlspecialchars($titre_etape) ?></h3>
                            <p class="text-sm text-textMuted mb-3"><?= htmlspecialchars($desc_etape) ?></p>
                            
                            <ul class="text-sm space-y-1 text-textMain">
                                <?php if(!empty($inclus)): ?>
                                    <?php foreach(explode("\n", $inclus) as $item): if(trim($item)): ?>
                                        <li><i class="fa-solid fa-check text-green-500 w-4"></i> <?= htmlspecialchars(trim($item)) ?></li>
                                    <?php endif; endforeach; ?>
                                <?php endif; ?>
                                
                                <?php if(!empty($non_inclus)): ?>
                                    <?php foreach(explode("\n", $non_inclus) as $item): if(trim($item)): ?>
                                        <li><i class="fa-solid fa-xmark text-red-500 w-4"></i> <?= htmlspecialchars(trim($item)) ?></li>
                                    <?php endif; endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                        
                        <div class="text-right sm:min-w-[150px] flex flex-col justify-center mt-4 sm:mt-0">
                            <?php if($statut_etape == 'Futur'): ?><div class="text-sm text-textMuted mb-1">Prix estimé</div><?php endif; ?>
                            
                            <div class="<?= $priceClass ?>"><?= number_format($etape['prix'], 0, ',', ' ') ?> €</div>
                            
                            <?php if(!empty($etape['prix_m2']) && $etape['prix_m2'] > 0): ?>
                                <?php if(!empty($projet['prix_m2_secteur']) && $projet['prix_m2_secteur'] > 0): ?>
                                    <div class="flex items-center justify-end gap-2 mt-1">
                                        <span class="text-xs text-gray-400 line-through font-medium" title="Prix moyen constaté dans ce secteur">
                                            <?= number_format($projet['prix_m2_secteur'], 0, ',', ' ') ?> €/m²
                                        </span>
                                        <span class="text-sm <?= $etape['statut'] == 'Terminé' ? 'text-gray-400' : 'text-dark font-extrabold' ?>">
                                            <?= number_format($etape['prix_m2'], 0, ',', ' ') ?> €/m²
                                        </span>
                                    </div>
                                <?php else: ?>
                                    <div class="text-sm <?= $etape['statut'] == 'Terminé' ? 'text-gray-400' : 'text-gray-600 font-medium' ?> mt-1">
                                        <?= number_format($etape['prix_m2'], 0, ',', ' ') ?> €/m²
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                            <?php if($etape['statut'] == 'Actuel'): ?>
                                <?php 
                                $titre_projet_decoded = html_entity_decode($projet['titre'], ENT_QUOTES, 'UTF-8');
                                $sujet_resa = "Réservation : " . $titre_projet_decoded . " (Réf: " . $projet['id'] . ")";
                                $message_resa = "Bonjour,\n\nJe souhaite réserver le bien \"" . $titre_projet_decoded . "\" à l'étape \"" . $titre_etape . "\" (Prix : " . number_format($etape['prix'], 0, ',', ' ') . " €).\n\nMerci de me recontacter pour la suite des démarches.";
                                ?>
                                <a href="<?= $base ?>contact.php?projet_id=<?= $projet['id'] ?>&sujet=<?= urlencode($sujet_resa) ?>&message=<?= urlencode($message_resa) ?>" class="mt-4 block w-full bg-primary hover:bg-primaryHover text-white text-center py-2.5 rounded-lg text-sm font-bold transition-colors shadow-sm">
                                    Réserver à ce prix
                                </a>
                            <?php elseif($etape['statut'] == 'Terminé'): ?>
                                <div class="text-sm font-medium text-gray-400 mt-2">Non disponible</div>
                            <?php elseif(!empty($date_dispo)): ?>
                                <div class="text-xs text-gray-500 mt-2">Dispo prévue : <?= htmlspecialchars($date_dispo) ?></div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="text-center text-gray-500 py-4">Aucune étape tarifaire renseignée pour le moment.</p>
        <?php endif; ?>
    </div>
</div>