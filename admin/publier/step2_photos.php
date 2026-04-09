<div id="step-2" class="form-step space-y-6">
    <h2 class="text-xl font-bold border-b pb-3 mb-4">2. Galerie Photos</h2>
    <p class="text-sm text-gray-500">Ajoutez vos photos, modifiez leurs tags, et glissez-les pour changer l'ordre (la première sera l'image principale).</p>
    
    <div id="photo-dropzone" 
         class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:bg-gray-100 hover:border-primary transition-colors relative cursor-pointer" 
         onclick="document.getElementById('photo-upload').click()"
         ondragover="event.preventDefault(); this.classList.add('bg-gray-100', 'border-primary');"
         ondragleave="event.preventDefault(); this.classList.remove('bg-gray-100', 'border-primary');"
         ondrop="handlePhotoDrop(event)">
        
        <i class="fa-solid fa-cloud-arrow-up text-4xl text-gray-400 mb-3 pointer-events-none"></i>
        <h3 class="font-bold text-dark pointer-events-none">Cliquez ou glissez vos photos ici</h3>
        <p class="text-xs text-gray-500 mt-1 pointer-events-none">JPG, PNG, WEBP (Max 50 photos)</p>
        <input type="file" id="photo-upload" multiple accept="image/*" class="hidden" onchange="handlePhotoSelect(event)">
    </div>

    <ul id="photos-list" class="space-y-3 mt-6">
        </ul>
</div>