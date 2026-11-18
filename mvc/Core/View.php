<?php

class View {
    /**
     * Open panel
     *
     * @return String HTML code to start a panel
     */
    public function panelOpen($title = '') {
        $render  = '<div class="albums-wrap">';
        $render .= '    <div class="container albums-panel">';
        $render .= '        <div class="row">';
        $render .= '            <div class="col-xs">';
        $render .= '                <h1>' . esc_html($title) . '</h1>';
        $render .= '            </div>';
        $render .= '            <div class="col-xs">';
        return $render;
    }

    /**
     * Close panel
     *
     * @return String HTML code to close a panel
     */
    public function panelClose() {
        $render  = '            </div>';
        $render .= '        </div>';
        $render .= '    </div>';
        $render .= '</div>';
        return $render;
    }

    /**
     * Delete form
     *
     * @return String HTML code to generate the delete form
     */
    public function deleteForm($id = 0, $model = '') {
        $form  = new Form();
        $input = new Input();

        // Form
        $html  = $form->init(['action' => "", 'method' => 'post']);
        $html .= wp_nonce_field( 'delete_custom_form_action', 'custom_form_nonce', true, false );
        $html .= $input->setLabel('');
        $html .= $input->create([
            'type'  => 'hidden',
            'id'    => 'id-' . $id,
            'name'  => 'id',
            'class' => 'form-control',
            'value' => $id
        ]);
        $html .= $input->setLabel('');
        $html .= $input->create([
            'type'  => 'hidden',
            'id'    => $model . '-' . $id,
            'name'  => 'model',
            'class' => 'form-control',
            'value' => $model
        ]);
        $html .= $input->setLabel('');
        $html .= $input->create(['type' => 'submit', 'name' => 'delete', 'value' => 'Delete', 'class' => 'btn btn-sm btn-danger']);
        $html .= $form->end();

        return $html;
    }

    /**
     * Delete Model
     *
     * @param String $model Model name
     * @return String $html HTML modal
     */
    public function deleteModal($model = '') {
        $html  = '<div class="modal fade" id="delete' . $model . '" tabindex="-1" aria-labelledby="delete' . $model . 'Label" aria-hidden="true">';
        $html .= '    <div class="modal-dialog modal-dialog-centered">';
        $html .= '        <div class="modal-content">';
        $html .= '            <div class="modal-header">';
        $html .= '                <h2 class="modal-title fs-5" id="exampleModalLabel">Delete ' . $model . '</h2>';
        $html .= '                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
        $html .= '            </div>';
        $html .= '            <div class="modal-body">';
        $html .= '                Are you sure to delete the ' . $model . '?';
        $html .= '            </div>';
        $html .= '            <div class="modal-footer">';
        $html .= '                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>';
        $html .= '                <button type="button" class="btn btn-danger">Delete ' . $model . '</button>';
        $html .= '            </div>';
        $html .= '        </div>';
        $html .= '    </div>';
        $html .= '</div>';
        return $html;
    }
}
