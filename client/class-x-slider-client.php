<?php
if (!function_exists('boolval')) {
    function boolval($val)
    {
        return (bool)$val;
    }
}
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
class X_Slider_Client
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
     * Plugin options
     *
     * @since 1.0.0
     * @access protected
     * @var array
     */
    protected $options;


    /**
     * Timeout option
     *
     * @var string
     */
    protected $timeout;

    /**
     * Button label option
     *
     * @var string
     */
    protected $button_label;


    /**
     * Initializes the plugin by defining the properties.
     *
     * @since 1.0.0
     */
    public function __construct($featured_image_name = null)
    {

        $this->name = 'x-slider';
        $this->version = '1.2.0';
        $this->featured_image_name = $featured_image_name ? $featured_image_name : 'x_slider_full';

        $this->options = get_option('x_slider_layout_options');

        $this->bullets = !empty($this->options['show_bullets']);
        $this->timeout = !empty($this->options['timeout']) ? $this->options['timeout'] : '5000';

        $this->button_label = !empty($this->options['button_label']) ? $this->options['button_label'] : 'Read more';
    }

    /**
     * Defines the hooks that will register and enqueue the JavaScriot
     * and the meta box that will render the option.
     *
     * @since 1.0.0
     */
    public function run()
    {
        $slides = $this->get_slides();

        if ($slides) {
            $slider = '<div class="x-slider" ' . $this->mount_attributes() . '>';

            $slider .= '<ul>';

            foreach ($slides as $slide) {
                $slider .= $this->loopAndRender($slide['image'], $slide['title'], $slide['description'], $slide['link'], $slide['label']);
            }

            $slider .= '</ul></div>';

            return $slider;
        } else {
            return "No sliders avaliable";
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
    private function loopAndRender($image, $title = null, $description = null, $link = null, $label = null)
    {
        $tmpl = '<li id="x-slider-' . mt_rand(1000, 9999) . '"  data-x-slider-image="' . esc_url_raw($image) . '">';

        $tmpl .= '<div class="x-slider__info" itemscope="" itemtype="http://schema.org/BlogPosting">';

        if (boolval($this->options['show_title'])) {
            $tmpl .= '<h2 itemprop="name headline">' . $title . '</h2>';
        }

        if (boolval($this->options['show_excerpt'])) {
            $tmpl .= '<p itemprop="description">' . $description . '</p>';
        }

        if ($link && $label) {
            $tmpl .= '<a class="btn" href="' . $link . '" itemprop="url">' . $label . '</a>';
        }

        $tmpl .= '</div>';

        $tmpl .= '</li>';

        return $tmpl;
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

        $args = array(
            'meta_key' => 'x-slider-selected',
            'meta_value' => 1
        );

        $q = new WP_Query($args);

        if ($q->have_posts()) :


            while ($q->have_posts()) : $q->the_post();
                $slides[] = array(
                    'title' => get_the_title($q->post->ID),
                    'description' => wp_trim_words($q->post->post_content),
                    'image' => get_post_meta($q->post->ID, 'slider-src', true),
                    'link' => get_the_permalink($q->post->ID),
                    'label' => $this->button_label
                );
            endwhile;

            wp_reset_postdata();

        else:
            # Todo:
        endif;

        return $slides;
    }


    /**
     * Render slide attriutes via html data
     *
     * @return string
     *
     * @since 1.0.0
     */
    private function mount_attributes()
    {
        $attr = '';

        $attr .= 'data-x-slider-timeout = "' . $this->timeout . '" ';
        $attr .= 'data-x-slider-bullets = "' . $this->bullets . '" ';

        return $attr;
    }


    /**
     * Fix get the excerpt wp function
     *
     * @param $post_id
     * @return mixed
     */
    public function robins_get_the_excerpt($post_id)
    {
        global $post;
        $save_post = $post;
        $post = get_post($post_id);
        $output = get_the_excerpt();
        $post = $save_post;
        return $output;
    }
}
