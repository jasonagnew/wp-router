<?php

require_once dirname( __FILE__ ) . '/../../vendor/autoload.php';

$wp_test_dirs = array(
	getenv( 'WP_DEVELOP_DIR' ) . '/tests/phpunit',
	getenv( 'WP_TESTS_DIR'   ),
	getenv( 'WP_ROOT_DIR'    ) . '/tests/phpunit',
	'../../../../tests/phpunit',
	'/tmp/wordpress-tests-lib',
);

$wp_test_path = '/';

foreach ( $wp_test_dirs as $dir )
{
	if ( file_exists( $dir ) )
	{
		$wp_test_path = $dir;
		break;
	}
}

require $wp_test_path . '/includes/functions.php';

function _manually_load_plugin()
{
	require_once dirname( __FILE__ ) . '/../../plugin.php';
}

tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

require $wp_test_path . '/includes/bootstrap.php';

require_once dirname( __FILE__ ) .  '/../support/testcase.php';
