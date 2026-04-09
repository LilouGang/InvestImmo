<script>
    // --- GESTION DE LA MODALE D'ESTIMATION ---
    const modal = document.getElementById('estimation-modal');
    const modalContent = document.getElementById('estimation-modal-content');
    const heroInput = document.getElementById('hero-adresse-input');
    const heroContainer = document.getElementById('hero-search-container');
    const heroError = document.getElementById('hero-error-msg');

    function openEstimationModal() {
        const adresseSaisie = heroInput.value;
        const typeSaisi = document.getElementById('hero-type-input').value;

        // Feedback visuel + message texte si vide
        if (adresseSaisie.trim() === '') {
            heroInput.focus();
            heroContainer.classList.add('ring-2', 'ring-red-400', 'bg-red-50');
            heroError.classList.remove('hidden');
            setTimeout(() => {
                heroError.classList.add('opacity-100');
            }, 10);
            
            setTimeout(() => {
                heroContainer.classList.remove('ring-2', 'ring-red-400', 'bg-red-50');
                heroError.classList.remove('opacity-100');
                setTimeout(() => heroError.classList.add('hidden'), 300);
            }, 2500);
            return;
        }

        document.getElementById('modal-adresse').value = adresseSaisie;
        document.getElementById('modal-type').value = typeSaisi;

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        setTimeout(() => {
            modal.classList.remove('opacity-0');
            modalContent.classList.remove('scale-95');
        }, 10);
        document.body.style.overflow = 'hidden'; 
    }

    function closeEstimationModal() {
        modal.classList.add('opacity-0');
        modalContent.classList.add('scale-95');
        setTimeout(() => {
            modal.classList.remove('flex');
            modal.classList.add('hidden');
            document.body.style.overflow = 'auto'; 
            
            // On cache l'écran de succès s'il était ouvert
            document.getElementById('index-form-success').classList.add('hidden');
            document.getElementById('ajax-index-form').classList.remove('invisible');
            document.getElementById('ajax-index-form').reset();
            heroInput.value = ''; // Réinitialise la barre hero
        }, 300); 
    }

    // --- SOUMISSION AJAX DU FORMULAIRE MODALE ---
    document.addEventListener('DOMContentLoaded', () => {
        const indexForm = document.getElementById('ajax-index-form');
        
        if (indexForm) {
            indexForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const btn = document.getElementById('submit-index-btn');
                const originalHtml = btn.innerHTML;
                
                btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Envoi...';
                btn.disabled = true;

                fetch('https://formsubmit.co/ajax/agence.etna@gmail.com', {
                    method: 'POST',
                    headers: { 'Accept': 'application/json' },
                    body: new FormData(indexForm)
                })
                .then(response => response.json())
                .then(data => {
                    if(data.success || data.ok) {
                        document.getElementById('index-form-success').classList.remove('hidden');
                        document.getElementById('index-form-success').classList.add('flex');
                        indexForm.classList.add('invisible');
                        btn.innerHTML = originalHtml;
                        btn.disabled = false;
                    } else throw new Error('Erreur API');
                })
                .catch(error => {
                    btn.innerHTML = 'Erreur d\'envoi. Réessayer';
                    btn.classList.add('bg-red-500', 'hover:bg-red-600');
                    btn.disabled = false;
                    setTimeout(() => {
                        btn.innerHTML = originalHtml;
                        btn.classList.remove('bg-red-500', 'hover:bg-red-600');
                    }, 3000);
                });
            });
        }
    });
    
    // --- SOUMISSION AJAX DU FORMULAIRE RAPIDE (SECTION 2) ---
    const quickForm = document.getElementById('ajax-quick-form');
    
    if (quickForm) {
        quickForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = document.getElementById('submit-quick-btn');
            const errorMsg = document.getElementById('quick-error-msg');
            const originalContent = btn.innerHTML;
            
            btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Envoi en cours...';
            btn.disabled = true;
            errorMsg.classList.add('hidden');

            fetch('https://formsubmit.co/ajax/agence.etna@gmail.com', {
                method: 'POST',
                headers: { 'Accept': 'application/json' },
                body: new FormData(quickForm)
            })
            .then(response => response.json())
            .then(data => {
                if(data.success || data.ok) {
                    document.getElementById('quick-form-success').classList.remove('hidden');
                    document.getElementById('quick-form-success').classList.add('flex');
                    quickForm.classList.add('invisible');
                    btn.innerHTML = originalContent;
                    btn.disabled = false;
                } else throw new Error('Erreur API');
            })
            .catch(error => {
                btn.innerHTML = originalContent;
                btn.disabled = false;
                errorMsg.innerText = "Une erreur est survenue lors de l'envoi. Veuillez réessayer.";
                errorMsg.classList.remove('hidden');
            });
        });
    }

    // Fonction pour fermer proprement l'écran de succès de la section 2
    function resetQuickForm() {
        document.getElementById('quick-form-success').classList.add('hidden');
        document.getElementById('quick-form-success').classList.remove('flex');
        quickForm.classList.remove('invisible');
        quickForm.reset();
    }
</script>