<?php

namespace block_sibcms\output;

defined('MOODLE_INTERNAL') || die();

class category_courses_table implements \renderable {

    public $courses;
    public $page;
    public $coursescount;
    public $categoryid;

    public function __construct($categoryid, $page) {
        $category = \coursecat::get($categoryid);
        $this->categoryid = $categoryid;
        $this->page = $page;
        $courses_count = $category->get_courses_count(array('recursive' => true));
        $this->coursescount = $courses_count;
        $this->courses = $category->get_courses(array('recursive' => true, 'offset' => $page * 20, 'limit' => 20));
    }

}
