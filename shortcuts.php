<?php
/**
 *
 * This file contains the Shortcuts to display discography sections.
 *
 */

/**
 * Generate buttons
 *
 * @param Object  $button Button record Default null
 * @param Boolean $label  Display label Default true
 * @return String Platform button
 */
function discography_button( $button = null, $label = true ) {
    $local_content  = '';
    // Checking it the id is valid
    if ( $button ) {
        $platforms = [
            '1' => [
                'icon'  => 'apple-music-streaming',
                'label' => 'Apple Music'
            ],
            '2' => [
                'icon'  => 'spotify-streaming',
                'label' => 'Spotify'
            ],
            '3' => [
                'icon'  => 'amazon-streaming',
                'label' => 'Amazon Music'
            ]
        ];
        $local_content .= '<a href="' . $button->link . '" target="_blank" class="btn btn-link btn-platform link-secondary">';
        $local_content .= '    <i class="bi bi-' . $platforms[$button->platform_id]['icon']. '"></i>';
        $local_content .=      $label ? ' <span class="label">' . $platforms[$button->platform_id]['label'] . '</span>' : '';
        $local_content .= '</a> ';
    }
    return $local_content;
}

/**
 * Generate a column
 *
 * @param Array  $image_id Image ID  Default 0
 * @param String $post_id  Post ID   Default 0
 * @param String $class    CSS class Default 'discography-col-sm-6'
 * @return String Gallery item
 */
function discography_col( $image_id = null, $post_id = 0, $class = 'discography-col-md-6' ) {
    $local_content  = '';
    // Checking it the id is valid
    if ( $image_id ) {
        // Not a valid Post ID
        $url = '';
        // Check if the Post ID > 0
        if ( $post_id > 0 ) {
            // Generate the url
            $url = get_permalink( $post_id );
        }
        $local_content .= '<div class="discography-col discography-col-12 ' . $class . '" data-id="' . $image_id . '">';
        $local_content .=      strlen( $url ) ? '<a href="' . esc_url( $url ) . '">' : '';
        $local_content .= '        <img src="' . wp_get_attachment_image_url( $image_id , 'medium' ) . '" class="discography-gallery-image-item-img">';
        $local_content .=      strlen( $url ) ? '</a>' : '';
        $local_content .= '</div>';
    }
    return $local_content;
}

/**
 * Generate a track
 *
 * @param Integer $li List item         Default 0
 * @param Object  $track Track record     Default null
 * @param Boolean $label  Display label Default true
 * @return String Track list item
 */
function discography_track( $li = 0, $track = null ) {
    $local_content = '';
    if ( $track ) {
        global $wpdb;
        // Get the Buttons
        $id = $track->id;
        $table_name = $wpdb->prefix . 'buttons';
        $buttons = $wpdb->get_results(
            $wpdb->prepare( "SELECT * FROM $table_name WHERE track_id=%d", $id )
        );
        // Total
        $total = count( $buttons );
        // Generate the track and buttons
        $local_content .= '<div class="row discography-track">';
        $local_content .= '    <div class="col-7">';
        $local_content .= '        <p>' . ($li < 10 ? '&nbsp;' : '') . $li . '&nbsp;&nbsp;&nbsp;&nbsp;' . $track->name. '</p>';
        $local_content .= '    </div>';
        $local_content .= '    <div class="col-5 text-end">';
        for ( $i = 0; $i < $total; $i++ ) {
            // Generate the buttons
            $local_content .= discography_button( $buttons[$i] , false );
        }
        $local_content .= '    </div>';
        $local_content .= '</div>';
    }
    return $local_content;
}

/**
 *
 * Shortcuts
 *
 */

/**
 * The [discography] shortcode
 *
 * @param Array  $atts    Shortcode attributes Default empty
 * @param String $content Shortcode content    Default null
 * @param String $tag     Shortcode tag (name) Default empty
 * @return String Shortcode output.
 */
function discography( $atts = [], $content = null , $tag = '' ) {
    global $wpdb;
    $local_content = '';
    // Get the Discography
    $type = isset( $atts['type'] ) ? $atts['type'] : '';
    $table_name = $wpdb->prefix . 'releases';
    if ( 'albums' == $type || 'singles' == $type ) {
        $types = [
            'albums'  => 1,
            'singles' => 2
        ];
        $records = $wpdb->get_results(
            $wpdb->prepare( "SELECT * FROM {$table_name} WHERE status_id=1 AND type_id=%d", $types[$type])
        );
    }
    else {
        $records = $wpdb->get_results(
            $wpdb->prepare( "SELECT * FROM {$table_name} WHERE status_id=1")
        );
    }
    // Total
    $total = count( $records );
    if ( $total ) {
        // Get the parameters
        $skip  = isset( $atts['skip'] ) ? $atts['skip'] : '';
        $class = isset( $atts['class'] ) ? $atts['class'] : 'discography-col-md-6 discography-col-lg-4';
        // Start the gallery
        $local_content .= '<div class="discography-row">';
        for ( $i = 0; $i < $total; $i++ ) {
            // Post ID
            $post_id = $records[$i]->post_id;
            // Image ID
            $image_id = $records[$i]->id === $skip ? null : $records[$i]->image_id;
            // Generate an item
            $local_content .= discography_col( $image_id , $post_id, $class );
        }
        $local_content .= '</div>';
    }
    return $local_content . $content;
}

