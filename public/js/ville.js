let villeInput = document.getElementById('ville_nom');

$(document).ready(function() {
    villeInput.on('change', function(event) {
        event.preventDefault();

        var form = $(this);
        $.ajax({
            type: form.attr('method'),
            url: form.attr('action'),
            data: form.serialize(),
            success: function(response) {
                $('#form-container').html(response);
            },
            error: function(xhr, status, error) {
                console.log('Erreur : ' + error);
            }
        });
    });
});