<?php

class ButtonModel extends Model {
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct('buttons');
    }

    /**
     * Select by Release id function
     *
     * @param Integer $id Release id
     * @return Array Buttons Object Array
     */
    public function selectByReleaseId($id = 0) {
        return $this->getDb()->get_results("SELECT * FROM {$this->getTable()} WHERE album_id={$id}");
    }

    /**
     * Select by Song id function
     *
     * @param Integer $id Song id
     * @return Array Buttons Object Array
     */
    public function selectBySongId($id = 0) {
        return $this->getDb()->get_results("SELECT * FROM {$this->getTable()} WHERE song_id={$id}");
    }
}
