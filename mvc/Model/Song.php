<?php

class SongModel extends Model {
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct('songs');
    }

    /**
     * Select by Release id function
     *
     * @param Integer $id Release id
     * @return Array Songs Object Array
     */
    public function selectByReleaseId($id = 0) {
        return $this->getDb()->get_results("SELECT * FROM {$this->getTable()} WHERE album_id={$id}");
    }
}
