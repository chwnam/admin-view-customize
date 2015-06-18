jQuery(document).ready(function() {

    avcOpenPointer(0);

    function avcOpenPointer(i) {

        var pointer = avcPointer.pointers[i];
        console.log(pointer);
        var options = jQuery.extend( pointer.options, {
            close: function() {
                jQuery.post( ajaxurl, {
                    pointer: pointer.pointer_id,
                    action: 'dismiss-wp-pointer'
                });
            }
        });

        jQuery(pointer.target).pointer( options ).pointer('open');
    }
});