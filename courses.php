<?php

require_once('../../config.php');
require_once ('../../lib/coursecatlib.php');

$category_id = required_param('category', PARAM_INT);
$page = optional_param('page', 0, PARAM_INT);

$PAGE->set_url(new moodle_url('/blocks/sibcms/courses.php', array('category' => $category_id)));

require_login(1);

require_capability('block/sibcms:monitoring', context_system::instance());

// Ссылка на корень системы
$PAGE->navbar->add(get_string('key21', 'block_sibcms'), new moodle_url('/blocks/sibcms/category.php'));
//$PAGE->navbar->add(get_course(1)->shortname, new moodle_url('/blocks/sibcms/category.php'));

$category = coursecat::get($category_id);
$PAGE->set_heading($category->name);
$path = $category->get_parents();
foreach ($path as $parent) {
    $parent_category = coursecat::get($parent);
    $PAGE->navbar->add($parent_category->name,
        new moodle_url('/blocks/sibcms/category.php', array('id' => $parent_category->id)));
}
$PAGE->navbar->add($category->name,
    $category->has_children() ?
        new moodle_url('/blocks/sibcms/category.php', array('id' => $category->id)) :
        null);
$PAGE->navbar->add(get_string('key8', 'block_sibcms'), $PAGE->url);
$PAGE->set_title(get_string('key22', 'block_sibcms', array('name' => $category->name)));

$output = $PAGE->get_renderer('block_sibcms');

$category_table = new block_sibcms\output\category_courses_table($category_id, $page);

echo $output->header();

echo $output->render($category_table);

echo $output->footer();
