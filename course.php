<?php

require_once('../../config.php');
require_once('../../lib/coursecatlib.php');
require_once("$CFG->libdir/formslib.php");

$course_id   = required_param('id', PARAM_INT);
$category_id = required_param('category', PARAM_INT);
$page        = optional_param('page', 0, PARAM_INT);

$PAGE->set_url(new moodle_url('/blocks/sibcms/course.php', array('id' => $course_id, 'category' => $category_id)));

require_login(1);

require_capability('block/sibcms:monitoring', context_system::instance());

$course = get_course($course_id);
$category = coursecat::get($category_id);
$PAGE->set_heading($course->fullname);
$PAGE->navbar->ignore_active();
$PAGE->navbar->add(get_string('key21', 'block_sibcms'), new moodle_url('/blocks/sibcms/category.php'));
$path = $category->get_parents();
foreach ($path as $parent) {
    $parent_category = coursecat::get($parent);
    $PAGE->navbar->add($parent_category->name, new moodle_url('/blocks/sibcms/category.php', array('id' => $parent_category->id)));
}
$PAGE->navbar->add($category->name,
    $category->has_children() ?
        new moodle_url('/blocks/sibcms/category.php', array('id' => $category->id)) :
        null);
$PAGE->navbar->add(get_string('key8', 'block_sibcms'),
    new moodle_url('/blocks/sibcms/courses.php', array('category' => $category->id)));
$PAGE->navbar->add($course->fullname, $PAGE->url);
$PAGE->set_title(get_string('key22', 'block_sibcms', array('name' => $course->fullname)));

$output = $PAGE->get_renderer('block_sibcms');

$custom_data = array(
    'course_data' => block_sibcms\sibcms_api::get_course_data($course)
);
$mform = new block_sibcms\feedback_form(null, $custom_data);

if ($mform->is_cancelled()) {
    redirect(new moodle_url('/blocks/sibcms/courses.php', array('category' => $category->id, 'page' => $page)));
} else if ($data = $mform->get_data()) {
    \block_sibcms\sibcms_api::save_feedback(
        $data->id,
        $data->feedback,
        $data->comment,
        $data->result
    );
    $event = \block_sibcms\event\comment_created::create(array('objectid' => $course_id, 'other' => array('category' => $category_id)));
    $event->trigger();
    redirect(new moodle_url('/blocks/sibcms/courses.php', array('category' => $category->id, 'page' => $page)));
} else {
    echo $output->header();

    $params = array(
        'id'       => $course_id,
        'category' => $category_id,
        'page'     => $page,
        'result'   => '',
        'feedback' => '',
        'comment'  => ''
    );
    $last_feedback = block_sibcms\sibcms_api::get_last_course_feedback($course_id);
    if ($last_feedback) {
        $params['result']   = $last_feedback->result;
        $params['feedback'] = $last_feedback->feedback;
        $params['comment']  = $last_feedback->comment;
    }

    $mform->set_data($params);
    $mform->display();

    echo $output->footer();
}
