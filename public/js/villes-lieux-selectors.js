let villeInput = document.getElementById('sortie_ville');
let lieuInput = document.getElementById('sortie_lieu');
lieuInput.innerHTML = '<option value="">Veuillez choisir une ville</option>';

villeInput.addEventListener('change', function () {
    let url = document.getElementById('villeSelected').value;
    lieuInput.innerHTML = '';
    let option = document.createElement('option');
    option.value = null;
    option.textContent = "Veuillez selectionner un lieu";
    option.disabled = true;
    option.defaultSelected = true;
    lieuInput.appendChild(option);
    fetch(url + '/' + villeInput.value)
        .then(response => response.json())
        .then(lieux => {
            lieux.forEach(lieu => {
                let option = document.createElement('option');
                option.value = lieu.id;
                option.textContent = lieu.nom;
                lieuInput.appendChild(option);
            });
        })
});