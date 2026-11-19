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
function albums_admin_menu() {
    /**
     * Albums
     */

    $label = 'Release';
    $module = 'release';
    // List
    add_menu_page(
        'Discography',                      // Page title
        'Discography',                      // Menu title
        'manage_options',                   // Required user capability
        'discography-release',           // URL slug
        'discography_release_list', // Function that outputs the HTML page
        'dashicons-format-gallery',         // Left menu icon
        '81'                                // The left menu link appears after the Settings link
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
    $total = count($module) - 1;
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

    /**
     * Buttons
     */

    // Add
    add_submenu_page(
        '',                     // Parent menu slug
        'Buttons',              // Page title
        '',                     // Menu title
        'manage_options',       // Capability
        'buttons-add-settings', // Menu slug
        'buttons_add_html'      // Callback function
    );

    // View
    add_submenu_page(
        '',                      // Parent menu slug
        'Buttons',               // Page title
        '',                      // Menu title
        'manage_options',        // Capability
        'buttons-view-settings', // Menu slug
        'buttons_view_html'      // Callback function
    );

    // Edit
    add_submenu_page(
        '',                      // Parent menu slug
        'Buttons',               // Page title
        '',                      // Menu title
        'manage_options',        // Capability
        'buttons-edit-settings', // Menu slug
        'buttons_edit_html'      // Callback function
    );
}
add_action( 'admin_menu', 'albums_admin_menu' );

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
function albums_admin_enqueue_scripts( $hook ) {
    $module = 'release';
    $sections = [
        // Album
        'toplevel_page_discography-' . $module        => true,
        'admin_page_discography-' . $module . '-add'  => true,
        'admin_page_discography-' . $module . '-edit' => true,
        'admin_page_discography-' . $module . '-view' => true,
        // Song
        'admin_page_songs-add-settings'  => true,
        'admin_page_songs-view-settings' => true,
        'admin_page_songs-edit-settings' => true,
        // Button
        'admin_page_buttons-add-settings'  => true,
        'admin_page_buttons-view-settings' => true,
        'admin_page_buttons-edit-settings' => true,
    ];
    // Load scripts only on your specific plugin settings page
    if ( ! ( isset( $sections[$hook] ) && $sections[$hook] ) ) {
        return;
    }
    // Call the core WordPress media files
    wp_enqueue_media();
    // Loading the assets
    wp_enqueue_script('albums-media-library-js', plugins_url('/assets/js/admin-gallery.js', __FILE__), false , DISCOGRAPHY_VERSION, true);
    wp_enqueue_style( 'albums_bootstrap_min_css', plugins_url('/assets/css/bootstrap.min.css', __FILE__), array(), DISCOGRAPHY_VERSION );
    wp_enqueue_style( 'albums_admin_css', plugins_url('/assets/css/admin.css', __FILE__), array(), DISCOGRAPHY_VERSION );
    wp_enqueue_script('albums-bootstrap-bundle-min-js', plugins_url('/assets/js/bootstrap.bundle.min.js', __FILE__), false , DISCOGRAPHY_VERSION, true);
}
add_action('admin_enqueue_scripts', 'albums_admin_enqueue_scripts');

/**
 * Shortcut section
 */
require_once plugin_dir_path( __FILE__ ) . 'shortcut.php';

/**
 * Redirection function
 */
function albums_redirections() {
    // Albums add in view page
    $page = isset( $_GET['page'] ) ? $_GET['page'] : null;
    if ( 'albums-view-settings' === $page ) {
        $model_id = isset( $_GET['album'] ) ? $_GET['album'] : 0 ;
        if ( 0 === $model_id && 0 === count($_POST) ) {
            wp_safe_redirect( admin_url( 'admin.php?page=albums-settings' ) );
            exit;
        }
    }

    // Buttons add in view page
    if ( 'buttons-view-settings' === $page ) {
        $model    = isset( $_GET['model'] ) ? $_GET['model'] : 0 ;
        $model_id = isset( $_GET['id'] ) ? $_GET['id'] : 0 ;
        // Albums
        if ('album' === $model) {
            if ( 0 === count($_POST) ) {
                wp_safe_redirect( admin_url( 'admin.php?page=albums-view-settings&album=' . $model_id) );
                exit;
            }
        }
        // Song
        if ('song' === $model) {
            if ( 0 === count($_POST) ) {
                wp_safe_redirect( admin_url( 'admin.php?page=songs-view-settings&song=' . $model_id) );
                exit;
            }
        }
    }

    // Albums add in view page
    if ( 'songs-view-settings' === $page ) {
        $model_id = isset( $_GET['album'] ) ? $_GET['album'] : 0 ;
        if ( $model_id && 0 === count($_POST) ) {
            wp_safe_redirect( admin_url( 'admin.php?page=albums-view-settings&album=' . $model_id) );
            exit;
        }
    }
}
add_action( 'admin_init', 'albums_redirections' );
