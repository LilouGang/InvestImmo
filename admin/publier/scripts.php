<script>
    // ==========================================
    // 1. GESTION DU MULTI-STEP WIZARD
    // ==========================================
    let currentStep = 0;
    const steps = document.querySelectorAll('.form-step');
    const indicators = document.querySelectorAll('.step-indicator');
    const progressBar = document.getElementById('progress-bar');
    const btnPrev = document.getElementById('btn-prev');
    const btnNext = document.getElementById('btn-next');
    const btnSubmit = document.getElementById('btn-submit');

    function updateWizard() {
        steps.forEach((s, i) => s.classList.toggle('active', i === currentStep));
        
        indicators.forEach((ind, i) => {
            if (i <= currentStep) {
                ind.className = "step-indicator z-10 w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-bold text-sm shadow-md transition-colors";
            } else {
                ind.className = "step-indicator z-10 w-10 h-10 rounded-full bg-white border-2 border-grayBorder text-gray-400 flex items-center justify-center font-bold text-sm transition-colors";
            }
        });
        
        const progress = Math.min(12.5 + currentStep * 25, 75);
        progressBar.style.width = progress + '%';
        
        btnPrev.classList.toggle('hidden', currentStep === 0);
        
        if (currentStep === steps.length - 1) {
            btnNext.classList.add('hidden');
            btnSubmit.classList.remove('hidden');
        } else {
            btnNext.classList.remove('hidden');
            btnSubmit.classList.add('hidden');
        }
    }
    updateWizard();

    btnNext.addEventListener('click', () => {
        let isValid = true;

        // Validation Étape 1 (Informations)
        if (currentStep === 0) {
            // 1. Validation Globale
            let fieldsToCheck = ['contact_prenom', 'contact_nom', 'contact_email', 'titre', 'description', 'adresse-input', 'etat_bien'];
            
            // Si ce n'est PAS un immeuble, on exige surface, pièces, et les étages
            if(document.getElementById('type_bien').value !== 'Immeuble' && document.getElementById('type_bien').value !== '') {
                fieldsToCheck.push('surface', 'pieces', 'etage', 'nombre_etages');
            }

            fieldsToCheck.forEach(id => {
                const el = document.getElementById(id);
                if (el && (!el.value || !el.value.trim())) {
                    isValid = false;
                    el.classList.remove('border-grayBorder');
                    el.classList.add('border-red-500', 'bg-red-50', 'ring-1', 'ring-red-500');
                }
            });
            
            const typeBien = document.getElementById('type_bien');
            if(!typeBien.value) {
                isValid = false;
                typeBien.classList.remove('border-grayBorder');
                typeBien.classList.add('border-red-500', 'bg-red-50', 'ring-1', 'ring-red-500');
            }

            // 2. Validation Spécifique aux Lots (Si Immeuble)
            if(typeBien.value === 'Immeuble') {
                const lots = document.querySelectorAll('.lot-detail-card');
                if(lots.length === 0) {
                    isValid = false;
                    alert("Vous devez ajouter au moins un logement à cet immeuble.");
                } else {
                    lots.forEach(lot => {
                        let requiredLotClasses = ['.l-nom', '.l-type', '.l-surf', '.l-pieces', '.l-etage'];
                        requiredLotClasses.forEach(cls => {
                            let input = lot.querySelector(cls);
                            if(input && (!input.value || !input.value.trim())) {
                                isValid = false;
                                input.classList.remove('border-grayBorder');
                                input.classList.add('border-red-500', 'bg-red-50', 'ring-1', 'ring-red-500');
                                input.addEventListener('input', function() { this.classList.remove('border-red-500', 'bg-red-50'); this.classList.add('border-grayBorder'); });
                            }
                        });
                    });
                }
            }
        }

        // Validation Étape 2 (Photos)
        if (currentStep === 1) {
            photosData.forEach(p => {
                if (!p.tag || p.tag === '') {
                    isValid = false;
                    p.hasError = true;
                }
            });
            if (!isValid) renderPhotos();
        }

        // Validation Étape 3 (Étapes obligatoires)
        if (currentStep === 2) {
            const etapes = document.querySelectorAll('.etape-item');
            let hasActuel = false;
            
            let step3Error = document.getElementById('step3-global-error');
            if (!step3Error) {
                step3Error = document.createElement('div');
                step3Error.id = 'step3-global-error';
                step3Error.className = 'text-red-500 bg-red-50 p-3 rounded-lg border border-red-200 font-bold mb-4 hidden';
                document.getElementById('etapes-list').parentNode.insertBefore(step3Error, document.getElementById('etapes-list'));
            }
            step3Error.classList.add('hidden');

            if (etapes.length === 0) {
                isValid = false;
                step3Error.innerText = "Veuillez ajouter au moins une étape pour ce projet.";
                step3Error.classList.remove('hidden');
            } else {
                etapes.forEach(etape => {
                    const statut = etape.querySelector('.e-statut').value;
                    if (statut === 'Actuel') hasActuel = true;

                    const titre = etape.querySelector('.e-titre');
                    const desc = etape.querySelector('.e-desc');
                    const prix = etape.querySelector('.e-prix');
                    const inclus = etape.querySelector('.e-inclus');
                    const noninclus = etape.querySelector('.e-noninclus');

                    const hasRealContent = (val) => val.replace(/[✓✗]\s?/g, '').trim().length > 0;
                    let stepValid = true;

                    [titre, desc, prix].forEach(el => {
                        if (!el.value.trim()) {
                            stepValid = false;
                            el.classList.add('border-red-500', 'bg-red-50', 'ring-1', 'ring-red-500');
                        }
                    });

                    if (!hasRealContent(inclus.value)) { stepValid = false; inclus.classList.add('border-red-500', 'bg-red-50', 'ring-1', 'ring-red-500'); }
                    if (!hasRealContent(noninclus.value)) { stepValid = false; noninclus.classList.add('border-red-500', 'bg-red-50', 'ring-1', 'ring-red-500'); }

                    if (!stepValid) {
                        isValid = false;
                        etape.querySelector('.view-mode').classList.add('hidden');
                        etape.querySelector('.edit-mode').classList.remove('hidden');
                        
                        let errorMsg = etape.querySelector('.etape-error-msg');
                        if (!errorMsg) {
                            errorMsg = document.createElement('p');
                            errorMsg.className = 'etape-error-msg text-red-500 text-sm font-bold mt-4 text-right';
                            etape.querySelector('.edit-mode').appendChild(errorMsg);
                        }
                        errorMsg.innerText = "Veuillez remplir les champs obligatoires encadrés en rouge.";
                        errorMsg.classList.remove('hidden');
                    }
                });

                if (isValid && !hasActuel) {
                    isValid = false;
                    step3Error.innerText = "Vous devez définir au moins une étape avec le statut 'Actuel'.";
                    step3Error.classList.remove('hidden');
                }
            }
        }

        if (!isValid) return; 

        if (currentStep < steps.length - 1) { 
            currentStep++; 
            updateWizard(); 
        }
    });
    
    btnPrev.addEventListener('click', () => {
        if (currentStep > 0) { currentStep--; updateWizard(); }
    });

    ['contact_prenom', 'contact_nom', 'contact_email', 'titre', 'description', 'adresse-input', 'surface', 'pieces', 'etage', 'nombre_etages', 'etat_bien'].forEach(id => {
        const el = document.getElementById(id);
        if(el) {
            el.addEventListener('input', function() {
                this.classList.remove('border-red-500', 'bg-red-50', 'ring-1', 'ring-red-500');
                this.classList.add('border-grayBorder');
            });
        }
    });

    function clearError(el) {
        el.classList.remove('border-red-500', 'bg-red-50', 'ring-1', 'ring-red-500');
    }

    // ==========================================
    // 2. GESTION DE LA CARTE (Leaflet + API BAN)
    // ==========================================
    var map = L.map('map', {zoomControl: false}).setView([46.603354, 1.888334], 5);
    L.control.zoom({ position: 'bottomright' }).addTo(map);
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

    const addrInput = document.getElementById('adresse-input');
    const cpInput = document.getElementById('cp-input');
    const villeInput = document.getElementById('ville-input');
    const latInput = document.getElementById('lat-input');
    const lngInput = document.getElementById('lng-input');
    let typingTimer;
    let isMapMovingByUser = true;

    function searchAddress() {
        clearTimeout(typingTimer);
        typingTimer = setTimeout(() => {
            // Combinaison des 3 champs
            let query = [addrInput.value, cpInput.value, villeInput.value].filter(Boolean).join(' ').trim();
            
            if(query.length > 4) {
                fetch(`https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(query)}&limit=1`)
                .then(res => res.json())
                .then(data => {
                    if(data.features && data.features.length > 0) {
                        let coords = data.features[0].geometry.coordinates;
                        let props = data.features[0].properties;
                        
                        isMapMovingByUser = false; 
                        map.flyTo([coords[1], coords[0]], 16, {duration: 1.5});
                        
                        cpInput.value = props.postcode || cpInput.value;
                        villeInput.value = props.city || villeInput.value;
                        latInput.value = coords[1];
                        lngInput.value = coords[0];
                    }
                });
            }
        }, 800);
    }

    // On écoute les modifications sur les 3 champs
    addrInput.addEventListener('keyup', searchAddress);
    cpInput.addEventListener('keyup', searchAddress);
    villeInput.addEventListener('keyup', searchAddress);

    map.on('moveend', function() {
        if(!isMapMovingByUser) { isMapMovingByUser = true; return; }
        var center = map.getCenter();
        fetch(`https://api-adresse.data.gouv.fr/reverse/?lon=${center.lng}&lat=${center.lat}`)
        .then(res => res.json())
        .then(data => {
            if(data.features && data.features.length > 0) {
                let props = data.features[0].properties;
                addrInput.value = props.name || '';
                cpInput.value = props.postcode || '';
                villeInput.value = props.city || '';
                latInput.value = center.lat;
                lngInput.value = center.lng;
            }
        });
    });

    document.querySelectorAll('#btn-next, #btn-prev').forEach(btn => {
        btn.addEventListener('click', () => { setTimeout(() => { map.invalidateSize(); }, 300); });
    });

    // ==========================================
    // 3. GESTION DES FICHIERS ET LISTES DYNAMIQUES
    // ==========================================
    let photosData = []; 
    let plansData = [];  

    // --- PHOTOS ---
    function handlePhotoSelect(e) {
        Array.from(e.target.files).forEach(file => {
            if(photosData.length < 50) {
                photosData.push({
                    id: 'photo_' + Date.now() + '_' + Math.floor(Math.random() * 1000),
                    file: file,
                    url: URL.createObjectURL(file),
                    tag: '', 
                    hasError: false
                });
            }
        });
        e.target.value = '';
        renderPhotos();
    }

    function renderPhotos() {
        const list = document.getElementById('photos-list');
        list.innerHTML = '';
        const tagsPredefinis = ["Façade", "Salon", "Séjour", "Cuisine", "Chambre", "Salle de bain", "Jardin", "Terrasse", "Piscine", "Garage", "Plan", "Autre"];

        if (photosData.length > 0) {
            list.innerHTML += `<div class="w-full text-center text-dark font-extrabold text-lg mb-4">Image principale</div>`;
        }

        photosData.forEach((p, index) => {
            let options = `<option value="" disabled ${p.tag === '' ? 'selected' : ''}>Choisir un tag...</option>`;
            options += tagsPredefinis.map(t => `<option value="${t}" ${p.tag === t ? 'selected' : ''}>${t}</option>`).join('');
            
            let isFirst = index === 0;
            
            if (index === 1) {
                list.innerHTML += `<div class="w-full text-center text-gray-500 font-extrabold text-base mb-4 mt-6 pt-4 border-t border-gray-200">Autres images par ordre d'apparition</div>`;
            }

            let numberDisplay = isFirst ? `<div class="w-6 flex-shrink-0 text-center"></div>` : `<div class="w-6 text-center font-bold text-gray-400 text-sm flex-shrink-0">${index}</div>`;

            let selectClass = (p.tag === '' && p.hasError) 
                ? 'border-red-500 bg-red-50 ring-2 ring-red-500 text-red-700' 
                : 'border-gray-300 bg-gray-50 hover:border-primary text-dark';

            list.innerHTML += `
                <li data-id="${p.id}" class="photo-item flex flex-col group">
                    <div class="flex items-center gap-3 p-3 bg-white border border-grayBorder rounded-xl shadow-sm hover:border-primary/50 transition-colors cursor-grab">
                        <i class="fa-solid fa-grip-vertical text-gray-300 group-hover:text-primary transition-colors"></i>
                        ${numberDisplay}
                        <div class="w-20 h-20 rounded-lg bg-gray-200 overflow-hidden flex-shrink-0 border border-gray-100">
                            <img src="${p.url}" class="w-full h-full object-cover">
                        </div>
                        <div class="flex-grow flex items-center gap-2">
                            <div class="flex-grow">
                                <select id="select-${p.id}" onchange="updatePhotoTag('${p.id}', this.value)" class="w-full text-sm font-bold border ${selectClass} rounded-lg focus:ring-2 focus:ring-primary/50 outline-none py-2 px-3 transition-all cursor-pointer shadow-sm appearance-none" style="background-image: url('data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23111827%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.5-12.8z%22%2F%3E%3C%2Fsvg%3E'); background-repeat: no-repeat; background-position: right 0.7rem top 50%; background-size: 0.65rem auto;">
                                    ${options}
                                </select>
                                <p class="text-[10px] text-gray-400 truncate mt-1.5 px-1">${p.file.name}</p>
                            </div>
                        </div>
                        <button type="button" onclick="removePhoto('${p.id}')" class="w-10 h-10 flex-shrink-0 bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-colors ml-2"><i class="fa-solid fa-trash"></i></button>
                    </div>
                </li>
            `;
        });
    }

    function updatePhotoTag(id, val) { 
        let photo = photosData.find(p => p.id === id);
        if(photo) {
            photo.tag = val;
            photo.hasError = false;
            let selectEl = document.getElementById('select-'+id);
            if(selectEl) {
                selectEl.classList.remove('border-red-500', 'bg-red-50', 'ring-2', 'ring-red-500', 'text-red-700');
                selectEl.classList.add('border-gray-300', 'bg-gray-50', 'text-dark');
            }
        }
    }

    function removePhoto(id) { 
        photosData = photosData.filter(p => p.id !== id); 
        renderPhotos(); 
    }
    
    // --- PLANS ---
    function handlePlanSelect(e) {
        Array.from(e.target.files).forEach(file => {
            plansData.push({ 
                id: 'plan_' + Date.now() + '_' + Math.floor(Math.random() * 1000), 
                file: file, 
                titre: file.name.replace('.pdf', '') 
            });
        });
        e.target.value = '';
        renderPlans();
    }

    function renderPlans() {
        const list = document.getElementById('plans-list');
        list.innerHTML = '';
        plansData.forEach(p => {
            list.innerHTML += `
                <li data-id="${p.id}" class="plan-item flex items-center gap-3 p-3 bg-white border border-grayBorder rounded-xl shadow-sm cursor-grab hover:border-primary/50 transition-colors group">
                    <i class="fa-solid fa-grip-vertical text-gray-300 px-2 group-hover:text-primary transition-colors"></i>
                    <div class="w-10 h-10 rounded-lg bg-red-50 text-red-500 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-file-pdf text-xl"></i></div>
                    <div class="flex-grow flex items-center gap-3">
                        <input type="text" id="plan-input-${p.id}" value="${p.titre}" onkeyup="updatePlanTitre('${p.id}', this.value)" onblur="lockPlanTitre('${p.id}')" readonly class="w-full text-sm font-bold border border-transparent rounded px-2 py-1.5 bg-transparent focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none transition-colors text-dark" placeholder="Nom du plan">
                        <button type="button" onclick="editPlanTitre('${p.id}')" class="bg-blue-500 hover:bg-blue-600 text-white text-xs w-8 h-8 rounded-lg flex items-center justify-center transition-colors flex-shrink-0 shadow-sm"><i class="fa-solid fa-pen"></i></button>
                    </div>
                    <button type="button" onclick="removePlan('${p.id}')" class="w-10 h-10 flex-shrink-0 bg-red-50 text-red-500 rounded-lg hover:bg-red-500 hover:text-white transition-colors ml-1"><i class="fa-solid fa-trash"></i></button>
                </li>
            `;
        });
    }

    function updatePlanTitre(id, val) { plansData.find(p => p.id === id).titre = val; }
    function removePlan(id) { plansData = plansData.filter(p => p.id !== id); renderPlans(); }

    function editPlanTitre(id) {
        let input = document.getElementById('plan-input-'+id);
        if(input) {
            input.removeAttribute('readonly');
            input.classList.add('border-gray-300', 'bg-white');
            input.focus();
        }
    }

    function lockPlanTitre(id) {
        let input = document.getElementById('plan-input-'+id);
        if(input) {
            input.setAttribute('readonly', 'true');
            input.classList.remove('border-gray-300', 'bg-white');
        }
    }

    // --- ETAPES ---
    function toggleDateDispo(selectEl) {
        const dateInput = selectEl.closest('.edit-mode').querySelector('.e-date');
        if(selectEl.value === 'À Venir') {
            dateInput.disabled = false;
        } else {
            dateInput.disabled = true;
            dateInput.value = ''; 
        }
    }

    function initBulletList(e, type) {
        let symbol = type === 'inclus' ? '✓ ' : '✗ ';
        if (e.target.value.trim() === '') e.target.value = symbol;
    }

    function handleBulletList(e, type) {
        if (e.key === 'Enter') {
            e.preventDefault();
            let symbol = type === 'inclus' ? '✓ ' : '✗ ';
            const start = e.target.selectionStart;
            const end = e.target.selectionEnd;
            const val = e.target.value;
            e.target.value = val.substring(0, start) + '\n' + symbol + val.substring(end);
            e.target.selectionStart = e.target.selectionEnd = start + 1 + symbol.length;
        }
    }

    function formatPrice(input) {
        let val = input.value.replace(/\D/g, ''); 
        input.value = val.replace(/\B(?=(\d{3})+(?!\d))/g, " "); 
        clearError(input);
    }

    function ajouterEtape() {
        const list = document.getElementById('etapes-list');
        const isFirst = list.children.length === 0;
        
        let li = document.createElement('li');
        li.className = 'etape-item bg-white border border-grayBorder rounded-xl shadow-sm mb-4 group overflow-hidden';
        li.innerHTML = `
            <div class="view-mode hidden p-3 sm:p-4 flex items-center gap-3 cursor-grab hover:bg-gray-50 transition-colors">
                <i class="fa-solid fa-grip-vertical text-gray-300 group-hover:text-primary transition-colors px-1"></i>
                <div class="etape-number w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center font-bold text-dark text-sm border border-gray-200 flex-shrink-0"></div>
                <div class="flex-grow overflow-hidden">
                    <h4 class="font-bold text-dark text-sm sm:text-base truncate"><span class="text-gray-400 font-normal mr-1 v-prefix"></span> <span class="v-titre"></span></h4>
                    <p class="text-xs text-gray-500 font-medium truncate"><span class="v-prix">0</span> € <span class="mx-1">•</span> <span class="v-date"></span></p>
                </div>
                <button type="button" onclick="editEtape(this)" class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center hover:bg-blue-600 hover:text-white transition-colors flex-shrink-0"><i class="fa-solid fa-pen text-xs"></i></button>
                <button type="button" onclick="removeEtape(this)" class="w-8 h-8 rounded-lg bg-red-50 text-red-600 flex items-center justify-center hover:bg-red-600 hover:text-white transition-colors flex-shrink-0"><i class="fa-solid fa-trash text-xs"></i></button>
            </div>

            <div class="edit-mode p-4 sm:p-6 bg-white">
                <div class="flex justify-between items-center mb-5 border-b pb-3">
                    <div class="flex items-center gap-2 font-bold text-dark"><i class="fa-solid fa-list-check text-primary"></i> <span class="edit-title-badge"></span></div>
                    <button type="button" onclick="removeEtape(this)" class="text-gray-400 hover:text-red-500 transition-colors"><i class="fa-solid fa-trash"></i></button>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-5">
                    <div class="md:col-span-2 flex items-center">
                        <span class="text-gray-400 text-sm whitespace-nowrap label-etape-num font-medium mr-3"></span>
                        <input type="text" class="e-titre w-full px-4 py-2 border border-grayBorder rounded-lg focus:ring-2 focus:ring-primary/50 outline-none" placeholder="Titre (Ex: Clos couvert)" oninput="clearError(this)">
                    </div>
                    <div class="md:col-span-2">
                        <select onchange="toggleDateDispo(this)" class="e-statut w-full px-4 py-2 border border-grayBorder rounded-lg focus:ring-2 focus:ring-primary/50 outline-none h-full">
                            <option value="Actuel" ${isFirst ? 'selected' : ''}>Actuel (Prix base)</option>
                            <option value="À Venir" ${!isFirst ? 'selected' : ''}>À Venir (Optionnel)</option>
                            <option value="Terminé">Terminé</option>
                        </select>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-5">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Prix de l'étape (€) <span class="text-red-500">*</span></label>
                        <input type="text" class="e-prix w-full px-4 py-2 border border-grayBorder rounded-lg focus:ring-2 focus:ring-primary/50 outline-none font-bold text-dark" value="0" oninput="formatPrice(this); calcPrixM2(this)">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 mb-1">Prix au m² (€/m²) <i class="fa-solid fa-calculator text-gray-300 ml-1"></i></label>
                        <input type="text" class="e-prix-m2 w-full px-4 py-2 border border-gray-200 bg-gray-50 rounded-lg focus:ring-2 focus:ring-primary/50 outline-none text-gray-600" value="0" oninput="formatPrice(this); calcPrixTotal(this)">
                    </div>
                    <div class="md:col-span-1">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Date Disponibilité</label>
                        <input type="text" class="e-date w-full px-4 py-2 border border-grayBorder rounded-lg focus:ring-2 focus:ring-primary/50 outline-none disabled:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed" placeholder="Ex: T3 2025" ${isFirst ? 'disabled' : ''}>
                    </div>
                </div>
                <div class="mb-5"><label class="block text-xs font-medium text-gray-500 mb-1">Description courte</label><input type="text" class="e-desc w-full px-4 py-2 border border-grayBorder rounded-lg focus:ring-2 focus:ring-primary/50 outline-none" oninput="clearError(this)"></div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div><label class="block text-xs font-medium text-gray-500 mb-1">Inclus</label><textarea onkeydown="handleBulletList(event, 'inclus')" onfocus="initBulletList(event, 'inclus')" oninput="clearError(this)" class="e-inclus w-full px-4 py-2 border border-grayBorder rounded-lg bg-green-50/30 text-sm outline-none focus:ring-2 focus:ring-primary/50" rows="3"></textarea></div>
                    <div><label class="block text-xs font-medium text-gray-500 mb-1">Non inclus</label><textarea onkeydown="handleBulletList(event, 'noninclus')" onfocus="initBulletList(event, 'noninclus')" oninput="clearError(this)" class="e-noninclus w-full px-4 py-2 border border-grayBorder rounded-lg bg-red-50/30 text-sm outline-none focus:ring-2 focus:ring-primary/50" rows="3"></textarea></div>
                </div>
                <div class="flex justify-end mt-6 pt-4 border-t border-gray-100">
                    <button type="button" onclick="validateEtape(this)" class="bg-primary text-white px-6 py-2.5 rounded-xl font-bold hover:bg-primaryHover transition-colors shadow-sm flex items-center gap-2"><i class="fa-solid fa-check"></i> Valider l'étape</button>
                </div>
            </div>
        `;
        list.appendChild(li);
        updateEtapeNumbers();
    }
    
    function calcPrixM2(prixInput) {
        let surf = parseFloat(document.getElementById('surface').value) || 0;
        let prix = parseInt(prixInput.value.replace(/\s/g, '')) || 0;
        let pm2Input = prixInput.closest('.edit-mode').querySelector('.e-prix-m2');
        if(surf > 0 && pm2Input) {
            let pm2 = Math.round(prix / surf);
            pm2Input.value = pm2.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
        }
    }

    function calcPrixTotal(pm2Input) {
        let surf = parseFloat(document.getElementById('surface').value) || 0;
        let pm2 = parseInt(pm2Input.value.replace(/\s/g, '')) || 0;
        let prixInput = pm2Input.closest('.edit-mode').querySelector('.e-prix');
        if(surf > 0 && prixInput) {
            let prix = Math.round(pm2 * surf);
            prixInput.value = prix.toString().replace(/\B(?=(\d{3})+(?!\d))/g, " ");
            clearError(prixInput);
        }
    }

    function ajouterLotImmeuble() {
        const idLot = Date.now() + Math.floor(Math.random() * 1000);
        const div = document.createElement('div');
        div.className = "bg-white border border-gray-300 rounded-xl p-8 shadow-sm lot-detail-card relative mb-6";
        
        div.innerHTML = `
            <div class="flex justify-between items-center mb-8 border-b border-gray-200 pb-4">
                <h4 class="font-bold text-xl text-dark"><i class="fa-solid fa-door-open text-primary mr-2"></i>Nouveau Logement</h4>
                <button type="button" onclick="this.closest('.lot-detail-card').remove()" class="text-red-500 hover:text-white hover:bg-red-500 bg-red-50 px-4 py-2 rounded-lg text-sm font-bold transition-colors"><i class="fa-solid fa-trash mr-1"></i> Supprimer</button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div><label class="block text-sm font-medium mb-1">Nom du lot <span class="text-red-500">*</span></label><input type="text" class="l-nom w-full px-4 py-2 border border-grayBorder rounded-lg outline-none focus:border-primary" placeholder="Ex: Appt A12"></div>
                <div><label class="block text-sm font-medium mb-1">Type de logement <span class="text-red-500">*</span></label><select class="l-type w-full px-4 py-2 border border-grayBorder rounded-lg outline-none focus:border-primary"><option value="T1">T1</option><option value="T2">T2</option><option value="T3">T3</option><option value="T4">T4</option><option value="T5+">T5+</option><option value="Local commercial">Local commercial</option></select></div>
                <div><label class="block text-sm font-medium mb-1">Prix estimé (€)</label><input type="text" class="l-prix w-full px-4 py-2 border border-grayBorder rounded-lg outline-none focus:border-primary" placeholder="Ex: 150000" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></div>
                <div><label class="block text-sm font-medium mb-1">Étage <span class="text-red-500">*</span></label><input type="text" class="l-etage w-full px-4 py-2 border border-grayBorder rounded-lg outline-none focus:border-primary" placeholder="Ex: 1er, RDC..."></div>
            </div>

            <h5 class="font-bold text-lg text-dark mb-4 border-b border-gray-100 pb-2">Fiscalité et Notaire</h5>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div><label class="block text-sm font-medium mb-1">Dispositif Fiscal</label><select class="l-fiscal w-full px-4 py-2 border border-grayBorder rounded-lg outline-none focus:border-primary"><option value="">Aucun spécifique</option><option value="Déficit Foncier">Déficit Foncier</option><option value="Loi Pinel">Loi Pinel</option><option value="Loi Malraux">Loi Malraux</option><option value="Loi Denormandie">Loi Denormandie</option><option value="LMNP">LMNP (Meublé Non Pro)</option><option value="LMP">LMP (Meublé Pro)</option><option value="Monuments Historiques">Monuments Historiques</option><option value="Éligible PTZ">Éligible PTZ</option></select></div>
                <div><label class="block text-sm font-medium mb-1">Frais de notaire</label><select class="l-notaire w-full px-4 py-2 border border-grayBorder rounded-lg outline-none focus:border-primary"><option value="">Non précisé</option><option value="Frais réduits (2 à 3%)">Frais réduits (2 à 3%)</option><option value="Frais classiques (7 à 8%)">Frais classiques (7 à 8%)</option></select></div>
            </div>

            <h5 class="font-bold text-lg text-dark mb-4 border-b border-gray-100 pb-2">Surfaces et Agencement</h5>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-8">
                <div><label class="block text-sm font-medium mb-1">Surf. Habitable <span class="text-red-500">*</span></label><input type="text" class="l-surf w-full px-4 py-2 border border-grayBorder rounded-lg outline-none focus:border-primary" placeholder="m²" oninput="this.value = this.value.replace(/[^0-9.]/g, '')"></div>
                <div><label class="block text-sm font-medium mb-1">Surface Carrez</label><input type="text" class="l-carrez w-full px-4 py-2 border border-grayBorder rounded-lg outline-none focus:border-primary" placeholder="m²" oninput="this.value = this.value.replace(/[^0-9.]/g, '')"></div>
                <div><label class="block text-sm font-medium mb-1">Nb de Pièces <span class="text-red-500">*</span></label><input type="text" class="l-pieces w-full px-4 py-2 border border-grayBorder rounded-lg outline-none focus:border-primary" placeholder="Ex: 2" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></div>
                <div><label class="block text-sm font-medium mb-1">Chambres</label><input type="text" class="l-chambres w-full px-4 py-2 border border-grayBorder rounded-lg outline-none focus:border-primary" placeholder="Ex: 1" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></div>
                <div><label class="block text-sm font-medium mb-1">Salles de bain</label><input type="text" class="l-sdb w-full px-4 py-2 border border-grayBorder rounded-lg outline-none focus:border-primary" placeholder="Ex: 1" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></div>
                <div><label class="block text-sm font-medium mb-1">Toilettes (WC)</label><input type="text" class="l-wc w-full px-4 py-2 border border-grayBorder rounded-lg outline-none focus:border-primary" placeholder="Ex: 1" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                <div>
                    <h5 class="font-bold text-lg text-dark mb-4 border-b border-gray-100 pb-2">Annexes & Extérieurs</h5>
                    <div class="flex flex-col gap-3 mb-6">
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" class="l-chk-cave w-4 h-4 accent-primary"><span class="text-sm font-medium">Cave privative</span></label>
                        <div class="flex flex-col gap-2"><label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" class="l-chk-parking w-4 h-4 accent-primary" onchange="this.closest('label').nextElementSibling.classList.toggle('hidden')"><span class="text-sm font-medium">Places de Parking</span></label><div class="hidden pl-6"><input type="text" class="l-places-parking w-24 px-3 py-1 border border-grayBorder rounded text-sm outline-none focus:border-primary" placeholder="Combien ?" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></div></div>
                        <div class="flex flex-col gap-2"><label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" class="l-chk-garage w-4 h-4 accent-primary" onchange="this.closest('label').nextElementSibling.classList.toggle('hidden')"><span class="text-sm font-medium">Garages / Box</span></label><div class="hidden pl-6"><input type="text" class="l-places-garage w-24 px-3 py-1 border border-grayBorder rounded text-sm outline-none focus:border-primary" placeholder="Combien ?" oninput="this.value = this.value.replace(/[^0-9]/g, '')"></div></div>
                    </div>
                    
                    <h5 class="font-bold text-sm text-gray-700 mb-3 border-b pb-2">Espaces Extérieurs</h5>
                    <div class="space-y-3 mb-6 bg-gray-50 p-4 rounded-xl">
                        <div class="flex flex-col gap-2"><label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" class="l-chk-ext-jardin w-4 h-4 accent-primary" onchange="this.closest('label').nextElementSibling.classList.toggle('hidden')"><span class="text-sm font-medium">Jardin</span></label><div class="hidden pl-6"><input type="text" class="l-surf-jardin w-32 px-3 py-1 border border-grayBorder rounded text-sm outline-none" placeholder="Surface en m²" oninput="this.value=this.value.replace(/[^0-9]/g, '')"></div></div>
                        <div class="flex flex-col gap-2"><label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" class="l-chk-ext-terrasse w-4 h-4 accent-primary" onchange="this.closest('label').nextElementSibling.classList.toggle('hidden')"><span class="text-sm font-medium">Terrasse</span></label><div class="hidden pl-6"><input type="text" class="l-surf-terrasse w-32 px-3 py-1 border border-grayBorder rounded text-sm outline-none" placeholder="Surface en m²" oninput="this.value=this.value.replace(/[^0-9]/g, '')"></div></div>
                        <div class="flex flex-col gap-2"><label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" class="l-chk-ext-balcon w-4 h-4 accent-primary" onchange="this.closest('label').nextElementSibling.classList.toggle('hidden')"><span class="text-sm font-medium">Balcon</span></label><div class="hidden pl-6"><input type="text" class="l-surf-balcon w-32 px-3 py-1 border border-grayBorder rounded text-sm outline-none" placeholder="Surface en m²" oninput="this.value=this.value.replace(/[^0-9]/g, '')"></div></div>
                        <div class="flex flex-col gap-2"><label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" class="l-chk-ext-loggia w-4 h-4 accent-primary" onchange="this.closest('label').nextElementSibling.classList.toggle('hidden')"><span class="text-sm font-medium">Loggia</span></label><div class="hidden pl-6"><input type="text" class="l-surf-loggia w-32 px-3 py-1 border border-grayBorder rounded text-sm outline-none" placeholder="Surface en m²" oninput="this.value=this.value.replace(/[^0-9]/g, '')"></div></div>
                    </div>
                </div>

                <div>
                    <h5 class="font-bold text-lg text-dark mb-4 border-b border-gray-100 pb-2">Énergie</h5>
                    <label class="block text-sm font-medium mb-2">Chauffage (Cochez les options)</label>
                    <div class="flex flex-wrap gap-3 mb-6 bg-gray-50 p-4 rounded-xl border border-gray-200">
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" value="Électrique" class="l-chk-chauffage w-4 h-4 accent-primary"><span class="text-sm">Électrique</span></label>
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" value="Pompe à chaleur" class="l-chk-chauffage w-4 h-4 accent-primary"><span class="text-sm">Pompe à chaleur</span></label>
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" value="Gaz" class="l-chk-chauffage w-4 h-4 accent-primary"><span class="text-sm">Gaz</span></label>
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" value="Climatisation réversible" class="l-chk-chauffage w-4 h-4 accent-blue-500"><span class="text-sm">Clim réversible</span></label>
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" value="Poêle à bois/granulés" class="l-chk-chauffage w-4 h-4 accent-primary"><span class="text-sm">Poêle/Insert</span></label>
                        <label class="flex items-center gap-2 cursor-pointer"><input type="checkbox" value="Réseau urbain" class="l-chk-chauffage w-4 h-4 accent-primary"><span class="text-sm">Réseau urbain</span></label>
                    </div>
                    
                    <div class="mb-4">
                        <label class="block text-sm font-medium mb-2">Classe Énergétique (DPE)</label>
                        <div class="flex flex-wrap gap-1">
                            <input type="radio" name="dpe_${idLot}" id="dpe_A_${idLot}" value="A" class="hidden dpe-radio l-dpe-radio"><label for="dpe_A_${idLot}" class="dpe-label dpe-label-sm" style="background-color: #32984b; color: #fff;">A</label>
                            <input type="radio" name="dpe_${idLot}" id="dpe_B_${idLot}" value="B" class="hidden dpe-radio l-dpe-radio"><label for="dpe_B_${idLot}" class="dpe-label dpe-label-sm" style="background-color: #33cc31; color: #fff;">B</label>
                            <input type="radio" name="dpe_${idLot}" id="dpe_C_${idLot}" value="C" class="hidden dpe-radio l-dpe-radio"><label for="dpe_C_${idLot}" class="dpe-label dpe-label-sm" style="background-color: #cbf000; color: #000;">C</label>
                            <input type="radio" name="dpe_${idLot}" id="dpe_D_${idLot}" value="D" class="hidden dpe-radio l-dpe-radio"><label for="dpe_D_${idLot}" class="dpe-label dpe-label-sm" style="background-color: #ffff00; color: #000;">D</label>
                            <input type="radio" name="dpe_${idLot}" id="dpe_E_${idLot}" value="E" class="hidden dpe-radio l-dpe-radio"><label for="dpe_E_${idLot}" class="dpe-label dpe-label-sm" style="background-color: #f0b000; color: #fff;">E</label>
                            <input type="radio" name="dpe_${idLot}" id="dpe_F_${idLot}" value="F" class="hidden dpe-radio l-dpe-radio"><label for="dpe_F_${idLot}" class="dpe-label dpe-label-sm" style="background-color: #eb680f; color: #fff;">F</label>
                            <input type="radio" name="dpe_${idLot}" id="dpe_G_${idLot}" value="G" class="hidden dpe-radio l-dpe-radio"><label for="dpe_G_${idLot}" class="dpe-label dpe-label-sm" style="background-color: #d21016; color: #fff;">G</label>
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium mb-2">Émissions Gaz (GES)</label>
                        <div class="flex flex-wrap gap-1">
                            <input type="radio" name="ges_${idLot}" id="ges_A_${idLot}" value="A" class="hidden dpe-radio l-ges-radio"><label for="ges_A_${idLot}" class="dpe-label dpe-label-sm" style="background-color: #32984b; color: #fff;">A</label>
                            <input type="radio" name="ges_${idLot}" id="ges_B_${idLot}" value="B" class="hidden dpe-radio l-ges-radio"><label for="ges_B_${idLot}" class="dpe-label dpe-label-sm" style="background-color: #33cc31; color: #fff;">B</label>
                            <input type="radio" name="ges_${idLot}" id="ges_C_${idLot}" value="C" class="hidden dpe-radio l-ges-radio"><label for="ges_C_${idLot}" class="dpe-label dpe-label-sm" style="background-color: #cbf000; color: #000;">C</label>
                            <input type="radio" name="ges_${idLot}" id="ges_D_${idLot}" value="D" class="hidden dpe-radio l-ges-radio"><label for="ges_D_${idLot}" class="dpe-label dpe-label-sm" style="background-color: #ffff00; color: #000;">D</label>
                            <input type="radio" name="ges_${idLot}" id="ges_E_${idLot}" value="E" class="hidden dpe-radio l-ges-radio"><label for="ges_E_${idLot}" class="dpe-label dpe-label-sm" style="background-color: #f0b000; color: #fff;">E</label>
                            <input type="radio" name="ges_${idLot}" id="ges_F_${idLot}" value="F" class="hidden dpe-radio l-ges-radio"><label for="ges_F_${idLot}" class="dpe-label dpe-label-sm" style="background-color: #eb680f; color: #fff;">F</label>
                            <input type="radio" name="ges_${idLot}" id="ges_G_${idLot}" value="G" class="hidden dpe-radio l-ges-radio"><label for="ges_G_${idLot}" class="dpe-label dpe-label-sm" style="background-color: #d21016; color: #fff;">G</label>
                        </div>
                    </div>
                </div>
            </div>
        `;
        document.getElementById('conteneur-lots').appendChild(div);
    }

    function validateEtape(btn) {
        const li = btn.closest('.etape-item');
        let stepValid = true;

        const titre = li.querySelector('.e-titre');
        const desc = li.querySelector('.e-desc');
        const prix = li.querySelector('.e-prix');
        const inclus = li.querySelector('.e-inclus');
        const noninclus = li.querySelector('.e-noninclus');
        const statut = li.querySelector('.e-statut').value;

        const hasRealContent = (val) => val.replace(/[✓✗]\s?/g, '').trim().length > 0;

        [titre, desc, prix].forEach(el => {
            if (!el.value.trim()) {
                stepValid = false;
                el.classList.add('border-red-500', 'bg-red-50', 'ring-1', 'ring-red-500');
            }
        });

        if (!hasRealContent(inclus.value)) {
            stepValid = false;
            inclus.classList.add('border-red-500', 'bg-red-50', 'ring-1', 'ring-red-500');
        }

        if (!hasRealContent(noninclus.value)) {
            stepValid = false;
            noninclus.classList.add('border-red-500', 'bg-red-50', 'ring-1', 'ring-red-500');
        }

        let errorMsg = li.querySelector('.etape-error-msg');
        if (!errorMsg) {
            errorMsg = document.createElement('p');
            errorMsg.className = 'etape-error-msg text-red-500 text-sm font-bold mt-4 text-right';
            li.querySelector('.edit-mode').appendChild(errorMsg);
        }

        if (!stepValid) {
            errorMsg.innerText = "Veuillez remplir les champs obligatoires encadrés en rouge.";
            errorMsg.classList.remove('hidden');
            return;
        } else {
            errorMsg.classList.add('hidden');
        }

        li.querySelector('.v-titre').innerText = titre.value;
        li.querySelector('.v-prix').innerText = prix.value;
        li.querySelector('.v-date').innerText = li.querySelector('.e-date').value || statut;
        
        li.querySelector('.edit-mode').classList.add('hidden');
        li.querySelector('.view-mode').classList.remove('hidden');
    }

    function editEtape(btn) {
        const li = btn.closest('.etape-item');
        li.querySelector('.view-mode').classList.add('hidden');
        li.querySelector('.edit-mode').classList.remove('hidden');
    }

    function removeEtape(btn) {
        btn.closest('.etape-item').remove();
        updateEtapeNumbers();
    }

    function updateEtapeNumbers() {
        const list = document.getElementById('etapes-list');
        Array.from(list.children).forEach((li, index) => {
            const num = index + 1;
            li.querySelector('.etape-number').innerText = num;
            li.querySelector('.v-prefix').innerText = `Étape ${num} :`;
            li.querySelector('.label-etape-num').innerText = `Étape ${num} :`;
            li.querySelector('.edit-title-badge').innerText = `Étape ${num}`;
        });
    }

    // INITIALISATION DES SORTABLES (DRAG & DROP)
    document.addEventListener('DOMContentLoaded', () => {
        if(document.getElementById('photos-list')) {
            Sortable.create(document.getElementById('photos-list'), { 
                animation: 150, 
                handle: '.cursor-grab', 
                draggable: '.photo-item',
                onEnd: function() {
                    const newOrderIds = Array.from(document.getElementById('photos-list').querySelectorAll('.photo-item')).map(li => li.getAttribute('data-id'));
                    photosData = newOrderIds.map(id => photosData.find(p => p.id === id)).filter(Boolean);
                    renderPhotos(); 
                }
            });
        }
        if(document.getElementById('plans-list')) Sortable.create(document.getElementById('plans-list'), { animation: 150, handle: '.cursor-grab' });
        if(document.getElementById('etapes-list')) {
            Sortable.create(document.getElementById('etapes-list'), { 
                animation: 150, 
                handle: '.cursor-grab',
                onEnd: updateEtapeNumbers
            });
        }
    });
    
    // --- GESTION DE L'UI DYNAMIQUE (PARKINGS & IMMEUBLES) ---
    
    // Toggle Parkings
    document.getElementById('has_parking').addEventListener('change', function() {
        const bloc = document.getElementById('bloc-places-parking');
        if(this.checked) { bloc.classList.remove('hidden'); document.getElementById('parking_places').focus(); }
        else { bloc.classList.add('hidden'); document.getElementById('parking_places').value = ''; }
    });

    document.getElementById('has_garage').addEventListener('change', function() {
        const bloc = document.getElementById('bloc-places-garage');
        if(this.checked) { bloc.classList.remove('hidden'); document.getElementById('garage_places').focus(); }
        else { bloc.classList.add('hidden'); document.getElementById('garage_places').value = ''; }
    });

    // Toggle Immeuble
    document.getElementById('type_bien').addEventListener('change', function() {
        const val = this.value;
        const blocSuite = document.getElementById('bloc_dynamique_suite');
        const blocStandard = document.getElementById('bloc_standard');
        const blocImmeuble = document.getElementById('bloc_immeuble');
        const blocLoft = document.getElementById('bloc_ancien_usage');
        
        blocSuite.classList.remove('hidden');
        
        if(val === 'Loft') blocLoft.classList.remove('hidden');
        else { blocLoft.classList.add('hidden'); document.getElementById('ancien_usage').value = ''; }

        if(val === 'Immeuble') {
            blocStandard.classList.add('hidden');
            blocImmeuble.classList.remove('hidden');
            if(document.getElementById('conteneur-lots').children.length === 0) ajouterLotImmeuble();
        } else {
            blocStandard.classList.remove('hidden');
            blocImmeuble.classList.add('hidden');
        }
        setTimeout(() => { map.invalidateSize(); }, 300);
    });

    // Cases à cocher Extérieurs/Annexes
    document.querySelectorAll('.ext-trigger').forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const containerSurface = this.closest('div').querySelector('.ext-surface');
            const inputSurface = containerSurface.querySelector('input');
            if(this.checked) {
                containerSurface.classList.remove('hidden');
                inputSurface.focus();
            } else {
                containerSurface.classList.add('hidden');
                inputSurface.value = '';
            }
        });
    });

    // ==========================================
    // 4. SOUMISSION FINALE VIA AJAX (FormData)
    // ==========================================
    document.getElementById('wizard-form').addEventListener('submit', function(e) {
        e.preventDefault();

        let submitError = document.getElementById('submit-global-error');
        if (!submitError) {
            submitError = document.createElement('div');
            submitError.id = 'submit-global-error';
            submitError.className = 'text-red-500 bg-red-50 p-3 rounded-lg border border-red-200 font-bold mb-6 hidden w-full';
            document.getElementById('btn-submit').parentNode.parentNode.insertBefore(submitError, document.getElementById('btn-submit').parentNode);
        }

        let hasActuel = false;
        document.querySelectorAll('.e-statut').forEach(sel => { if(sel.value === 'Actuel') hasActuel = true; });
        if(!hasActuel) { 
            submitError.innerText = "Impossible de publier : Vous devez définir au moins une étape avec le statut 'Actuel'.";
            submitError.classList.remove('hidden');
            return; 
        } else {
            submitError.classList.add('hidden');
        }

        const btn = document.getElementById('btn-submit');
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Création en cours...';
        btn.disabled = true;

        let fd = new FormData();
        fd.append('ajax_submit', '1');
        
        // Contacts
        fd.append('contact_prenom', document.getElementById('contact_prenom').value);
        fd.append('contact_nom', document.getElementById('contact_nom').value);
        fd.append('contact_telephone', document.getElementById('contact_telephone').value);
        fd.append('contact_email', document.getElementById('contact_email').value);
        
        // Données Globales
        ['titre', 'description', 'type_bien', 'etat_bien', 'dispositif_fiscal', 'ancien_usage', 'frais_notaire', 'avancement'].forEach(id => {
            let el = document.getElementById(id); if(el) fd.append(id, el.value);
        });
        
        // Localisation
        ['adresse-input', 'cp-input', 'ville-input', 'lat-input', 'lng-input'].forEach(id => {
            let el = document.getElementById(id); if(el) fd.append(id.replace('-input', id === 'adresse-input' ? 'adresse_complete' : ''), el.value);
        });
        fd.append('prix_m2_secteur', document.getElementById('prix_m2_secteur').value || 0);

        // Si ce n'est PAS un immeuble
        if(document.getElementById('type_bien').value !== 'Immeuble') {
            ['surface', 'surface_carrez', 'pieces', 'chambres', 'salles_de_bain', 'wc', 'nombre_etages'].forEach(id => {
                let el = document.getElementById(id); if(el) fd.append(id, el.value || 0);
            });
            fd.append('etage', document.getElementById('etage').value);

            ['jardin', 'terrasse', 'balcon', 'loggia'].forEach(type => {
                let chk = document.getElementById('ext_'+type);
                if(chk && chk.checked) {
                    fd.append('ext_'+type, 1);
                    fd.append('surf_'+type, document.getElementById('surf_'+type).value || 0);
                } else {
                    fd.append('ext_'+type, 0); fd.append('surf_'+type, 0);
                }
            });

            ['has_parking', 'has_garage'].forEach(id => {
                let chk = document.getElementById(id);
                if(chk && chk.checked) {
                    fd.append(id, 1);
                    fd.append(id.replace('has_', '') + '_places', document.getElementById(id.replace('has_', '') + '_places').value || 0);
                } else {
                    fd.append(id, 0); fd.append(id.replace('has_', '') + '_places', 0);
                }
            });

            ['cave', 'ascenseur', 'acces_pmr'].forEach(id => {
                let el = document.getElementById(id); if(el) fd.append(id, el.checked ? 1 : 0);
            });

            let chauffages = [];
            document.querySelectorAll('.chk-chauffage:checked').forEach(chk => chauffages.push(chk.value));
            fd.append('type_chauffage', chauffages.join(', '));

            const dpeChecked = document.querySelector('input[name="dpe"]:checked'); if(dpeChecked) fd.append('dpe', dpeChecked.value);
            const gesChecked = document.querySelector('input[name="ges"]:checked'); if(gesChecked) fd.append('ges', gesChecked.value);
        } 
        
        // Si c'est un Immeuble
        else {
            // Pour l'immeuble global (Surfaces totales)
            ['surface', 'surface_carrez', 'pieces', 'chambres', 'salles_de_bain', 'wc', 'nombre_etages'].forEach(id => {
                let el = document.getElementById(id); if(el) fd.append(id, el.value || 0);
            });
            fd.append('etage', document.getElementById('etage').value);
            
            ['jardin', 'terrasse', 'balcon', 'loggia'].forEach(type => {
                let chk = document.getElementById('ext_'+type);
                if(chk && chk.checked) {
                    fd.append('ext_'+type, 1);
                    fd.append('surf_'+type, document.getElementById('surf_'+type).value || 0);
                } else {
                    fd.append('ext_'+type, 0); fd.append('surf_'+type, 0);
                }
            });
            
            ['has_parking', 'has_garage'].forEach(id => {
                let chk = document.getElementById(id);
                if(chk && chk.checked) {
                    fd.append(id, 1);
                    fd.append(id.replace('has_', '') + '_places', document.getElementById(id.replace('has_', '') + '_places').value || 0);
                } else {
                    fd.append(id, 0); fd.append(id.replace('has_', '') + '_places', 0);
                }
            });
            
            ['cave', 'ascenseur', 'acces_pmr'].forEach(id => {
                let el = document.getElementById(id); if(el) fd.append(id, el.checked ? 1 : 0);
            });
            
            let chauffagesGlobaux = [];
            document.querySelectorAll('#bloc_standard .chk-chauffage:checked').forEach(chk => chauffagesGlobaux.push(chk.value));
            fd.append('type_chauffage', chauffagesGlobaux.join(', '));

            const dpeChecked = document.querySelector('#bloc_standard input[name="dpe"]:checked'); if(dpeChecked) fd.append('dpe', dpeChecked.value);
            const gesChecked = document.querySelector('#bloc_standard input[name="ges"]:checked'); if(gesChecked) fd.append('ges', gesChecked.value);

            // Pour CHAQUE LOT
            document.querySelectorAll('.lot-detail-card').forEach(card => {
                fd.append('lot_nom[]', card.querySelector('.l-nom').value);
                fd.append('lot_type[]', card.querySelector('.l-type').value);
                fd.append('lot_etage[]', card.querySelector('.l-etage').value);
                fd.append('lot_prix[]', card.querySelector('.l-prix').value || 0);
                fd.append('lot_fiscal[]', card.querySelector('.l-fiscal').value);
                fd.append('lot_notaire[]', card.querySelector('.l-notaire').value);
                
                fd.append('lot_surf[]', card.querySelector('.l-surf').value || 0);
                fd.append('lot_carrez[]', card.querySelector('.l-carrez').value || 0);
                fd.append('lot_pieces[]', card.querySelector('.l-pieces').value || 1);
                fd.append('lot_chambres[]', card.querySelector('.l-chambres').value || 0);
                fd.append('lot_sdb[]', card.querySelector('.l-sdb').value || 0);
                fd.append('lot_wc[]', card.querySelector('.l-wc').value || 0);
                
                const lDpe = card.querySelector('.l-dpe-radio:checked');
                fd.append('lot_dpe[]', lDpe ? lDpe.value : '');
                
                const lGes = card.querySelector('.l-ges-radio:checked');
                fd.append('lot_ges[]', lGes ? lGes.value : '');
                
                let chauf = [];
                card.querySelectorAll('.l-chk-chauffage:checked').forEach(c => chauf.push(c.value));
                fd.append('lot_chauffage[]', chauf.join(', '));
                
                fd.append('lot_cave[]', card.querySelector('.l-chk-cave').checked ? 1 : 0);
                
                let hasLotParking = card.querySelector('.l-chk-parking').checked;
                fd.append('lot_parking[]', hasLotParking ? (card.querySelector('.l-places-parking').value || 1) : 0);
                
                let hasLotGarage = card.querySelector('.l-chk-garage').checked;
                fd.append('lot_garage[]', hasLotGarage ? (card.querySelector('.l-places-garage').value || 1) : 0);

                ['jardin', 'terrasse', 'balcon', 'loggia'].forEach(type => {
                    let chk = card.querySelector('.l-chk-ext-'+type);
                    if(chk && chk.checked) {
                        fd.append('lot_ext_'+type+'[]', 1);
                        fd.append('lot_surf_'+type+'[]', card.querySelector('.l-surf-'+type).value || 0);
                    } else {
                        fd.append('lot_ext_'+type+'[]', 0);
                        fd.append('lot_surf_'+type+'[]', 0);
                    }
                });
            });
        }

        // =========================================================
        // ETAPES, PHOTOS, PLANS, FETCH
        // =========================================================
        
        document.querySelectorAll('.etape-item').forEach(item => {
            fd.append('etape_titre[]', item.querySelector('.e-titre').value);
            fd.append('etape_statut[]', item.querySelector('.e-statut').value);
            fd.append('etape_prix[]', item.querySelector('.e-prix').value.replace(/\s/g, '')); 
            fd.append('etape_prix_m2[]', item.querySelector('.e-prix-m2').value.replace(/\s/g, ''));
            fd.append('etape_date[]', item.querySelector('.e-date').value);
            fd.append('etape_desc[]', item.querySelector('.e-desc').value);
            
            let inclusClean = item.querySelector('.e-inclus').value.replace(/[✓]\s?/g, '').trim();
            let noninclusClean = item.querySelector('.e-noninclus').value.replace(/[✗]\s?/g, '').trim();

            fd.append('etape_inclus[]', inclusClean);
            fd.append('etape_noninclus[]', noninclusClean);
        });

        document.querySelectorAll('.photo-item').forEach(item => {
            let id = item.getAttribute('data-id');
            let photoObj = photosData.find(p => p.id === id);
            if(photoObj) {
                fd.append('photos[]', photoObj.file);
                fd.append('photo_tags[]', photoObj.tag);
            }
        });

        document.querySelectorAll('.plan-item').forEach(item => {
            let id = item.getAttribute('data-id');
            let planObj = plansData.find(p => p.id === id);
            if(planObj) {
                fd.append('plans[]', planObj.file);
                fd.append('plan_titres[]', planObj.titre);
            }
        });

        fetch('publier.php', { method: 'POST', body: fd })
        .then(res => res.json())
        .then(data => {
            if(data.success) { window.location.href = data.redirect; } 
            else {
                submitError.innerText = 'Erreur serveur : ' + data.error;
                submitError.classList.remove('hidden');
                btn.innerHTML = '<i class="fa-solid fa-cloud-arrow-up"></i> Mettre en ligne';
                btn.disabled = false;
            }
        }).catch(err => {
            submitError.innerText = 'Une erreur est survenue lors de la communication avec le serveur.';
            submitError.classList.remove('hidden');
            btn.innerHTML = '<i class="fa-solid fa-cloud-arrow-up"></i> Mettre en ligne';
            btn.disabled = false;
        });
    });
</script>