<?php

class StatusModel extends SmallModel {
    /**
     * Constructor
     */
    public function __construct() {
        $records = [
            [
                'id'   => 0,
                'name' => 'Disabled'
            ],
            [
                'id'   => 1,
                'name' => 'Enabled'
            ],
        ];
        parent::__construct($records);
    }
}
