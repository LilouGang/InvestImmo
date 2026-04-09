<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>AvenirImmo</title>
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-B9QSKFSKD5"></script>
    <script>
      window.dataLayer = window.dataLayer || [];
      function gtag(){dataLayer.push(arguments);}
      gtag('js', new Date());

      gtag('config', 'G-B9QSKFSKD5');
    </script>
</head>

<body>

<?php $base = isset($base) ? $base : ''; ?>
<header id="header" class="bg-white border-b border-grayBorder sticky top-0 z-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-20">
            
            <div class="flex-1 flex justify-start items-center">
                <a href="<?= $base ?>index.php" class="flex items-center gap-2">
                    <i class="fa-solid fa-house-chimney text-primary text-2xl"></i>
                    <span class="font-bold text-xl tracking-tight text-dark">Invest<span class="text-primary">Immo</span></span>
                </a>
            </div>

            <nav class="hidden md:flex justify-center space-x-8 whitespace-nowrap">
                <a href="<?= $base ?>index.php" class="text-textMain hover:text-primary font-medium py-7 transition-colors">Accueil</a>
                <a href="<?= $base ?>projets.php" class="text-textMain hover:text-primary font-medium py-7 transition-colors">Projets</a>
                <a href="<?= $base ?>estimation.php" class="text-textMain hover:text-primary font-medium py-7 transition-colors">Estimation</a>
                <a href="<?= $base ?>contact.php" class="text-textMain hover:text-primary font-medium py-7 transition-colors">Contact</a>
            </nav>

            <div class="flex-1 flex justify-end items-center">
                
                <div class="hidden md:flex items-center">
                    <?php if(isset($_SESSION['utilisateur_id'])): ?>
                        <div class="relative">
                            <button id="admin-menu-btn" class="flex items-center text-dark font-medium bg-grayLight px-4 py-2 rounded-md border border-gray-200 hover:bg-gray-100 transition-colors focus:outline-none">
                                <i class="fa-solid fa-shield-halved text-primary mr-2"></i>Admin Panel
                                <i class="fa-solid fa-chevron-down ml-2 text-xs text-gray-400 transition-transform duration-200" id="admin-chevron"></i>
                            </button>
                            
                            <div id="admin-dropdown" class="hidden absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-float border border-grayBorder py-2 z-50 transform opacity-0 scale-95 transition-all duration-200 origin-top-right">
                                <div class="px-4 py-2 border-b border-gray-100 mb-1">
                                    <p class="text-xs text-textMuted uppercase font-bold tracking-wider">Gestion</p>
                                </div>
                                <a href="<?= $base ?>admin/publier.php" class="block px-4 py-2 text-sm font-medium text-textMain hover:bg-orange-50 hover:text-primary transition-colors flex items-center">
                                    <i class="fa-solid fa-plus w-6 text-center"></i> Publier un bien
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="<?= isset($base) ? $base : '' ?>admin/messages.php" class="block px-4 py-2 text-sm text-dark hover:bg-gray-100 hover:text-primary transition-colors flex items-center gap-2">
                                    <i class="fa-solid fa-inbox w-4 text-center"></i> Messages
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="<?= isset($base) ? $base : '' ?>admin/compte.php" class="block px-4 py-2 text-sm text-dark hover:bg-gray-100 hover:text-primary transition-colors flex items-center gap-2">
                                    <i class="fa-solid fa-user-gear w-4 text-center"></i> Mon profil
                                </a>
                                <div class="border-t border-gray-100 my-1"></div>
                                <a href="<?= $base ?>admin/deconnexion.php" class="block px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50 transition-colors flex items-center">
                                    <i class="fa-solid fa-arrow-right-from-bracket w-6 text-center"></i> Déconnexion
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="md:hidden flex items-center">
                    <button id="menu-toggle" class="text-textMain hover:text-primary focus:outline-none p-2">
                        <i class="fa-solid fa-bars text-2xl"></i>
                    </button>
                </div>
            </div>

        </div>
    </div>

    <div id="mobile-menu" class="hidden md:hidden bg-white border-t border-grayBorder absolute w-full left-0 shadow-xl z-50">
        <div class="px-4 pt-2 pb-6 space-y-1">
            <a href="<?= $base ?>index.php" class="block px-3 py-4 text-base font-medium text-textMain hover:text-primary hover:bg-gray-50 rounded-md">Accueil</a>
            <a href="<?= $base ?>projets.php" class="block px-3 py-4 text-base font-medium text-textMain hover:text-primary hover:bg-gray-50 rounded-md">Projets</a>
            <a href="<?= $base ?>estimation.php" class="block px-3 py-4 text-base font-medium text-textMain hover:text-primary hover:bg-gray-50 rounded-md">Estimation</a>
            <a href="<?= $base ?>contact.php" class="block px-3 py-4 text-base font-medium text-textMain hover:text-primary hover:bg-gray-50 rounded-md">Contact</a>
            
            <?php if(isset($_SESSION['utilisateur_id'])): ?>
            <div class="pt-4 border-t border-gray-100 mt-2">
                <div class="bg-gray-50 rounded-xl p-4 border border-gray-200 mx-2">
                    <p class="text-dark font-bold text-sm mb-3 flex items-center uppercase tracking-wider text-textMuted">
                        <i class="fa-solid fa-shield-halved text-primary mr-2"></i>Administration
                    </p>
                    <div class="space-y-2">
                        <a href="<?= $base ?>admin/publier.php" class="block w-full bg-primary hover:bg-primaryHover text-white px-4 py-3 rounded-lg font-bold text-center text-sm shadow-sm transition-colors flex items-center justify-center gap-2">
                            <i class="fa-solid fa-plus"></i> Publier un bien
                        </a>
                        <a href="<?= $base ?>admin/deconnexion.php" class="block w-full bg-white text-red-600 border border-red-200 hover:bg-red-50 px-4 py-3 rounded-lg font-bold text-center text-sm transition-colors flex items-center justify-center gap-2">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i> Déconnexion
                        </a>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</header>

