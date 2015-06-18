<?php
/*
 * Plugin Name: admin-view-customize
 * Description: WordPress Plugin #5 sample plugin. This code is split!
 */

// cancelling default
if( has_action( 'welcome_panel' ) ) {
	remove_action( 'welcome_panel', 'wp_welcome_panel' );
}

// use our panel
add_action( 'welcome_panel', __callback_func__, 10, 0 );
function steal_welcome_panel() {
	// include something
}

// override admin footer, "Thank you for creating with WordPress"
add_filter( 'admin_footer_text', function( $html ) {
	return whatever_you_want;
});

// cancelling default
if( has_filter( 'update_footer' ) ) {
	remove_filter( 'update_footer', 'core_update_footer' );
}

// override update footer (version number)
add_filter( 'update_footer', function() {
	return whatever_you_want;
}, 20, 0 );


// dashboard widget registration
add_action( 'wp_dashboard_setup', 'avc_dashboard_widget' );



// metabox in the post edit screen
add_action( 'add_meta_boxes', 'avc_add_meta_boxes' );


// Theme widget
class AVC_Widget extends WP_Widget {

	public function __construct() {

		parent::__construct( 'avc_widget', 'AVC Widget', array(
			'classname'   => 'widget_meta',
			'description' => 'AVC Widget 예제'
		) );
	}
}

// Theme widget
add_action( 'widgets_init', function() {
	register_widget( 'AVC_Widget' );
} );

// more simple javascript
add_action( 'admin_enqueue_scripts', 'avc_simple_pointer_load');
