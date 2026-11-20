<?php

// Add
function discography_button_add() {
    $controller = new ButtonController();
    $controller->add();
}

// View
function discography_button_view() {
    $button_id = isset( $_GET['button'] ) ? $_GET['button'] : 0 ;
    $controller = new ButtonController();
    $controller->view( $button_id );
}

// Edit
function discography_button_edit() {
    $button_id = isset( $_GET['button'] ) ? $_GET['button'] : 0 ;
    $controller = new ButtonController();
    $controller->edit( $button_id );
}
