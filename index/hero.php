<style>
    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in-up {
        animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        opacity: 0;
    }
</style>

<section class="relative pt-28 pb-16 lg:pt-32 lg:pb-20 overflow-hidden min-h-[85vh] flex flex-col justify-center bg-cover bg-center bg-no-repeat bg-zinc-950" 
         style="background-image: url('https://images.unsplash.com/photo-1745433071446-27825b69c612?q=80&w=2000&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D');">
    
    <div class="absolute inset-0 bg-gradient-to-r from-zinc-950/95 via-zinc-950/70 to-zinc-900/30 z-0"></div>

    <div class="absolute top-1/4 right-0 w-[600px] h-[600px] bg-primary rounded-full filter blur-[120px] opacity-20 pointer-events-none z-10 mix-blend-screen"></div>

    <div class="max-w-[90rem] mx-auto px-4 sm:px-6 lg:px-8 relative z-20 w-full mb-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">

            <div class="lg:col-span-7 animate-fade-in-up pr-0 lg:pr-8 relative z-20 py-8">
                
                <div class="inline-flex items-center gap-3 px-5 py-2.5 rounded-full border border-white/10 bg-white/5 backdrop-blur-md mb-8 shadow-lg">
                    <span class="w-2 h-2 rounded-full bg-primary shadow-[0_0_8px_rgba(var(--color-primary),0.8)]"></span>
                    <span class="text-[10px] font-bold tracking-[0.2em] text-zinc-300 uppercase">Marchand de biens / Rénovation</span>
                </div>

                <h1 class="text-5xl sm:text-6xl lg:text-7xl font-sans font-light text-white leading-[1.1] tracking-tight mb-6">
                    Achetez votre maison <br>
                    en pleine <br>
                    <span class="font-serif italic text-primary pr-2">métamorphose.</span>
                </h1>

                <p class="text-lg text-zinc-300 max-w-xl mb-10 leading-relaxed font-light">
                    Découvrez nos biens en cours de réhabilitation. Positionnez-vous à l'étape clé des travaux pour maîtriser votre budget et personnaliser vos futures finitions.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 mb-10 relative z-20">
                    <a href="estimation.php" class="bg-primary hover:bg-primary/90 text-white px-8 py-4 rounded-full font-light transition-all duration-300 shadow-[0_4px_20px_0_rgba(var(--color-primary),0.2)] hover:shadow-[0_6px_25px_rgba(var(--color-primary),0.3)] text-center flex items-center justify-center gap-2 group transform hover:-translate-y-1">
                        Estimer mon bien <i class="fa-solid fa-arrow-right group-hover:translate-x-1 transition-transform"></i>
                    </a>
                    <a href="contact.php" class="bg-white/5 hover:bg-white/10 backdrop-blur-md border border-white/10 text-white px-8 py-4 rounded-full font-light transition-all duration-300 text-center flex items-center justify-center gap-2 transform hover:-translate-y-1">
                        Nous contacter
                    </a>
                </div>

                <div class="flex flex-wrap items-center gap-x-8 gap-y-4 text-sm font-light text-zinc-300">
                    <div class="flex items-center gap-2"><i class="fa-solid fa-check text-primary text-xs"></i> Achat en direct</div>
                    <div class="flex items-center gap-2"><i class="fa-solid fa-check text-primary text-xs"></i> Tarifs évolutifs</div>
                    <div class="flex items-center gap-2"><i class="fa-solid fa-check text-primary text-xs"></i> Accompagnement travaux</div>
                </div>
            </div>

            <div class="lg:col-span-5 grid grid-cols-2 gap-4 lg:gap-5 h-[380px] sm:h-[450px] lg:h-[480px] animate-fade-in-up relative z-20 mt-8 lg:mt-0" style="animation-delay: 0.2s;">
                
                <div class="flex flex-col gap-4 lg:gap-5">
                    <div class="flex-grow rounded-[2rem] overflow-hidden relative group shadow-2xl border border-white/10">
                        <div class="absolute inset-0 bg-zinc-900/20 group-hover:bg-transparent transition-colors duration-500 z-10"></div>
                        <img src="https://images.unsplash.com/photo-1600247406677-d38e0052d95f?q=80&w=1374&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Chantier en cours" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    </div>
                    
                    <div class="h-24 sm:h-32 lg:h-36 bg-zinc-900/50 backdrop-blur-xl rounded-[2rem] flex flex-col items-center justify-center text-center border border-white/10 shadow-[0_8px_32px_0_rgba(0,0,0,0.3)] transition-all duration-300 hover:bg-zinc-900/70 hover:-translate-y-1 group">
                        <span class="text-3xl lg:text-4xl font-light text-white mb-1 group-hover:text-primary transition-colors">-20%</span>
                        <span class="text-[10px] lg:text-xs font-semibold tracking-widest text-zinc-300 uppercase">D'économie</span>
                    </div>
                </div>
                
                <div class="flex flex-col gap-4 lg:gap-5 pt-8 lg:pt-12">
                    <div class="h-24 sm:h-32 lg:h-36 bg-primary/20 backdrop-blur-xl rounded-[2rem] flex flex-col items-center justify-center text-center border border-primary/30 shadow-[0_8px_32px_0_rgba(0,0,0,0.3)] transition-all duration-300 hover:bg-primary/30 hover:-translate-y-1">
                        <span class="text-3xl lg:text-4xl font-light text-white mb-1">100%</span>
                        <span class="text-[10px] lg:text-xs font-semibold tracking-widest text-white/80 uppercase">Sur-mesure</span>
                    </div>
                    
                    <div class="flex-grow rounded-[2rem] overflow-hidden relative group shadow-2xl border border-white/10">
                        <div class="absolute inset-0 bg-zinc-900/20 group-hover:bg-transparent transition-colors duration-500 z-10"></div>
                        <img src="https://images.unsplash.com/photo-1542621334-a254cf47733d?q=80&w=1740&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Maison finie" class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-105">
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>