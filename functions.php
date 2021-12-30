<?php
/**
 * Altitude Pro.
 *
 * This file adds the functions to the Altitude Pro Theme.
 *
 * @package Altitude Pro
 * @author  StudioPress
 * @license GPL-2.0-or-later
 * @link    https://my.studiopress.com/themes/altitude/
 */

// Starts the engine.
require_once get_template_directory() . '/lib/init.php';

// Defines the child theme (do not remove).
define( 'CHILD_THEME_HANDLE', sanitize_title_with_dashes( wp_get_theme()->get( 'Name' ) ) );
define( 'CHILD_THEME_VERSION', wp_get_theme()->get( 'Version' ) );

// Sets up theme.
require_once get_stylesheet_directory() . '/lib/theme-defaults.php';

add_action( 'after_setup_theme', 'altitude_localization_setup' );
/**
 * Sets localization (do not remove).
 *
 * @since 1.0.0
 */
function altitude_localization_setup() {
	load_child_theme_textdomain( 'altitude-pro', get_stylesheet_directory() . '/languages' );
}

// Adds the theme helper functions.
require_once get_stylesheet_directory() . '/lib/helper-functions.php';

// Adds Image upload and Color select to WordPress Theme Customizer.
require_once get_stylesheet_directory() . '/lib/customize.php';

// Includes Customizer CSS.
require_once get_stylesheet_directory() . '/lib/output.php';

// Includes the WooCommerce setup functions.
require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-setup.php';

// Includes the WooCommerce custom CSS if customized.
require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-output.php';

// Includes notice to install Genesis Connect for WooCommerce.
require_once get_stylesheet_directory() . '/lib/woocommerce/woocommerce-notice.php';

add_action( 'after_setup_theme', 'genesis_child_gutenberg_support' );
/**
 * Adds Gutenberg opt-in features and styling.
 *
 * @since 1.2.0
 */
function genesis_child_gutenberg_support() { // phpcs:ignore WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedFunctionFound -- using same in all child themes to allow action to be unhooked.
	require_once get_stylesheet_directory() . '/lib/gutenberg/init.php';
}

add_action( 'wp_enqueue_scripts', 'altitude_enqueue_scripts_styles' );
/**
 * Enqueues scripts and styles.
 *
 * @since 1.0.0
 */
function altitude_enqueue_scripts_styles() {

	wp_enqueue_script(
		'altitude-global',
		get_stylesheet_directory_uri() . '/js/global.js',
		array( 'jquery' ),
		'1.0.0',
		true
	);

	wp_enqueue_style( 'dashicons' );

	wp_enqueue_style(
		'altitude-google-fonts',
		'https://fonts.googleapis.com/css2?family=Lato:ital,wght@0,100;0,300;0,400;0,700;0,900;1,100;1,300;1,400;1,700;1,900&display=swap',
		array(),
		CHILD_THEME_VERSION
	);

	$suffix = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
	wp_enqueue_script(
		'altitude-responsive-menu',
		get_stylesheet_directory_uri() . '/js/responsive-menus' . $suffix . '.js',
		array( 'jquery' ),
		CHILD_THEME_VERSION,
		true
	);

	wp_localize_script(
		'altitude-responsive-menu',
		'genesis_responsive_menu',
		altitude_responsive_menu_settings()
	);

}

/**
 * Defines responsive menu settings.
 *
 * @since 1.1.0
 */
function altitude_responsive_menu_settings() {

	$settings = array(
		'mainMenu'    => __( '', 'wmw' ),
		'subMenu'     => __( 'Submenu', 'wmw' ),
		'menuClasses' => array(
			'combine' => array(
				'.nav-primary',
				'.nav-secondary',
			),
		),
	);

	return $settings;

}

add_action( 'after_setup_theme', 'altitude_theme_support', 9 );
/**
 * Add desired theme supports.
 *
 * See config file at `config/theme-supports.php`.
 *
 * @since 1.3.0
 */
function altitude_theme_support() {

	$theme_supports = genesis_get_config( 'theme-supports' );

	foreach ( $theme_supports as $feature => $args ) {
		add_theme_support( $feature, $args );
	}

}

// Adds new image sizes.
add_image_size( 'featured-page', 1140, 400, true );

