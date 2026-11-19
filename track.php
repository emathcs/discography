<?php

// Add
function discography_track_add() {
    // Get the album id
    $album_id = isset( $_GET['album'] ) ? $_GET['album'] : 0 ;
    $controller = new SongController();
    $controller->add($album_id);
}

// View
function discography_track_view() {
    $song_id = isset( $_GET['song'] ) ? $_GET['song'] : 0 ;
    $controller = new SongController();
    $controller->view( $song_id );
}

// Edit
function discography_track_edit() {
    $song_id = isset( $_GET['song'] ) ? $_GET['song'] : 0 ;
    $controller = new SongController();
    $controller->edit( $song_id );
}
