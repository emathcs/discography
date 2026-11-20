<?php

class TrackView extends View {
    /**
     * Table function
     *
     * @param Array $records Track records
     * @return String $html An HTML table
     */
    public function table($records = []) {
        $html = '';
        $total = count($records);
        if ($total) {
            $html .= '<table class="table table-sm table-striped table-hover table-bordered">';
            $html .= '    <thead>';
            $html .= '        <tr>';
            $html .= '            <th scope="col">Id</th>';
            $html .= '            <th scope="col">Name</th>';
            $html .= '            <th scope="col" colspan="3" class="col-actions">Actions</th>';
            $html .= '        </tr>';
            $html .= '    </thead>';
            $html .= '    <tbody>';
            for ($i = 0; $i < $total; $i++) {
                $html .= '    <tr>';
                $html .= '        <th scope="row">' . $records[$i]->id . '</th>';
                $html .= '        <td>' . $records[$i]->name . '</td>';
                $html .= '        <td class="col-action"><a href="?page=discography-track-view&track=' . $records[$i]->id . '" class="btn btn-sm btn-success">View</a></td>';
                $html .= '        <td class="col-action"><a href="?page=discography-track-edit&track=' . $records[$i]->id . '" class="btn btn-sm btn-warning">Edit</a></td>';
                $html .= '        <td class="col-action">';
                $html .= '            <button type="button" class="btn btn-sm btn-danger delete-record" data-bs-toggle="modal" data-bs-target="#deleteTrack" data-id="' . $records[$i]->id . '" data-model="track">';
                $html .= '                Delete';
                $html .= '            </button>';
                $html .= '        </td>';
                $html .= '    </tr>';
            }
            $html .= '    </tbody>';
            $html .= '</table>';
            // Delete form
            $html .= $this->deleteModal('Track', 'track');
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

        // Panel
        echo $this->panelOpen('Tracks');
        echo '<div class="mb-3">';
        echo '    <a href="?page=discography-track-add" class="btn btn-sm btn-primary">Add a new track</a>';
        echo '</div>';
        echo $this->table($records);
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
        $release_id = isset($data['release_id']) ? $data['release_id'] : 0;
        $navigation = new Navigation('mb-3');

        // Model for redirection
        $parameters = '';
        if (isset($data['release_id']) && $data['release_id']) {
            $parameters = '&release=' . $data['release_id'];
        }

        // Panel
        echo $this->panelOpen('Add a new track');
        echo $form->init(['action' => 'admin.php?page=discography-track-view' . $parameters, 'method' => 'post']);
        // Security field: Nonce to prevent CSRF attacks
        wp_nonce_field( 'save_custom_form_action', 'custom_form_nonce' );
        // Release id
        echo $input->create([
            'type'     => 'hidden',
            'id'       => 'release-id',
            'name'     => 'release_id',
            'class'    => 'form-control',
            'value'    => $release_id
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
        // Navigation buttons
        echo $input->setLabel('');
        echo $navigation->buttons(
            ['href' => '?page=discography-release-view&release=' . $release_id , 'label' => 'Go to the Release', 'class' => 'btn btn-warning'],
            ['type' => 'submit', 'name' => 'submit', 'value' =>'Save Track', 'class' => 'btn btn-primary']
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
        echo $this->panelOpen( 'View Track' );
        if ($record) {
            // Get the Button view
            $buttonsTable = false;
            if (isset($data['buttons']) && count($data['buttons'])) {
                $buttonsTable = true;
                $buttonView = new ButtonView();
            }
            echo '<table class="table table-sm table-striped table-hover table-bordered">';
            echo '    <tbody>';
            echo '        <tr>';
            echo '            <th>Name</th>';
            echo '            <td>' . $record->name . '</td>';
            echo '        </tr>';
            echo '    </tbody>';
            echo '</table>';
            echo '<div class="mb-3">';
            echo '    <a href="?page=discography-release-view&release=' . $record->release_id .'" class="btn btn-warning">Go to the Release</a>';
            echo '    <a href="?page=discography-track-edit&track=' . $record->id .'" class="btn btn-secondary">Edit Track</a>';
            echo '    <a href="?page=discography-track-add&release=' . $record->release_id . '" class="btn btn-primary">Add Track</a>';
            echo '</div>';
            // Button view
            echo '<br><hr><br>';
            echo '<h2>Buttons</h2>';
            echo '<a href="?page=discography-button-add&track=' . $record->id . '" class="btn btn-primary">Add Button</a>';
            echo '<br><br>';
            if ($buttonsTable) {
                echo $buttonView->table($data['buttons'], $data['platforms']);
            }
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
        $navigation = new Navigation('mb-3');

        // Panel
        echo $this->panelOpen('Edit Track');
        if ($record) {
            echo $form->init(['action' => "admin.php?page=discography-track-view&track=" . $record->id, 'method' => 'post']);
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
            // Navigation buttons
            echo $input->setLabel('');
            echo $navigation->buttons(
                ['href' => '?page=discography-track-view&track=' . $record->id , 'label' => 'Go to the Track', 'class' => 'btn btn-warning'],
                ['type' => 'submit', 'name' => 'submit', 'value' =>'Update Track', 'class' => 'btn btn-primary']
            );
            echo $form->end();
        }
        echo $this->panelClose();
    }
}
