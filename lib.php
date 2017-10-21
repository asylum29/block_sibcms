<?php

defined('MOODLE_INTERNAL') || die;

require_once($CFG->libdir.'/coursecatlib.php');

// Link to feedback form page
function block_sibcms_extend_navigation_course(navigation_node $navigation, $course, $context)
{
    $monitoring        = has_capability('block/sibcms:activity_report', context_system::instance());
    $activity_report   = has_capability('block/sibcms:activity_report', $context);
    $monitoring_report = has_capability('block/sibcms:monitoring_report', $context) &&
                         count(coursecat::make_categories_list('block/sibcms:monitoring_report_category')) > 0;

    if ($monitoring || $activity_report || $monitoring_report) {
        $navigation = $navigation->add(get_string('key20', 'block_sibcms'), null, navigation_node::TYPE_CONTAINER, null, 'sibcms_reports', new pix_icon('i/stats', ''));
    }

    if ($activity_report) {
        $url = new moodle_url('/blocks/sibcms/report.php', array('id' => $course->id));
        $navigation->add(get_string('key61', 'block_sibcms'), $url, navigation_node::TYPE_SETTING, null, null, new pix_icon('i/report', ''));
    }

    if ($monitoring_report) {
        $category_id = optional_param('category', 0, PARAM_INT);
        $url = new moodle_url('/blocks/sibcms/report.php', array('id' => $course->id, 'category' => $category_id));
        $navigation->add(get_string('key21', 'block_sibcms'), $url, navigation_node::TYPE_SETTING, null, null, new pix_icon('i/report', ''));
    }

    if ($monitoring) {
        $url = new moodle_url('/blocks/sibcms/course.php', array('id' => $course->id, 'category' => $course->category));
        $navigation->add(get_string('key19', 'block_sibcms'), $url, navigation_node::TYPE_SETTING, null, null, new pix_icon('monitoring', '', 'block_sibcms'));
    }
}

function block_sibcms_extend_navigation_frontpage(navigation_node $navigation, $course, $context)
{
    block_sibcms_extend_navigation_course($navigation, $course, $context);
}
