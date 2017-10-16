<?php

require_once('../../config.php');
require_once ('../../lib/coursecatlib.php');
require_once("$CFG->libdir/formslib.php");
$courseid = required_param('id', PARAM_INT);
$categoryid = required_param('category', PARAM_INT);

$PAGE->set_url(new moodle_url('/blocks/sibcms/course.php', array('id' => $courseid, 'category' => $categoryid)));

require_login(1);

$course = get_course($courseid);
$category = coursecat::get($categoryid);
$PAGE->set_heading($course->fullname);
$path = $category->get_parents();
foreach ($path as $parent) {
    $parent_category = coursecat::get($parent);
    $PAGE->navbar->add($parent_category->name, new moodle_url('/blocks/sibcms/category.php', array('id' => $parent_category->id)));
}
$PAGE->navbar->add($category->name, $category->has_children() ? new moodle_url('/blocks/sibcms/category.php', array('id' => $category->id)) : null);
$PAGE->navbar->add(get_string('key8', 'block_sibcms'), new moodle_url('/blocks/sibcms/courses.php', array('category' => $category->id)));
$PAGE->navbar->add($course->fullname, $PAGE->url);
$PAGE->set_title(get_string('key22', 'block_sibcms', array('name' => $course->fullname)));

$output = $PAGE->get_renderer('block_sibcms');

$customData = array(
    'coursedata' => block_sibcms\sibcms_api::get_course_data($course)
);
$mform = new block_sibcms\feedback_form(null, $customData);
if ($mform->is_cancelled()) {

    redirect(new moodle_url('/blocks/sibcms/courses.php', array('category' => $category->id)));

} else if ($data = $mform->get_data()) {
    \block_sibcms\sibcms_api::save_feedback(
        $data->id,
        $data->feedback,
        $data->comment,
        $data->result
    );
    redirect(new moodle_url('/blocks/sibcms/courses.php', array('category' => $category->id)));

} else {
    $last_feedback = block_sibcms\sibcms_api::get_last_course_feedback($courseid);

    echo $output->header();

    $mform->set_data(array(
        'id' => $courseid,
        'category' => $categoryid,
        'result' => $last_feedback->result,
        'feedback' => $last_feedback->feedback,
        'comment' => $last_feedback->comment
    ));
    $mform->display();

    echo $output->footer();
}

