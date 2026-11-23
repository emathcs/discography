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
        $success = [];
        // Children model
        $trackModel = new TrackModel();
        $buttonModel = new ButtonModel();
        if (count($_POST)) {
            $status = 0;
            $process = 0;
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
                if (isset($_POST['id'])) {
                    $id = sanitize_text_field($_POST['id']);
                    // Process
                    $process = 'update';
                    // Update record: -1, 0, 1, or false
                    $status = $this->update($id, $_POST, $fields);
                }
                else {
                    // Process
                    $process = 'insert';
                    // Insert a new record: -1, 0, record id, or false
                    $id = $this->insert($_POST, $fields);
                    $status = $id > 0 ? 1 : $id;
                }
            }
            else {
                // Delete Button or Track
                if (isset($_POST['delete']) && isset($_POST['model'])) {
                    $process = 'delete';
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
                        $status = $this->delete();
                    }
                }
            }
            if ('update' === $process) {
                $messages = [
                    'The record was not updated, check the values have the right type.',
                    'Action succeeded, but values were already identical.',
                    'The record was updated.',
                ];
                if (false === $status) {
                    $success = [
                        'message' => 'There was an error when the program tried to update the record.',
                        'status'  => 0,
                    ];
                }
                else {
                    $success = [
                        'message' => $messages[(1 + $status)],
                        'status'  => $status < 0 ? 0 : $status,
                    ];
                }
            }
            else {
                if ('insert' === $process) {
                    $messages = [
                        'The record was not stored, check the values have the right type.',
                        'The record was not stored, check the values have the right type.',
                        'The record was stored.',
                    ];
                    if (false === $status) {
                        $success = [
                            'message' => 'There was an error when the program tried to store the record.',
                            'status'  => 0,
                        ];
                    }
                    else {
                        $success = [
                            'message' => $messages[(1 + $status)],
                            'status'  => $status < 0 ? 0 : $status,
                        ];
                    }
                }
                else {
                    if ('delete' === $process) {
                        $messages = [
                            'The record was not deleted.',
                            'The record was deleted.',
                        ];
                        if (false === $status) {
                            $success = [
                                'message' => 'There was an error when the program tried to store the record.',
                                'status'  => 0,
                            ];
                        }
                        else {
                            $success = [
                                'message' => $messages[$status],
                                'status'  => $status < 0 ? 0 : $status,
                            ];
                        }
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
            'success'   => $success,
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