// Unregisters the header right widget area.
unregister_sidebar( 'header-right' );

// Repositions the primary navigation menu.
remove_action( 'genesis_after_header', 'genesis_do_nav' );
add_action( 'genesis_header', 'genesis_do_nav', 12 );

// Repositions the secondary navigation menu.
remove_action( 'genesis_after_header', 'genesis_do_subnav' );
add_action( 'genesis_header', 'genesis_do_subnav', 5 );

add_filter( 'genesis_skip_links_output', 'altitude_skip_links_output' );
/**
 * Removes skip link for primary navigation and adds a skip link for footer widgets.
 *
 * @since 1.1.0
 *
 * @param array $links The list of skip links.
 * @return array The modified list of skip links.
 */
function altitude_skip_links_output( $links ) {

	if ( isset( $links['genesis-nav-primary'] ) ) {
		unset( $links['genesis-nav-primary'] );
	}

	return $links;

}

add_filter( 'body_class', 'altitude_secondary_nav_class' );
/**
 * Adds secondary-nav class if secondary navigation is used.
 *
 * @since 1.0.0
 *
 * @param array $classes Original body classes.
 * @return array Modified body classes.
 */
function altitude_secondary_nav_class( $classes ) {

	$menu_locations = get_theme_mod( 'nav_menu_locations' );

	if ( ! empty( $menu_locations['secondary'] ) ) {
		$classes[] = 'secondary-nav';
	}

	return $classes;

}

add_action( 'genesis_footer', 'altitude_footer_menu', 7 );
/**
 * Hooks footer menu in footer.
 *
 * @since 1.1.0
 */
function altitude_footer_menu() {

	genesis_nav_menu(
		array(
			'theme_location' => 'footer',
			'container'      => false,
			'depth'          => 1,
			'fallback_cb'    => false,
			'menu_class'     => 'genesis-nav-menu',
		)
	);

}

add_action( 'after_setup_theme', 'altitude_additional_schema', 11 );
/**
 * Adds attributes for Footer Navigation if Genesis is outputting schema.
 */
function altitude_additional_schema() {

	if ( ! genesis_is_wpseo_outputting_jsonld() && ! apply_filters( 'genesis_disable_microdata', false ) ) { // phpcs:ignore -- uses genesis filter function
		add_filter( 'genesis_attr_nav-footer', 'StudioPress\Genesis\Functions\Schema\nav_primary' );
	}

}

// Unregisters layout settings.
genesis_unregister_layout( 'content-sidebar-sidebar' );
genesis_unregister_layout( 'sidebar-content-sidebar' );
genesis_unregister_layout( 'sidebar-sidebar-content' );

// Unregisters secondary sidebar.
unregister_sidebar( 'sidebar-alt' );

add_filter( 'genesis_author_box_gravatar_size', 'altitude_author_box_gravatar' );
/**
 * Modifies the size of the Gravatar in the author box.
 *
 * @since 1.0.0
 *
 * @return int The new author box Gravatar size.
 */
function altitude_author_box_gravatar() {
	return 176;
}

add_filter( 'genesis_comment_list_args', 'altitude_comments_gravatar' );
/**
 * Modifies the size of the Gravatar in the entry comments.
 *
 * @since 1.0.0
 *
 * @param array $args Comment list arguments.
 * @return array Comment list arguments with modified avatar size.
 */
function altitude_comments_gravatar( $args ) {

	$args['avatar_size'] = 120;

	return $args;

}

// Relocates after entry widget.
remove_action( 'genesis_after_entry', 'genesis_after_entry_widget_area' );
add_action( 'genesis_after_entry', 'genesis_after_entry_widget_area', 5 );

/**
 * Counts widgets in given sidebar.
 *
 * @since 1.0.0
 *
 * @param string $id The id of the widget area.
 * @return void|int The number of widgets, or nothing.
 */
function altitude_count_widgets( $id ) {

	$sidebars_widgets = wp_get_sidebars_widgets();

	if ( isset( $sidebars_widgets[ $id ] ) ) {
		return count( $sidebars_widgets[ $id ] );
	}

}

/**
 * Gets class name for widget areas based on widget count.
 *
 * Used by front-page.php.
 *
 * @since 1.0.0
 *
 * @param string $id The ID of the widget area.
 * @return string The class name to use based on the widget count.
 */
