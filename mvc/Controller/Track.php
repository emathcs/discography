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
        $status = 0;
        $process = '';
        $navigation = [];
        $notification = [];
        // Button Model
        $buttonModel = new ButtonModel();
        // This process has been added here to avoid the process to redirect to this page from add or edit page
        if (isset($_POST['submit'])) {
            if (isset($_POST['id'])) {
                $process = 'update';
                $id = sanitize_text_field($_POST['id']);
                $fields = [
                    [ 'name' => 'name', 'format' => '%s' ],
                ];
                // Update record
                $status = $this->update($id, $_POST, $fields);
            }
            else {
                $process = 'insert';

                $release_id =  $_POST['release_id'];

                $navigation['model']  = 'release';
                $navigation['id']     = $release_id;
                $navigation['backTo'] = '?page=discography-release-view&release=' . $release_id;
                $navigation['backToLabel'] = 'Go to the Release';

                $fields = [
                    [ 'name' => 'name',       'format' => '%s' ],
                    [ 'name' => 'release_id', 'format' => '%d' ],
                ];
                // Insert a new record
                $id = $this->insert($_POST, $fields);
                $status = $id > 0 ? 1 : $id;
            }
        }
        else {
            // Delete Button
            if (isset($_POST['delete']) && isset($_POST['model'])) {
                // Track model
                if ('button' === $_POST['model']) {
                    $this->setDeleteModel($buttonModel);
                }
                $process = 'delete';
                $status = $this->delete();
            }
        }
        $notification = $this->getNotification($process, $status);
        // Get the record
        $record = $this->getModel()->selectById($id);
        // Get the related buttons
        $buttons = $buttonModel->selectByTrackId($id);
        // Platforms
        $platformModel = new PlatformModel();
        $platforms = $platformModel->getListFormat();
        // View
        $this->getView()->view([
            'record' => $record,
            'buttons' => $buttons,
            'platforms' => $platforms,
            'navigation' => $navigation,
            'notification'   => $notification,
        ]);
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
