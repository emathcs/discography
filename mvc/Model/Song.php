<?php

class SongModel extends Model {
    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct('songs');
    }

    /**
     * Select by Album id function
     *
     * @param Integer $id Album id
     * @return Array Songs Object Array
     */
    public function selectByAlbumId($id = 0) {
        return $this->getDb()->get_results("SELECT * FROM {$this->getTable()} WHERE album_id={$id}");
    }
}
