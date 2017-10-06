<?php

require_once('../../config.php');
require_once('locallib.php');

$categoryid = optional_param('id', 0, PARAM_INT);
$recursivemode = optional_param('recursive', false, PARAM_BOOL);

$PAGE->set_url(new moodle_url('/blocks/sibcms/categories.php', array('category' => $categoryid, 'recursive' => $recursivemode)));

require_login(1);

$PAGE->navbar->add(get_string('key5', 'block_sibcms'), new moodle_url('/blocks/sibcms/categories.php', array('recursive' => $recursivemode)));

if ($categoryid == 0) {
    $PAGE->set_heading(get_string('key5', 'block_sibcms'));
} else {
    $category = $DB->get_record('course_categories', array('id' => $categoryid));
    $PAGE->set_heading($category->name);
    $path = block_sibcms\sibcms_api::get_category_link_path($categoryid);
    foreach ($path as $category) {
        $PAGE->navbar->add($category->name, 
            $category->has_subcategories ? new moodle_url('/blocks/sibcms/categories.php', array(
                'id' => $category->id,
                'recursive' => $recursivemode
            )) : null);
    }
}

$output = $PAGE->get_renderer('block_sibcms');

$categories_table = new block_sibcms\categories_statistic_table($categoryid, $recursivemode);

echo $output->header();

echo $output->render($categories_table);

echo $output->footer();
