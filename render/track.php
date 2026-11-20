<?php

// Add
function discography_track_add() {
    // Get the release id
    $release_id = isset( $_GET['release'] ) ? $_GET['release'] : 0 ;
    $controller = new TrackController();
    $controller->add($release_id);
}

// View
function discography_track_view() {
    $track_id = isset( $_GET['track'] ) ? $_GET['track'] : 0 ;
    $controller = new TrackController();
    $controller->view( $track_id );
}

// Edit
function discography_track_edit() {
    $track_id = isset( $_GET['track'] ) ? $_GET['track'] : 0 ;
    $controller = new TrackController();
    $controller->edit( $track_id );
}
