<?php

namespace block_sibcms\output;

defined('MOODLE_INTERNAL') || die();

class form_quiz_data_table implements \renderable
{
    public $table_head;
    public $table_size;
    public $table_data;

    /**
     * Quiz_data_table constructor.
     * @param $course_data
     */
    public function __construct($course_data)
    {
        if (count($course_data->quiz) == 0) {
            return '';
        }

        $this->table_head = array();
        // Test name
        $this->table_head[] = get_string('key46', 'block_sibcms');
        // Participant count
        $this->table_head[] = get_string('key47', 'block_sibcms');
        // Submitted attempts
        $this->table_head[] = get_string('key48', 'block_sibcms');

        $this->table_size = array(
            '70%',
            '15%',
            '15%'
        );

        $this->table_data = array();
        foreach ($course_data->quiz as $quiz) {
            $table_row_data = array();
            // Quiz name
            $table_row_data[] = $quiz->name;
            // Participant count
            $table_row_data[] = $quiz->participants;
            // Submitted attempts
            $table_row_data[] = $quiz->submitted;
            // Write data
            $this->table_data[] = $table_row_data;
        }

    }

}
