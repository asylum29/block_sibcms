<?php

namespace block_sibcms;

defined('MOODLE_INTERNAL') || die();

class categories_statistic_table implements \renderable {
    
    public $recursive;
    public $categoryid;
    public $categories;

    public function __construct($categoryid, $recursivemode) {
        global $DB;
        $this->recursive = $recursivemode;
        $this->categoryid = $categoryid;
        $this->categories = sibcms_api::get_categories($categoryid, $recursivemode);
    }

}

class category_courses_table implements \renderable {
    
    public $courses;

    public function __construct($categoryid) {
        global $DB;
        $this->courses = sibcms_api::get_category_courses($categoryid, true);
    }

}
