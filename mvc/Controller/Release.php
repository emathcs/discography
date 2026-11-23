<?php

class ReleaseController extends Controller {
    /**
     * Constructor
     */
    public function __construct() {
        $Model = new ReleaseModel();
        $View  = new ReleaseView();
        // ModelChild
        $ChildModel = new TypeModel();
        // Parent constructor
        parent::__construct($Model, $View, $ChildModel);
    }

    /**
     * List
     */
    public function list() {
        // Delete
        if (isset($_POST['delete']) && isset($_POST['model'])) {
            // Release model
            if ('release' === $_POST['model']) {
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
     */
    public function add() {
        // Status
        $statusModel = new StatusModel();
        $status = $statusModel->getOptionsFormat();
        // Types
        $types = $this->getChildModel()->getOptionsFormat();
        // Default release image
        $default_src = $src_default = esc_url(DISCOGRAPHY_URL . 'assets/images/default-cover.png');
        // View
        $this->getView()->add(['default_src' => $default_src, 'types' => $types, 'status' => $status]);
    }

    /**
     * View
     *
     * @param Integer $id Release id
     */
    public function view($id = 0) {
        // Children model
        $trackModel = new TrackModel();
        $buttonModel = new ButtonModel();
        if (count($_POST)) {
            // This process has been added here to avoid the process to redirect to this page from add or edit page
            if (isset($_POST['submit'])) {
                if ( ! isset( $_POST['custom_form_nonce'] ) || ! wp_verify_nonce( $_POST['custom_form_nonce'], 'save_custom_form_action' ) ) {
                    wp_die( 'Security check failed. Please try again.' );
                }
                $fields = [
                    [ 'name' => 'name',      'format' => '%s' ],
                    [ 'name' => 'type_id',   'format' => '%d' ],
                    [ 'name' => 'post_id',   'format' => '%d' ],
                    [ 'name' => 'image_id',  'format' => '%d' ],
                    [ 'name' => 'status_id', 'format' => '%d' , 'verify' => false ],
                ];
                $success = 0;
                if (isset($_POST['id'])) {
                    $id = sanitize_text_field($_POST['id']);
                    // Update record
                    $success = $this->update($id, $_POST, $fields);
                    $success = $success ? 2 : $success;
                }
                else {
                    // Insert a new record
                    $id = $this->insert($_POST, $fields);
                    $succcess = $id ? 1 : $id;
                }
            }
            else {
                // Delete Button or Track
                if (isset($_POST['delete']) && isset($_POST['model'])) {
                    $deleteModel = null;
                    // Track model
                    if ('track' === $_POST['model']) {
                        $deleteModel = $trackModel;
                    }
                    else {
                        // Button model
                        if ('button' === $_POST['model']) {
                            $deleteModel = $buttonModel;
                        }
                    }
                    if ($deleteModel) {
                        $this->setDeleteModel($deleteModel);
                        $this->delete();
                    }
                }
            }
        }
        // Get the record
        $record = $this->getModel()->selectById($id);
        // Get the related tracks
        $tracks = $trackModel->selectByReleaseId($id);
        // Get the related buttons
        $buttons = $buttonModel->selectByReleaseId($id);
        // Platforms
        $platformModel = new PlatformModel();
        $platforms = $platformModel->getListFormat();
        // Types
        $types = $this->getChildModel()->getListFormat();
        // Image section // Default release image
        $src = esc_url(DISCOGRAPHY_URL . 'assets/images/default-cover.png');
        if ($record && $record->image_id) {
            $src = wp_get_attachment_image_url($record->image_id, 'medium');
        }
        // Status
        $statusModel = new StatusModel();
        $status = $statusModel->getListFormat();
        // View
        $this->getView()->view([
            'src'       => $src,
            'tracks'    => $tracks,
            'types'     => $types,
            'record'    => $record,
            'status'    => $status,
            'buttons'   => $buttons,
            'platforms' => $platforms,
        ]);
    }

    /**
     * Edit
     *
     * @param Integer $id Release id
     */
    public function edit($id = 0) {
        // Get the record
        $record = $this->getModel()->selectById($id);
        // Types
        $types = $this->getChildModel()->getOptionsFormat();
        // Images
        $src = esc_url(DISCOGRAPHY_URL . 'assets/images/default-cover.png');
        $default_src = $src;
        // Check if there is a record
        if ($record && $record->image_id) {
            // Image section
            $src = wp_get_attachment_image_url($record->image_id, 'medium');
        }
        // Status
        $statusModel = new StatusModel();
        $status = $statusModel->getOptionsFormat();
        // View
        $this->getView()->edit([
            'src'         => $src,
            'types'       => $types,
            'record'      => $record,
            'status'      => $status,
            'default_src' => $default_src
        ]);
    }
}
