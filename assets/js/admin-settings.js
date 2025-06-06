jQuery(document).ready(function($) {
    $('.addweb-upload-button').click(function(e) {
        e.preventDefault();
        let target = $(this).data('target');
        let frame = wp.media({
            title: 'Select or Upload Image',
            button: { text: 'Use this image' },
            multiple: false
        });

        frame.on('select', function() {
            let attachment = frame.state().get('selection').first().toJSON();
            $('#' + target).val(attachment.url).prev('img').remove();
            $('#' + target).before('<img src="' + attachment.url + '" style="max-height:60px; display:block; margin-bottom:10px;" />');
        });

        frame.open();
    });
    
});
