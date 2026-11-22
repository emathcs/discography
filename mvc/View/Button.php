<?php

class ButtonView extends View {
    /**
     * Table
     *
     * @param Array $records Button records
     * @return String $html An HTML table
     */
    public function table($records = [], $platforms = []) {
        $html = '';
        $total = count($records);
        if ($total) {
            $html .= '<table class="table table-sm table-striped table-hover table-bordered">';
            $html .= '    <thead>';
            $html .= '        <tr>';
            $html .= '            <th scope="col">Id</th>';
            $html .= '            <th scope="col">Platform</th>';
            $html .= '            <th scope="col">Link</th>';
            $html .= '            <th scope="col" colspan="3" class="col-actions">Actions</th>';
            $html .= '        </tr>';
            $html .= '    </thead>';
            $html .= '    <tbody>';
            for ($i = 0; $i < $total; $i++) {
                $html .= '    <tr>';
                $html .= '        <th scope="row">' . $records[$i]->id . '</th>';
                $html .= '        <td>' . $platforms[$records[$i]->platform_id] . '</td>';
                $html .= '        <td><a href="' . $records[$i]->link . '" target="_blank">' . $records[$i]->link . '</a></td>';
                $html .= '        <td class="col-action"><a href="?page=discography-button-view&button=' . $records[$i]->id . '" class="btn btn-sm btn-success">View</a></td>';
                $html .= '        <td class="col-action"><a href="?page=discography-button-edit&button=' . $records[$i]->id . '" class="btn btn-sm btn-warning">Edit</a></td>';
                $html .= '        <td class="col-action">';
                $html .= '            <button type="button" class="btn btn-sm btn-danger delete-record" data-bs-toggle="modal" data-bs-target="#deleteButton" data-id="' . $records[$i]->id . '" data-model="button">';
                $html .= '                Delete';
                $html .= '            </button>';
                $html .= '        </td>';
                $html .= '    </tr>';
            }
            $html .= '    </tbody>';
            $html .= '</table>';
            // Delete form
            $html .= $this->deleteModal('Button', 'button');
        }
        return $html;
    }

