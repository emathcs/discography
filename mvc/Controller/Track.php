<?php

class TrackController extends Controller {
    /**
     * Constructor
     */
    public function __construct() {
        $Model = new TrackModel();
        $View  = new TrackView();
        // Parent constructor
        parent::__construct($Model, $View);
    }

    /**
     * List
     */
    public function list() {
        // Delete
        if (isset($_POST['delete']) && isset($_POST['model'])) {
            // Track model
            if ('track' === $_POST['model']) {
                $this->setDeleteModel($this->getModel());
            }
            $this->delete();
        }
        // Get the records
        $records = $this->getModel()->select();
        // View
        $this->getView()->list(['records' => $records]);
    }

    /**
     * Add
     *
     * @param Integer $release_id Release id
     */
    public function add($release_id = 0) {
        // View
        $this->getView()->add(['release_id' => $release_id]);
    }

    /**
     * View
     *
     * @param Integer $id Track id
     */
    public function view($id = 0) {
        // Button Model
        $buttonModel = new ButtonModel();
        // This process has been added here to avoid the process to redirect to this page from add or edit page
        if (isset($_POST['submit'])) {
            if ( ! isset( $_POST['custom_form_nonce'] ) || ! wp_verify_nonce( $_POST['custom_form_nonce'], 'save_custom_form_action' ) ) {
                wp_die( 'Security check failed. Please try again.' );
            }
            if (isset($_POST['id'])) {
                $id = sanitize_text_field($_POST['id']);
                $fields = [
                    [ 'name' => 'name', 'format' => '%s' ],
                ];
                // Update record
                $this->update($id, $_POST, $fields);
            }
            else {
                $fields = [
                    [ 'name' => 'name',       'format' => '%s' ],
                    [ 'name' => 'release_id', 'format' => '%d' ],
                ];
                // Insert a new record
                $id = $this->insert($_POST, $fields);
            }
        }
        // Delete Button
        if (isset($_POST['delete']) && isset($_POST['model'])) {
            // Track model
            if ('button' === $_POST['model']) {
                $this->setDeleteModel($buttonModel);
            }
            $this->delete();
        }
        // Get the record
        $record = $this->getModel()->selectById($id);
        // Get the related buttons
        $buttons = $buttonModel->selectByTrackId($id);
        // Platforms
        $platformModel = new PlatformModel();
        $platforms = $platformModel->getListFormat();
        // View
        $this->getView()->view(['record' => $record, 'buttons' => $buttons, 'platforms' => $platforms]);
    }

    /**
     * Edit
     *
     * @param Integer $id Track id
     */
    public function edit($id = 0) {
        // Get the record
        $record = $this->getModel()->selectById($id);
        // View
        $this->getView()->edit(['record' => $record]);
    }
}
