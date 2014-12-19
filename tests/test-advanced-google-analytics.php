<?php

namespace AGA;
use \AGA\Utils;

class Advanced_Google_Analytics_Test extends \WP_UnitTestCase {

	function setup_postdata()
	{
		global $post;
		global $wp_query;

		$wp_query->is_singular = true;

		$post_id = $this->factory->post->create( array(
			'post_title' => 'Hello!',
			'post_author' => 1,
			'post_status' => 'publish',
			'post_date' => '2014-01-01 00:00:00',
		) );

		$post = get_post( $post_id );
		setup_postdata( $post );
	}

	/**
	 * @test
	 */
	function get_dimensions_label()
	{
		$labels = Utils::get_dimensions_label();
		$this->assertSame( 4, count( $labels ) );
	}

	/**
	 * @test
	 */
	function get_author()
	{
		$this->setup_postdata();
		$this->assertSame( 'admin', Utils::get_author() );
	}

	/**
	 * @test
	 */
	function get_published()
	{
		$this->setup_postdata();
		$this->assertSame( '2014-01-01', Utils::get_published( 'Y-m-d' ) );
		$this->assertSame( '2014-01-01 00:00:00', Utils::get_published( 'Y-m-d H:i:s' ) );
		$this->assertSame( '2014/01/01', Utils::get_published( 'Y/m/d' ) );
	}

	/**
	 * @test
	 */
	function get_taxonomies()
	{
		$this->setup_postdata();
		$this->assertSame( array( 'Uncategorized' ), Utils::get_taxonomies( 'category' ) );
	}

	/**
	 * @test
	 */
	function format_label()
	{
		$this->assertSame( 'Category', Utils::format_label( 'category' ) );
		$this->assertSame( 'Post Format', Utils::format_label( 'post_format' ) );
	}

	/**
	 * @test
	 */
	function get_ga_dimensions()
	{
		$this->setup_postdata();

		$this->assertSame( "ga('send', 'pageview');", Utils::get_ga_dimensions() );

		update_option( 'advanced_google_analytics_category', 'dimension1' );
		$this->assertContains( "'dimension1': 'Uncategorized'", Utils::get_ga_dimensions() );

		update_option( 'advanced_google_analytics_author', 'dimension1' );
		$this->assertContains( "'dimension1': 'admin'", Utils::get_ga_dimensions() );

		add_filter( 'advanced_google_analytics_dimensions_labels', function(){
			return array(
				'author'   => 'dimension1',
				'category' => 'dimension2',
			);
		} );
		$this->assertContains( "'dimension1': 'admin'", Utils::get_ga_dimensions() );
		$this->assertContains( "'dimension2': 'Uncategorized'", Utils::get_ga_dimensions() );
	}

	/**
	 * @test
	 */
	function get_ga_code_01()
	{
		$this->setup_postdata();

		update_option( 'advanced_google_analytics_category', 'dimension1' );
		update_option( 'advanced_google_analytics_author', 'dimension2' );
		$this->assertContains( "'dimension1': 'Uncategorized'", Utils::get_ga_code( 'xxxx' ) );
		$this->assertContains( "'dimension2': 'admin'", Utils::get_ga_code( 'xxxx' ) );
		$this->assertContains( "ga('create', 'xxxx', 'auto');", Utils::get_ga_code( 'xxxx' ) );
	}

	/**
	 * @test
	 */
	function get_ga_code_02()
	{
		update_option( 'advanced_google_analytics_category', 'dimension1' );
		$this->assertContains( "ga('create', 'xxxx', 'auto');", Utils::get_ga_code( 'xxxx' ) );
		$this->assertContains( "ga('send', 'pageview');", Utils::get_ga_code( 'xxxx' ) );
	}

	/**
	 * @test
	 */
	function get_script()
	{
		$this->assertSame( null, Utils::get_script() );

		update_option( 'advanced_google_analytics_profile', 'xxxx' );
		$this->assertContains( "<!-- Advanced Google Analytics -->", Utils::get_script() );
	}
}

