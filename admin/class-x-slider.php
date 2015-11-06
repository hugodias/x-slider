<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://github.com/hugodias
 * @since      1.0.0
 *
 * @package    X_Slider
 * @subpackage X_Slider/admin
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, the meta box functionality
 * and the JavaScript for loading the Media Uploader.
 *
 * @package    X_Slider
 * @subpackage X_Slider/admin
 * @author     Hugodias <hugooodias@gmail.com>
 */
class X_Slider
{

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
     * @var string
     */
    private $thumbnail_slug;

    /**
     * @var string
     */
    private $thumbnail_name;

    /**
     * @var
     */
    private $thumbnail_height;

    /**
     * @var
     */
    private $thumbnail_width;

    /**
     * Initializes the plugin by defining the properties.
     *
     * @since 1.0.0
     */
    public function __construct()
    {

        $this->name = 'x-slider';
        $this->version = '1.0.0';
        $this->thumbnail_slug = 'x_slider_full';
        $this->thumbnail_name = 'X-Slider Full';
        $this->thumbnail_width = 9999;
        $this->thumbnail_height = 350;

    }

    /**
     * Defines the hooks that will register and enqueue the JavaScriot
     * and the meta box that will render the option.
     *
     * @since 1.0.0
     */
    public function run()
    {

        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));

        add_action('wp_enqueue_scripts', array($this, 'enqueue_client_styles'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_client_scripts'));

        add_action('add_meta_boxes', array($this, 'add_meta_box'));
        add_action('save_post', array($this, 'save_post'));

        add_action('init', array($this, 'add_thumbnail_size_image'));

        add_filter('image_size_names_choose', array($this, 'add_custom_sizes'));


        add_action('admin_menu', array($this, 'sandbox_example_theme_menu'));
        add_action('admin_init', array($this, 'sandbox_initialize_theme_options'));
    }


    public function settings_page()
    {
        include_once(dirname(__FILE__) . '/views/settings.php');
    }

    /**
     * Renders the meta box on the post and pages.
     *
     * @since 1.0.0
     */
    public function add_meta_box()
    {

        $screens = array('post', 'page');

        foreach ($screens as $screen) {

            add_meta_box(
                $this->name,
                'Slider image',
                array($this, 'display_x_slider_image'),
                $screen,
                'side'
            );

        }

    }

    /**
     * Registers the JavaScript for handling the media uploader.
     *
     * @since 1.0.0
     */
    public function enqueue_admin_scripts()
    {

        wp_enqueue_media();

        wp_enqueue_script(
            $this->name,
            plugin_dir_url(__FILE__) . 'js/admin.js',
            array('jquery'),
            $this->version,
            'all'
        );

    }

    /**
     * Registers the stylesheets for handling the meta box
     *
     * @since 1.0.0
     */
    public function enqueue_admin_styles()
    {

        wp_enqueue_style(
            $this->name,
            plugin_dir_url(__FILE__) . 'css/admin.css',
            array()
        );

    }

    /**
     * Registers client side scripts
     *
     * @since 1.0.0
     */
    public function enqueue_client_scripts()
    {

        wp_enqueue_script(
            'unslider',
            plugin_dir_url(__FILE__) . '../client/js/unslider.min.js',
            array('jquery'),
            '1.0',
            'all'
        );

        wp_enqueue_script(
            $this->name,
            plugin_dir_url(__FILE__) . '../client/js/x-slider.js',
            array('jquery'),
            '1.0',
            'all'
        );

    }

    /**
     * Register client side styles
     */
    public function enqueue_client_styles()
    {
        wp_enqueue_style(
            $this->name,
            plugin_dir_url(__FILE__) . '../client/css/app.css');
    }

    /**
     * Sanitized and saves the post featured footer image meta data specific with this post.
     *
     * @param    int $post_id The ID of the post with which we're currently working.
     *
     * @since    1.0.0
     */
    public function save_post($post_id)
    {

        if (isset($_REQUEST['slider-src'])) {
            update_post_meta($post_id, 'slider-src', sanitize_text_field($_REQUEST['slider-src']));
        }

        if (isset($_REQUEST['slider-title'])) {
            update_post_meta($post_id, 'slider-title', sanitize_text_field($_REQUEST['slider-title']));
        }

        if (isset($_REQUEST['x-slider-selected'])) {
            update_post_meta($post_id, 'x-slider-selected', $_REQUEST['x-slider-selected']);
        } else {
            update_post_meta($post_id, 'x-slider-selected', 0);
        }

    }


    /**
     * Set a default image size for the slides
     *
     * @since 1.0.0
     */
    public function add_thumbnail_size_image()
    {

        add_theme_support('post-thumbnails');

        add_image_size(
            $this->thumbnail_slug,
            $this->thumbnail_width,
            $this->thumbnail_height,
            true);
    }


    /**
     * Add our custom image size to the media uploader as an option
     *
     * @param $imageSizes
     *
     * @return array
     *
     * @since 1.0.0
     */
    public function add_custom_sizes($imageSizes)
    {
        $my_sizes = array(
            $this->thumbnail_slug => $this->thumbnail_name
        );

        return array_merge($imageSizes, $my_sizes);
    }


    /**
     * Retrieve slides selected in the Wordpress Admin
     *
     * @return array
     *
     * @since 1.0.0
     */
    private function get_slides()
    {
        $slides = array();

        $the_query = get_posts(array(
            'meta_key' => 'x-slider-selected',
            'meta_value' => 1
        ));

        foreach ($the_query as $post) : setup_postdata($post);
            $slides[] = get_post_meta(get_the_ID(), 'slider-src', true);
        endforeach;

        wp_reset_postdata();

        return $slides;
    }


    /**
     * Renders the view that displays the contents for the meta box that for triggering
     * the meta box.
     *
     * @param    WP_Post $post The post object
     *
     * @since    1.0.0
     */
    public function display_x_slider_image($post)
    {
        include_once(dirname(__FILE__) . '/views/admin.php');
    }


    public function sandbox_example_theme_menu()
    {

        add_theme_page(
            'Sandbox Theme',            // The title to be displayed in the browser window for this page.
            'Sandbox Theme',            // The text to be displayed for this menu item
            'administrator',            // Which type of users can see this menu item
            'sandbox_theme_options',    // The unique ID - that is, the slug - for this menu item
            array($this, 'sandbox_theme_display')     // The name of the function to call when rendering this menu's page
        );

    } // end sandbox_example_theme_menu


    /**
     * Renders a simple page to display for the theme menu defined above.
     */
    public function sandbox_theme_display()
    {
        ?>
        <!-- Create a header in the default WordPress 'wrap' container -->
        <div class="wrap">

            <div id="icon-themes" class="icon32"></div>
            <h2>X-Slider Settings</h2>
            <?php settings_errors(); ?>

            <form method="post" action="options.php">
                <?php settings_fields('x_slider_layout_options'); ?>
                <?php do_settings_sections('x_slider_layout_options'); ?>
                <?php submit_button(); ?>
            </form>

        </div><!-- /.wrap -->
        <?php
    } // end sandbox_theme_display

    public function sandbox_initialize_theme_options()
    {

        // If the theme options don't exist, create them.
        if (false == get_option('x_slider_layout_options')) {
            add_option('x_slider_layout_options');
        } // end if

        // First, we register a section. This is necessary since all future options must belong to a
        add_settings_section(
            'layout_settings_section',         // ID used to identify this section and with which to register options
            'Design Options',                  // Title to be displayed on the administration page
            array($this, 'x_slider_layout_options_callback'), // Callback used to render the description of the section
            'x_slider_layout_options'     // Page on which to add this section of options
        );

        // Next, we'll introduce the fields for toggling the visibility of content elements.
        add_settings_field(
            'show_bullets',                      // ID used to identify the field throughout the theme
            'Bullets',                           // The label to the left of the option interface element
            array($this, 'x_slider_toggle_bullets_callback'),   // The name of the function responsible for rendering the option interface
            'x_slider_layout_options',    // The page on which this option will be displayed
            'layout_settings_section',         // The name of the section to which this field belongs
            array(                              // The array of arguments to pass to the callback. In this case, just a description.
                'Activate this setting to display the header.'
            )
        );

        add_settings_field(
            'show_arrows',
            'Arrows',
            array($this, 'x_slider_toggle_arrows_callback'),
            'x_slider_layout_options',
            'layout_settings_section',
            array(
                'Activate this setting to display the content.'
            )
        );


        // Finally, we register the fields with WordPress
        register_setting(
            'x_slider_layout_options',
            'x_slider_layout_options'
        );

    } // end sandbox_initialize_theme_options


    public function x_slider_layout_options_callback()
    {
        echo '<p>Select which features of slider you wish to display.</p>';
    } // end x_slider_layout_options_callback

    public function x_slider_toggle_bullets_callback($args)
    {
        // First, we read the options collection
        $options = get_option('x_slider_layout_options');

        // Next, we update the name attribute to access this element's ID in the context of the display options array
        // We also access the show_bullets element of the options collection in the call to the checked() helper function
        $html = '<input type="checkbox" id="show_bullets" name="x_slider_layout_options[show_bullets]" value="1" ' . checked(1, $options['show_bullets'], false) . '/>';

        // Here, we'll take the first argument of the array and add it to a label next to the checkbox
        $html .= '<label for="show_bullets"> ' . $args[0] . '</label>';

        echo $html;

    } // end x_slider_toggle_bullets_callback

    public function x_slider_toggle_arrows_callback($args)
    {

        $options = get_option('x_slider_layout_options');

        $html = '<input type="checkbox" id="show_arrows" name="x_slider_layout_options[show_arrows]" value="1" ' . checked(1, $options['show_arrows'], false) . '/>';
        $html .= '<label for="show_arrows"> ' . $args[0] . '</label>';

        echo $html;

    } // end x_slider_toggle_arrows_callback


}

