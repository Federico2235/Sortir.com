let villeInput = document.getElementById('sortie_ville');
let lieuInput = document.getElementById('sortie_lieu');

villeInput.addEventListener('change', function () {
        lieuInput.innerHTML = 'none';
});
