let villeInput = document.getElementById('ville_ville');
let lieuxInput = document.getElementById('ville_lieu');
let updateForm = async (data, url, method) => {
    const response = await fetch(url, {
        method: method,
        data: data,
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'charset': 'utf-8'
        }
    });
    return await response.text();
};

const parseTextToHtml = (text) => {
    const parser = new DOMParser();
    const html = parser.parseFromString(text, 'text/html');

    return html;
};

const changeOptions = async (event) => {

    const requestBody = '/ville=' + event.target.value;
    const updateFormResponse = await updateForm(requestBody, villeInput.getAttribute('action'), villeInput.getAttribute('method'));
    const html = parseTextToHtml(updateFormResponse);

    const newLieuxInput = html.getElementById('lieu_nom');
    lieuxInput.innerHTML = newLieuxInput.innerHTML;
};
villeInput.addEventListener('change', (event) => changeOptions(event));