<?php

namespace block_sibcms\event;
defined('MOODLE_INTERNAL') || die();

class comment_created extends \core\event\base {

    protected function init() {
        $this->context = \context_system::instance();
        $this->data['crud'] = 'u';
        $this->data['edulevel'] = self::LEVEL_TEACHING;
        $this->data['objecttable'] = 'block_sibcms_feedbacks';
    }

    public static function get_name() {
        return get_string('key71', 'block_sibcms');
    }

    public function get_description() {
        return "The user with id '{$this->userid}' has left the comment for the course with id '{$this->data['objectid']}'.";
    }

    public function get_url() {
        return new \moodle_url('/blocks/sibcms/course.php', array('id' => $this->data['objectid'], 'category' => $this->other['category']));
    }

}
