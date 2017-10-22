<?php

namespace block_sibcms\event;
defined('MOODLE_INTERNAL') || die();

class monitoring_viewed extends \core\event\base {

    protected function init() {
        $this->data['crud'] = 'r';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
        $this->data['objecttable'] = 'course_categories';
    }

    public static function get_name() {
        return get_string('key70', 'block_sibcms');
    }

    public function get_description() {
        return "The user with id '$this->userid' viewed the monitoring report for the course with id '$this->courseid'.";
    }

    public function get_url() {
        return new \moodle_url('/blocks/sibcms/report.php', array('id' => $this->courseid, 'category' => $this->data['objectid']));
    }

}
