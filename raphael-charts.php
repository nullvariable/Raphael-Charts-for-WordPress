<?php
/*
Plugin Name: Raphaël Charts
Plugin URI:
Description: RaphaëlJS Chart drawing with gRaphaël for WordPress
Version: 0.0.1
Author: Nullvariable
Author URI: http://www.nullvariable.com
License: GPLv2 or later
Raphael included under MIT License: http://raphaeljs.com/license.html

THE SOFTWARE IS PROVIDED “AS IS”, WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED
TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

Useful links:
    https://github.com/michael/multiselect
    https://github.com/vanderlee/colorpicker
*/

//include classes
include 'classes/raphael_charts_display.class.php';
include 'classes/raphael_charts_settings.class.php';

/**
 * WordPress hooks
 */

add_action('admin_init', array('raphael_charts_settings','settings_init')); //initialize our settings
add_action('admin_menu', array('raphael_charts_settings', 'menu_item')); //add our menu items
add_action('admin_enqueue_scripts', array('raphael_charts_settings', 'admin_enqueue_scripts')); //enqueue admin js
add_action('wp_enqueue_scripts', array('raphael_charts_display', 'register_scripts'));
add_shortcode('raphaelcharts', array('raphael_charts_display', 'do_shortcode')); //add our shortcode
add_action('wp_footer', array('raphael_charts_display', 'print_footer_js'));

//because I'm that way
define('RAPHAEL_SETTINGS', 'raphael_settings');
define('RAPHAEL_FIELDS_CACHE', 'raphael_fields_cache');
define('RAPHAEL_BASE', trailingslashit( get_bloginfo('wpurl') ).PLUGINDIR.'/'. dirname( plugin_basename(__FILE__) ));


//doug's debug function
if (!function_exists('dpr')) {
    function dpr($input) {
        if (function_exists('krumo')) {
            krumo($input);
        } else {
            print '<br /><pre>';
            print_r($input);
            print '<br /></pre>';
        }
    }
}