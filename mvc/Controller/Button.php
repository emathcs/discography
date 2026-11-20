<?php

class ButtonController extends Controller {
    /**
     * Constructor
     */
    public function __construct() {
        $Model = new ButtonModel();
        $View  = new ButtonView();
        // ModelChild
        $ChildModel = new PlatformModel();
        // Parent constructor
        parent::__construct($Model, $View, $ChildModel);
    }

    /**
     * List
     */
    public function list() {
        // Delete
        if (isset($_POST['delete']) && isset($_POST['model'])) {
            // Button model
            if ('button' === $_POST['model']) {
                $this->setDeleteModel($this->getModel());
            }
            $this->delete();
        }
        // Platforms
        $platforms = $this->getChildModel()->getListFormat();
        // Get the records
        $records = $this->getModel()->select();
        // View
        $this->getView()->list(['records' => $records, 'platforms' => $platforms]);
    }

    /**
     * Add
     */
    public function add() {
        // Release id
        $release_id = isset($_GET['release']) ? $_GET['release'] : 0;
        // Track id
        $track_id = isset($_GET['track']) ? $_GET['track'] : 0;
        // Platforms
        $platforms = $this->getChildModel()->getOptionsFormat();
        // View
        $this->getView()->add(['platforms' => $platforms, 'release_id' => $release_id, 'track_id' => $track_id]);
    }

    /**
     * View
     *
     * @param Integer $id Button id
     */
    public function view($id = 0) {
        // This process has been added here to avoid the process to redirect to this page from add or edit page
        if (isset($_POST['submit'])) {
            if ( ! isset( $_POST['custom_form_nonce'] ) || ! wp_verify_nonce( $_POST['custom_form_nonce'], 'save_custom_form_action' ) ) {
                wp_die( 'Security check failed. Please try again.' );
            }
            if (isset($_POST['id'])) {
                $id = sanitize_text_field($_POST['id']);
                $i = 0;
                $fields[$i++] = [ 'name' => 'link', 'format' => '%s' ];
                $fields[$i++] = [ 'name' => 'platform_id', 'format' => '%d' ];
                // Update record
                $this->update($id, $_POST, $fields);
            }
            else {
                $i = 0;
                $fields[$i++] = [ 'name' => 'link', 'format' => '%s' ];
                if (isset($_POST['track_id'])) {
                    $fields[$i++] = [ 'name' => 'track_id', 'format' => '%d' ];
                }
                if (isset($_POST['release_id'])) {
                    $fields[$i++] = [ 'name' => 'release_id', 'format' => '%d' ];
                }
                $fields[$i++] = [ 'name' => 'platform_id', 'format' => '%d' ];
                // Insert a new record
                $id = $this->insert($_POST, $fields);
            }
        }
        // Platforms
        $platforms = $this->getChildModel()->getListFormat();
        // Get the record
        $record = $this->getModel()->selectById($id);
        // View
        $this->getView()->view(['record' => $record, 'platforms' => $platforms]);
    }

    /**
     * Edit
     *
     * @param Integer $id Button id
     */
    public function edit($id = 0) {
        // Platforms
        $platforms = $this->getChildModel()->getOptionsFormat();
        // Get the record
        $record = $this->getModel()->selectById($id);
        // View
        $this->getView()->edit(['record' => $record, 'platforms' => $platforms]);
    }
}
