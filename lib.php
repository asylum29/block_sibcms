<?php

// Ссылка на страницу мониторинга курса
function block_sibcms_extend_navigation_course(navigation_node $navigation, $course, $context) {
    if (has_capability('block/sibcms:monitoring', $context)) {
        $url = new moodle_url('/blocks/sibcms/course.php', array('id' => $course->id, 'category' => $course->category));
        $navigation_reports = $navigation->find('coursereports', 90);
        $navigation_reports->add(get_string('key20', 'block_sibcms'), $url, navigation_node::TYPE_SETTING, null, null, new pix_icon('i/report', ''));
    }
}