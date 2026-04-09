<script>
    // 1. Gestion des onglets
    document.addEventListener('DOMContentLoaded', () => {
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        tabBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                tabBtns.forEach(b => {
                    b.classList.remove('border-primary', 'text-primary');
                    b.classList.add('border-transparent', 'text-textMuted');
                });
                tabContents.forEach(c => c.classList.remove('active'));

                btn.classList.remove('border-transparent', 'text-textMuted');
                btn.classList.add('border-primary', 'text-primary');
                
                const targetId = btn.getAttribute('data-target');
                document.getElementById(targetId).classList.add('active');
            });
        });
    });

    // 2. Logique du Scroll des tags (Gestion des flèches sans dépassement)
    const tagsScrollContainer = document.getElementById('tags-scroll-container');
    const btnLeft = document.getElementById('btn-scroll-left');
    const btnRight = document.getElementById('btn-scroll-right');

    function updateTagsArrows() {
        if (!tagsScrollContainer || !btnLeft || !btnRight) return;
        
        const maxScroll = tagsScrollContainer.scrollWidth - tagsScrollContainer.clientWidth;
        
        if (tagsScrollContainer.scrollLeft <= 5) {
            btnLeft.classList.add('opacity-0', 'invisible');
            btnLeft.classList.remove('opacity-100', 'visible');
        } else {
            btnLeft.classList.remove('opacity-0', 'invisible');
            btnLeft.classList.add('opacity-100', 'visible');
        }

        if (tagsScrollContainer.scrollLeft >= maxScroll - 5) {
            btnRight.classList.add('opacity-0', 'invisible');
            btnRight.classList.remove('opacity-100', 'visible');
        } else {
            btnRight.classList.remove('opacity-0', 'invisible');
            btnRight.classList.add('opacity-100', 'visible');
        }
    }

    if (tagsScrollContainer) {
        tagsScrollContainer.addEventListener('scroll', updateTagsArrows);
        window.addEventListener('resize', updateTagsArrows);
        setTimeout(updateTagsArrows, 100);
    }

    function scrollTagsLeft() {
        if (!tagsScrollContainer) return;
        // On limite à 0 pour ne pas aller en négatif
        const targetScroll = Math.max(tagsScrollContainer.scrollLeft - 150, 0);
        tagsScrollContainer.scrollTo({ left: targetScroll, behavior: 'smooth' });
    }

    function scrollTagsRight() {
        if (!tagsScrollContainer) return;
        // On limite au maximum scrollable pour empêcher l'overscroll
        const maxScroll = tagsScrollContainer.scrollWidth - tagsScrollContainer.clientWidth;
        const targetScroll = Math.min(tagsScrollContainer.scrollLeft + 150, maxScroll);
        tagsScrollContainer.scrollTo({ left: targetScroll, behavior: 'smooth' });
    }

    // 3. Défilement automatique des miniatures sur MOBILE
    const thumbnailsContainer = document.getElementById('hero-thumbnails-container');
    let thumbScrollReq;
    let autoScrollPaused = false;
    let resumeTimeout;
    let exactScrollLeft = 0; // Stocke la valeur exacte avec décimales pour vaincre le bug iPhone

    function autoScrollThumbnails() {
        if (window.innerWidth < 1024 && thumbnailsContainer && !autoScrollPaused) {
            
            // Si l'utilisateur a scrollé avec son doigt, on resynchronise notre variable
            if (Math.abs(exactScrollLeft - thumbnailsContainer.scrollLeft) > 2) {
                exactScrollLeft = thumbnailsContainer.scrollLeft;
            }

            // On accumule la décimale en JS (0.8 pixel par image générée)
            exactScrollLeft += 0.5;
            
            // On force le navigateur à prendre l'entier
            thumbnailsContainer.scrollLeft = Math.floor(exactScrollLeft);
            
            // Boucle infinie
            if (thumbnailsContainer.scrollLeft >= thumbnailsContainer.scrollWidth - thumbnailsContainer.clientWidth - 2) {
                thumbnailsContainer.scrollLeft = 0;
                exactScrollLeft = 0;
            }
            
            thumbScrollReq = requestAnimationFrame(autoScrollThumbnails);
        }
    }

    function handleAutoScroll() {
        if (window.innerWidth < 1024) {
            if (!thumbScrollReq) {
                exactScrollLeft = thumbnailsContainer ? thumbnailsContainer.scrollLeft : 0;
                autoScrollThumbnails();
            }
        } else {
            if (thumbScrollReq) {
                cancelAnimationFrame(thumbScrollReq);
                thumbScrollReq = null;
            }
        }
    }

    window.addEventListener('resize', handleAutoScroll);
    document.addEventListener('DOMContentLoaded', () => {
        setTimeout(handleAutoScroll, 500);

        // Mettre en pause si l'utilisateur touche la galerie mobile
        if(thumbnailsContainer) {
            thumbnailsContainer.addEventListener('touchstart', () => {
                autoScrollPaused = true;
                if(thumbScrollReq) {
                    cancelAnimationFrame(thumbScrollReq);
                    thumbScrollReq = null;
                }
                clearTimeout(resumeTimeout);
            }, {passive: true});
            
            // Reprendre le scroll 2 secondes après qu'il ait lâché l'écran
            thumbnailsContainer.addEventListener('touchend', () => {
                resumeTimeout = setTimeout(() => {
                    autoScrollPaused = false;
                    exactScrollLeft = thumbnailsContainer.scrollLeft; // Resync après le toucher
                    handleAutoScroll();
                }, 2000);
            }, {passive: true});
        }
    });

    // 4. Gestion des Tags multiples cliquables
    let selectedTags = new Set();

    function toggleTag(buttonClicked, tag) {
        if (selectedTags.has(tag)) {
            selectedTags.delete(tag);
        } else {
            selectedTags.add(tag);
        }

        document.querySelectorAll('.tag-btn').forEach(btn => {
            if (selectedTags.has(btn.dataset.tag)) {
                btn.classList.add('bg-primary', 'border-primary', 'text-white');
                btn.classList.remove('bg-transparent', 'border-white/40');
            } else {
                btn.classList.remove('bg-primary', 'border-primary');
                btn.classList.add('bg-transparent', 'border-white/40');
            }
        });

        filtrerGalerieModal();
        filtrerHeroThumbnails();
    }

    function filtrerGalerieModal() {
        const items = document.querySelectorAll('.galerie-item');
        let count = 0;
        
        items.forEach(item => {
            if (selectedTags.size === 0 || selectedTags.has(item.getAttribute('data-tag'))) {
                item.style.display = 'block';
                count++;
            } else {
                item.style.display = 'none';
            }
        });

        const countSpan = document.getElementById('modal-title-count');
        if(countSpan) countSpan.textContent = count;
    }

    function filtrerHeroThumbnails() {
        const items = document.querySelectorAll('.hero-thumbnail');
        const matchedItems = [];

        // 1. Identifier les éléments qui correspondent au filtre
        items.forEach(item => {
            if (selectedTags.size === 0 || selectedTags.has(item.getAttribute('data-tag'))) {
                matchedItems.push(item);
                item.classList.remove('hidden'); // On rend visible de base (surtout pour mobile)
            } else {
                item.classList.add('hidden'); // Ne correspond pas : on cache totalement
                item.classList.add('lg:hidden'); 
            }
        });

        // 2. Gérer l'affichage Desktop (Max 3) et nettoyer les overlays
        items.forEach(item => {
            const overlay = item.querySelector('.overlay-more-photos');
            if (overlay) {
                overlay.classList.add('hidden');
                overlay.classList.remove('lg:flex');
            }
        });

        matchedItems.forEach((item, index) => {
            // Seuls les 3 premiers éléments trouvés sont visibles sur PC
            if (index < 3) {
                item.classList.remove('lg:hidden'); 
            } else {
                item.classList.add('lg:hidden'); 
            }
        });

        // 3. Calcul dynamique du "+ X photos" si la sélection dépasse 3 images
        if (matchedItems.length > 3) {
            const thirdItem = matchedItems[2]; // L'index 2 = la 3ème image
            const overlay = thirdItem.querySelector('.overlay-more-photos');
            const overlayText = thirdItem.querySelector('.overlay-text');
            const extraCount = matchedItems.length - 3;
            
            if (overlay && overlayText) {
                overlayText.textContent = `+ ${extraCount} photo${extraCount > 1 ? 's' : ''}`;
                overlay.classList.remove('hidden');
                overlay.classList.add('lg:flex'); // Affiche le filigrane uniquement sur PC
            }
        }
    }

    // 5. Modale
    function ouvrirModalGalerie() {
        document.getElementById('modal-galerie').classList.remove('hidden');
        document.body.style.overflow = 'hidden'; 
    }

    function fermerModalGalerie() {
        document.getElementById('modal-galerie').classList.add('hidden');
        document.body.style.overflow = 'auto'; 
    }
    
    // 6. Envoi du formulaire en AJAX avec FormSubmit
    const contactForm = document.getElementById('ajax-contact-form');
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const btn = form.querySelector('button[type="submit"]');
            const originalBtnHtml = btn.innerHTML; // On sauvegarde le texte initial
            
            // 1. État de chargement visuel
            btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Envoi en cours...';
            btn.disabled = true;
            btn.classList.add('opacity-75', 'cursor-not-allowed');
    
            const formData = new FormData(form);
    
            // 2. Préparation des deux envois (BDD et FormSubmit)
            // Le .catch(() => {}) empêche toute erreur silencieuse de bloquer la suite du code
            const reqDatabase = fetch('sauvegarder_contact.php', { 
                method: 'POST', 
                body: formData 
            }).catch(() => {});
    
            const reqMail = fetch('https://formsubmit.co/ajax/agence.etna@gmail.com', { 
                method: 'POST', 
                headers: { 'Accept': 'application/json' }, // CRUCIAL : Empêche l'erreur réseau !
                body: formData 
            }).catch(() => {});
    
            // 3. On attend que les deux actions soient terminées (succès ou échec, peu importe)
            Promise.all([reqDatabase, reqMail]).then(() => {
                
                const successDiv = document.getElementById('form-success');
                
                if (successDiv) {
                    // CAS A : La page possède un écran de succès caché (ex: contact.php)
                    successDiv.classList.remove('hidden');
                    successDiv.classList.add('flex');
                    form.classList.add('hidden', 'invisible');
                } else {
                    // CAS B : Pas d'écran de succès (ex: detail.php ou page d'accueil)
                    btn.innerHTML = '<i class="fa-solid fa-check"></i> Demande envoyée !';
                    
                    // Nettoyage de toutes les couleurs potentielles
                    btn.classList.remove('opacity-75', 'cursor-not-allowed', 'bg-primary', 'hover:bg-primaryHover', 'bg-accent', 'hover:bg-orange-600');
                    
                    // On met le bouton en vert
                    btn.classList.add('bg-green-500', 'hover:bg-green-600'); 
                    
                    // On vide le formulaire
                    form.reset();
                    
                    // Au bout de 4 secondes, on remet le bouton à son état normal pour permettre un autre message
                    setTimeout(() => {
                        btn.innerHTML = originalBtnHtml;
                        btn.classList.remove('bg-green-500', 'hover:bg-green-600');
                        btn.classList.add('bg-primary', 'hover:bg-primaryHover');
                        btn.disabled = false;
                    }, 4000);
                }
            });
        });
    }
</script>