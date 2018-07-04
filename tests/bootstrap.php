<?php
/**
 * PHPUnit bootstrap file
 *
 * @since 2.1.0
 *
 * @package Mobile_Contact_Bar
 */

// Try the WP_TESTS_DIR environment variable first.
$mcb_tests_dir = ( getenv( 'WP_TESTS_DIR' ) ) ? getenv( 'WP_TESTS_DIR' ) : '/tmp/wordpress-tests-lib';

if ( ! file_exists( $mcb_tests_dir . '/includes/functions.php' ) ) {
	echo '"Could not find $mcb_tests_dir/includes/functions.php, have you run ./tests/bin/install-wp-tests.sh?"';
	exit( 1 );
}

// Load test function so tests_add_filter() is available.
require_once $this->wp_tests_dir . '/includes/functions.php';

/**
 * Manually load the plugin being tested.
 */
function mcb_manually_load_plugin() {

	require_once dirname( dirname( __FILE__ ) ) . '/mobile-contact-bar.php';
}
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

// Start up the WP testing environment.
require_once $mcb_tests_dir . '/includes/bootstrap.php';
