<footer id="footer" class="bg-dark text-white pt-16 pb-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
            <div class="col-span-1 md:col-span-1">
                <div class="flex items-center gap-2 mb-6">
                    <i class="fa-solid fa-house-chimney text-primary text-2xl"></i>
                    <span class="font-bold text-xl tracking-tight">Invest<span class="text-primary">Immo</span></span>
                </div>
                <p class="text-gray-400 mb-6 text-sm leading-relaxed">
                    Marchand de biens expert dans l'acquisition, la rénovation et la revente de biens immobiliers avec potentiel.
                </p>
                <div class="flex space-x-4">
                    <a href="https://www.facebook.com/profile.php?id=61588349854169" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-primary hover:text-white transition-colors">
                        <i class="fa-brands fa-facebook-f"></i>
                    </a>
                    <a href="https://www.instagram.com/etna.web/" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-primary hover:text-white transition-colors">
                        <i class="fa-brands fa-instagram"></i>
                    </a>
                    <a href="https://www.linkedin.com/in/etna-web-40a2743b5/" class="w-10 h-10 rounded-full bg-gray-800 flex items-center justify-center text-gray-400 hover:bg-primary hover:text-white transition-colors">
                        <i class="fa-brands fa-linkedin-in"></i>
                    </a>
                </div>
            </div>
            
            <div>
                <h4 class="text-lg font-semibold mb-6">Liens rapides</h4>
                <ul class="space-y-3 text-gray-400 text-sm">
                    <li><a href="<?= $base ?>index.php" class="hover:text-primary transition-colors">Accueil</a></li>
                    <li><a href="<?= $base ?>projets.php" class="hover:text-primary transition-colors">Nos projets</a></li>
                    <li><a href="<?= $base ?>estimation.php" class="hover:text-primary transition-colors">Demander une estimation</a></li>
                    <li><a href="<?= $base ?>contact.php" class="hover:text-primary transition-colors">Contact</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-lg font-semibold mb-6">Légal</h4>
                <ul class="space-y-3 text-gray-400 text-sm">
                    <li><a href="mentions.php" class="hover:text-primary transition-colors">Mentions légales</a></li>
                    <li><a href="confidentialite.php" class="hover:text-primary transition-colors">Politique de confidentialité</a></li>
                    <li><a href="conditions.php" class="hover:text-primary transition-colors">CGU / CGV</a></li>
                    <li><a href="cookies.php" class="hover:text-primary transition-colors">Gestion des cookies</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-lg font-semibold mb-6">Contact</h4>
                <ul class="space-y-4 text-gray-400 text-sm">
                    <li class="flex items-start gap-3">
                        <i class="fa-solid fa-location-dot mt-1 text-primary"></i>
                        <span>923 Rue du 19 Mars 1962<br>24150 Lalinde, France</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fa-solid fa-phone text-primary"></i>
                        <span>06 25 55 43 93</span>
                    </li>
                    <li class="flex items-center gap-3">
                        <i class="fa-solid fa-envelope text-primary"></i>
                        <span>agence.etna@gmail.com</span>
                    </li>
                </ul>
            </div>
        </div>
        
        <div class="border-t border-gray-800 pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <p class="text-gray-500 text-sm">© <?= date('Y') ?> AvenirImmo. Tous droits réservés.</p>
            <div class="flex items-center gap-2 text-gray-500 text-sm">
                <span>Fait avec</span> <i class="fa-solid fa-heart text-primary text-xs"></i> <span>pour l'immobilier</span>
            </div>
        </div>
    </div>
</footer>