<?php

namespace AGA;

use \AGA\Admin;
use \AGA\Utils;

class Admin {

	private function __construct() {}

	/**
	 * Fire the settings API
	 *
	 * @access public
	 * @param  none
	 * @return none
	 */
	public static function admin_init()
	{
		/*
		 * Google Analytics Profile section
		 */
		add_settings_section(
			'advanced-google-analytics-profile', // id
			'Google Analytics Profile',
			false,
			'advanced-google-analytics' // page
		);

		add_settings_field(
			'advanced_google_analytics_profile',
			'Profile',
			function (){
				printf(
					'<input type="text" name="%s" placeholder="%s" value="%s">',
					'advanced_google_analytics_profile',
					'UA-xxxxxxx-x',
					esc_attr( get_option( 'advanced_google_analytics_profile' ) )
				);
			},
			'advanced-google-analytics',
			'advanced-google-analytics-profile'
		);

		register_setting( 'advanced-google-analytics', 'advanced_google_analytics_profile' );

		/*
		 * Custom Dimensions section
		 */
		add_settings_section(
			'advanced-google-analytics-dimensions', // id
			'Custom Dimensions',
			false,
			'advanced-google-analytics' // page
		);

		$i = 1;
		foreach ( \AGA\Utils::get_dimensions_label() as $dimension => $label ) {
			$id = 'advanced_google_analytics_' . $dimension;
			add_settings_field(
				$id,
				Utils::format_label( $dimension ),
				function () use ( $id, $label, $i ) {
					printf(
						'<input type="text" name="%s" placeholder="%s" value="%s">',
						esc_attr( $id ),
						'dimension' . $i,
						esc_attr( $label )
					);
				},
				'advanced-google-analytics',
				'advanced-google-analytics-dimensions'
			);

			register_setting( 'advanced-google-analytics', $id );
			$i = $i + 1;
		}
	}

	/**
	 * Adding Google Analytic Menu in the options.
	 *
	 * @access public
	 * @param  none
	 * @return none
	 */
	public static function admin_menu()
	{
		add_options_page(
			'Google Analytics',
			'Google Analytics',
			'switch_themes',
			'advanced-google-analytics',
			array( '\AGA\Admin', 'admin_panel' )
		);
	}

	/**
	 * Generate admin panel
	 *
	 * @access public
	 * @param  none
	 * @return none
	 */
	public static function admin_panel()
	{
	?>
		<div class="wrap">
			<h2>Google Analytics</h2>
			<form action="options.php" method="POST">
				<?php settings_fields( 'advanced-google-analytics' ); ?>
				<?php do_settings_sections( 'advanced-google-analytics' ); ?>
				<?php submit_button(); ?>
			</form>
		</div>
	<?php
	}

	/**
	 * Send notification when google analytics profile doesn't set.
	 *
	 * @access public
	 * @param  none
	 * @return none
	 */
	public static function admin_notices()
	{
		if ( get_option( 'advanced_google_analytics_profile' ) ) {
			return;
		}

		?>
			<div class="error">
				<p>
					<?php _e(
						'Please setup <a href="options-general.php">GA Custom Dimensions</a>.',
						'advanced-google-analytics'
					); ?>
				</p>
			</div>
		<?php
	}
}
