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
            $verify = isset($fields[$i]['verify']);
            if (!$verify || ($verify && $fields[$i]['verify'])) {
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
     * Security verification
     *
     * @param Array $form Form to validate
     */
    public function securityVerification($form = []) {
        if ( ! isset( $form['custom_form_nonce'] ) || ! wp_verify_nonce( $form['custom_form_nonce'], 'custom_form_action' ) ) {
            wp_die( 'Security check failed. Please try again.' );
        }
    }

    /**
     * Insert into table
     *
     * @param Array $form   Data to insert
     * @param Array $fields Name and formats
     * @return Integer $id Record id, -1, 0 or false
     */
    public function insert($form = [], $fields = []) {
        $this->securityVerification($form);
        $id = -1;
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
     * @return Integer $status -1, 0, 1 or false
     */
    public function update($id = 0, $form = [], $fields = []) {
        $this->securityVerification($form);
        $status = -1;
        if (count($fields)) {
            $dataFormats = $this->createDataAndFormats($form, $fields);
            if (count($dataFormats)) {
                $status = $this->getModel()->updateById(
                    $id,
                    $dataFormats['data'],
                    $dataFormats['formats']
                );
            }
        }
        return $status;
    }

    /**
     * Delete function
     *
     * @return Boolean Status of the operation
     */
    public function delete() {
        if (isset( $_POST['delete'])) {
            if (!isset( $_POST['custom_form_nonce']) || !wp_verify_nonce($_POST['custom_form_nonce'], 'custom_form_action')) {
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

    /**
     * Get notification after insert, update or delete a record
     *
     * @param String  $process Process: insert, update or delete
     * @param Integer $status Status of the process: -1, 0, 1 or false on error
     * @return Array [Message, Status]
     */
    public function getNotification($process = '', $status = 0) {
        $message = [];
        if (strlen($process)) {
            if ('update' === $process) {
                $messages = [
                    'The record was not updated, check the values have the right type.',
                    'Action succeeded, but values were already identical.',
                    'The record was updated.',
                ];
                if (false === $status) {
                    $message = [
                        'message' => 'There was an error when the program tried to update the record.',
                        'status'  => 0,
                    ];
                }
                else {
                    $message = [
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
                        $message = [
                            'message' => 'There was an error when the program tried to store the record.',
                            'status'  => 0,
                        ];
                    }
                    else {
                        $message = [
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
                            $message = [
                                'message' => 'There was an error when the program tried to store the record.',
                                'status'  => 0,
                            ];
                        }
                        else {
                            $message = [
                                'message' => $messages[$status],
                                'status'  => $status < 0 ? 0 : $status,
                            ];
                        }
                    }
                }
            }
        }
        return $message;
    }
}