</body>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Gestion du menu hamburger mobile
        const btnMenu = document.getElementById('menu-toggle');
        const menuMobile = document.getElementById('mobile-menu');
        
        if (btnMenu && menuMobile) {
            btnMenu.addEventListener('click', function() { 
                menuMobile.classList.toggle('hidden'); 
            });
        }

        // Gestion du menu déroulant Admin (PC)
        const adminBtn = document.getElementById('admin-menu-btn');
        const adminDropdown = document.getElementById('admin-dropdown');
        const adminChevron = document.getElementById('admin-chevron');

        if (adminBtn && adminDropdown) {
            // Ouvrir/Fermer au clic sur le bouton
            adminBtn.addEventListener('click', (e) => {
                e.stopPropagation(); // Empêche le clic de se propager au document
                
                if (adminDropdown.classList.contains('hidden')) {
                    // Ouverture : on enlève "hidden" d'abord, puis on anime
                    adminDropdown.classList.remove('hidden');
                    setTimeout(() => {
                        adminDropdown.classList.remove('opacity-0', 'scale-95');
                        adminDropdown.classList.add('opacity-100', 'scale-100');
                        if(adminChevron) adminChevron.classList.add('rotate-180');
                    }, 10); // Petit délai pour laisser le navigateur appliquer le display:block
                } else {
                    // Fermeture : on anime d'abord, puis on ajoute "hidden"
                    adminDropdown.classList.remove('opacity-100', 'scale-100');
                    adminDropdown.classList.add('opacity-0', 'scale-95');
                    if(adminChevron) adminChevron.classList.remove('rotate-180');
                    
                    setTimeout(() => {
                        adminDropdown.classList.add('hidden');
                    }, 200); // Temps de la transition Tailwind
                }
            });

            // Fermer si on clique n'importe où ailleurs sur la page
            document.addEventListener('click', (e) => {
                if (!adminBtn.contains(e.target) && !adminDropdown.classList.contains('hidden')) {
                    adminDropdown.classList.remove('opacity-100', 'scale-100');
                    adminDropdown.classList.add('opacity-0', 'scale-95');
                    if(adminChevron) adminChevron.classList.remove('rotate-180');
                    
                    setTimeout(() => {
                        adminDropdown.classList.add('hidden');
                    }, 200);
                }
            });
        }
    });
</script>