function altitude_widget_area_class( $id ) {

	$count = altitude_count_widgets( $id );

	$class = '';

	if ( 1 === $count ) {
		$class .= ' widget-full';
	} elseif ( 1 === $count % 3 ) {
		$class .= ' widget-thirds';
	} elseif ( 1 === $count % 4 ) {
		$class .= ' widget-fourths';
	} elseif ( 0 === $count % 2 ) {
		$class .= ' widget-halves uneven';
	} else {
		$class .= ' widget-halves';
	}

	return $class;

}

// Relocates the post info.
remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
add_action( 'genesis_entry_header', 'genesis_post_info', 5 );

add_filter( 'genesis_post_info', 'altitude_post_info_filter' );
/**
 * Modifies the entry meta in the entry header.
 *
 * @since 1.0.0
 *
 * @return string New post info.
 */
function altitude_post_info_filter() {

	return '[post_date format="M d Y"] [post_edit]';

}

add_filter( 'genesis_post_meta', 'altitude_post_meta_filter' );
/**
 * Modifies the entry meta in the entry footer.
 *
 * @return string The new entry meta.
 */
function altitude_post_meta_filter() {

	return 'Written by [post_author_posts_link] [post_categories before=" &middot; Categorized: "]  [post_tags before=" &middot; Tagged: "]';

}

// Registers widget areas.
genesis_register_sidebar(
	array(
		'id'          => 'front-page-1',
		'name'        => __( 'Front Page 1', 'wmw' ),
		'description' => __( 'This is the front page 1 section.', 'wmw' ),
	)
);
genesis_register_sidebar(
	array(
		'id'          => 'front-page-2',
		'name'        => __( 'Front Page 2', 'wmw' ),
		'description' => __( 'This is the front page 2 section.', 'wmw' ),
	)
);
genesis_register_sidebar(
	array(
		'id'          => 'front-page-3',
		'name'        => __( 'Front Page 3', 'wmw' ),
		'description' => __( 'This is the front page 3 section.', 'wmw' ),
	)
);
genesis_register_sidebar(
	array(
		'id'          => 'front-page-4',
		'name'        => __( 'Front Page 4', 'wmw' ),
		'description' => __( 'This is the front page 4 section.', 'wmw' ),
	)
);
genesis_register_sidebar(
	array(
		'id'          => 'front-page-5',
		'name'        => __( 'Front Page 5', 'wmw' ),
		'description' => __( 'This is the front page 5 section.', 'wmw' ),
	)
);
genesis_register_sidebar(
	array(
		'id'          => 'front-page-6',
		'name'        => __( 'Front Page 6', 'wmw' ),
		'description' => __( 'This is the front page 6 section.', 'wmw' ),
	)
);
genesis_register_sidebar(
	array(
		'id'          => 'front-page-7',
		'name'        => __( 'Front Page 7', 'wmw' ),
		'description' => __( 'This is the front page 7 section.', 'wmw' ),
	)
);




function wmw_header_phone() {
    register_sidebar(
        array (
            'name' => __( 'Header Phone & Button', 'wmw' ),
            'id' => 'wmw-header-phone-bar',
            'description' => __( 'Custom Sidebar', 'wmw' ),
            'before_widget' => '',
            'after_widget' => '',
            'before_title' => '',
            'after_title' => '',
        )
    );
}
add_action( 'widgets_init', 'wmw_header_phone' );

function wmw_menu_ctas() {
	if ( is_active_sidebar( 'wmw-header-phone-bar' ) ) {
		dynamic_sidebar( 'wmw-header-phone-bar' );
	}
}
add_filter( 'genesis_header', 'wmw_menu_ctas', 11 );




function wmw_mobile_ft_phone() {
    register_sidebar(
        array (
            'name' => __( 'Footer Mobile Phone & Button', 'wmw' ),
            'id' => 'wmw-ft-phone-bar',
            'description' => __( 'Custom sticky buttons that appear on mobile at the bottom of the screen.', 'wmw' ),
            'before_widget' => '',
            'after_widget' => '',
            'before_title' => '',
            'after_title' => '',
        )
    );
}
add_action( 'widgets_init', 'wmw_mobile_ft_phone' );
function wmw_ft_ctas() {
	if ( is_active_sidebar( 'wmw-ft-phone-bar' ) ) {
		dynamic_sidebar( 'wmw-ft-phone-bar' );
	}
}
add_filter( 'genesis_after', 'wmw_ft_ctas', 11 );




