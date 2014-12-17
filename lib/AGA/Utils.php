<?php

namespace AGA;

use \AGA\Utils;

class Utils {

	private function __construct() {}

	/**
	 * Return the Google Anlytics code.
	 *
	 * @access public
	 * @param  none
	 * @return none
	 */
	public static function get_ga_code( $account )
	{
		$ga =<<<EOL
(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
ga('create', '%s', 'auto');
%s
EOL;

		return sprintf( $ga, esc_js( $account ), self::get_ga_dimensions() );
	}

	/**
	 * Returns the Google Analytics custom dimensions code.
	 *
	 * @access public
	 * @param  none
	 * @return string The Google Analytics custom dimensions code.
	 */
	public static function get_ga_dimensions()
	{
		if ( ! is_singular() ) {
			return "ga('send', 'pageview');";
		}

		$js = "'%s': %s";

		$dimensions = self::get_dimensions();

		$code = array();
		foreach ( $dimensions as $key => $value ) {
			if ( ! $key || ! $value ) {
				continue;
			}
			if ( is_array( $value ) && 1 < count( $value ) ) {
				$code[] = sprintf( esc_js( $js ), $key, json_encode( $value ) );
			} elseif ( is_array( $value ) && 1 === count( $value ) ) {
				$code[] = sprintf( $js, esc_js( $key ), "'" . esc_js( $value[0] ) . "'" );
			} else {
				$code[] = sprintf( $js, esc_js( $key ), "'" . esc_js( $value ) . "'" );
			}
		}

		if ( ! $code ) {
			return "ga('send', 'pageview');";
		} else {
			return "ga('send', 'pageview', {\n" . join( ",\n", $code ) . "\n});\n";
		}
	}

	/**
	 * Retunrs the custom dimensions array.
	 *
	 * @access public
	 * @param  none
	 * @return array The custom dimensions array.
	 */
	public static function get_dimensions()
	{
		$dimensions_label = self::get_dimensions_label();

		foreach ( $dimensions_label as $dimension => $label ) {
			$dimensions[ $label ] = self::get_taxonomies( $dimension );
		}

		$dimensions[ $dimensions_label['author'] ] = self::get_author();

		return apply_filters(
			'advanced_google_analytics_dimensions',
			$dimensions
		);
	}

	/**
	 * Retunrs the custom dimension's labels as array.
	 *
	 * @access public
	 * @param  none
	 * @return array The custom dimension's labels as array.
	 */
	public static function get_dimensions_label()
	{
		$dimensions_label = array(
			'author' => '',
		);

		$taxonomies = get_taxonomies( array(
			'public'   => true,
			'_builtin' => true,
		) );

		foreach ( $taxonomies as $slug => $name ) {
			$dimensions_label[ $name ] = '';
		}

		return apply_filters( 'advanced_google_analytics_dimensions_labels', $dimensions_label );
	}

	/**
	 * Returns the author's display name.
	 *
	 * @access public
	 * @param  none
	 * @return string The author's display name.
	 */
	public static function get_author()
	{
		global $post;

		$u = get_userdata( $post->post_author );
		return $u->display_name;
	}

	/**
	 * Returns the post publishing date.
	 *
	 * @access public
	 * @param  none
	 * @return string The post publishing date.
	 */
	public static function get_published( $format )
	{
		global $post;

		$date = strtotime( $post->post_date );

		return date( $format, $date );
	}

	/**
	 * Returns the tax names in the specified taxonomy.
	 *
	 * @access public
	 * @param  string $tax_slug The taxonomie's slug.
	 * @return array  The tax name array.
	 */
	public static function get_taxonomies( $tax_slug )
	{
		global $post;

		$tax_names = array();
		foreach ( wp_get_object_terms( $post->ID, $tax_slug ) as $tax ) {
			$tax_names[] = $tax->name;
		}

		return $tax_names;
	}

	/**
	 * Returns the label that is uppercased.
	 *
	 * @access public
	 * @param  string $label
	 * @return string Uppercased label.
	 */
	public static function format_label( $label )
	{
		return ucwords( str_replace( '_', ' ', $label ) );
	}
}
