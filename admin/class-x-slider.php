<?php
if (!function_exists('boolval')) {
    function boolval($val)
    {
        return (bool)$val;
    }
}
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
     * @var
     * @since 1.1.0
     */
    private $crop;

    /**
     * @var
     * @since 1.1.0
     */
    private $options;

    /**
     * Initializes the plugin by defining the properties.
     *
     * @since 1.0.0
     */
    public function __construct()
    {

        $this->name = 'x-slider';
        $this->version = '1.3.1';
        $this->thumbnail_slug = 'x_slider_full';
        $this->thumbnail_name = 'X-Slider Full';

        $this->options = get_option('x_slider_layout_options');

        $this->thumbnail_width = !empty($this->options['width']) ? intval($this->options['width']) : 9999;
        $this->thumbnail_height = !empty($this->options['height']) ? intval($this->options['height']) : 450;

        $this->crop = !empty($this->options['crop']) ? boolval($this->options['crop']) : true;
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

        add_action('admin_menu', array($this, 'x_slider_plugin_menu'));

        add_action('admin_init', array($this, 'x_slider_initialize_tips'));
        add_action('admin_init', array($this, 'x_slider_initialize_layout_options'));
        add_action('admin_init', array($this, 'x_slider_initialize_display_options'));
        add_action('admin_init', array($this, 'x_slider_initialize_upload_options'));

        add_shortcode('x-slider', array($this, 'x_slider_shortcode'));
    }

    /**
     * X-Slider shortcode
     *
     * @since 1.2.0
     */
    public function x_slider_shortcode()
    {
        $xSlider = new X_Slider_Client();
        return $xSlider->run();
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
        wp_register_script(
            $this->name,
            plugin_dir_url(__FILE__) . '../client/js/x-slider.min.js',
            array('jquery'),
            '1.0.1',
            true);

        wp_enqueue_script($this->name);
    }


    /**
     * Register client side styles
     *
     * @since 1.0.0
     */
    public function enqueue_client_styles()
    {
        wp_enqueue_style(
            $this->name,
            plugin_dir_url(__FILE__) . '../client/css/x-slider.min.css');
    }


    /**
     * Sanitized and saves the post slider src meta data specific with this post.
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

            if (empty($_REQUEST['slider-src'])) {
                update_post_meta($post_id, 'x-slider-selected', 0);
            } else {
                update_post_meta($post_id, 'x-slider-selected', $_REQUEST['x-slider-selected']);
            }

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
            $this->crop);
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


    /**
     * Register the plugin menu link
     *
     * @since 1.0.0
     */
    public function x_slider_plugin_menu()
    {

        add_menu_page(
            'X-Slider Settings',
            'X-Slider Settings',
            'administrator',
            'x_slider_plugin_options',
            array(
                $this,
                'x_slider_plugin_display'
            ),
            plugin_dir_url(__FILE__) . 'img/icon.png'
        );

    }


    /**
     * Settings page container
     *
     * @since 1.0.0
     */
    public function x_slider_plugin_display()
    {
        ?>
        <div class="wrap">
            <h2>X-Slider Settings</h2>
            <?php settings_errors(); ?>

            <form method="post" action="options.php">
                <?php settings_fields('x_slider_layout_options'); ?>
                <?php do_settings_sections('x_slider_layout_options'); ?>
                <?php submit_button(); ?>
            </form>

        </div>
        <?php
    }


    /**
     * Layout section helper text
     *
     * @since 1.0.0
     */
    public function x_slider_layout_options_callback()
    {
        echo '<p>Select which features of slider you wish to display.</p>';
    }


    /**
     * Display section helper text
     *
     * @since 1.0.0
     */
    public function x_slider_display_options_callback()
    {
        echo '<p>Select which information you want to show on each slide.</p>';
    }


    /**
     * Display section helper text
     *
     * @since 1.1.0
     */
    public function x_slider_upload_options_callback()
    {
        echo '<p>Select options that will be applied on the slides upload</p>';
    }

    /**
     * Display tips helper
     *
     * @since 1.2.0
     */
    public function x_slider_tips_callback()
    {
        echo '<p>To show the slider you just need to call the <code>x_slider()</code> function in any place of your theme.</p>';
        echo '<p>You can show the slider inside any post or page as well using the <code>[x-slider]</code> shortcode</p>';
        echo '<hr/>';
    }

    /**
     * Tips section
     *
     * @since 1.2.0
     */
    public function x_slider_initialize_tips()
    {
        add_settings_section(
            'tips_section',
            'Getting Started',
            array($this, 'x_slider_tips_callback'),
            'x_slider_layout_options'
        );
    }

    /**
     * Register the upload options for the settings page
     *
     * @since 1.1.0
     */
    public function x_slider_initialize_upload_options()
    {

        if (false == get_option('x_slider_layout_options')) {
            add_option('x_slider_layout_options');
        }

        add_settings_section(
            'upload_settings_section',
            'Slider Upload Options',
            array($this, 'x_slider_upload_options_callback'),
            'x_slider_layout_options'
        );

        add_settings_field(
            'width',
            'Width',
            array($this, 'x_slider_change_width_callback'),
            'x_slider_layout_options',
            'upload_settings_section',
            array(
                'The width for each slide. Set 9999 if will want unlimited width.'
            )
        );

        add_settings_field(
            'height',
            'Height',
            array($this, 'x_slider_change_height_callback'),
            'x_slider_layout_options',
            'upload_settings_section',
            array(
                'The height for each slide. Set 9999 if will want unlimited height.'
            )
        );

        add_settings_field(
            'crop',
            'Crop image',
            array($this, 'x_slider_crop_callback'),
            'x_slider_layout_options',
            'upload_settings_section',
            array(
                'Activate this if you want to crop the image to fit the width and height selected.'
            )
        );
    }


    /**
     * Register the display options for the settings page
     *
     * @since 1.0.0
     */
    public function x_slider_initialize_display_options()
    {

        if (false == get_option('x_slider_layout_options')) {
            add_option('x_slider_layout_options');
        }

        add_settings_section(
            'display_settings_section',
            'Slider Display Options',
            array($this, 'x_slider_display_options_callback'),
            'x_slider_layout_options'
        );


        add_settings_field(
            'show_title',
            'Display the post title',
            array($this, 'x_slider_show_title_callback'),
            'x_slider_layout_options',
            'display_settings_section',
            array(
                'Activate this setting to display the post title on each slide.'
            )
        );

        add_settings_field(
            'show_excerpt',
            'Display the post excerpt',
            array($this, 'x_slider_show_excerpt_callback'),
            'x_slider_layout_options',
            'display_settings_section',
            array(
                'Activate this setting to display the post excerpt on each slide.'
            )
        );

        add_settings_field(
            'button_label',
            'Button label',
            array($this, 'x_slider_change_button_label_callback'),
            'x_slider_layout_options',
            'display_settings_section',
            array(
                'The label of the button.'
            )
        );

    }

    /**
     * Register the layout options for the settings page
     *
     * @since 1.0.0
     */
    public function x_slider_initialize_layout_options()
    {
        if (false == get_option('x_slider_layout_options')) {
            add_option('x_slider_layout_options');
        }

        add_settings_section(
            'layout_settings_section',
            'Design Options',
            array($this, 'x_slider_layout_options_callback'),
            'x_slider_layout_options'
        );

        add_settings_field(
            'show_bullets',
            'Bullets',
            array($this, 'x_slider_toggle_bullets_callback'),
            'x_slider_layout_options',
            'layout_settings_section',
            array(
                'Check this box if you want to display the bullets for your slider.'
            )
        );

        add_settings_field(
            'timeout',
            'Timeout',
            array($this, 'x_slider_change_timeout_callback'),
            'x_slider_layout_options',
            'layout_settings_section',
            array(
                'How many milliseconds between the slides transition?'
            )
        );

        register_setting(
            'x_slider_layout_options',
            'x_slider_layout_options'
        );

    }

    /**
     * Show title field
     *
     * @param $args
     *
     * @since 1.0.0
     */
    public function x_slider_show_title_callback($args)
    {
        $options = get_option('x_slider_layout_options');

        $html = '<input type="checkbox" id="show_title" name="x_slider_layout_options[show_title]" value="1" ' . checked(1, $options['show_title'], false) . '/>';
        $html .= '<label for="show_title"> ' . $args[0] . '</label>';

        echo $html;
    }

    /**
     * Show excerpt field
     *
     * @param $args
     *
     * @since 1.0.0
     */
    public function x_slider_show_excerpt_callback($args)
    {
        $options = get_option('x_slider_layout_options');

        $html = '<input type="checkbox" id="show_excerpt" name="x_slider_layout_options[show_excerpt]" value="1" ' . checked(1, $options['show_excerpt'], false) . '/>';
        $html .= '<label for="show_excerpt"> ' . $args[0] . '</label>';

        echo $html;
    }

    /**
     * Show bullets field
     *
     * @param $args
     *
     * @since 1.0.0
     */
    public function x_slider_toggle_bullets_callback($args)
    {
        $options = get_option('x_slider_layout_options');

        $html = '<input type="checkbox" id="show_bullets" name="x_slider_layout_options[show_bullets]" value="1" ' . checked(1, $options['show_bullets'], false) . '/>';
        $html .= '<label for="show_bullets"> ' . $args[0] . '</label>';

        echo $html;
    }


    /**
     * Button label field
     *
     * @param $args
     *
     * @since 1.0.0
     */
    public function x_slider_change_button_label_callback($args)
    {
        $defaults = array(
            'button_label' => 'Read more'
        );
        $options = wp_parse_args(get_option('x_slider_layout_options'), $defaults);

        $html = '<input type="text" id="button_label" name="x_slider_layout_options[button_label]" value="' . sanitize_text_field($options['button_label']) . '" />';
        $html .= '<label for="button_label"> ' . $args[0] . '</label>';

        echo $html;
    }

    /**
     * Width field
     *
     * @param $args
     *
     * @since 1.1.0
     */
    public function x_slider_change_width_callback($args)
    {
        $defaults = array(
            'width' => '9999'
        );
        $options = wp_parse_args(get_option('x_slider_layout_options'), $defaults);

        $html = '<input type="text" id="width" name="x_slider_layout_options[width]" value="' . sanitize_text_field($options['width']) . '" />';
        $html .= '<label for="width"> ' . $args[0] . '</label>';

        echo $html;
    }

    /**
     * Height field
     *
     * @param $args
     *
     * @since 1.1.0
     */
    public function x_slider_change_height_callback($args)
    {
        $defaults = array(
            'height' => '450'
        );
        $options = wp_parse_args(get_option('x_slider_layout_options'), $defaults);

        $html = '<input type="text" id="height" name="x_slider_layout_options[height]" value="' . sanitize_text_field($options['height']) . '" />';
        $html .= '<label for="height"> ' . $args[0] . '</label>';

        echo $html;
    }

    /**
     * Crop field
     *
     * @param $args
     *
     * @since 1.1.0
     */
    public function x_slider_crop_callback($args)
    {
        $options = get_option('x_slider_layout_options');

        $html = '<input type="checkbox" id="crop" name="x_slider_layout_options[crop]" value="1" ' . checked(1, $options['crop'], false) . '/>';
        $html .= '<label for="crop"> ' . $args[0] . '</label>';

        echo $html;
    }

    /**
     * Timeout field
     *
     * @param $args
     *
     * @since 1.0.0
     */
    public function x_slider_change_timeout_callback($args)
    {
        $defaults = array(
            'timeout' => '5000'
        );
        $options = wp_parse_args(get_option('x_slider_layout_options'), $defaults);

        $html = '<input type="text" id="timeout" name="x_slider_layout_options[timeout]" value="' . sanitize_text_field($options['timeout']) . '" />';
        $html .= '<label for="timeout"> ' . $args[0] . '</label>';

        echo $html;
    }
}
