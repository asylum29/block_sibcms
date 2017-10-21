<?php

namespace block_sibcms;

defined('MOODLE_INTERNAL') || die();

class sibcms_observers {

    public static function course_deleted($event) {
        sibcms_api::delete_feedbacks($event->objectid);
    }

}
