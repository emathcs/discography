<?php

// List
function discography_release_list() {
    $controller = new AlbumController();
    $controller->list();
}

// Add
function discography_release_add() {
    $controller = new AlbumController();
    $controller->add();
}

// View
function discography_release_view() {
    $album_id = isset( $_GET['album'] ) ? $_GET['album'] : 0 ;
    $controller = new AlbumController();
    $controller->view( $album_id );
}

// Edit
function discography_release_edit() {
    $album_id = isset( $_GET['album'] ) ? $_GET['album'] : 0 ;
    $controller = new AlbumController();
    $controller->edit( $album_id );
}
