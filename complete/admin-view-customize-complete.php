<?php
/*
 * Plugin Name: admin-view-customize-complete
 * Description: WordPress Plugin #5 sample plugin.
 */

// cancelling default
if( has_action( 'welcome_panel' ) ) {
	remove_action( 'welcome_panel', 'wp_welcome_panel' );
}

// use our panel
add_action( 'welcome_panel', 'steal_welcome_panel', 10, 0 );
function steal_welcome_panel() {
	include( "includes/welcome-panel-content.html" );
}

// override admin footer, "Thank you for creating with WordPress"
add_filter( 'admin_footer_text', function( $html ) {
	return $html . " <span>AVC 플러그인 실습중이에요</span>.";
});

// cancelling default
if( has_filter( 'update_footer' ) ) {
	remove_filter( 'update_footer', 'core_update_footer' );
}

// override update footer (version number)
add_filter( 'update_footer', function() {
	return "You're using AVC plugin!";
}, 20, 0 );

// dashboard widget registration
add_action( 'wp_dashboard_setup', 'avc_dashboard_widget' );
function avc_dashboard_widget() {
	wp_add_dashboard_widget(
		'avc_dashboard',         // Widget slug.
		'AVC Dashboard',         // Title.
		'avc_dashboard_display', // Display function.
		'avc_dashboard_control'
	);
}

// dashboard output
function avc_dashboard_display( $post, $callback_args ) {
	echo '<span class="dashicons dashicons-star-empty"></span>';
	echo '<span>Ok! 여기는 우리 위젯이에요.</span>';
	echo '<p>AVC: ' . get_transient('avc');
	if( !get_transient('avc') ) echo '<p>configure 버튼을 눌러 값을 설정해 보세요. 몇초간만 값이 유지됩니다.</p>';
}

// control output
function avc_dashboard_control( $post, $callback_args ) {
	if( isset( $_POST['avc'] ) ) {
		set_transient( 'avc', $_POST['avc'], 15 );
	}
	?>
	<label for="avc">AVC Transient value:</label>
	<input type="text" name="avc" id="avc" value="<?=get_transient('avc')?>" />
	<?php
}

// metabox in the post edit screen
add_action( 'add_meta_boxes', 'avc_add_meta_boxes' );
function avc_add_meta_boxes() {
	add_meta_box( 'avc_meta', 'AVC Metabox', 'avc_meta_box', 'post', 'advanced' );
}

// metabox callback
function avc_meta_box( $post ) {
	echo "<div>여기는 AVC 사이드 메타박스입니다.</div>";
}

// Theme widget
class AVC_Widget extends WP_Widget {

	public function __construct() {

		parent::__construct( 'avc_widget', 'AVC Widget', array(
			'classname' => 'widget_meta',
			'description' => 'AVC Widget 예제'
		) );
	}

	public function widget( $args, $instance ) {

		$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? 'My Info': $instance['title'], $instance, $this->id_base );

		$title = esc_attr( $title );
		$movie = esc_attr( $instance['movie'] );
		$song  = esc_attr( $instance['song'] );

		echo $args['before_widget'];
		echo $args['before_title'] . $title . $args['after_title'];
		echo "<ul>";
		echo "<li>Fav Movie: $movie</li>";
		echo "<li>Fav Song: $song</li>";
		echo "</ul>";
		echo $args['after_widget'];
	}

	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;
		$instance['title'] = sanitize_text_field( $new_instance['title'] );
		$instance['movie'] = sanitize_text_field( $new_instance['movie'] );
		$instance['song']  = sanitize_text_field( $new_instance['song'] );

		return $instance;
	}

	public function form( $instance ) {
		$defaults = array( 'title' => 'My Info', 'movie' => '', 'song' => '' );
		$instance = wp_parse_args( (array)$instance, $defaults );
		extract( $instance );
		include( plugin_dir_path( __FILE__) . 'includes/widget-form-content.php' );
	}
}

// Theme widget
add_action( 'widgets_init', function() {
	register_widget( 'AVC_Widget' );
} );

// get pointer option
add_filter( 'avc_admin_pointers-post', 'avc_register_pointer_testing' );
function avc_register_pointer_testing( $p ) {
	$p['xyz140'] = array(
		'target' => 'input#title',
		'options' => array(
			'content' => sprintf( '<h3> %s </h3> <p> %s </p>',
				__( 'Title' ,'plugindomain'),
				__( 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.','plugindomain')
			),
			'position' => array( 'edge' => 'top', 'align' => 'left' )
		)
	);
	return $p;
}

// pointer javascript load
add_action( 'admin_enqueue_scripts', 'avc_pointer_load' );
function avc_pointer_load( $hook ) {

	if ( get_bloginfo( 'version' ) < '3.3' )
		return;

	// Get the screen ID
	$screen = get_current_screen();
	$screen_id = $screen->id;

	// Get pointers for this screen
	$pointers = apply_filters( 'avc_admin_pointers-' . $screen_id, array() );

	// No pointers? Then we stop.
	if ( ! $pointers || ! is_array( $pointers ) )
		return;

	// Get dismissed pointers
	$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );
	$valid_pointers = array();

	// Check pointers and remove dismissed ones.
	foreach ( $pointers as $pointer_id => $pointer ) {

		// Sanity check
		if ( in_array( $pointer_id, $dismissed ) || empty( $pointer )  || empty( $pointer_id ) || empty( $pointer['target'] ) || empty( $pointer['options'] ) )
			continue;

		$pointer['pointer_id'] = $pointer_id;

		// Add the pointer to $valid_pointers array
		$valid_pointers['pointers'][] =  $pointer;
	}

	// No valid pointers? Stop here.
	if ( empty( $valid_pointers ) )
		return;

	// Add pointers style to queue.
	wp_enqueue_style( 'wp-pointer' );

	// Add pointers script to queue. Add custom script.
	wp_enqueue_script( 'avc-pointer', plugins_url( 'statics/js/avc-pointer.js', __FILE__ ), array( 'wp-pointer' ) );

	// Add pointer options to script.
	wp_localize_script( 'avc-pointer', 'avcPointer', $valid_pointers );
}

// more simple javascript
add_action( 'admin_enqueue_scripts', 'avc_simple_pointer_load');
function avc_simple_pointer_load( $hook ) {

	if( $hook == 'options-general.php' ) {
		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'avc-pointer-simple', plugins_url( 'statics/js/avc-pointer-simple.js', __FILE__ ), array( 'wp-pointer' ) );
	}
}