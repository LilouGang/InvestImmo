<style>
    .dpe-radio:checked + label { transform: scale(1.15); border: 2px solid #111827; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.2); z-index: 10; }
    .dpe-label { transition: all 0.2s; color: white; font-weight: bold; display: flex; align-items: center; justify-content: center; width: 2.5rem; height: 2.5rem; cursor: pointer; border-radius: 0.375rem; }
</style>

<div id="step-1" class="form-step active space-y-8">
    
    <div class="bg-gray-50 p-5 rounded-xl border border-grayBorder">
        <h3 class="text-lg font-bold mb-4 text-dark flex items-center gap-2"><i class="fa-solid fa-address-card text-primary"></i> Contact du bien</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div><label class="block text-sm font-medium mb-1">Prénom <span class="text-red-500">*</span></label><input type="text" id="contact_prenom" required value="<?= htmlspecialchars($utilisateur['prenom'] ?? '') ?>" class="w-full px-4 py-2 border border-grayBorder bg-white rounded-lg outline-none"></div>
            <div><label class="block text-sm font-medium mb-1">Nom <span class="text-red-500">*</span></label><input type="text" id="contact_nom" required value="<?= htmlspecialchars($utilisateur['nom'] ?? '') ?>" class="w-full px-4 py-2 border border-grayBorder bg-white rounded-lg outline-none"></div>
            <div><label class="block text-sm font-medium mb-1">Téléphone</label><input type="tel" id="contact_telephone" value="<?= htmlspecialchars($utilisateur['telephone'] ?? '') ?>" oninput="this.value = this.value.replace(/[^0-9]/g, '')" maxlength="15" class="w-full px-4 py-2 border border-grayBorder bg-white rounded-lg outline-none"></div>
            <div><label class="block text-sm font-medium mb-1">Email <span class="text-red-500">*</span></label><input type="email" id="contact_email" required value="<?= htmlspecialchars($utilisateur['email'] ?? '') ?>" class="w-full px-4 py-2 border border-grayBorder bg-white rounded-lg outline-none"></div>
        </div>
    </div>

    <div>
        <h3 class="text-lg font-bold text-dark border-b pb-2 mb-4">L'identité du projet</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="md:col-span-2"><label class="block text-sm font-medium mb-1">Titre de l'annonce <span class="text-red-500">*</span></label><input type="text" id="titre" required class="w-full px-4 py-2 border border-grayBorder rounded-lg outline-none"></div>
            <div>
                <label class="block text-sm font-medium mb-1">Type de bien <span class="text-red-500">*</span></label>
                <select id="type_bien" required class="w-full px-4 py-2 border border-grayBorder rounded-lg outline-none bg-white font-bold text-primary">
                    <option value="" disabled selected>Sélectionnez un type...</option>
                    <option value="Appartement">Appartement</option>
                    <option value="Maison">Maison / Villa</option>
                    <option value="Loft">Loft / Atelier</option>
                    <option value="Local commercial">Local commercial</option>
                    <option value="Immeuble">🏢 Immeuble complet</option>
                </select>
            </div>
            
            <div class="md:col-span-3"><label class="block text-sm font-medium mb-1">Description <span class="text-red-500">*</span></label><textarea id="description" required rows="4" class="w-full px-4 py-2 border border-grayBorder rounded-lg outline-none"></textarea></div>
            
            <div id="bloc_ancien_usage" class="hidden md:col-span-3">
                <label class="block text-sm font-medium mb-1 text-primary">Ancien usage (Loft) <i class="fa-solid fa-industry"></i></label>
                <input type="text" id="ancien_usage" placeholder="Ex: Ancienne grange, Usine textile, Garage auto..." class="w-full px-4 py-2 border border-primary/50 bg-orange-50 rounded-lg outline-none">
            </div>

            <div class="md:col-span-3 bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                <div class="flex justify-between items-end mb-2">
                    <label class="block text-sm font-bold text-dark">Avancement du projet</label>
                    <span class="text-primary font-extrabold text-xl"><span id="val_avancement">0</span>%</span>
                </div>
                <div class="relative w-full h-3 bg-gray-100 rounded-full mt-3 shadow-inner">
                    <div id="progress_avancement" class="absolute top-0 left-0 h-full rounded-full transition-all duration-150 pointer-events-none" style="width: 0%; background-color: hsl(0, 85%, 45%);"></div>
                    
                    <div id="thumb_avancement" class="absolute top-1/2 -translate-y-1/2 w-5 h-5 bg-white border-2 border-red-500 rounded-full shadow pointer-events-none z-10 transition-all duration-150" style="left: calc(0% - 10px);"></div>
            
                    <input type="range" id="avancement" min="0" max="100" value="0" step="5" class="absolute top-0 left-0 w-full h-full opacity-0 cursor-pointer z-20" oninput="
                        document.getElementById('val_avancement').innerText = this.value;
                        document.getElementById('progress_avancement').style.width = this.value + '%';
                        let hue = Math.round(this.value * 1.2);
                        document.getElementById('progress_avancement').style.backgroundColor = 'hsl(' + hue + ', 85%, 45%)';
                        document.getElementById('thumb_avancement').style.left = 'calc(' + this.value + '% - 10px)';
                        document.getElementById('thumb_avancement').style.borderColor = 'hsl(' + hue + ', 85%, 45%)';
                    ">
                </div>
                <div class="flex justify-between text-[10px] text-gray-400 mt-3 font-bold uppercase tracking-widest"><span>Projet (0%)</span><span>Hors d'eau</span><span>Livré (100%)</span></div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-1">État des travaux <span class="text-red-500">*</span></label>
                <select id="etat_bien" required class="w-full px-4 py-2 border border-grayBorder rounded-lg outline-none bg-white">
                    <option value="">Sélectionnez...</option>
                    <option value="Neuf / Sur plan (VEFA)">Neuf / Sur plan (VEFA)</option>
                    <option value="Plateau brut à aménager">Plateau brut à aménager</option>
                    <option value="Plateau viabilisé">Plateau viabilisé</option>
                    <option value="À rénover intégralement">À rénover intégralement</option>
                    <option value="À rafraîchir">À rafraîchir</option>
                    <option value="Rénové à neuf (Clé en main)">Rénové à neuf (Clé en main)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Dispositif Fiscal (Optionnel)</label>
                <select id="dispositif_fiscal" class="w-full px-4 py-2 border border-grayBorder rounded-lg outline-none bg-white">
                    <option value="">Aucun spécifique</option>
                    <option value="Déficit Foncier">Déficit Foncier</option>
                    <option value="Loi Pinel">Loi Pinel</option>
                    <option value="Loi Malraux">Loi Malraux</option>
                    <option value="Loi Denormandie">Loi Denormandie</option>
                    <option value="LMNP">LMNP (Meublé Non Pro)</option>
                    <option value="LMP">LMP (Meublé Pro)</option>
                    <option value="Monuments Historiques">Monuments Historiques</option>
                    <option value="Éligible PTZ">Éligible PTZ</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Frais de notaire</label>
                <select id="frais_notaire" class="w-full px-4 py-2 border border-grayBorder rounded-lg outline-none bg-white">
                    <option value="">Non précisé</option>
                    <option value="Frais réduits (2 à 3%)">Frais réduits (2 à 3% - Neuf)</option>
                    <option value="Frais classiques (7 à 8%)">Frais classiques (7 à 8% - Ancien)</option>
                </select>
            </div>
        </div>
    </div>

    <div id="bloc_dynamique_suite" class="hidden space-y-8 transition-all duration-500">
        
        <div>
            <h3 class="text-lg font-bold text-dark border-b pb-2 mb-4">Localisation & Marché</h3>
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <label class="block text-sm font-medium mb-1">Adresse complète <span class="text-red-500">*</span></label>
                    <input type="text" id="adresse-input" class="w-full px-4 py-2 border border-grayBorder rounded-lg outline-none" placeholder="Rechercher une adresse..." autocomplete="off">
                    <div class="grid grid-cols-2 gap-4">
                        <input type="text" id="cp-input" class="w-full px-4 py-2 border border-grayBorder bg-white rounded-lg focus:border-primary outline-none" placeholder="Code postal">
                        <input type="text" id="ville-input" class="w-full px-4 py-2 border border-grayBorder bg-white rounded-lg focus:border-primary outline-none" placeholder="Ville">
                    </div>
                    <input type="hidden" id="lat-input"><input type="hidden" id="lng-input">
                    
                    <div class="mt-4 p-5 bg-blue-50 border border-blue-100 rounded-xl relative">
                        <div class="absolute top-4 right-4 text-blue-300 text-3xl opacity-50"><i class="fa-solid fa-chart-line"></i></div>
                        <label class="block text-sm font-bold text-blue-900 mb-1">Prix moyen du secteur au m² (€)</label>
                        <p class="text-xs text-blue-700 mb-3 max-w-[90%]">Indiquez le prix moyen constaté <b>dans ce quartier</b>. Le site calculera automatiquement le prix au m² de vos offres pour prouver la rentabilité de l'investissement.</p>
                        <input type="text" id="prix_m2_secteur" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="Ex: 4500" class="w-1/2 px-4 py-2 border border-blue-200 rounded-lg outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="relative h-full min-h-[250px] rounded-xl border border-grayBorder overflow-hidden z-0 shadow-inner">
                    <div id="map" class="absolute inset-0 z-0"></div>
                    <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-full z-10 pointer-events-none drop-shadow-lg"><i class="fa-solid fa-location-dot text-4xl text-primary"></i></div>
                </div>
            </div>
        </div>

        <div id="bloc_standard" class="space-y-8">
            
            <div>
                <h3 class="text-lg font-bold text-dark border-b pb-2 mb-4">Surfaces et Agencement (Bâtiment)</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div><label class="block text-sm font-medium mb-1">Surface Hab. totale <span class="text-red-500">*</span></label><input type="text" id="surface" placeholder="m²" oninput="this.value = this.value.replace(/[^0-9.]/g, '')" class="w-full px-4 py-2 border border-grayBorder rounded-lg outline-none focus:border-primary"></div>
                    <div><label class="block text-sm font-medium mb-1">Surface Carrez totale</label><input type="text" id="surface_carrez" placeholder="m²" oninput="this.value = this.value.replace(/[^0-9.]/g, '')" class="w-full px-4 py-2 border border-grayBorder rounded-lg outline-none focus:border-primary"></div>
                    <div><label class="block text-sm font-medium mb-1">Nb total de Pièces <span class="text-red-500">*</span></label><input type="text" id="pieces" placeholder="Ex: 8" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full px-4 py-2 border border-grayBorder rounded-lg outline-none focus:border-primary"></div>
                    <div><label class="block text-sm font-medium mb-1">Nb total de Chambres</label><input type="text" id="chambres" placeholder="Ex: 4" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full px-4 py-2 border border-grayBorder rounded-lg outline-none focus:border-primary"></div>
                    
                    <div><label class="block text-sm font-medium mb-1">Salles de bain/eau</label><input type="text" id="salles_de_bain" placeholder="Ex: 2" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full px-4 py-2 border border-grayBorder rounded-lg outline-none focus:border-primary"></div>
                    <div><label class="block text-sm font-medium mb-1">Toilettes (WC)</label><input type="text" id="wc" placeholder="Ex: 2" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full px-4 py-2 border border-grayBorder rounded-lg outline-none focus:border-primary"></div>
                    <div><label class="block text-sm font-medium mb-1">Étage du bien <span class="text-red-500">*</span></label><input type="text" id="etage" placeholder="Ex: RDC, Bâtiment entier..." class="w-full px-4 py-2 border border-grayBorder rounded-lg outline-none focus:border-primary"></div>
                    <div><label class="block text-sm font-medium mb-1">Nb total d'étages <span class="text-red-500">*</span></label><input type="text" id="nombre_etages" placeholder="Ex: 4" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-full px-4 py-2 border border-grayBorder rounded-lg outline-none focus:border-primary"></div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="bg-white p-5 rounded-xl border border-gray-200 shadow-sm">
                    <h4 class="font-bold text-dark mb-4"><i class="fa-solid fa-tree text-green-500 mr-2"></i>Espaces Extérieurs</h4>
                    <div class="space-y-3">
                        <div class="flex flex-col gap-2"><label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" id="ext_jardin" class="w-4 h-4 accent-primary ext-trigger"><span class="text-sm font-medium">Jardin</span></label><div class="ext-surface hidden pl-6"><input type="text" id="surf_jardin" placeholder="Surface en m²" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-32 px-3 py-1 border border-grayBorder rounded text-sm outline-none"></div></div>
                        <div class="flex flex-col gap-2"><label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" id="ext_terrasse" class="w-4 h-4 accent-primary ext-trigger"><span class="text-sm font-medium">Terrasse</span></label><div class="ext-surface hidden pl-6"><input type="text" id="surf_terrasse" placeholder="Surface en m²" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-32 px-3 py-1 border border-grayBorder rounded text-sm outline-none"></div></div>
                        <div class="flex flex-col gap-2"><label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" id="ext_balcon" class="w-4 h-4 accent-primary ext-trigger"><span class="text-sm font-medium">Balcon</span></label><div class="ext-surface hidden pl-6"><input type="text" id="surf_balcon" placeholder="Surface en m²" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-32 px-3 py-1 border border-grayBorder rounded text-sm outline-none"></div></div>
                        <div class="flex flex-col gap-2"><label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" id="ext_loggia" class="w-4 h-4 accent-primary ext-trigger"><span class="text-sm font-medium">Loggia</span></label><div class="ext-surface hidden pl-6"><input type="text" id="surf_loggia" placeholder="Surface en m²" oninput="this.value = this.value.replace(/[^0-9]/g, '')" class="w-32 px-3 py-1 border border-grayBorder rounded text-sm outline-none"></div></div>
                    </div>
                </div>

                <div class="bg-gray-50 p-5 rounded-xl border border-gray-100">
                    <h4 class="font-bold text-dark mb-4"><i class="fa-solid fa-car text-gray-500 mr-2"></i>Annexes & Bâtiment</h4>
                    <div class="space-y-3">
                        <div class="flex flex-col gap-2"><label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" id="has_parking" class="w-4 h-4 accent-primary ext-trigger"><span class="text-sm font-medium">Places de Parking</span></label><div class="ext-surface hidden pl-6"><input type="text" id="parking_places" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="Combien ?" class="w-24 px-3 py-1 border border-grayBorder rounded text-sm outline-none"></div></div>
                        <div class="flex flex-col gap-2"><label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" id="has_garage" class="w-4 h-4 accent-primary ext-trigger"><span class="text-sm font-medium">Garages / Box</span></label><div class="ext-surface hidden pl-6"><input type="text" id="garage_places" oninput="this.value = this.value.replace(/[^0-9]/g, '')" placeholder="Combien ?" class="w-24 px-3 py-1 border border-grayBorder rounded text-sm outline-none"></div></div>
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" id="cave" class="w-4 h-4 accent-primary"><span class="text-sm font-medium">Cave</span></label>
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" id="ascenseur" class="w-4 h-4 accent-primary"><span class="text-sm font-medium">Ascenseur</span></label>
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" id="acces_pmr" class="w-4 h-4 accent-primary"><span class="text-sm font-medium text-blue-600">Accès PMR</span></label>
                    </div>
                </div>
            </div>

            <div>
                <h3 class="text-lg font-bold text-dark border-b pb-2 mb-4">Énergie et Confort Thermique</h3>
                <div class="mb-6">
                    <label class="block text-sm font-medium mb-3">Modes de chauffage / refroidissement</label>
                    <div class="flex flex-wrap gap-4 bg-white p-4 rounded-xl border border-gray-200">
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" value="Électrique" class="chk-chauffage w-4 h-4 accent-primary"><span class="text-sm">Électrique</span></label>
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" value="Pompe à chaleur" class="chk-chauffage w-4 h-4 accent-primary"><span class="text-sm">Pompe à chaleur</span></label>
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" value="Gaz" class="chk-chauffage w-4 h-4 accent-primary"><span class="text-sm">Gaz</span></label>
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" value="Climatisation réversible" class="chk-chauffage w-4 h-4 accent-blue-500"><span class="text-sm">Clim réversible</span></label>
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" value="Poêle à bois/granulés" class="chk-chauffage w-4 h-4 accent-primary"><span class="text-sm">Poêle/Insert</span></label>
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" value="Réseau urbain" class="chk-chauffage w-4 h-4 accent-primary"><span class="text-sm">Réseau urbain</span></label>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <div>
                        <label class="block text-sm font-medium mb-2">Classe Énergétique (DPE)</label>
                        <div class="flex flex-wrap gap-1">
                            <?php $colors = ['A'=>'#32984b', 'B'=>'#33cc31', 'C'=>'#cbf000', 'D'=>'#ffff00', 'E'=>'#f0b000', 'F'=>'#eb680f', 'G'=>'#d21016'];
                            foreach($colors as $lettre => $hex): ?>
                                <input type="radio" name="dpe" id="dpe_<?= $lettre ?>" value="<?= $lettre ?>" class="hidden dpe-radio"><label for="dpe_<?= $lettre ?>" class="dpe-label" style="background-color: <?= $hex ?>; color: <?= in_array($lettre, ['C','D']) ? '#000' : '#fff' ?>;"><?= $lettre ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Émissions Gaz (GES)</label>
                        <div class="flex flex-wrap gap-1">
                            <?php foreach($colors as $lettre => $hex): ?>
                                <input type="radio" name="ges" id="ges_<?= $lettre ?>" value="<?= $lettre ?>" class="hidden dpe-radio"><label for="ges_<?= $lettre ?>" class="dpe-label" style="background-color: <?= $hex ?>; color: <?= in_array($lettre, ['C','D']) ? '#000' : '#fff' ?>;"><?= $lettre ?></label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="bloc_immeuble" class="hidden mt-10">
            <div class="bg-orange-50 border-2 border-primary/30 p-6 rounded-2xl shadow-sm">
                <div class="flex justify-between items-center mb-6 border-b border-primary/20 pb-4">
                    <div>
                        <h3 class="text-2xl font-bold text-dark flex items-center gap-2"><i class="fa-solid fa-building text-primary"></i> Les logements de l'immeuble</h3>
                        <p class="text-gray-600 mt-1">Créez des fiches complètes pour chaque lot. <span class="text-red-500 font-bold">Les champs avec * sont obligatoires.</span></p>
                    </div>
                    <button type="button" onclick="ajouterLotImmeuble()" class="bg-primary text-white px-5 py-3 rounded-xl font-bold hover:bg-primaryHover transition-colors shadow-md flex items-center gap-2"><i class="fa-solid fa-plus"></i> Ajouter un lot</button>
                </div>
                
                <div id="conteneur-lots" class="space-y-6">
                    </div>
            </div>
        </div>
        
    </div>
</div>