jQuery(document).ready(function($) {

    const action_images = ['default', 'writing', 'userWriting', 'userSend', 'error'];
    action_images.forEach((image) => {
        // Subir imagen
        $('#polybot-upload-' + image).click(function(e) {
            e.preventDefault();
            var imageUploader = wp.media({
                title: 'Subir Imagen',
                button: {
                    text: 'Usar esta imagen'
                },
                multiple: false
            }).on('select', function() {
                var attachment = imageUploader.state().get('selection').first().toJSON();
                $('#polybot-image-' + image).val(attachment.id);
                $('#polybot-upload-' + image).html('<img src="' + attachment.url + '" style="max-width:100px;height:auto;" />');
                $('#polybot-remove-' + image).show();
            }).open();
        });

        // Eliminar imagen
        $('#polybot-remove-' + image).click(function(e) {
            e.preventDefault();
            $('#polybot-image-' + image).val('');
            $('#polybot-preview-' + image).html('');
            $(this).hide();
        });
    });
});