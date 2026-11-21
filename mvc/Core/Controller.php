<?php

class Controller {
    /**
     * Model
     */
    private $Model;

    /**
     * Child model
     */
    private $ChildModel;

    /**
     * Delete model
     */
    private $DeleteModel;

    /**
     * View
     */
    private $View;

    /**
     * Constructor
     *
     * @param Object $Model Object to manage the database table operations
     * @param Object $View  Object to render HTML
     * @param Object $ChildModel Object to manage the related database table operations
     */
    public function __construct($Model = null, $View = null, $ChildModel = null) {
        // Model
        $this->setModel($Model);
        $this->setChildModel($ChildModel);
        $this->setDeleteModel(null);
        // View
        $this->setView($View);
    }

    /**
     * Set Model
     *
     * @param Object $Model Object to manage the database table operations
     */
    public function setModel($Model = null) {
        $this->Model = $Model;
    }

    /**
     * Get Model
     *
     * @return Object Object to manage the database table operations
     */
    public function getModel() {
        return $this->Model;
    }

    /**
     * Set Child Model
     *
     * @param Object $ChildModel Object to manage the related database table operations
     */
    public function setChildModel($ChildModel = null) {
        $this->ChildModel = $ChildModel;
    }

    /**
     * Get Child Model
     *
     * @$return Object Object to manage the related database table operations
     */
    public function getChildModel() {
        return $this->ChildModel;
    }

    /**
     * Set Delete Model
     *
     * @param Object $Model Object to manage the database table operations
     */
    public function setDeleteModel($Model = null) {
        $this->DeleteModel = $Model;
    }

    /**
     * Get Delete Model
     *
     * @return Object Object to manage the database table operations
     */
    public function getDeleteModel() {
        return $this->DeleteModel;
    }

    /**
     * Set View
     *
     * @param Object $View Object to render HTML
     */
    public function setView($View = null) {
        $this->View = $View;
    }

    /**
     * Get View
     *
     * @param Object Object to render HTML
     */
    public function getView() {
        return $this->View;
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
            $data[$fields[$i]['name']] = sanitize_text_field(wp_unslash($form[$fields[$i]['name']]));
            // Process to check if the field could be empty
            $verify = isset($fields[$i]['name']);
            if (!$verify || ($verify && $data[$fields[$i]['name']])) {
                $condition = $condition && !empty($data[$fields[$i]['name']]);
            }
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
     * Delete function
     *
     * @return Boolean Status of the operation
     */
    public function delete() {
        if (isset( $_POST['delete'])) {
            if (!isset( $_POST['custom_form_nonce']) || !wp_verify_nonce($_POST['custom_form_nonce'], 'delete_custom_form_action')) {
                wp_die('Security check failed. Please try again.');
            }
            $deleteModel = $this->getDeleteModel();
            if (!$deleteModel) {
                return 0;
            }
            $id = sanitize_text_field($_POST['id']);
            return $this->getDeleteModel()->deleteById($id);
        }
    }
}
