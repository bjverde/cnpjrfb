var script = document.createElement('script'); // Define o src do script para o seu arquivo JavaScript
script.src = 'app/lib/widget/FormDin5/javascript/FormDin5GeoLocation.js';
document.body.appendChild(script); // Define o src do script para o seu arquivo JavaScript

// Adiciona um ouvinte para o evento personalizado
document.addEventListener('fd5GeolocationLoad', function() {
    fd5GetLocation();
});