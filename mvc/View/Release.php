<?php

class ReleaseView extends View {
    /**
     * List page
     *
     * @param Array $data Data to render
     */
    public function list($data = []) {
        $records = isset($data['records']) ? $data['records'] : [];
        $total = count($records);

        // Panel
        echo $this->panelOpen('Releases');
        echo $this->notification($data['notification']);
        echo '<div class="mb-3">';
        echo '    <a href="?page=discography-release-add" class="btn btn-primary">Add Release</a>';
        echo '</div>';
        if ($total) {
            echo '<table class="table table-sm table-striped table-hover table-bordered">';
            echo '    <thead>';
            echo '        <tr>';
            echo '            <th scope="col">Id</th>';
            echo '            <th scope="col">Name</th>';
            echo '            <th scope="col" colspan="3" class="col-actions">Actions</th>';
            echo '        </tr>';
            echo '    </thead>';
            echo '    <tbody>';
            for ($i = 0; $i < $total; $i++) {
                echo '    <tr>';
                echo '        <th scope="row">' . $records[$i]->id . '</th>';
                echo '        <td>' . $records[$i]->name . '</td>';
                echo '        <td class="col-action"><a href="?page=discography-release-view&release=' . $records[$i]->id . '" class="btn btn-sm btn-success">View</a></td>';
                echo '        <td class="col-action"><a href="?page=discography-release-edit&release=' . $records[$i]->id . '" class="btn btn-sm btn-warning">Edit</a></td>';
                echo '        <td class="col-action">';
                echo '            <button type="button" class="btn btn-sm btn-danger delete-record" data-bs-toggle="modal" data-bs-target="#deleteRelease" data-id="' . $records[$i]->id . '" data-model="release">';
                echo '                Delete';
                echo '            </button>';
                echo '        </td>';
                echo '    </tr>';
            }
            echo '    </tbody>';
            echo '</table>';
            // Delete form
            echo $this->deleteModal('Release', 'release');
        }
        echo $this->panelClose();
    }

    /**
     * Image section
     *
     * @param Array $data Data to render
     * @return String $html HTML for the image section
     */
    public function getImage($action = 'add', $src = '', $default_src = '') {
        $hideClass = [
            'add' => [
                'src'     => 'discography-upload-btn discography-image-gallery-item-preview discography-admin-hide',
                'default' => 'discography-upload-btn discography-image-gallery-item-preview',
                'select'  => '',
                'remove'  => 'discography-admin-hide',
            ],
            'edit' => [
                'src'     => 'discography-upload-btn discography-image-gallery-item-preview',
                'default' => 'discography-upload-btn discography-image-gallery-item-preview discography-admin-hide',
                'select'  => 'discography-admin-hide',
                'remove'  => '',
            ],
        ];
        $html = '';
        if ('add' === $action || 'edit' === $action) {
            $html .= '<div id="discography-image-gallery-item-preview" class="mb-3 col-12 col-sm-4 ' . $hideClass[$action]['src'] . '">';
            $html .= '    <img src="' . $src . '" id="discography-cover-img">';
            $html .= '</div>';
            $html .= '<div id="discography-image-gallery-item-upload" class="mb-3 col-12 col-sm-4 ' . $hideClass[$action]['default'] . '">';
            $html .= '    <img src="' . $default_src . '" id="discography-admin">';
            $html .= '</div>';
            $html .= '<div class="mb-3">';
            $html .= '    <button type="button" id="upload-btn" class="discography-upload-btn btn btn-secondary ' . $hideClass[$action]['select'] . '">Select an image</button> ';
            $html .= '    <button type="button" id="remove-btn" class="discography-remove-btn btn btn-warning ' . $hideClass[$action]['remove'] . '">Remove image</button>';
            $html .= '</div>';
        }
        return $html;
    }
    /**
     * Add page
     *
     * @param Array $data Data to render
     */
    public function add($data = []) {
        $form  = new Form();
        $input = new Input('mb-3');
        $input->setLabelClass('form-label');
        $select = new Select('mb-3');
        $select->setLabelClass('form-label');
        $navigation = new Navigation('mb-3');

        // Panel
        echo $this->panelOpen('Add Release');
        echo $form->init(['action' => "admin.php?page=discography-release-view", 'method' => 'post']);
        // Security field: Nonce to prevent CSRF attacks
        wp_nonce_field( 'custom_form_action', 'custom_form_nonce' );
        // Cover image
        echo $this->getImage('add', '', $data['default_src']);
        // Image ID
        $input->setLabel('');
        echo $input->create([
            'type'     => 'hidden',
            'id'       => 'image-id',
            'name'     => 'image_id',
            'class'    => 'form-control',
            'required' => ''
        ]);
        // Name
        $input->setLabel('Name');
        echo $input->create([
            'type'     => 'text',
            'id'       => 'name',
            'name'     => 'name',
            'class'    => 'form-control',
            'required' => ''
        ]);
        // Type
        $select->setLabel('Type');
        echo $select->create(
            [
                'type'     => 'text',
                'id'       => 'type-id',
                'name'     => 'type_id',
                'class'    => 'form-control',
                'required' => '',
                'value'    => ''
            ],
            $data['types']
        );
        // Post ID
        $input->setLabel('Post ID');
        echo $input->create([
            'type'     => 'text',
            'id'       => 'post-id',
            'name'     => 'post_id',
            'class'    => 'form-control',
            'required' => ''
        ]);
        // Status
        $select->setLabel('Status');
        echo $select->create(
            [
                'type'     => 'text',
                'id'       => 'status-id',
                'name'     => 'status_id',
                'class'    => 'form-control',
                'required' => '',
                'value'    => ''
            ],
            $data['status']
        );
        // Navigation buttons
        echo $input->setLabel('');
        echo $navigation->buttons(
            ['href' => '?page=discography-release', 'label' => 'Go to Releases', 'class' => 'btn btn-warning'],
            ['type' => 'submit', 'name' => 'submit', 'value' =>'Save Release', 'class' => 'btn btn-primary']
        );
        echo $form->end();
        echo $this->panelClose();
    }

