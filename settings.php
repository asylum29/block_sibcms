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
 * @copyright  2017 Sergey Shlyanin, Aleksandr Raetskiy <ksenon3@mail.ru>
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die;

require_once($CFG->dirroot . '/mod/assign/adminlib.php');

if ($ADMIN->fulltree) {

    $name = get_string('key23', 'block_sibcms');
    $description = get_string('key78', 'block_sibcms');
    $settings->add(new admin_setting_configduration(
        'block_sibcms/no_errors_relevance_duration',
        $name,
        $description,
        15 * 24 * 60 * 60 // 15 days
    ));

    $name = get_string('key24', 'block_sibcms');
    $description = get_string('key79', 'block_sibcms');
    $settings->add(new admin_setting_configduration(
        'block_sibcms/not_critical_errors_relevance_duration',
        $name,
        $description,
        7 * 24 * 60 * 60 // 7 days
    ));

    $name = get_string('key25', 'block_sibcms');
    $description = get_string('key80', 'block_sibcms');
    $settings->add(new admin_setting_configduration(
        'block_sibcms/critical_errors_relevance_duration',
        $name,
        $description,
        3 * 24 * 60 * 60 // 3 days
    ));

    $name = get_string('key26', 'block_sibcms');
    $description = get_string('key81', 'block_sibcms');
    $settings->add(new admin_setting_configduration(
        'block_sibcms/empty_course_relevance_duration',
        $name,
        $description,
        3 * 24 * 60 * 60 // 3 days
    ));

}
