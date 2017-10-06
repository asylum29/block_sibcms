<?php

require_once('../../config.php');
require_once('locallib.php');

$categoryid = required_param('id', PARAM_INT);

$PAGE->set_url(new moodle_url('/blocks/sibcms/category.php', array('id' => $categoryid)));

require_login(1);

$PAGE->navbar->add(get_string('key5', 'block_sibcms'), new moodle_url('/blocks/sibcms/categories.php'));

$category = $DB->get_record('course_categories', array('id' => $categoryid));
$PAGE->set_heading($category->name);
$path = block_sibcms\sibcms_api::get_category_link_path($categoryid);
foreach ($path as $category) {
    $PAGE->navbar->add($category->name, $category->has_subcategories ? new moodle_url('/blocks/sibcms/categories.php', array('id' => $category->id)) : null);
}
$PAGE->navbar->add(get_string('key8', 'block_sibcms'), new moodle_url('/blocks/sibcms/category.php', array('id' => $category->id)));


$output = $PAGE->get_renderer('block_sibcms');

$category_table = new block_sibcms\category_courses_table($categoryid);

echo $output->header();

echo $output->render($category_table);

echo $output->footer();