function wmw_fontawesome_embed() {
	echo '<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">';
}
add_filter( 'get_header', 'wmw_fontawesome_embed' );




function cptui_register_my_cpts() {
	/**
	 * Post Type: Projects.
	 */
	$labels = [
		"name" => __( "Projects", "wmw" ),
		"singular_name" => __( "Project", "wmw" ),
	];
	$args = [
		"label" => __( "Projects", "wmw" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => [ "slug" => "projects", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail" ],
		"taxonomies" => [ "media-category", "media-tag" ],
	];
	register_post_type( "projects", $args );

	/**
	 * Post Type: Products.
	 */
	$labels = [
		"name" => __( "Products", "wmw" ),
		"singular_name" => __( "Products", "wmw" ),
	];
	$args = [
		"label" => __( "Products", "wmw" ),
		"labels" => $labels,
		"description" => "",
		"public" => true,
		"publicly_queryable" => true,
		"show_ui" => true,
		"show_in_rest" => true,
		"rest_base" => "",
		"rest_controller_class" => "WP_REST_Posts_Controller",
		"has_archive" => false,
		"show_in_menu" => true,
		"show_in_nav_menus" => true,
		"delete_with_user" => false,
		"exclude_from_search" => false,
		"capability_type" => "post",
		"map_meta_cap" => true,
		"hierarchical" => false,
		"rewrite" => [ "slug" => "products", "with_front" => true ],
		"query_var" => true,
		"supports" => [ "title", "editor", "thumbnail" ],
		"taxonomies" => [ "media-category", "media-tag" ],
	];
	register_post_type( "products", $args );
}
add_action( 'init', 'cptui_register_my_cpts' );



add_action( 'customize_register', 'themeprefix_customizer_featured_image' ); 
function themeprefix_customizer_featured_image() {
	global $wp_customize;	
	// Add featured image section to the Customizer
	$wp_customize->add_section(
	'themeprefix_single_image_section',
	array(
		'title'       => __( 'Post and Page Images', 'themeprefix' ),
		'description' => __( 'Choose if you would like to display the featured image above the content on single posts and pages.', 'themeprefix' ),
		'priority' => 200, // puts the customizer section lower down
	)
);
	// Add featured image setting to the Customizer
	$wp_customize->add_setting(
	'themeprefix_single_image_setting',
	array(
		'default'           => true, // set the option as enabled by default
		'capability'        => 'edit_theme_options',
	)
);
	$wp_customize->add_control(
	'themeprefix_single_image_setting',
	array(
		'section'   => 'themeprefix_single_image_section',
		'settings'  => 'themeprefix_single_image_setting',
		'label'     => __( 'Show featured image on posts and pages?', 'themeprefix' ),
		'type'      => 'checkbox'
	)
);

}

// Add featured image on single post
add_action( 'genesis_entry_content', 'themeprefix_featured_image', 1 );
function themeprefix_featured_image() {

	$add_single_image = get_theme_mod( 'themeprefix_single_image_setting', true ); //sets the customizer setting to a variable

	$image = genesis_get_image( array( // more options here -> genesis/lib/functions/image.php
			'format'  => 'html',
			'size'    => 'large',// add in your image size large, medium or thumbnail - for custom see the post
			'context' => '',
			'attr'    => array ( 'class' => 'aligncenter' ), // set a default WP image class
			
		) );

	if ( is_singular() && ( true === $add_single_image ) ) {
		if ( $image ) {
			printf( '<div class="featured-image-class">%s</div>', $image ); // wraps the featured image in a div with css class you can control
		}
	}

}
add_filter( 'gform_zapier_field_value', function ( $value, $form_id, $field_id, $entry ) {
	$field = GFAPI::get_field( $form_id, $field_id );

	if ( $field instanceof GF_Field_FileUpload ) {
		$value = $field->get_value_export( $entry );
	}

	return $value;
}, 10, 4 );