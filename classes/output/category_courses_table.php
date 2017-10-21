<?php

namespace block_sibcms\output;

defined('MOODLE_INTERNAL') || die();

class category_courses_table implements \renderable
{
    public $courses;
    public $page;
    public $courses_count;
    public $category_id;

    public function __construct($category_id, $page)
    {
        $category = \coursecat::get($category_id);
        $this->category_id = $category_id;
        $this->page = $page;
        $courses_count = $category->get_courses_count(array('recursive' => true));
        $this->courses_count = $courses_count;
        $this->courses = $category->get_courses(array('recursive' => true, 'offset' => $page * 20, 'limit' => 20));
    }

}
