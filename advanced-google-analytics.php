<?php
/*
Plugin Name: Advanced Google Analytics
Version: 0.1.0
Description: advanced-google-analytics
Author: digitalcube
Text Domain: advanced-google-analytics
Domain Path: /languages
*/

require dirname( __FILE__ ) . '/vendor/autoload.php';

$advanced_google_analytics = new Advanced_Google_Analytics();
$advanced_google_analytics->register();

class Advanced_Google_Analytics {

	public function __construct() {

	}

	public function register()
	{
		add_action( 'plugins_loaded', array( $this, 'plugins_loaded' ) );
	}


	public function plugins_loaded()
	{
		add_action( 'wp_head', array( $this, 'wp_head' ) );

		add_action( 'admin_init', array( '\AGA\Admin', 'admin_init' ) );
		add_action( 'admin_menu', array( '\AGA\Admin', 'admin_menu' ) );
		add_action( 'admin_notices', array( '\AGA\Admin', 'admin_notices' ) );

		add_filter(
			'advanced_google_analytics_dimensions_labels',
			array( $this, 'advanced_google_analytics_dimensions_labels' )
		);
	}

	public function wp_head()
	{
		echo \AGA\Utils::get_script();
	}

	public function advanced_google_analytics_dimensions_labels( $labels )
	{
		foreach ( $labels as $dimension => $label ) {
			if ( get_option( 'advanced_google_analytics_' . $dimension ) ) {
				$labels[ $dimension ] = get_option( 'advanced_google_analytics_' . $dimension );
			} else {
				$labels[ $dimension ] = $label;
			}
		}

		return $labels;
	}
}
