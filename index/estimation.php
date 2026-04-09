<section class="py-20 bg-white relative">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <div class="bg-dark rounded-[2.5rem] p-8 md:p-12 lg:p-16 shadow-2xl relative overflow-hidden">
            
            <div class="absolute -top-32 -right-32 w-[500px] h-[500px] bg-primary rounded-full mix-blend-screen filter blur-[120px] opacity-20 pointer-events-none"></div>
            <div class="absolute -bottom-32 -left-32 w-[400px] h-[400px] bg-white rounded-full mix-blend-overlay filter blur-[100px] opacity-10 pointer-events-none"></div>

            <div class="relative z-10 text-center max-w-2xl mx-auto mb-10">
                <span class="text-[10px] font-bold tracking-[0.2em] text-primary uppercase mb-4 block">Sans engagement</span>
                <h2 class="text-3xl md:text-5xl font-sans font-light text-white mb-4">Estimez votre bien instantanément</h2>
                <p class="text-gray-400 font-light text-lg">Renseignez les caractéristiques de votre propriété et recevez une proposition d'achat ferme sous 48h.</p>
            </div>

            <form id="ajax-quick-form" class="relative z-10 bg-white/5 backdrop-blur-md border border-white/10 p-6 md:p-8 rounded-3xl space-y-5 shadow-xl max-w-4xl mx-auto">
                <input type="hidden" name="_subject" value="Demande d'estimation rapide (Section 2)">
                <input type="hidden" name="_captcha" value="false"> 
                <input type="hidden" name="_template" value="table">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                    <div>
                        <select name="Type_de_bien" required class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl text-white focus:ring-2 focus:ring-primary focus:border-transparent outline-none appearance-none font-light [&>option]:text-dark cursor-pointer">
                            <option value="" disabled selected>Type de bien...</option>
                            <option value="Appartement">Appartement</option>
                            <option value="Maison">Maison</option>
                            <option value="Immeuble">Immeuble</option>
                            <option value="Terrain">Terrain</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <input type="text" name="Adresse" required placeholder="Adresse complète de la propriété" class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl text-white placeholder-gray-400 focus:ring-2 focus:ring-primary outline-none font-light transition-all">
                    </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div>
                        <input type="number" name="Surface_m2" required placeholder="Surface (m²)" class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl text-white placeholder-gray-400 focus:ring-2 focus:ring-primary outline-none font-light transition-all">
                    </div>
                    <div>
                        <input type="tel" name="Telephone" required placeholder="Téléphone" class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl text-white placeholder-gray-400 focus:ring-2 focus:ring-primary outline-none font-light transition-all">
                    </div>
                    <div>
                        <input type="email" name="Email_Contact" required placeholder="Email" class="w-full px-5 py-4 bg-white/5 border border-white/10 rounded-2xl text-white placeholder-gray-400 focus:ring-2 focus:ring-primary outline-none font-light transition-all">
                    </div>
                </div>

                <div class="pt-2">
                    <button type="submit" id="submit-quick-btn" class="w-full h-full min-h-[56px] bg-primary hover:bg-primaryHover text-white rounded-2xl font-light transition-all duration-300 shadow-lg flex items-center justify-center gap-2 group transform hover:scale-[1.01]">
                        <span>Recevoir mon offre</span> <i class="fa-solid fa-paper-plane group-hover:translate-x-1 transition-transform"></i>
                    </button>
                    <p id="quick-error-msg" class="text-red-400 text-sm mt-3 font-light text-center hidden"></p>
                </div>
            </form>
            
            <div id="quick-form-success" class="hidden absolute inset-0 bg-dark z-20 flex-col items-center justify-center text-center p-8 rounded-[2.5rem]">
                <div class="w-20 h-20 bg-green-500/10 text-green-400 rounded-full flex items-center justify-center text-4xl mb-6 border border-green-500/20 shadow-[0_0_30px_rgba(34,197,94,0.2)]">
                    <i class="fa-solid fa-check"></i>
                </div>
                <h3 class="text-3xl font-sans font-light text-white mb-3">C'est noté !</h3>
                <p class="text-gray-400 font-light text-lg mb-8 max-w-md">Notre équipe étudie les caractéristiques de votre bien et vous recontactera très vite avec une offre.</p>
                <button onclick="resetQuickForm()" class="px-8 py-3 bg-white/10 hover:bg-white/20 text-white rounded-full font-light transition-colors">Nouvelle estimation</button>
            </div>

        </div>
    </div>
</section>