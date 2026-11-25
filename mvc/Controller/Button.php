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
            $this->delete($_POST);
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

        // Navigation
        $navigation = [];
        if ($release_id) {
            $navigation = [
                'label'      => 'Go to the Release',
                'parameters' => '&model=release&id=' . $release_id,
                'backTo'     => '?page=discography-release-view&release=' . $release_id,
            ];
        } else {
            if ($track_id) {
                $navigation = [
                    'label'      => 'Go to the Track',
                    'parameters' => '&model=track&id=' . $track_id,
                    'backTo'     => '?page=discography-track-view&track=' . $track_id,
                ];
            }
        }

        // Platforms
        $platforms = $this->getChildModel()->getOptionsFormat();
        // View
        $this->getView()->add(['platforms' => $platforms, 'release_id' => $release_id, 'track_id' => $track_id, 'navigation' => $navigation]);
    }

    /**
     * View
     *
     * @param Integer $id Button id
     */
    public function view($id = 0) {
        $navigation = [];
        $notification = [];
        // This process has been added here to avoid the process to redirect to this page from add or edit page
        if (isset($_POST['submit'])) {
            $status = 0;
            $process = '';
            if (isset($_POST['id'])) {
                $process = 'update';
                $id = sanitize_text_field($_POST['id']);
                $i = 0;
                $fields[$i++] = [ 'name' => 'link', 'format' => '%s' ];
                $fields[$i++] = [ 'name' => 'platform_id', 'format' => '%d' ];
                // Update record
                $status = $this->update($id, $_POST, $fields);
            }
            else {
                $i = 0;
                $process = 'insert';
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
                $status = $id > 0 ? 1 : $id;

            }
            $notification = $this->getNotification($process, $status);
        }
        // Platforms
        $platforms = $this->getChildModel()->getListFormat();
        // Get the record
        $record = $this->getModel()->selectById($id);

        // Navigation
        $track_id   = isset($_POST['track_id']) ? $_POST['track_id'] : 0;
        $release_id = isset($_POST['release_id']) ? $_POST['release_id'] : 0;
        $navigation = [];
        if ($release_id) {
            $navigation = [
                'id'         => $release_id,
                'model'      => 'release',
                'label'      => 'Go to the Release',
                'parameters' => '&model=release&id=' . $release_id,
                'backTo'     => '?page=discography-release-view&release=' . $release_id,
            ];
        } else {
            if ($track_id) {
                $navigation = [
                    'id'         => $track_id,
                    'model'      => 'track',
                    'label'      => 'Go to the Track',
                    'parameters' => '&model=track&id=' . $track_id,
                    'backTo'     => '?page=discography-track-view&track=' . $track_id,
                ];
            }
        }

        // View
        $this->getView()->view(['record' => $record, 'platforms' => $platforms, 'notification'   => $notification, 'navigation' => $navigation]);
    }

    /**
     * Edit
     *
     * @param Integer $id Button id
     */
    public function edit($id = 0) {
        // Get the record
        $record = $this->getModel()->selectById($id);
        // Navigation
        $navigation = [];
        if ($record->release_id) {
            $navigation = [
                'label'      => 'Go to the Release',
                'backTo'     => '?page=discography-release-view&release=' . $record->release_id,
            ];
        } else {
            if ($record->track_id) {
                $navigation = [
                    'label'      => 'Go to the Track',
                    'backTo'     => '?page=discography-track-view&track=' . $record->track_id,
                ];
            }
        }

        // Platforms
        $platforms = $this->getChildModel()->getOptionsFormat();

        // View
        $this->getView()->edit(['record' => $record, 'platforms' => $platforms, 'navigation' => $navigation]);
    }
}
