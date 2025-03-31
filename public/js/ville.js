let villeInput = document.getElementById('ville_ville');
let lieuxInput = document.getElementById('ville_lieu');

let updateForm = async (url) => {

    const response = await fetch(url, {
        method: "GET",
        headers: {
            "Content-Type": "application/json",
            "charset": "utf-8"
        }
    });
    console.log(response);

    return await response.json();

};

const changeOptions = async (event) => {
    let villeId = event.target.value;
    let url = `/ville=${villeId}`;
    let lieux = await updateForm(url);

    lieuxInput.innerHTML = "";
    lieux.forEach(lieu => {
        let option = document.createElement("option");
        option.value = lieu.id;
        option.textContent = lieu.nom;
        lieuxInput.appendChild(option);
    });
};

villeInput.addEventListener("change", changeOptions);