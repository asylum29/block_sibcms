<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * block_sibcms
 *
 * @package    block_sibcms
 * @copyright  2017 Sergey Shlyanin <sergei.shlyanin@gmail.com>, Aleksandr Raetskiy <ksenon3@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

require_once('../../config.php');

$PAGE->set_url(new moodle_url('/blocks/sibcms/properties.php'));

require_login(1);

require_capability('block/sibcms:monitoring', context_system::instance());

$PAGE->set_heading(get_string('key105', 'block_sibcms'));
$PAGE->navbar->ignore_active();
$PAGE->navbar->add(get_string('key21', 'block_sibcms'), new moodle_url('/blocks/sibcms/category.php'));

$PAGE->navbar->add(get_string('key105', 'block_sibcms'), new moodle_url('/blocks/sibcms/properties.php'));


$show = optional_param('show', 0, PARAM_INT);
$hide = optional_param('hide', 0, PARAM_INT);
$delete = optional_param('delete', 0, PARAM_INT);
$id = $show ? $show : ($hide ? $hide : $delete);

$name = optional_param('name', null, PARAM_TEXT);

$return_url  = optional_param('returnurl',
    new moodle_url('/blocks/sibcms/properties.php'), PARAM_URL);


$properties = \block_sibcms\sibcms_api::get_properties(false);

if ($id) {
    if (!array_key_exists($id, $properties)) {
        print_error('invalidargument');
    }
}

if ($show && confirm_sesskey()) {
    if ($properties[$id]->hidden) {
        $properties[$id]->hidden = 0;
        $DB->update_record('block_sibcms_properties', $properties[$id]);
    }
    redirect($return_url);
} else if ($hide && confirm_sesskey()) {
    if (!$properties[$id]->hidden) {
        $properties[$id]->hidden = 1;
        $DB->update_record('block_sibcms_properties', $properties[$id]);
    }
    redirect($return_url);
} else if ($delete && confirm_sesskey()) {
    \block_sibcms\sibcms_api::delete_property($id);
    redirect($return_url);
} else if ($name && confirm_sesskey()) {
    \block_sibcms\sibcms_api::add_property($name);
    redirect($return_url);
}


$output = $PAGE->get_renderer('block_sibcms');

$properties_table = new \block_sibcms\output\properties_table();

echo $output->header();

echo $output->render($properties_table);
echo $output->display_property_create_form();

echo $output->footer();