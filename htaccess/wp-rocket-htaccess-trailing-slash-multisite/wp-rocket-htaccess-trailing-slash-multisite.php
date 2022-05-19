<?php
/**
 * Plugin Name: WP Rocket | Enforce Trailing Slash on multisite
 * Description: Enforces Trailing Slash on URLs.
 * Author:      WP Rocket Support Team
 * Plugin URI:  https://github.com/wp-media/wp-rocket-helpers/tree/master/htaccess/wp-rocket-htaccess-trailing-slash-multisite/
 * Author URI:  http://wp-rocket.me/
 * License:     GNU General Public License v2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 *
 * Copyright SAS WP MEDIA 2021
 */
 
namespace WP_Rocket\Helpers\htaccess\redirect\enforce_trailing_slash_multisite;

// Standard plugin security, keep this line in place.
defined( 'ABSPATH' ) or die();


function render_rewrite_rules( $marker ) {
	
	$redirection  = '# Add trailing slash' . PHP_EOL;
	$redirection  .= 'RewriteCond %{REQUEST_URI} /+[^\.]+$' . PHP_EOL;
	$redirection  .= 'RewriteRule ^(.+[^/])$ https://%{HTTP_HOST}/$1/ [R=301,L]' . PHP_EOL;
	
	
	// Prepend redirection rules to WP Rocket block.
	$marker = $redirection . $marker;
	
	return $marker;
}
add_filter( 'before_rocket_htaccess_rules', __NAMESPACE__ . '\render_rewrite_rules' );

/**
 * Updates .htaccess, regenerates WP Rocket config file.
 *
 * @author Caspar Hübinger
 */
function flush_wp_rocket() {
	
	if ( ! function_exists( 'flush_rocket_htaccess' )
	  || ! function_exists( 'rocket_generate_config_file' ) ) {
		return false;
	}
	
	// Update WP Rocket .htaccess rules.
	flush_rocket_htaccess();
	
	// Regenerate WP Rocket config file.
	rocket_generate_config_file();
}
register_activation_hook( __FILE__, __NAMESPACE__ . '\flush_wp_rocket' );

/**
 * Removes customizations, updates .htaccess, regenerates config file.
 *
 * @author Caspar Hübinger
 */
function deactivate() {
	
	// Remove all functionality added above.
	remove_filter( 'before_rocket_htaccess_rules', __NAMESPACE__ . '\render_rewrite_rules' );
	
	// Flush .htaccess rules, and regenerate WP Rocket config file.
	flush_wp_rocket();
}
register_deactivation_hook( __FILE__, __NAMESPACE__ . '\deactivate' );
