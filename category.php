<?php

require_once('../../config.php');
require_once ('../../lib/coursecatlib.php');

$categoryid = optional_param('id', 0, PARAM_INT);

$PAGE->set_url(new moodle_url('/blocks/sibcms/category.php', array('category' => $categoryid)));

require_login(1);

// Ссылка на корень системы
$PAGE->navbar->add(get_string('key21', 'block_sibcms'), new moodle_url('/blocks/sibcms/category.php'));
//$PAGE->navbar->add(get_course(1)->shortname, new moodle_url('/blocks/sibcms/category.php', array('recursive' => $recursivemode)));

if ($categoryid == 0) {
    $PAGE->set_heading(get_course(1)->shortname);
    $PAGE->set_title(get_string('key22', 'block_sibcms', array('name' => get_course(1)->shortname)));
} else {
    $category = coursecat::get($categoryid);
    $PAGE->set_heading($category->name);
    $path = $category->get_parents();
    foreach ($path as $parent) {
        $parent_category = coursecat::get($parent);
        $PAGE->navbar->add($parent_category->name, new moodle_url('/blocks/sibcms/category.php', array('id' => $parent_category->id)));
    }
    $PAGE->navbar->add($category->name, $category->has_children() ? new moodle_url('/blocks/sibcms/category.php', array('id' => $category->id)) : null);
    $PAGE->set_title(get_string('key22', 'block_sibcms', array('name' => $category->name)));
}


$output = $PAGE->get_renderer('block_sibcms');

$categories_table = new block_sibcms\output\category_statistic_table($categoryid);

echo $output->header();

echo $output->render($categories_table);

echo $output->footer();
