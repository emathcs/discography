<?php

// List
function discography_release_list() {
    $controller = new ReleaseController();
    $controller->list();
}

// Add
function discography_release_add() {
    $controller = new ReleaseController();
    $controller->add();
}

// View
function discography_release_view() {
    $release_id = isset( $_GET['release'] ) ? $_GET['release'] : 0 ;
    $controller = new ReleaseController();
    $controller->view( $release_id );
}

// Edit
function discography_release_edit() {
    $release_id = isset( $_GET['release'] ) ? $_GET['release'] : 0 ;
    $controller = new ReleaseController();
    $controller->edit( $release_id );
}
