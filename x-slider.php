<?php
/**
 * X-Slider
 *
 * Simple and lightweight slider for Wordpress
 *
 * @package   X_Slider
 * @author    Hugo Dias <hugooodias@gmail.com>
 * @license   GPL-2.0+
 * @link      https://github.com/hugodias/x-slider
 * @copyright 2015 Hugo Dias
 *
 * @wordpress-plugin
 * Plugin Name: X-Slider
 * Plugin URI:  https://github.com/hugodias/x-slider
 * Description: Simple and lightweight slider for Wordpress
 * Version:     1.4.2
 * Author:      Hugo Dias
 * Author URI:  http://github.com/hugodias
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if (!defined('WPINC')) {
    die;
}

/**
 * Includes the core plugin class for executing the plugin.
 */
require_once(plugin_dir_path(__FILE__) . 'admin/class-x-slider.php');
require_once(plugin_dir_path(__FILE__) . 'client/class-x-slider-client.php');

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_x_slider()
{
    $plugin = new X_Slider();
    $plugin->run();
}

run_x_slider();


/**
 * Render the slider
 *
 * The slider will be rendered where you put this function
 *
 * None of the fields are required
 *
 * @param null $featured_image
 *
 * @since 1.0.0
 */
function x_slider($featured_image = null)
{
    $xSlider = new X_Slider_Client($featured_image);
    echo $xSlider->run();
}
