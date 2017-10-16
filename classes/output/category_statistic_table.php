<?php

namespace block_sibcms\output;

defined('MOODLE_INTERNAL') || die();

class category_statistic_table implements \renderable {

    public $categoryid;
    public $categories;

    public function __construct($categoryid) {
        global $DB;
        $this->categoryid = $categoryid;

        $parent_category = \coursecat::get($categoryid);

        $categories = $parent_category->get_children();

        $this->categories = array();
        foreach ($categories as $category) {
            $cat = new \stdClass();
            $cat->id = $category->id;
            $cat->name = $category->name;
            $courses = $category->get_courses(array('recursive' => true));
            $cat->courses_total = count($courses);
            $cat->courses_need_attention = 0;
            foreach ($courses as $course) {
                if (\block_sibcms\sibcms_api::need_attention($course->id)) {
                    $cat->courses_need_attention++;
                }
            }
            $cat->has_subcategories = $category->has_children();
            $this->categories[] = $cat;
        }
    }

}
