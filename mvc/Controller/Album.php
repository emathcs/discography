<?php

class AlbumController extends Controller {
    /**
     * Constructor
     */
    public function __construct() {
        $Model = new AlbumModel('albums');
        $View  = new AlbumView();
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
            // Album model
            if ('album' === $_POST['model']) {
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
        // Types
        $types = $this->getChildModel()->getOptionsFormat();
        // Default album image
        $default_src = $src_default = esc_url(ALBUM_URL . 'assets/images/default-cover.png');
        // View
        $this->getView()->add(['default_src' => $default_src, 'types' => $types]);
    }

    /**
     * Create data and their formats
     *
     * @param Array $form   Data to insert
     * @param Array $fields Name and formats
     * @return Array $dataFormat Data and their formats
     */
    public function createDataAndFormats($form = [], $fields = []) {
        $data = [];
        $formats = [];
        $condition = true;
        $total = count($fields);
        for ($i = 0; $i < $total; $i++) {
            $formats[$i] = $fields[$i]['format'];
            $data[$fields[$i]['name']] = sanitize_text_field($form[$fields[$i]['name']]);
            $condition = $condition && !empty($data[$fields[$i]['name']]);
        }
        $dataFormats = [];
        if ($condition) {
            $dataFormats = [
                'data'    => $data,
                'formats' => $formats
            ];
        }
        return $dataFormats;
    }

    /**
     * Insert into table
     *
     * @param Array $form   Data to insert
     * @param Array $fields Name and formats
     * @return Integer $id Id of the inserted record
     */
    public function insert($form = [], $fields = []) {
        $id = 0;
        if (count($fields)) {
            $dataFormats = $this->createDataAndFormats($form, $fields);
            if (count($dataFormats)) {
                $id = $this->getModel()->insert($dataFormats['data'], $dataFormats['formats']);
            }
        }
        return $id;
    }

    /**
     * Update table
     *
     * @param Integer $id     Record id to update
     * @param Array   $form   Data to insert
     * @param Array   $fields Name and formats
     * @return Integer $id Id of the inserted record
     */
    public function update($id = 0, $form = [], $fields = []) {
        if (count($fields)) {
            $dataFormats = $this->createDataAndFormats($form, $fields);
            if (count($dataFormats)) {
                $id = $this->getModel()->updateById(
                    $id,
                    $dataFormats['data'],
                    $dataFormats['formats']
                );
            }
        }
        return $id;
    }

    /**
     * View
     *
     * @param Integer $id Album id
     */
    public function view($id = 0) {
        // Children model
        $songModel = new SongModel('songs');
        $buttonModel = new ButtonModel('buttons');
        // This process has been added here to avoid the process to redirect to this page from add or edit page
        if (isset($_POST['submit'])) {
            if ( ! isset( $_POST['custom_form_nonce'] ) || ! wp_verify_nonce( $_POST['custom_form_nonce'], 'save_custom_form_action' ) ) {
                wp_die( 'Security check failed. Please try again.' );
            }
            $fields = [
                [ 'name' => 'name',     'format' => '%s' ],
                [ 'name' => 'type_id',  'format' => '%d' ],
                [ 'name' => 'image_id', 'format' => '%d' ],
                [ 'name' => 'post_id',  'format' => '%d' ],
            ];
            $id = sanitize_text_field($_POST['id']);
            if ($id) {
                // Update the record
                $this->update($id, $_POST, $fields);
            }
            else {
                // Insert a new record
                $id = $this->insert($_POST, $fields);
            }
        }
        // Delete Button or Song
        if (isset($_POST['delete']) && isset($_POST['model'])) {
            // Song model
            if ('song' === $_POST['model']) {
                $this->setDeleteModel($songModel);
            }
            // Button model
            if ('button' === $_POST['model']) {
                $this->setDeleteModel($buttonModel);
            }
            $this->delete();
        }
        // Get the record
        $record = $this->getModel()->selectById($id);
        // Get the related songs
        $songs = $songModel->selectByAlbumId($id);
        // Get the related buttons
        $buttons = $buttonModel->selectByAlbumId($id);
        // Platforms
        $platformModel = new PlatformModel();
        $platforms = $platformModel->getListFormat();
        // Types
        $types = $this->getChildModel()->getListFormat();
        // Image section // Default album image
        $src = esc_url(ALBUM_URL . 'assets/images/default-cover.png');
        if ($record) {
            $src = wp_get_attachment_image_url($record->image_id, 'medium');
        }
        // View
        $this->getView()->view([
            'src'       => $src,
            'songs'     => $songs,
            'types'     => $types,
            'record'    => $record,
            'buttons'   => $buttons,
            'platforms' => $platforms,
        ]);
    }

    /**
     * Edit
     *
     * @param Integer $id Album id
     */
    public function edit($id = 0) {
        // Get the record
        $record = $this->getModel()->selectById($id);
        // Types
        $types = $this->getChildModel()->getOptionsFormat();
        // Images
        $src = esc_url(ALBUM_URL . 'assets/images/default-cover.png');
        $default_src = esc_url(ALBUM_URL . 'assets/images/default-cover.png');
        // Check if there is a record
        if ($record) {
            // Image section
            $src = wp_get_attachment_image_url($record->image_id, 'medium');
        }
        // View
        $this->getView()->edit(['record' => $record, 'src' => $src, 'types' => $types, 'default_src' => $default_src]);
    }
}
