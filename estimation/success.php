<div id="success-screen" class="hidden grid-cols-1 lg:grid-cols-2 gap-8 items-start mb-8">
    <div class="bg-white rounded-2xl shadow-card border border-grayBorder p-8 md:p-12 text-center h-full flex flex-col justify-center">
        <div class="w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-6">
            <i class="fa-solid fa-check text-3xl text-green-500"></i>
        </div>
        <h1 class="text-3xl md:text-4xl font-bold text-dark mb-4">Demande envoyée !</h1>
        <p class="text-lg text-textMuted mb-8">
            Merci pour votre confiance. Notre équipe analyse actuellement les caractéristiques de votre bien.
        </p>
        
        <div class="bg-orange-50 border border-orange-100 rounded-xl p-6 inline-block max-w-xl mx-auto">
            <div class="flex items-start gap-4 text-left">
                <i class="fa-regular fa-clock text-primary text-2xl mt-1"></i>
                <div>
                    <h3 class="font-bold text-dark text-lg mb-1">Prochaine étape</h3>
                    <p class="text-textMain">
                        Un expert AvenirImmo vous contactera sous <strong>48 heures</strong> pour vous présenter une première estimation ferme, sans engagement.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-card border border-grayBorder p-6 md:p-8 h-full">
        <h2 class="text-xl font-bold text-dark mb-6 flex items-center gap-2">
            <i class="fa-regular fa-file-lines text-primary"></i> Récapitulatif
        </h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div class="space-y-4 overflow-hidden">
                <div>
                    <span class="block text-sm text-textMuted mb-1">Type de bien</span>
                    <span id="sum-type" class="font-medium text-dark break-words">...</span>
                </div>
                <div>
                    <span class="block text-sm text-textMuted mb-1">Adresse</span>
                    <span id="sum-adresse" class="font-medium text-dark break-words">...</span>
                </div>
                <div>
                    <span class="block text-sm text-textMuted mb-1">Agencement</span>
                    <span id="sum-surface" class="font-medium text-dark break-words">...</span>
                </div>
            </div>
            <div class="space-y-4 overflow-hidden">
                <div>
                    <span class="block text-sm text-textMuted mb-1">État & Énergie</span>
                    <span id="sum-etat" class="font-medium text-dark break-words">...</span>
                </div>
                <div>
                    <span class="block text-sm text-textMuted mb-1">Délai de vente</span>
                    <span id="sum-delai" class="font-medium text-dark break-words">...</span>
                </div>
                <div>
                    <span class="block text-sm text-textMuted mb-1">Contact</span>
                    <span id="sum-contact" class="font-medium text-dark break-words">...</span>
                </div>
            </div>
            
            <div class="sm:col-span-2 mt-2 pt-4 border-t border-gray-100">
                <span class="block text-sm text-textMuted mb-1">Informations complémentaires</span>
                <span id="sum-infos" class="font-medium text-dark break-words text-sm">...</span>
            </div>
        </div>
    </div>
</div>