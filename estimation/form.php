<div id="form-container" class="max-w-4xl mx-auto bg-white rounded-2xl shadow-card border border-grayBorder overflow-hidden">
    <form id="estimation-form-full" class="p-4 sm:p-6 md:p-10">
        
        <input type="hidden" name="_subject" value="Nouvelle demande d'estimation détaillée !">
        <input type="hidden" name="_captcha" value="false">
        <input type="hidden" name="_template" value="table">

        <div id="step-1" class="form-step active space-y-8">
            <div class="flex items-center gap-3 mb-6 border-b border-grayBorder pb-4">
                <h2 class="text-xl font-bold text-dark">1. Caractéristiques du bien</h2>
            </div>

            <div class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-dark mb-3">Type de bien <span class="text-red-500">*</span></label>
                    <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-6 gap-3">
                        <label class="cursor-pointer relative">
                            <input type="radio" name="Type_de_bien" value="Appartement" class="custom-radio sr-only" checked>
                            <div class="border border-grayBorder rounded-lg p-3 text-center hover:border-primary/50 transition-colors bg-white h-full flex flex-col justify-center">
                                <i class="fa-regular fa-building text-xl mb-2 radio-icon text-gray-400"></i>
                                <div class="font-medium text-xs">Appartement</div>
                            </div>
                        </label>
                        <label class="cursor-pointer relative">
                            <input type="radio" name="Type_de_bien" value="Maison" class="custom-radio sr-only">
                            <div class="border border-grayBorder rounded-lg p-3 text-center hover:border-primary/50 transition-colors bg-white h-full flex flex-col justify-center">
                                <i class="fa-solid fa-house text-xl mb-2 radio-icon text-gray-400"></i>
                                <div class="font-medium text-xs">Maison</div>
                            </div>
                        </label>
                        <label class="cursor-pointer relative">
                            <input type="radio" name="Type_de_bien" value="Loft" class="custom-radio sr-only">
                            <div class="border border-grayBorder rounded-lg p-3 text-center hover:border-primary/50 transition-colors bg-white h-full flex flex-col justify-center">
                                <i class="fa-solid fa-industry text-xl mb-2 radio-icon text-gray-400"></i>
                                <div class="font-medium text-xs">Loft / Atelier</div>
                            </div>
                        </label>
                        <label class="cursor-pointer relative">
                            <input type="radio" name="Type_de_bien" value="Immeuble" class="custom-radio sr-only">
                            <div class="border border-grayBorder rounded-lg p-3 text-center hover:border-primary/50 transition-colors bg-white h-full flex flex-col justify-center">
                                <i class="fa-solid fa-city text-xl mb-2 radio-icon text-gray-400"></i>
                                <div class="font-medium text-xs">Immeuble</div>
                            </div>
                        </label>
                        <label class="cursor-pointer relative">
                            <input type="radio" name="Type_de_bien" value="Local commercial" class="custom-radio sr-only">
                            <div class="border border-grayBorder rounded-lg p-3 text-center hover:border-primary/50 transition-colors bg-white h-full flex flex-col justify-center">
                                <i class="fa-solid fa-store text-xl mb-2 radio-icon text-gray-400"></i>
                                <div class="font-medium text-xs">Local</div>
                            </div>
                        </label>
                        <label class="cursor-pointer relative">
                            <input type="radio" name="Type_de_bien" value="Terrain" class="custom-radio sr-only">
                            <div class="border border-grayBorder rounded-lg p-3 text-center hover:border-primary/50 transition-colors bg-white h-full flex flex-col justify-center">
                                <i class="fa-solid fa-tree text-xl mb-2 radio-icon text-gray-400"></i>
                                <div class="font-medium text-xs">Terrain</div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-dark mb-1">Adresse complète <span class="text-red-500">*</span></label>
                            <input type="text" id="adresse-input" name="Adresse" class="w-full px-4 py-3 border border-grayBorder rounded-md focus:ring-2 focus:ring-primary/50 outline-none" placeholder="Ex: 12 rue de la Paix" autocomplete="off">
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <input type="text" id="cp-input" name="Code_Postal" class="w-full px-4 py-3 border border-grayBorder rounded-md focus:ring-2 focus:ring-primary/50 outline-none" placeholder="Code postal">
                            <input type="text" id="ville-input" name="Ville" class="w-full px-4 py-3 border border-grayBorder rounded-md focus:ring-2 focus:ring-primary/50 outline-none" placeholder="Ville">
                        </div>
                        <p class="text-xs text-textMuted mt-2"><i class="fa-solid fa-circle-info text-primary mr-1"></i> Tapez l'adresse ou déplacez la carte pour pointer votre bien.</p>
                    </div>
                    
                    <div class="relative h-64 lg:h-full min-h-[250px] rounded-lg border border-grayBorder overflow-hidden z-0">
                        <div id="map" class="absolute inset-0 z-0"></div>
                        <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-full z-10 pointer-events-none drop-shadow-xl">
                            <i class="fa-solid fa-location-dot text-4xl text-primary"></i>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1">Surface Hab. (m²) <span class="text-red-500">*</span></label>
                        <input type="number" name="Surface" required class="w-full px-3 py-3 border border-grayBorder rounded-md focus:ring-2 focus:ring-primary/50 outline-none" placeholder="Ex: 80">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1">Surface Carrez</label>
                        <input type="number" step="0.1" name="Surface_Carrez" class="w-full px-3 py-3 border border-grayBorder rounded-md focus:ring-2 focus:ring-primary/50 outline-none" placeholder="Ex: 78.5">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1">Pièces <span class="text-red-500">*</span></label>
                        <input type="number" name="Pieces" required class="w-full px-3 py-3 border border-grayBorder rounded-md focus:ring-2 focus:ring-primary/50 outline-none" placeholder="Ex: 4">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1">Chambres</label>
                        <input type="number" name="Chambres" class="w-full px-3 py-3 border border-grayBorder rounded-md focus:ring-2 focus:ring-primary/50 outline-none" placeholder="Ex: 2">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1">Sdb / Eau</label>
                        <input type="number" name="Salles_de_bain" class="w-full px-3 py-3 border border-grayBorder rounded-md focus:ring-2 focus:ring-primary/50 outline-none" placeholder="Ex: 1">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1">WC</label>
                        <input type="number" name="WC" class="w-full px-3 py-3 border border-grayBorder rounded-md focus:ring-2 focus:ring-primary/50 outline-none" placeholder="Ex: 1">
                    </div>
                    <div class="col-span-2">
                        <label class="block text-sm font-medium text-dark mb-1" title="Laissez vide si Maison">Étage du bien / Nb total d'étages</label>
                        <div class="flex gap-2">
                            <input type="text" name="Etage" class="w-1/2 px-3 py-3 border border-grayBorder rounded-md focus:ring-2 focus:ring-primary/50 outline-none" placeholder="Étage (Ex: 3)">
                            <input type="number" name="Nombre_Etages" class="w-1/2 px-3 py-3 border border-grayBorder rounded-md focus:ring-2 focus:ring-primary/50 outline-none" placeholder="Sur (Ex: 5)">
                        </div>
                    </div>
                </div>

                <div class="bg-grayLight p-4 rounded-lg border border-grayBorder">
                    <label class="block text-sm font-medium text-dark mb-3">Extérieurs & Annexes (Les "Plus")</label>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="Plus_Jardin" value="Oui" class="w-4 h-4 text-primary"> Jardin</label>
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="Plus_Terrasse" value="Oui" class="w-4 h-4 text-primary"> Terrasse / Balcon</label>
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="Plus_Ascenseur" value="Oui" class="w-4 h-4 text-primary"> Ascenseur</label>
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="Plus_PMR" value="Oui" class="w-4 h-4 text-blue-600"> Accès PMR</label>
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="Plus_Parking" value="Oui" class="w-4 h-4 text-primary"> Parking</label>
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="Plus_Garage" value="Oui" class="w-4 h-4 text-primary"> Garage fermé</label>
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" name="Plus_Cave" value="Oui" class="w-4 h-4 text-primary"> Cave</label>
                    </div>
                </div>

                <div class="pt-6 border-t border-grayBorder flex justify-end">
                    <button type="button" class="btn-next bg-dark hover:bg-gray-800 text-white py-3 px-8 rounded-lg font-bold transition-colors shadow-sm flex items-center gap-2">
                        Étape suivante <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <div id="step-2" class="form-step space-y-8">
            <div class="flex items-center gap-3 mb-6 border-b border-grayBorder pb-4">
                <h2 class="text-xl font-bold text-dark">2. État du projet & Énergie</h2>
            </div>

            <div class="space-y-6">
                
                <div class="bg-orange-50 p-5 rounded-xl border border-primary/20">
                    <label class="block text-sm font-bold text-dark mb-1">État actuel des travaux <span class="text-red-500">*</span></label>
                    <select name="Etat_General" required class="w-full px-4 py-3 mb-5 border border-primary/30 rounded-md focus:outline-none focus:ring-2 focus:ring-primary/50 bg-white">
                        <option value="">Sélectionnez un état d'avancement...</option>
                        <option value="Plateau brut à aménager">Plateau brut à aménager</option>
                        <option value="Plateau viabilisé">Plateau viabilisé (Fluides en attente)</option>
                        <option value="À rénover intégralement">À rénover intégralement</option>
                        <option value="À rafraîchir">À rafraîchir</option>
                        <option value="Neuf / Sur plan (VEFA)">Neuf / Sur plan (VEFA)</option>
                        <option value="Rénové à neuf (Clé en main)">Rénové à neuf (Clé en main)</option>
                    </select>

                    <div class="flex justify-between items-end mb-2">
                        <label class="block text-sm font-medium text-dark">Avancement global du projet (Estimation)</label>
                        <span class="text-primary font-bold"><span id="val_avancement_form">0</span>%</span>
                    </div>
                    <div class="relative w-full h-2 bg-white border border-gray-300 rounded-full mt-2 shadow-inner">
                        <div id="progress_avancement_form" class="absolute top-0 left-0 h-full rounded-full transition-all duration-150 pointer-events-none bg-red-500" style="width: 0%;"></div>
                        <div id="thumb_avancement_form" class="absolute top-1/2 -translate-y-1/2 w-4 h-4 bg-white border-2 border-red-500 rounded-full shadow pointer-events-none z-10 transition-all duration-150" style="left: calc(0% - 8px);"></div>
                        <input type="range" name="Avancement_Projet" min="0" max="100" value="0" step="5" class="absolute top-0 left-0 w-full h-full opacity-0 cursor-pointer z-20" oninput="
                            document.getElementById('val_avancement_form').innerText = this.value;
                            document.getElementById('progress_avancement_form').style.width = this.value + '%';
                            let hue = Math.round(this.value * 1.2);
                            document.getElementById('progress_avancement_form').style.backgroundColor = 'hsl(' + hue + ', 85%, 45%)';
                            document.getElementById('thumb_avancement_form').style.left = 'calc(' + this.value + '% - 8px)';
                            document.getElementById('thumb_avancement_form').style.borderColor = 'hsl(' + hue + ', 85%, 45%)';
                        ">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1">Diagnostic (DPE)</label>
                        <select name="DPE" class="w-full px-4 py-3 border border-grayBorder rounded-md focus:outline-none focus:ring-2 focus:ring-primary/50 bg-white">
                            <option value="Non renseigné">Non renseigné</option>
                            <option value="A">A - Très performant</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            <option value="F">F - Passoire thermique</option>
                            <option value="G">G - Passoire thermique</option>
                            <option value="Vierge">DPE Vierge / Non soumis</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1">Émissions (GES)</label>
                        <select name="GES" class="w-full px-4 py-3 border border-grayBorder rounded-md focus:outline-none focus:ring-2 focus:ring-primary/50 bg-white">
                            <option value="Non renseigné">Non renseigné</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                            <option value="E">E</option>
                            <option value="F">F</option>
                            <option value="G">G</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1">Type de chauffage</label>
                        <select name="Chauffage" class="w-full px-4 py-3 border border-grayBorder rounded-md focus:outline-none focus:ring-2 focus:ring-primary/50 bg-white">
                            <option value="Non précisé">Sélectionner...</option>
                            <option value="Électrique">Électrique (Radiateurs)</option>
                            <option value="Pompe à chaleur">Pompe à chaleur</option>
                            <option value="Climatisation réversible">Climatisation réversible</option>
                            <option value="Gaz">Gaz</option>
                            <option value="Poêle à bois/granulés">Poêle à bois/granulés</option>
                            <option value="Réseau urbain / Collectif">Réseau urbain / Collectif</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-dark mb-1">Délai de vente souhaité <span class="text-red-500">*</span></label>
                    <select name="Delai_Vente" required class="w-full px-4 py-3 border border-grayBorder rounded-md focus:outline-none focus:ring-2 focus:ring-primary/50 bg-white">
                        <option value="">Sélectionner...</option>
                        <option value="Urgent">Urgent (Dès que possible)</option>
                        <option value="1 à 3 mois">1 à 3 mois</option>
                        <option value="3 à 6 mois">3 à 6 mois</option>
                        <option value="Plus de 6 mois / En réflexion">Plus de 6 mois / En réflexion</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-dark mb-1">Informations complémentaires sur le projet</label>
                    <textarea name="Informations_Complementaires" class="w-full px-4 py-3 border border-grayBorder rounded-md focus:outline-none focus:ring-2 focus:ring-primary/50" rows="3" placeholder="Travaux à prévoir, viabilisation réalisée ou non, dispositifs fiscaux éventuels (Déficit foncier, Pinel...)..."></textarea>
                </div>

                <div class="pt-6 border-t border-grayBorder flex justify-between">
                    <button type="button" class="btn-prev bg-white border border-grayBorder hover:bg-gray-50 text-dark py-3 px-6 rounded-lg font-bold transition-colors">
                        <i class="fa-solid fa-arrow-left mr-2"></i> Retour
                    </button>
                    <button type="button" class="btn-next bg-dark hover:bg-gray-800 text-white py-3 px-8 rounded-lg font-bold transition-colors shadow-sm flex items-center gap-2">
                        Étape suivante <i class="fa-solid fa-arrow-right"></i>
                    </button>
                </div>
            </div>
        </div>

        <div id="step-3" class="form-step space-y-8">
            <div class="flex items-center gap-3 mb-6 border-b border-grayBorder pb-4">
                <h2 class="text-xl font-bold text-dark">3. Vos coordonnées</h2>
            </div>

            <div class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1">Prénom <span class="text-red-500">*</span></label>
                        <input type="text" name="Prenom" required class="w-full px-4 py-3 border border-grayBorder rounded-md focus:outline-none focus:ring-2 focus:ring-primary/50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1">Nom <span class="text-red-500">*</span></label>
                        <input type="text" name="Nom" required class="w-full px-4 py-3 border border-grayBorder rounded-md focus:outline-none focus:ring-2 focus:ring-primary/50">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1">Email <span class="text-red-500">*</span></label>
                        <input type="email" name="Email" required class="w-full px-4 py-3 border border-grayBorder rounded-md focus:outline-none focus:ring-2 focus:ring-primary/50" placeholder="votre@email.com">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-dark mb-1">Téléphone <span class="text-red-500">*</span></label>
                        <input type="tel" name="Telephone" required class="w-full px-4 py-3 border border-grayBorder rounded-md focus:outline-none focus:ring-2 focus:ring-primary/50" placeholder="06 12 34 56 78">
                    </div>
                </div>

                <div class="pt-8 flex flex-col items-center border-t border-grayBorder mt-8">
                    <div class="w-full flex justify-between mb-6">
                        <button type="button" class="btn-prev bg-white border border-grayBorder hover:bg-gray-50 text-dark py-3 px-6 rounded-lg font-bold transition-colors">
                            <i class="fa-solid fa-arrow-left mr-2"></i> Retour
                        </button>
                        <button type="submit" id="submit-btn" class="bg-primary hover:bg-primaryHover text-white py-3 px-8 rounded-lg font-bold text-lg transition-colors shadow-float flex items-center gap-3">
                            Envoyer ma demande <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-textMuted">
                        <i class="fa-solid fa-shield-halved text-green-500"></i>
                        <span>Vos données sont sécurisées et strictement confidentielles.</span>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>