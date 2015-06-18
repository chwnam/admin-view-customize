jQuery(document).ready(function() {
    var pointer = jQuery("input#blogname").pointer({
        'content': '<h3>포인터 예제</h3><p>우리가 만든 포인터입니다.</p>',
        'position': {
            'edge': 'top',
            'align': 'right'
        },
        'pointer': 'avc-pointer',
        'close': function() {
            jQuery.post( ajaxurl, {
                pointer: 'avc-pointer',
                action: 'dismiss-wp-pointer'
            });
        }
    }).pointer('open');
});