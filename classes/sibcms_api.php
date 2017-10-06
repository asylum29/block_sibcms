<?php

namespace block_sibcms;

class sibcms_api {
    
    public static function get_category_courses($categoryid, $recursive = false) {
        global $DB;
        
        $courses = $DB->get_records('course', array('category' => $categoryid));
        foreach ($courses as $course) {
            if (sibcms_api::need_attention($course->id)) {
                $course->need_attention = true;
            }
            $course->last_feedback = sibcms_api::get_last_feedback($course->id);
        }
    
        if ($recursive) {
            $subcategories = $DB->get_records('course_categories', array('parent' => $categoryid));
            foreach ($subcategories as $subcategory) {
                $subcourses = sibcms_api::get_category_courses($subcategory->id, $recursive);
                $courses = array_merge($courses, $subcourses);
            }
        }

        return $courses;

    }

    public static function get_couse_feedbacks($courseid) {
        global $DB;

        $feedbacks = $DB->get_records('block_sibcms_feedbacks', array('courseid' => $courseid));

        return $feedbacks;
    }

    public static function get_last_feedback($courseid) {
        global $DB;

        $sql = 'SELECT *
                FROM {block_sibcms_feedbacks}
                WHERE courseid = ?
                ORDER BY timecreated DESC';
        $feedback = $DB->get_record_sql($sql, array($courseid), 0, 1);

        return $feedback;

    }

    public static function get_categories($parent, $recursive = false) {
        global $DB;
        $categories = $DB->get_records('course_categories', array('parent' => $parent));
        $result = array();
        foreach ($categories as $category) {
            $category->courses = sibcms_api::get_category_courses($category->id, true);
            $category->courses_need_attention = 0;
            foreach ($category->courses as $course) {
                if (sibcms_api::need_attention($course->id)) {
                    $category->courses_need_attention++;
                }
            }
            $subcategories = sibcms_api::get_categories($category->id, $recursive);
            $category->has_subcategories = count($subcategories) > 0;
            $result[] = $category;
            if ($recursive) {
                foreach ($subcategories as $subcategory) {
                    $subcategory->name = '... / ' . $subcategory->name;
                    $result[] = $subcategory;
                }
            }
        }
        return $result;
    }

    public static function get_category_link_path($categoryid) {
        global $DB;
        $result = array();
        $category = $DB->get_record('course_categories', array('id' => $categoryid));
        $subcategories = sibcms_api::get_categories($category->id);
        $category->has_subcategories = count($subcategories) > 0;
        $result[] = $category;
        if ($category->parent != 0) {
            $path = sibcms_api::get_category_link_path($category->parent);
            $result = array_merge($path, $result);
        }
        return $result;
    }

    public static function need_attention($courseid) {
        global $DB;
        $lastfeedback = sibcms_api::get_last_feedback($courseid);
        //Если нет отзыва
        if (!$lastfeedback) { return true; }
        //Если курс без замечаний - 15 дней
        if ($lastfeedback->result == 0 && time() - $lastfeedback->timecreated > 3600 * 24 * 15) {
            return true;
        }
        //Если курс с незначительными замечаниями - 7 дней
        if ($lastfeedback->result == 1 && time() - $lastfeedback->timecreated > 3600 * 24 * 7) {
            return true;
        }
        //Если курс с критическими замечаниями - 3 дня
        if ($lastfeedback->result == 2 && time() - $lastfeedback->timecreated > 3600 * 24 * 3) {
            return true;
        }
        return false;
    }


}
