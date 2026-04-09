<section id="project-hero" class="bg-olive relative overflow-hidden pt-12 pb-24 lg:pt-20 lg:pb-32">
    <div class="absolute inset-0 flex items-center justify-center opacity-10 pointer-events-none select-none overflow-hidden">
        <h1 class="font-serif text-[15vw] text-white whitespace-nowrap tracking-wider"><?= htmlspecialchars($projet['titre']) ?></h1>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-8 gap-4 text-white/80">
            <div class="flex items-center gap-2 text-sm">
                <a href="projets.php" class="hover:text-white transition-colors">Projets</a>
                <i class="fa-solid fa-chevron-right text-xs"></i>
                <span class="text-white"><?= htmlspecialchars($projet['titre']) ?></span>
            </div>
            <div class="flex gap-3">
                <span class="bg-blue-500/20 text-blue-300 border border-blue-500/30 px-3 py-1 rounded-full text-xs font-medium"><?= htmlspecialchars($projet['etat_bien']) ?></span>
                <span class="bg-white/10 border border-white/20 px-3 py-1 rounded-full text-xs font-medium"><i class="fa-solid fa-location-dot mr-1"></i> <?= htmlspecialchars($projet['ville']) ?></span>
            </div>
        </div>

        <div class="flex flex-col lg:grid lg:grid-cols-12 gap-8 lg:gap-12 items-start">
            
            <div class="order-1 lg:hidden text-white w-full">
                <h1 class="text-4xl font-serif mb-4 leading-tight flex items-center flex-wrap gap-3">
                    <?= htmlspecialchars($projet['titre']) ?>
                    <?php if(isset($projet['statut_commercial']) && in_array($projet['statut_commercial'], ['Réservé', 'Vendu'])): ?>
                        <span class="text-sm font-sans font-bold px-3 py-1 rounded-md uppercase tracking-wider border <?= $projet['statut_commercial'] == 'Vendu' ? 'bg-red-500/20 text-red-200 border-red-500/30' : 'bg-orange-500/20 text-orange-200 border-orange-500/30' ?>">
                            <?= htmlspecialchars($projet['statut_commercial']) ?>
                        </span>
                    <?php endif; ?>
                </h1>
                
                <?php if(isset($_SESSION['utilisateur_id']) && $_SESSION['utilisateur_id'] == $projet['id_utilisateur']): ?>
                    <div class="flex flex-wrap gap-2 mb-2 bg-white/10 p-2 rounded-lg backdrop-blur-sm border border-white/20 inline-flex">
                        <a href="admin/modifier.php?id=<?= $projet['id'] ?>" class="bg-white text-dark hover:bg-gray-100 px-3 py-1.5 rounded font-medium text-xs transition-colors shadow-sm"><i class="fa-solid fa-pen mr-1.5"></i>Modifier</a>
                        <a href="admin/supprimer.php?id=<?= $projet['id'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer tout ce projet ?');" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1.5 rounded font-medium text-xs transition-colors shadow-sm"><i class="fa-solid fa-trash mr-1.5"></i>Supprimer</a>
                    </div>
                <?php endif; ?>
            </div>

            <div class="lg:col-span-4 text-white order-4 lg:order-1 w-full">
                
                <div class="hidden lg:block">
                    <h1 class="text-4xl md:text-5xl font-serif mb-6 leading-tight flex items-center flex-wrap gap-4">
                        <?= htmlspecialchars($projet['titre']) ?>
                        <?php if(isset($projet['statut_commercial']) && in_array($projet['statut_commercial'], ['Réservé', 'Vendu'])): ?>
                            <span class="text-base font-sans font-bold px-3 py-1 rounded-md uppercase tracking-wider border <?= $projet['statut_commercial'] == 'Vendu' ? 'bg-red-500/20 text-red-200 border-red-500/30' : 'bg-orange-500/20 text-orange-200 border-orange-500/30' ?>">
                                <?= htmlspecialchars($projet['statut_commercial']) ?>
                            </span>
                        <?php endif; ?>
                    </h1>
                    
                    <?php if(isset($_SESSION['utilisateur_id']) && $_SESSION['utilisateur_id'] == $projet['id_utilisateur']): ?>
                        <div class="flex flex-wrap gap-3 mb-6 bg-white/10 p-3 rounded-lg backdrop-blur-sm border border-white/20 inline-flex">
                            <a href="admin/modifier.php?id=<?= $projet['id'] ?>" class="bg-white text-dark hover:bg-gray-100 px-4 py-2 rounded font-medium text-sm transition-colors shadow-sm"><i class="fa-solid fa-pen mr-2"></i>Modifier</a>
                            <a href="admin/supprimer.php?id=<?= $projet['id'] ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer tout ce projet ?');" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded font-medium text-sm transition-colors shadow-sm"><i class="fa-solid fa-trash mr-2"></i>Supprimer</a>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="flex flex-col gap-4 mb-8 mt-4 lg:mt-0">
                    <div class="flex items-center justify-between border-b border-white/10 pb-3"><span class="text-white/60 text-sm">Surface totale</span><span class="font-medium"><?= htmlspecialchars($projet['surface']) ?> m²</span></div>
                    <div class="flex items-center justify-between border-b border-white/10 pb-3"><span class="text-white/60 text-sm">Pièces / Chambres</span><span class="font-medium"><?= (int)$projet['pieces'] ?> pièces (<?= (int)$projet['chambres'] ?> ch.)</span></div>
                    <?php if(!empty($projet['exterieur'])): ?>
                        <div class="flex items-center justify-between border-b border-white/10 pb-3"><span class="text-white/60 text-sm">Extérieur</span><span class="font-medium"><?= htmlspecialchars($projet['exterieur']) ?></span></div>
                    <?php endif; ?>
                </div>

                <div class="bg-white/5 border border-white/10 p-5 rounded-xl backdrop-blur-sm">
                    <div class="flex justify-between items-center mb-2"><span class="text-sm font-medium">Progression du projet</span><span class="text-primary font-bold"><?= (int)$projet['avancement'] ?>%</span></div>
                    <div class="w-full bg-white/10 rounded-full h-2 mb-4"><div class="bg-primary h-2 rounded-full" style="width: <?= (int)$projet['avancement'] ?>%"></div></div>
                </div>
            </div>

            <div class="lg:col-span-5 relative z-20 order-2 lg:order-2 w-full">
                <div class="aspect-[4/5] rounded-2xl overflow-hidden shadow-2xl border-4 border-oliveLight relative cursor-pointer group" onclick="ouvrirModalGalerie()">
                    
                    <?php if(!empty($photos[0]['tag'])): ?>
                    <div class="absolute top-4 left-4 z-30 pointer-events-none flex items-center justify-center">
                        <div class="absolute inset-[-30px]" style="backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); mask-image: radial-gradient(ellipse at center, black 0%, transparent 70%); -webkit-mask-image: radial-gradient(ellipse at center, black 0%, transparent 70%);"></div>
                        <span class="relative z-10 text-white text-sm font-bold tracking-wide"><?= htmlspecialchars($photos[0]['tag']) ?></span>
                    </div>
                    <?php endif; ?>

                    <img class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700" src="<?= htmlspecialchars($imgPrincipale) ?>" alt="Image principale" />
                    
                    <div class="absolute bottom-4 left-4 right-4 sm:bottom-6 sm:left-6 sm:right-6 bg-white/95 backdrop-blur-md p-4 sm:p-5 rounded-xl shadow-lg flex flex-col gap-4" onclick="event.stopPropagation();">
                        
                        <div class="flex w-full items-center">
                            
                            <div class="<?= $prix_futur ? 'w-1/2 border-r border-gray-200 pr-3 sm:pr-4' : 'w-full' ?> flex flex-col">
                                <p class="text-[10px] sm:text-xs font-bold text-gray-500 uppercase tracking-widest mb-1 flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse shadow-[0_0_6px_rgba(34,197,94,0.6)]"></span> 
                                    Prix actuel
                                </p>
                                <p class="text-xl sm:text-2xl font-bold text-dark whitespace-nowrap truncate">
                                    <?= number_format($prix_actuel, 0, ',', ' ') ?> €
                                </p>
                            </div>

                            <?php if($prix_futur): ?>
                                <div class="w-1/2 pl-3 sm:pl-4 flex flex-col">
                                    <p class="text-[10px] sm:text-xs font-bold text-primary uppercase tracking-widest mb-1 flex items-center gap-1.5">
                                        <i class="fa-solid fa-wand-magic-sparkles"></i> Clé en main
                                    </p>
                                    <p class="text-lg sm:text-xl font-bold text-gray-500 whitespace-nowrap truncate">
                                        ~ <?= number_format($prix_futur, 0, ',', ' ') ?> €
                                    </p>
                                </div>
                            <?php endif; ?>

                        </div>

                        <a href="<?= isset($base) ? $base : '' ?>contact.php?projet_id=<?= $projet['id'] ?>&sujet=Projet_<?= urlencode($projet['titre'] ?? 'AvenirImmo') ?>"
                           class="w-full bg-primary hover:bg-primaryHover text-white px-5 py-3 rounded-lg text-sm sm:text-base font-bold transition-all shadow-sm flex items-center justify-center gap-2 group">
                            <i class="fa-regular fa-envelope group-hover:scale-110 transition-transform"></i> 
                            Être contacté
                        </a>

                    </div>
                </div>
            </div>

            <div class="lg:col-span-3 flex flex-col gap-4 order-3 lg:order-3 w-full">
                
                <div class="relative w-full z-30 mb-2 flex items-center">
                    <div id="btn-scroll-left" class="absolute left-0 z-40 flex items-center justify-start opacity-0 invisible transition-all duration-300 pointer-events-none w-10 h-10">
                        <button onclick="scrollTagsLeft()" class="w-full h-full flex items-center justify-start text-white lg:hover:text-primary transition-colors text-xl cursor-pointer pointer-events-auto">
                            <i class="fa-solid fa-chevron-left shadow-sm"></i>
                        </button>
                    </div>
                    
                    <div id="tags-scroll-container" class="overflow-x-auto hide-scrollbar flex items-center w-full relative z-30" style="mask-image: linear-gradient(to right, transparent 0%, black 15%, black 85%, transparent 100%); -webkit-mask-image: linear-gradient(to right, transparent 0%, black 15%, black 85%, transparent 100%);">
                        <div id="tags-track" class="flex w-max gap-2 px-8 py-1">
                            <?php foreach($tags_existants as $tag): ?>
                                <button type="button" onclick="toggleTag(this, '<?= addslashes(htmlspecialchars($tag)) ?>')" class="tag-btn whitespace-nowrap px-3 py-1 rounded-full border border-white/40 text-white text-xs lg:hover:bg-white/20 transition-colors cursor-pointer" data-tag="<?= htmlspecialchars($tag) ?>">
                                    <?= htmlspecialchars($tag) ?>
                                </button>
                            <?php endforeach; ?>
                        </div>
                    </div>
                
                    <div id="btn-scroll-right" class="absolute right-0 z-40 flex items-center justify-end transition-all duration-300 pointer-events-none w-10 h-10">
                        <button onclick="scrollTagsRight()" class="w-full h-full flex items-center justify-end text-white lg:hover:text-primary transition-colors text-xl cursor-pointer pointer-events-auto">
                            <i class="fa-solid fa-chevron-right shadow-sm"></i>
                        </button>
                    </div>
                </div>

                <div class="flex lg:flex-col gap-4 overflow-x-auto lg:overflow-visible pb-4 lg:pb-0 gallery-scroll" id="hero-thumbnails-container">
                    <?php 
                    $hero_photos = array_slice($photos, 1);
                    foreach($hero_photos as $index => $photo): 
                        $desktopVisibility = $index > 2 ? 'lg:hidden' : '';
                    ?>
                    <div class="hero-thumbnail w-48 lg:w-full h-32 lg:h-40 rounded-xl overflow-hidden flex-shrink-0 cursor-pointer relative group <?= $desktopVisibility ?>" data-tag="<?= htmlspecialchars($photo['tag']) ?>" onclick="ouvrirModalGalerie()">
                        <img class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110" src="<?= htmlspecialchars($photo['chemin_photo']) ?>" />
                        <div class="absolute inset-0 bg-black/20 group-hover:bg-transparent transition-colors"></div>
                        
                        <?php if(!empty($photo['tag'])): ?>
                        <div class="absolute top-3 left-3 z-20 pointer-events-none flex items-center justify-center">
                            <div class="absolute inset-[-20px]" style="backdrop-filter: blur(12px); -webkit-backdrop-filter: blur(12px); mask-image: radial-gradient(ellipse at center, black 0%, transparent 80%); -webkit-mask-image: radial-gradient(ellipse at center, black 0%, transparent 80%);"></div>
                            <span class="relative z-10 text-white text-xs font-bold tracking-wide"><?= htmlspecialchars($photo['tag']) ?></span>
                        </div>
                        <?php endif; ?>

                        <?php 
                        // Préparation de l'overlay : Actif au 1er chargement sur la 3ème image, mais modifiable par le JS ensuite
                        $isThirdInitial = ($index == 2 && $nbPhotos > 4);
                        $overlayClasses = $isThirdInitial ? 'hidden lg:flex' : 'hidden';
                        $overlayInitialText = $isThirdInitial ? '+ ' . ($nbPhotos - 4) . ' photos' : '';
                        ?>
                        <div class="overlay-more-photos <?= $overlayClasses ?> absolute inset-0 bg-black/60 z-30 items-center justify-center flex-col text-white group-hover:bg-black/70 transition-colors">
                            <i class="fa-regular fa-images text-2xl mb-2"></i>
                            <span class="overlay-text text-sm font-medium"><?= $overlayInitialText ?></span>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

            </div>

        </div>
    </div>
</section>