    /**
     * View page
     *
     * @param Array $data Data to render
     */
    public function view($data = []) {
        $record = isset($data['record']) ? $data['record'] : [];

        // Panel
        echo $this->panelOpen( 'View Release' );
        echo $this->notification($data['notification']);
        if ($record) {
            // Get the Button view
            $buttonsTable = false;
            if (isset($data['buttons']) && count($data['buttons'])) {
                $buttonsTable = true;
                $buttonView = new ButtonView();
            }
            // Get the Track view
            $tracksTable = false;
            if (isset($data['tracks']) && count($data['tracks'])) {
                $tracksTable = true;
                $trackView = new TrackView();
            }

            // Cover image
            echo '<div class="mb-3 col-12 col-sm-4">';
            echo '    <img src="' . $data['src'] . '">';
            echo '</div>';
            echo '<table class="table table-sm table-striped table-hover table-bordered">';
            echo '    <tbody>';
            echo '        <tr>';
            echo '            <th>Id</th>';
            echo '            <td>' . $record->id . '</td>';
            echo '        </tr>';
            echo '        <tr>';
            echo '            <th>Name</th>';
            echo '            <td>' . $record->name . '</td>';
            echo '        </tr>';
            echo '        <tr>';
            echo '            <th>Type</th>';
            echo '            <td>' . $data['types'][$record->type_id] . '</td>';
            echo '        </tr>';
            echo '        <tr>';
            echo '            <th>Image ID</th>';
            echo '            <td>' . $record->image_id . '</td>';
            echo '        </tr>';
            echo '        <tr>';
            echo '            <th>Post ID</th>';
            echo '            <td>' . $record->post_id . '</td>';
            echo '        </tr>';
            echo '        <tr>';
            echo '            <th>Status</th>';
            echo '            <td>' . $data['status'][$record->status_id] . '</td>';
            echo '        </tr>';
            echo '    </tbody>';
            echo '</table>';
            echo '<div class="mb-3">';
            echo '    <a href="?page=discography-release" class="btn btn-warning">Go to Releases</a>';
            echo '    <a href="?page=discography-release-edit&release=' . $record->id .'" class="btn btn-secondary">Edit Release</a>';
            echo '    <a href="?page=discography-release-add" class="btn btn-primary">Add Release</a>';
            echo '</div>';
            // Button view
            echo '<br><hr><br>';
            echo '<h2>Buttons</h2>';
            echo '<a href="?page=discography-button-add&release=' . $record->id . '" class="btn btn-primary">Add Button</a>';
            echo '<br>';
            if ($buttonsTable) {
                echo '<br>';
                echo $buttonView->table($data['buttons'], $data['platforms']);
            }
            // Track view
            echo '<br><hr><br>';
            echo '<h2>Tracks</h2>';
            echo '<a href="?page=discography-track-add&release=' . $record->id . '" class="btn btn-primary">Add Track</a>';
            echo '<br><br>';
            if ($tracksTable) {
                echo $trackView->table($data['tracks']);
            }
        }
        else {
            echo '<div class="mb-3">';
            echo '    <a href="?page=discography-release" class="btn btn-warning">Go to Releases</a>';
            echo '    <a href="?page=discography-release-add" class="btn btn-primary">Add Release</a>';
            echo '</div>';
        }
        echo $this->panelClose();
    }

