<?php

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
