<script>
    document.addEventListener('DOMContentLoaded', function() {
        const steps = document.querySelectorAll('.form-step');
        const nextBtns = document.querySelectorAll('.btn-next');
        const prevBtns = document.querySelectorAll('.btn-prev');
        const indicators = document.querySelectorAll('.step-indicator');
        const progressBar = document.getElementById('progress-bar');
        let currentStep = 0;

        function updateForm() {
            steps.forEach((step, index) => {
                if (index === currentStep) step.classList.add('active');
                else step.classList.remove('active');
            });
            
            const progress = (currentStep / (steps.length - 1)) * 100;
            if(progressBar) progressBar.style.width = progress + '%';
            
            indicators.forEach((indicator, index) => {
                if (index <= currentStep) {
                    indicator.classList.remove('bg-white', 'text-textMuted', 'border-2', 'border-grayBorder');
                    indicator.classList.add('bg-primary', 'text-white', 'border-primary');
                } else {
                    indicator.classList.add('bg-white', 'text-textMuted', 'border-2', 'border-grayBorder');
                    indicator.classList.remove('bg-primary', 'text-white', 'border-primary');
                }
            });
        }

        nextBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const currentStepEl = steps[currentStep];
                const inputs = currentStepEl.querySelectorAll('input[required], select[required]');
                let allValid = true;
                
                inputs.forEach(input => {
                    if (!input.checkValidity()) {
                        input.reportValidity();
                        allValid = false;
                    }
                });

                if (allValid && currentStep < steps.length - 1) {
                    currentStep++;
                    updateForm();
                    window.scrollTo({ top: document.querySelector('.bg-white.rounded-2xl').offsetTop - 100, behavior: 'smooth' });
                }
            });
        });

        prevBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                if (currentStep > 0) {
                    currentStep--;
                    updateForm();
                    window.scrollTo({ top: document.querySelector('.bg-white.rounded-2xl').offsetTop - 100, behavior: 'smooth' });
                }
            });
        });
        
        updateForm();

        const formFull = document.getElementById('estimation-form-full');
        
        formFull.addEventListener('submit', function(e) {
            e.preventDefault(); 
            
            const btn = document.getElementById('submit-btn');
            const originalText = btn.innerHTML;
            
            btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Envoi en cours...';
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');

            fetch('https://formsubmit.co/ajax/agence.etna@gmail.com', {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: new FormData(formFull)
            })
            .then(response => response.json())
            .then(data => {
                if(data.success || data.ok) {
                    const typeBien = formFull.querySelector('input[name="Type_de_bien"]:checked').value;
                    const adresse = formFull.querySelector('input[name="Adresse"]').value;
                    const cp = formFull.querySelector('input[name="Code_Postal"]').value;
                    const ville = formFull.querySelector('input[name="Ville"]').value;
                    
                    const surface = formFull.querySelector('input[name="Surface"]').value;
                    const pieces = formFull.querySelector('input[name="Pieces"]').value;
                    const chambres = formFull.querySelector('input[name="Chambres"]').value;
                    const sdb = formFull.querySelector('input[name="Salles_de_bain"]').value;
                    const etage = formFull.querySelector('input[name="Etage"]').value;
                    
                    const etat = formFull.querySelector('select[name="Etat_General"]').value;
                    const dpe = formFull.querySelector('select[name="DPE"]').value;
                    const chauffage = formFull.querySelector('select[name="Chauffage"]').value;
                    const delai = formFull.querySelector('select[name="Delai_Vente"]').value;
                    const infos = formFull.querySelector('textarea[name="Informations_Complementaires"]').value;
                    
                    const email = formFull.querySelector('input[name="Email"]').value;
                    const tel = formFull.querySelector('input[name="Telephone"]').value;

                    document.getElementById('sum-type').textContent = etage ? `${typeBien} (Étage ${etage})` : typeBien;
                    document.getElementById('sum-adresse').textContent = `${adresse}, ${cp} ${ville}`;
                    
                    let surfaceText = `${surface} m²`;
                    if (pieces) surfaceText += ` - ${pieces} Pièce(s)`;
                    if (chambres) surfaceText += ` (${chambres} ch, ${sdb || 0} sdb)`;
                    document.getElementById('sum-surface').textContent = surfaceText;

                    document.getElementById('sum-etat').textContent = `${etat} | DPE: ${dpe} | Chauffage: ${chauffage}`;
                    document.getElementById('sum-delai').textContent = delai || 'Non précisé';
                    document.getElementById('sum-contact').textContent = tel ? `${tel} / ${email}` : email;
                    
                    document.getElementById('sum-infos').textContent = infos || 'Aucune information supplémentaire.';

                    document.getElementById('header-container').classList.add('hidden');
                    document.getElementById('stepper-container').classList.add('hidden');
                    document.getElementById('form-container').classList.add('hidden');
                    document.getElementById('trust-indicators').classList.add('hidden');

                    const successScreen = document.getElementById('success-screen');
                    successScreen.classList.remove('hidden');
                    successScreen.classList.add('grid');

                    window.scrollTo({ top: 0, behavior: 'smooth' });
                } else {
                    throw new Error('Erreur API');
                }
            })
            .catch(error => {
                btn.innerHTML = 'Erreur. Réessayer';
                btn.disabled = false;
                btn.classList.remove('opacity-75', 'cursor-not-allowed');
                alert('Une erreur est survenue lors de l\'envoi du formulaire.');
            });
        });
    });

    document.addEventListener('DOMContentLoaded', function() {
        var map = L.map('map', {zoomControl: false}).setView([46.603354, 1.888334], 5);
        L.control.zoom({ position: 'bottomright' }).addTo(map);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', { maxZoom: 19 }).addTo(map);

        const addressInput = document.getElementById('adresse-input');
        const cpInput = document.getElementById('cp-input');
        const villeInput = document.getElementById('ville-input');
        let typingTimer;
        let isMapMovingByUser = true;

        function searchAddressOnMap() {
            let query = `${addressInput.value} ${cpInput.value} ${villeInput.value}`.trim();
            if(query.length > 5) {
                fetch(`https://api-adresse.data.gouv.fr/search/?q=${encodeURIComponent(query)}&limit=1`)
                .then(res => res.json())
                .then(data => {
                    if(data.features && data.features.length > 0) {
                        let coords = data.features[0].geometry.coordinates;
                        let props = data.features[0].properties;
                        
                        isMapMovingByUser = false; 
                        map.flyTo([coords[1], coords[0]], 17, {duration: 1.5});
                        
                        if(document.activeElement === addressInput) {
                            cpInput.value = props.postcode || '';
                            villeInput.value = props.city || '';
                        }
                    }
                });
            }
        }

        [addressInput, cpInput, villeInput].forEach(input => {
            input.addEventListener('keyup', () => {
                clearTimeout(typingTimer);
                typingTimer = setTimeout(searchAddressOnMap, 800);
            });
        });

        map.on('moveend', function() {
            if(!isMapMovingByUser) { isMapMovingByUser = true; return; }
            if(document.activeElement === addressInput || document.activeElement === cpInput || document.activeElement === villeInput) return;

            var center = map.getCenter();
            fetch(`https://api-adresse.data.gouv.fr/reverse/?lon=${center.lng}&lat=${center.lat}`)
            .then(res => res.json())
            .then(data => {
                if(data.features && data.features.length > 0) {
                    let properties = data.features[0].properties;
                    addressInput.value = properties.name || '';
                    cpInput.value = properties.postcode || '';
                    villeInput.value = properties.city || '';
                }
            });
        });

        document.querySelectorAll('.btn-next, .btn-prev').forEach(btn => {
            btn.addEventListener('click', () => { setTimeout(() => { map.invalidateSize(); }, 300); });
        });
    });
</script>