    /**
     * List page
     *
     * @param Array $data Data to render
     */
    public function list($data = []) {
        $records = isset($data['records']) ? $data['records'] : [];
        $total = count($records);
        // Panel
        echo $this->panelOpen('Buttons');
        echo '<div class="mb-3">';
        echo '    <a href="?page=discography-button-add" class="btn btn-sm btn-primary">Add Button</a>';
        echo '</div>';
        echo $this->table($records, $data['platforms']);
        echo $this->panelClose();
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

        // Navigation
        $goTo = '';
        $goToLabel = '';
        $parameters = '';
        if (isset($data['release_id']) && $data['release_id']) {
            $goToLabel = 'Go to the Release';
            $parameters = '&model=&id=' . $data['release_id'];
            $goTo = '?page=discography-release-view&release=' . $data['release_id'] ;
        }
        if (isset($data['track_id']) && $data['track_id']) {
            $goToLabel = 'Go to the Track';
            $parameters = '&model=track&id=' . $data['track_id'];
            $goTo = '?page=discography-track-view&track=' . $data['track_id'] ;
        }

        // Panel
        echo $this->panelOpen('Add Button');
        echo $form->init(['action' => 'admin.php?page=discography-button-view' . $parameters, 'method' => 'post']);
        // Security field: Nonce to prevent CSRF attacks
        wp_nonce_field( 'save_custom_form_action', 'custom_form_nonce' );
        // Release id
        if ($data['release_id']) {
            echo $input->create([
                'type'     => 'hidden',
                'id'       => 'release-id',
                'name'     => 'release_id',
                'class'    => 'form-control',
                'value'    => $data['release_id']
            ]);
        }
        // Track id
        if ($data['track_id']) {
            echo $input->create([
                'type'     => 'hidden',
                'id'       => 'track-id',
                'name'     => 'track_id',
                'class'    => 'form-control',
                'value'    => $data['track_id']
            ]);
        }
        // Platform
        $select->setLabel('Platform');
        echo $select->create(
            [
                'type'     => 'text',
                'id'       => 'platform-id',
                'name'     => 'platform_id',
                'class'    => 'form-control',
                'required' => '',
                'value'    => ''
            ],
            $data['platforms']
        );
        // Link
        $input->setLabel('Link');
        echo $input->create([
            'type'     => 'text',
            'id'       => 'link',
            'name'     => 'link',
            'class'    => 'form-control',
            'required' => ''
        ]);
        // Navigation buttons
        echo $input->setLabel('');
        echo $navigation->buttons(
            ['href' =>  $goTo, 'label' => $goToLabel, 'class' => 'btn btn-warning'],
            ['type' => 'submit', 'name' => 'submit', 'value' =>'Save Button', 'class' => 'btn btn-primary']
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

        // Navigation
        $goTo = '';
        $goToLabel = '';
        $parameters = '';
        if ($record->release_id) {
            $parameter = 'release';
            $id = $record->release_id;
            $goToLabel = 'Go to the Release';
            $parameters = '&model=release&id=' . $record->release_id;
            $goTo = '?page=discography-release-view&release=' . $record->release_id ;
        }
        if ($record->track_id) {
            $parameter = 'track';
            $id = $record->track_id;
            $goToLabel = 'Go to the Track';
            $parameters = '&model=track&id=' . $record->track_id;
            $goTo = '?page=discography-track-view&track=' . $record->track_id;
        }

        // Panel
        echo $this->panelOpen( 'View Button' );
        if ($record) {
            echo '<table class="table table-sm table-striped table-hover table-bordered">';
            echo '    <tbody>';
            echo '        <tr>';
            echo '            <th>Platform</th>';
            echo '            <td>' . $data['platforms'][$record->platform_id] . '</td>';
            echo '        </tr>';
            echo '        <tr>';
            echo '            <th>Link</th>';
            echo '            <td>' . $record->link . '</td>';
            echo '        </tr>';
            echo '    </tbody>';
            echo '</table>';
            echo '<div class="mb-3">';
            // Navigation buttons
            echo '<a href="' . $goTo .'" class="btn btn-warning">' . $goToLabel . '</a> ';
            echo '<a href="?page=discography-button-edit&button=' . $record->id .'" class="btn btn-secondary">Edit Button</a> ';
            echo '<a href="?page=discography-button-add&' . $parameter . '=' . $id . '" class="btn btn-primary">Add Button</a>';
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
        $select = new Select('mb-3');
        $select->setLabelClass('form-label');
        $record = isset($data['record']) ? $data['record'] : [];
        $navigation = new Navigation('mb-3');

        // Navigation
        $goTo = '';
        $goToLabel = '';
        $parameters = '';
        if ($record->release_id) {
            $goToLabel = 'Go to the Release';
            $parameters = '&model=release&id=' . $record->release_id;
            $goTo = '?page=discography-release-view&release=' . $record->release_id ;
        }
        if ($record->track_id) {
            $goToLabel = 'Go to the Track';
            $parameters = '&model=track&id=' . $record->track_id;
            $goTo = '?page=discography-track-view&track=' . $record->track_id;
        }

        // Panel
        echo $this->panelOpen('Edit Button');
        if ($record) {
            echo $form->init(['action' => "admin.php?page=discography-button-view&button=" . $record->id, 'method' => 'post']);
            // Security field: Nonce to prevent CSRF attacks
            wp_nonce_field( 'save_custom_form_action', 'custom_form_nonce' );
            // Id
            echo $input->create([
                'type'     => 'hidden',
                'id'       => 'id',
                'name'     => 'id',
                'class'    => 'form-control',
                'value'    => $record->id
            ]);
            // Platform
            $select->setLabel('Platform');
            echo $select->create(
                [
                    'type'     => 'text',
                    'id'       => 'platform-id',
                    'name'     => 'platform_id',
                    'class'    => 'form-control',
                    'required' => '',
                    'value'    => $record->platform_id
                ],
                $data['platforms']
            );
            // Link
            $input->setLabel('Link');
            echo $input->create([
                'type'     => 'text',
                'id'       => 'link',
                'name'     => 'link',
                'class'    => 'form-control',
                'required' => '',
                'value'    => $record->link
            ]);
            // Navigation buttons
            echo $input->setLabel('');
            echo $navigation->buttons(
                ['href' =>  $goTo,   'label' => $goToLabel, 'class' => 'btn btn-warning'],
                ['type' => 'submit', 'name' => 'submit', 'value' =>'Update Button', 'class' => 'btn btn-primary']
            );
            echo $form->end();
        }
        echo $this->panelClose();
    }
}
