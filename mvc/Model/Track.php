<?php

class TrackModel extends Model {
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct('Tracks');
    }

    /**
     * Select by Release id function
     *
     * @param Integer $id Release id
     * @return Array Tracks Object Array
     */
    public function selectByReleaseId($id = 0) {
        return $this->getDb()->get_results("SELECT * FROM {$this->getTable()} WHERE release_id={$id}");
    }
}
