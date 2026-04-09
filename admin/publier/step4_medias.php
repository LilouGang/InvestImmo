<div id="step-4" class="form-step space-y-6">
    <h2 class="text-xl font-bold border-b pb-3 mb-4">4. Plans et Visite 3D</h2>
    
    <div>
        <label class="block font-bold text-dark mb-2"><i class="fa-solid fa-vr-cardboard text-blue-500 mr-2"></i>Lien Visite Virtuelle (Optionnel)</label>
        <input type="url" id="visite_vr" placeholder="Ex: https://my.matterport.com/show/?m=..." class="w-full px-4 py-3 border border-grayBorder rounded-lg focus:ring-2 focus:ring-blue-500/50 outline-none">
    </div>

    <div class="pt-6 border-t border-gray-100 mt-6">
        <label class="block font-bold text-dark mb-2"><i class="fa-solid fa-file-pdf text-red-500 mr-2"></i>Plans PDF (Optionnels)</label>
        <div class="bg-gray-50 border-2 border-dashed border-gray-300 rounded-xl p-6 text-center hover:bg-gray-100 transition-colors relative cursor-pointer mb-4" onclick="document.getElementById('plan-upload').click()">
            <i class="fa-solid fa-file-circle-plus text-3xl text-gray-400 mb-2"></i>
            <h3 class="font-bold text-dark text-sm">Ajouter des plans PDF</h3>
            <input type="file" id="plan-upload" multiple accept="application/pdf" class="hidden" onchange="handlePlanSelect(event)">
        </div>
        <ul id="plans-list" class="space-y-2">
            </ul>
    </div>
</div>