<?php
// On récupère le mail de la fiche projet, sinon le mail de l'agent qui l'a posté, sinon un mail par défaut.
$email_cta = !empty($projet['contact_email']) ? $projet['contact_email'] : (!empty($projet['email_agent']) ? $projet['email_agent'] : 'contact@avenirimmo.fr');
?>
<section class="bg-white py-16 lg:py-24 border-t border-grayBorder overflow-hidden relative -mb-10 z-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            
            <div>
                <p class="text-sm font-medium text-textMuted mb-2">Un projet similaire en tête ?</p>
                <h2 class="text-4xl md:text-5xl font-bold text-accent mb-6 leading-tight">
                    Confiez-nous la rénovation de <br>votre bien
                </h2>
                <p class="text-lg text-textMain mb-8 max-w-lg">
                    Vous possédez un bien à rénover ou vous cherchez un projet sur mesure ? Notre équipe d'experts vous accompagne de A à Z.
                </p>
                <div class="mb-4">
                    <p class="text-sm font-medium text-textMuted mb-1 flex items-center gap-2">Contactez-nous dès maintenant <i class="fa-solid fa-arrow-right-long"></i></p>
                    <a href="mailto:<?= htmlspecialchars($email_cta) ?>" class="text-2xl md:text-3xl font-bold text-accent hover:text-primary transition-colors break-all">
                        <?= htmlspecialchars($email_cta) ?>
                    </a>
                </div>
            </div>
            
            <div class="block relative mt-10 lg:mt-0">
                <div class="absolute right-0 top-1/2 -translate-y-1/2 w-[500px] h-[500px] bg-blue-900 rounded-full mix-blend-multiply filter blur-3xl opacity-20"></div>
                <div class="absolute right-20 top-1/2 -translate-y-1/2 w-[300px] h-[300px] bg-accent rounded-full mix-blend-multiply filter blur-2xl opacity-20"></div>
                
                <div class="relative bg-dark rounded-3xl p-8 text-white shadow-2xl lg:rotate-3 transform lg:hover:rotate-0 transition-transform duration-500">
                    <h3 class="text-2xl font-serif font-bold mb-4">Demander une estimation</h3>
                    <p class="text-gray-300 mb-6 text-sm">Obtenez une valorisation précise de votre bien en l'état ou avec potentiel de rénovation.</p>
                    
                    <form id="ajax-estimation-form" class="space-y-3">
                        <input type="hidden" name="_subject" value="Demande d'estimation / Contact projet">
                        <input type="hidden" name="_captcha" value="false">
                        <input type="hidden" name="Sujet" value="Demande d'estimation (Réf Projet: <?= $projet['id'] ?? 'Non défini' ?>)">
                        
                        <div class="grid grid-cols-2 gap-3">
                            <input type="text" name="Prenom" placeholder="Prénom" required class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:border-primary">
                            <input type="text" name="Nom" placeholder="Nom" required class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:border-primary">
                        </div>
                        <input type="email" name="Email" placeholder="Votre adresse e-mail" required class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:border-primary">
                        <textarea name="Message" rows="3" placeholder="Parlez-nous de votre projet..." required class="w-full bg-white/10 border border-white/20 rounded-lg px-4 py-3 text-white placeholder-gray-400 focus:outline-none focus:border-primary resize-none"></textarea>
                        
                        <button type="submit" class="w-full bg-primary hover:bg-primaryHover text-white font-bold py-3 px-4 rounded-lg transition-colors mt-2 flex justify-center items-center gap-2 group">
                            <span>Envoyer la demande</span>
                            <i class="fa-solid fa-paper-plane group-hover:translate-x-1 transition-transform"></i>
                        </button>
                    </form>
                </div>
            </div>
            
        </div>
    </div>
</section>

<script>
    // Ajout d'une soumission propre pour ce formulaire via FormSubmit
    document.getElementById('ajax-estimation-form').addEventListener('submit', function(e) {
        e.preventDefault();
        const form = this;
        const btn = form.querySelector('button[type="submit"]');
        const originalContent = btn.innerHTML;
        
        btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Envoi en cours...';
        btn.disabled = true;

        fetch('https://formsubmit.co/ajax/agence.etna@gmail.com', {
            method: 'POST',
            headers: { 'Accept': 'application/json' },
            body: new FormData(form)
        })
        .then(response => response.json())
        .then(data => {
            if(data.success || data.ok) {
                btn.innerHTML = '<i class="fa-solid fa-check"></i> Demande envoyée !';
                btn.classList.replace('bg-primary', 'bg-green-500');
                btn.classList.replace('hover:bg-primaryHover', 'hover:bg-green-600');
                form.reset();
                setTimeout(() => {
                    btn.innerHTML = originalContent;
                    btn.classList.replace('bg-green-500', 'bg-primary');
                    btn.classList.replace('hover:bg-green-600', 'hover:bg-primaryHover');
                    btn.disabled = false;
                }, 4000);
            }
        })
        .catch(error => {
            btn.innerHTML = 'Erreur. Réessayer';
            btn.disabled = false;
        });
    });
</script>