/**
 * The [discography_cover] shortcode
 *
 * @param Array  $atts    Shortcode attributes Default empty
 * @param String $content Shortcode content    Default null
 * @param String $tag     Shortcode tag (name) Default empty
 * @return String Shortcode output.
 */
function discography_cover( $atts = [], $content = null , $tag = '' ) {
    $local_content = '';
    $id = isset( $atts['id'] ) ? $atts['id'] : 0;
    if ( $id > 0 ) {
        global $wpdb;
        // Get a Release
        $table_name = $wpdb->prefix . 'releases';
        $record = $wpdb->get_row(
            $wpdb->prepare( "SELECT * FROM {$table_name} WHERE status_id=1 AND id=%d", $id )
        );
        if ( $record ) {
            // Get the Buttons
            $id = $record->id;
            $table_name = $wpdb->prefix . 'buttons';
            $buttons = $wpdb->get_results(
                $wpdb->prepare( "SELECT * FROM $table_name WHERE release_id=%d", $id )
            );
            // Get the parameters
            $class = isset( $atts['class'] ) ? $atts['class'] : '';
            // Total
            $total = count( $buttons );
            // Generate the gallery
            $local_content .= '<div class="discography-row">';
            $local_content .=      discography_col( $record->image_id, 0, $class );
            $local_content .= '    <div class="discography-col discography-col-12">';
            for ( $i = 0; $i < $total; $i++ ) {
                // Gererate the buttons
                $local_content .= discography_button( $buttons[$i] );
            }
            $local_content .= '    </div>';
            $local_content .= '</div>';
        }
    }
    return $local_content . $content;
}

/**
 * The [discography_tracklist] shortcode
 *
 * @param Array  $atts    Shortcode attributes Default empty
 * @param String $content Shortcode content    Default null
 * @param String $tag     Shortcode tag (name) Default empty
 * @return String Shortcode output.
 */
function discography_tracklist( $atts = [], $content = null , $tag = '' ) {
    $local_content = '';
    $id = isset( $atts['id'] ) ? $atts['id'] : 0;
    if ( $id > 0 ) {
        global $wpdb;
        // Track
        $table_name = $wpdb->prefix . 'tracks';
        $records = $wpdb->get_results(
            $wpdb->prepare( "SELECT * FROM {$table_name} WHERE release_id=%d", $id )
        );
        // Get the parameters
        $class = isset( $atts['class'] ) ? $atts['class'] : '';
        // Total
        $total = count( $records );
        // Generate the gallery
        for ( $i = 0; $i < $total; $i++ ) {
            $local_content .=  discography_track( $i + 1, $records[$i] );
        }
    }
    return $local_content . $content;
}

/**
 * The [discography_cover_tracklist] shortcode
 *
 * @param Array  $atts    Shortcode attributes Default empty
 * @param String $content Shortcode content    Default null
 * @param String $tag     Shortcode tag (name) Default empty
 * @return String Shortcode output.
 */
function discography_cover_tracklist( $atts = [], $content = null , $tag = '' ) {
    $local_content  = '';
    $id = isset( $atts['id'] ) ? $atts['id'] : 0;
    if ( $id > 0 ) {
        $container_class = isset( $atts['container_class'] ) ? $atts['container_class'] : '';
        $local_content .= '<div class="discography-row ' . $container_class . '">';
        $local_content .= '    <div class="col-12 col-lg-6">';
        $local_content .=          discography_cover( $atts, '', $tag );
        $local_content .= '    </div>';
        $local_content .= '    <div class="col-12 col-lg-6 discography-track-list">';
        $local_content .=          discography_tracklist( $atts, '', $tag );
        $local_content .= '    </div>';
        $local_content .= '</div>';
    }
    return $local_content . $content;
}

/**
 * Central location to create all shortcodes
 */
function discography_init() {
    add_shortcode( 'discography', 'discography' );
    add_shortcode( 'discography_cover', 'discography_cover' );
    add_shortcode( 'discography_tracklist', 'discography_tracklist' );
    add_shortcode( 'discography_cover_tracklist', 'discography_cover_tracklist' );
}
add_action( 'init', 'discography_init' );

/**
 * Enqueue scripts and styles.
 */
function discography_wp_enqueue_scripts() {
    wp_enqueue_style( 'discography-bootstrap_grid_css', plugins_url('/assets/css/bootstrap-grid.css', __FILE__), array(), '1.0.0' );
    wp_enqueue_style( 'discography-bootstrap_icons_min_css', plugins_url('/assets/css/bootstrap-icons.min.css', __FILE__), array(), '1.0.0' );
}
add_action( 'wp_enqueue_scripts', 'discography_wp_enqueue_scripts' );
