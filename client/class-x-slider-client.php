<?php

/**
 * The client-side functionality of the plugin.
 *
 * @link       http://github.com/hugodias
 * @since      1.0.0
 *
 * @package    X_Slider_Client
 * @subpackage X_Slider_Client/client
 */

/**
 * The client-side functionality of the plugin.
 *
 * Defines the plugin name, version, the meta box functionality
 * and the JavaScript for loading the Media Uploader.
 *
 * @package    X_Slider_Client
 * @subpackage X_Slider_Client/client
 * @author     Hugodias <hugooodias@gmail.com>
 */
class X_Slider_Client {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $name The ID of this plugin.
	 */
	private $name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $version The version of the plugin
	 */
	private $version;

	/**
	 * The Featured Image Slug used for the slides
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string $featured_image_name The Featured image slug.
	 */
	private $featured_image_name;


	/**
	 * Enable bullets navigation
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var bool
	 */
	private $bullets;

	/**
	 * Enable arrows navigation
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var bool
	 */
	private $arrows;

	/**
	 * Initializes the plugin by defining the properties.
	 *
	 * @since 1.0.0
	 */
	public function __construct( $featured_image_name = null, $bullets = null, $arrows = null ) {

		$this->name                = 'x-slider';
		$this->version             = '1.0.0';
		$this->featured_image_name = $featured_image_name ? $featured_image_name : 'x_slider_full';
		$this->bullets             = $bullets ? $featured_image_name : true;
		$this->arrows              = $arrows ? $featured_image_name : true;

	}

	/**
	 * Defines the hooks that will register and enqueue the JavaScriot
	 * and the meta box that will render the option.
	 *
	 * @since 1.0.0
	 */
	public function run() {
		$slides = $this->get_slides();

		if ( $slides ) {
			$slider = '<ul class="x-slider">';

			foreach ( $slides as $slide ) {
				$slider .= $this->loopAndRender( $slide['image'], $slide['title'], $slide['description'], $slide['link'], $slide['label'] );
			}

			$slider .= '</ul>';

			return $slider;
		}

		return;
	}


	/**
	 * Render a single slide
	 *
	 * @param $image
	 * @param $title
	 * @param $description
	 * @param $link
	 * @param $label
	 *
	 * @return string
	 *
	 * @since 1.0.0
	 */
	private function loopAndRender( $image, $title = null, $description = null, $link = null, $label = null ) {
		$tmpl = "<li style='background-image: url('" . $image . "');'>";


		if ( $title ) {
			$tmpl .= "<h1>" . $title . "</h1>";
		}

		if ( $description ) {
			$tmpl .= "<p>" . $description . "</p>";
		}

		if ( $link && $label ) {
			$tmpl .= "<a class='btn' href='" . $link . " '>' . $label . '</a>";
		}

		$tmpl .= "</li>";

		return $tmpl;
	}

	/**
	 * Retrieve slides selected in the Wordpress Admin
	 *
	 * @return array
	 *
	 * @since 1.0.0
	 */
	private function get_slides() {
		$slides = array();

		$the_query = get_posts( array(
			'meta_key'   => 'x-slider-selected',
			'meta_value' => 1
		) );

		foreach ( $the_query as $post ) : setup_postdata( $post );
			$slides[] = array(
				'title'       => get_the_title(),
				'description' => get_the_excerpt(),
				'image'       => get_post_meta( get_the_ID(), 'slider-src', true ),
				'link'        => get_the_permalink(),
				'label'       => 'See'
			);
		endforeach;

		wp_reset_postdata();

		return $slides;
	}
}
