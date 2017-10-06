<?php

namespace block_sibcms\output;

defined('MOODLE_INTERNAL') || die();

class moderating_categories_table implements \renderable {
    
    public $categories;

    public function __construct($userid) {
        $this->categories = sibcms_api::get_user_categories($userid);
    }

}
