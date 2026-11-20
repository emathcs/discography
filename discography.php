<?php
/**
 * Plugin Name:       Discography
 * Plugin URI:        https://www.github.com/emathcs/discography
 * Description:       Plugin to manage albums, singles, songs and streaming platforms.
 * Author:            Carlos E. Alvarez
 * Author URI:        https://www.emathcs.com
 * Version:           1.0.0
 * Tested up to:      7.0
 * Requires at least: 6.3
 * Requires PHP:      8.2.0
 * License:           GNU General Public License v3.0 or later
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 * Powered by:        https://www.aomath.com
 */

// Prevent direct file access for security
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// Plugin version
if ( ! defined( 'DISCOGRAPHY_VERSION' ) ) {
    // Replace the version number of the theme on each release.
    define( 'DISCOGRAPHY_VERSION', '1.0.0' );
}

// Plugin URL
define( 'DISCOGRAPHY_URL', plugin_dir_url( __FILE__ ) );

/**
 * Create the tables into the database
 */
global $jal_db_version;
$jal_db_version = '1.0';

require_once plugin_dir_path( __FILE__ ) . 'jal-install.php';
register_activation_hook( __FILE__, 'jal_install' );

/**
 * Register the plugin into the admin section
 */
function discography_admin_menu() {
    // Release ist
    add_menu_page(
        'Discography',              // Page title
        'Discography',              // Menu title
        'manage_options',           // Required user capability
        'discography-release',      // URL slug
        'discography_release_list', // Function that outputs the HTML page
        'dashicons-format-gallery', // Left menu icon
        '81'                        // The left menu link appears after the Settings link
    );

    $module =[
        [
            'name'  => 'release',
            'label' => 'Release',
        ],
        [
            'name'  => 'track',
            'label' => 'Track',
        ],
        [
            'name'  => 'button',
            'label' => 'Button',
        ],
    ];
    $total = count($module);
    for ($i = 0; $i < $total; $i++) {
        // Add
        add_submenu_page(
            '',                                            // Parent menu slug
            'Add ' . $module[$i]['label'],                 // Page title
            '',                                            // Menu title
            'manage_options',                              // Capability
            'discography-' . $module[$i]['name'] . '-add', // Menu slug
            'discography_' . $module[$i]['name'] . '_add'  // Callback function
        );

        // View
        add_submenu_page(
            '',                                             // Parent menu slug
            'View ' . $module[$i]['label'],                 // Page title
            '',                                             // Menu title
            'manage_options',                               // Capability
            'discography-' . $module[$i]['name'] . '-view', // Menu slug
            'discography_' . $module[$i]['name'] . '_view'  // Callback function
        );

        // Edit
        add_submenu_page(
            '',                                             // Parent menu slug
            'Edit ' . $module[$i]['label'],                 // Page title
            '',                                             // Menu title
            'manage_options',                               // Capability
            'discography-' . $module[$i]['name'] . '-edit', // Menu slug
            'discography_' . $module[$i]['name'] . '_edit'  // Callback function
        );
    }
}
add_action( 'admin_menu', 'discography_admin_menu' );

/**
 * Load HTML elements library
 */
require_once plugin_dir_path( __FILE__ ) . 'html-elements/Load.php';

/**
 * Load the MVC
 */
require_once plugin_dir_path( __FILE__ ) . 'mvc/Load.php';

/**
 * Render function
 */
require_once plugin_dir_path( __FILE__ ) . 'release.php';
require_once plugin_dir_path( __FILE__ ) . 'track.php';
require_once plugin_dir_path( __FILE__ ) . 'button.php';

/**
 * Enqueue admin scripts and styles
 */
function discography_admin_enqueue_scripts( $hook ) {
    $module =[
        [
            'name'  => 'release',
        ],
        [
            'name'  => 'track',
        ],
        [
            'name'  => 'button',
        ],
    ];

    $total = count($module);
    $route['toplevel_page_discography-release'] = true;
    for ($i = 0; $i < $total; $i++) {
        $route['admin_page_discography-' . $module[$i]['name'] . '-add'] = true;
        $route['admin_page_discography-' . $module[$i]['name'] . '-view'] = true;
        $route['admin_page_discography-' . $module[$i]['name'] . '-edit'] = true;
    }

    // Load scripts only on your specific plugin settings page
    if ( ! ( isset( $route[$hook] ) && $route[$hook] ) ) {
        return;
    }
    // Call the core WordPress media files
    wp_enqueue_media();
    // Loading the assets
    wp_enqueue_style( 'discography-bootstrap_min_css', plugins_url('/assets/css/bootstrap.min.css', __FILE__), array(), DISCOGRAPHY_VERSION );
    wp_enqueue_style( 'discography-admin_css', plugins_url('/assets/css/admin.css', __FILE__), array(), DISCOGRAPHY_VERSION );
    wp_enqueue_script( 'discography-admin_gallery_js', plugins_url('/assets/js/admin-gallery.js', __FILE__), false , DISCOGRAPHY_VERSION, true);
    wp_enqueue_script( 'discography-bootstrap_bundle_min_js', plugins_url('/assets/js/bootstrap.bundle.min.js', __FILE__), false , DISCOGRAPHY_VERSION, true);
}
add_action('admin_enqueue_scripts', 'discography_admin_enqueue_scripts');

/**
 * Shortcut section
 */
require_once plugin_dir_path( __FILE__ ) . 'shortcut.php';

/**
 * Redirection function
 */
function discography_redirections() {
    // Rereleases add in view page
    $page = isset( $_GET['page'] ) ? $_GET['page'] : null;
    if ( 'discography-release-view-settings' === $page ) {
        $model_id = isset( $_GET['release'] ) ? $_GET['release'] : 0 ;
        if ( 0 === $model_id && 0 === count($_POST) ) {
            wp_safe_redirect( admin_url( 'admin.php?page=discography-release' ) );
            exit;
        }
    }

    // Buttons add in view page
    if ( 'discography-button-view' === $page ) {
        $model    = isset( $_GET['model'] ) ? $_GET['model'] : 0 ;
        $model_id = isset( $_GET['id'] ) ? $_GET['id'] : 0 ;
        // Release
        if ('release' === $model) {
            if ( 0 === count($_POST) ) {
                wp_safe_redirect( admin_url( 'admin.php?page=discography-release-view&release=' . $model_id) );
                exit;
            }
        }
        // Track
        if ('track' === $model) {
            if ( 0 === count($_POST) ) {
                wp_safe_redirect( admin_url( 'admin.php?page=discography-track-view-&track=' . $model_id) );
                exit;
            }
        }
    }

    // Release add in view page
    if ( 'discography-track-view' === $page ) {
        $model_id = isset( $_GET['release'] ) ? $_GET['release'] : 0 ;
        if ( $model_id && 0 === count($_POST) ) {
            wp_safe_redirect( admin_url( 'admin.php?page=discography-release-view&release=' . $model_id) );
            exit;
        }
    }
}
add_action( 'admin_init', 'discography_redirections' );
