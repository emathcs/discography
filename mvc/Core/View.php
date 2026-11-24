<?php

class View {
    /**
     * Open panel
     *
     * @return String HTML code to start a panel
     */
    public function panelOpen($title = '') {
        $render  = '<div class="discography-wrap">';
        $render .= '    <div class="container discography-panel">';
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
     * Notification section
     *
     * @param Array $data Array with the notification information
     * @return String HTML code to generate the notification section
     */
    public function notification($data = []) {
        $html = '';
        if (count($data)) {
            $alertClass = 'danger';
            if ($data['status']) {
                $alertClass = 'success';
            }
            $html .= '<div class="alert alert-' . $alertClass . '" role="alert">' . $data['message'] . '</div>';
        }
        return $html;
    }

    /**
     * Delete form
     *
     * @param String $model       Model name
     * @param String $buttonClass Button class
     * @return String HTML code to generate the delete form
     */
    public function deleteForm($model = '', $buttonClass = '') {
        $form  = new Form();
        $input = new Input();

        // Form
        $html  = $form->init(['action' => "", 'method' => 'post']);
        $html .= wp_nonce_field( 'custom_form_action', 'custom_form_nonce', true, false );
        $html .= $input->setLabel('');
        $html .= $input->create([
            'type'  => 'hidden',
            'id'    => 'delete-' . $model . '-id',
            'name'  => 'id',
            'class' => 'form-control',
            'value' => ''
        ]);
        $html .= $input->setLabel('');
        $html .= $input->create([
            'type'  => 'hidden',
            'id'    => 'delete-' . $model,
            'name'  => 'model',
            'class' => 'form-control',
            'value' => ''
        ]);
        $html .= $input->setLabel('');
        $html .= $input->create(['type' => 'submit', 'name' => 'delete', 'value' => 'Delete', 'class' => 'btn btn-danger' . $buttonClass]);
        $html .= $form->end();

        return $html;
    }

    /**
     * Delete Model
     *
     * @param String $label Label model name
     * @param String $model Model name
     * @return String $html HTML modal
     */
    public function deleteModal($label = '', $model = '') {
        $html  = '<div class="modal fade" id="delete' . $label . '" tabindex="-1" aria-labelledby="delete' . $label . 'Label" aria-hidden="true">';
        $html .= '    <div class="modal-dialog modal-dialog-centered">';
        $html .= '        <div class="modal-content">';
        $html .= '            <div class="modal-header">';
        $html .= '                <h2 class="modal-title fs-5" id="exampleModalLabel">Delete ' . $label . '</h2>';
        $html .= '                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>';
        $html .= '            </div>';
        $html .= '            <div class="modal-body">';
        $html .= '                Are you sure to delete the ' . $label . '?';
        $html .= '            </div>';
        $html .= '            <div class="modal-footer">';
        $html .= '                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>';
        $html .=                  $this->deleteForm($model);
        $html .= '            </div>';
        $html .= '        </div>';
        $html .= '    </div>';
        $html .= '</div>';
        return $html;
    }
}