    /**
     * Edit page
     *
     * @param Array $data Data to render
     */
    public function edit($data = []) {
        $form  = new Form();
        $input = new Input('mb-3');
        $input->setLabelClass('form-label');
        $record = isset($data['record']) ? $data['record'] : [];
        $select = new Select('mb-3');
        $select->setLabelClass('form-label');
        $navigation = new Navigation('mb-3');

        // Panel
        echo $this->panelOpen('Edit Release');
        if ($record) {
            echo $form->init(['action' => "admin.php?page=discography-release-view&release=" . $record->id, 'method' => 'post']);
            // Security field: Nonce to prevent CSRF attacks
            wp_nonce_field( 'custom_form_action', 'custom_form_nonce' );
            // Cover image
            echo '<div id="discography-image-gallery-item-preview" class="mb-3 col-12 col-sm-4 discography-upload-btn discography-image-gallery-item-preview">';
            echo '    <img src="' . $data['src'] . '" id="discography-cover-img">';
            echo '</div>';
            echo '<div id="discography-image-gallery-item-upload" class="mb-3 col-12 col-sm-4 discography-upload-btn discography-image-gallery-item-preview discography-admin-hide">';
            echo '    <img src="' . $data['default_src'] . '" id="discography-admin">';
            echo '</div>';
            echo '<div class="mb-3">';
            // Select
            echo '    <button type="button" id="upload-btn" class="discography-upload-btn btn btn-secondary discography-admin-hide">Select an image</button> ';
            // Remove
            echo '    <button type="button" id="remove-btn" class="discography-remove-btn btn btn-warning">Remove image</button>';
            echo '</div>';
            // Id
            echo $input->create([
                'type'     => 'hidden',
                'id'       => 'id',
                'name'     => 'id',
                'class'    => 'form-control',
                'value'    => $record->id
            ]);
            // Cover
            $input->setLabel('');
            echo $input->create([
                'type'     => 'hidden',
                'id'       => 'image-id',
                'name'     => 'image_id',
                'class'    => 'form-control',
                'required' => '',
                'value'    => $record->image_id
            ]);
            // Name
            $input->setLabel('Name');
            echo $input->create([
                'type'     => 'text',
                'id'       => 'name',
                'name'     => 'name',
                'class'    => 'form-control',
                'required' => '',
                'value'    => $record->name
            ]);
            // Type
            $select->setLabel('Type');
            echo $select->create(
                [
                    'type'     => 'text',
                    'id'       => 'type-id',
                    'name'     => 'type_id',
                    'class'    => 'form-control',
                    'required' => '',
                    'value'    => $record->type_id
                ],
                $data['types']
            );
            // Post ID
            $input->setLabel('Post ID');
            echo $input->create([
                'type'     => 'text',
                'id'       => 'post-id',
                'name'     => 'post_id',
                'class'    => 'form-control',
                'required' => '',
                'value'    => $record->post_id
            ]);
            // Status
            $select->setLabel('Status');
            echo $select->create(
                [
                    'type'     => 'text',
                    'id'       => 'status-id',
                    'name'     => 'status_id',
                    'class'    => 'form-control',
                    'required' => '',
                    'value'    => $record->status_id
                ],
                $data['status']
            );
            // Navigation buttons
            echo $input->setLabel('');
            echo $navigation->buttons(
                ['href' => '?page=discography-release-view&release=' . $record->id, 'label' => 'Go to the Release', 'class' => 'btn btn-warning'],
                ['type' => 'submit', 'name' => 'submit', 'value' =>'Update Release', 'class' => 'btn btn-primary']
            );
            echo $form->end();
        }
        echo $this->panelClose();
    }
}
