<?php

defined('MOODLE_INTERNAL') || die();

$observers = array(

    array(
        'eventname' => '\core\event\course_deleted',
        'callback'  => '\block_sibcms\sibcms_observers::course_deleted',
    ),

);
