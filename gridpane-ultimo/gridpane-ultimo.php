<?php
/**
 * All this is Dima really, I just made it a plugin
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'Hello, Hello, Hello, what\'s going on here then?' );
}

function gridpane_integration_init() {
	if ( class_exists( 'WU_Domain_Mapping_Hosting_Support' ) ) {

		require plugin_dir_path( __FILE__ ) . '/class-gp-wu-domain-mapping-hosting-support.php';

	}
}
add_action( 'plugins_loaded', 'gridpane_integration_init' );