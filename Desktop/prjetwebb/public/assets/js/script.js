// Génération automatique du slug pour les catégories
document.addEventListener('DOMContentLoaded', function() {
    // Pour le formulaire des catégories
    const nomCatInput = document.getElementById('nom_cat');
    const slugInput = document.getElementById('slug');
    const slugPreview = document.getElementById('slugPreview');
    
    if (nomCatInput && slugInput) {
        nomCatInput.addEventListener('input', function() {
            if (!slugInput._changed) {
                const slug = generateSlug(this.value);
                slugInput.value = slug;
                if (slugPreview) {
                    slugPreview.textContent = '/categorie/' + slug;
                }
            }
        });
        
        slugInput.addEventListener('input', function() {
            this._changed = true;
            if (slugPreview) {
                slugPreview.textContent = '/categorie/' + this.value;
            }
        });
    }
    
    // Validation des formulaires
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const requiredFields = this.querySelectorAll('[required]');
            let isValid = true;
            
            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.style.borderColor = 'var(--error-color)';
                } else {
                    field.style.borderColor = '';
                }
            });
            
            if (!isValid) {
                e.preventDefault();
                alert('Veuillez remplir tous les champs obligatoires');
            }
        });
    });
});

function generateSlug(text) {
    return text
        .toLowerCase()
        .replace(/[^\w\s-]/g, '') // Supprime les caractères spéciaux
        .replace(/[\s_-]+/g, '-') // Remplace espaces et _ par -
        .replace(/^-+|-+$/g, ''); // Supprime les - en début et fin
}