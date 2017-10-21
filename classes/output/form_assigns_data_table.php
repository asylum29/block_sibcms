<?php

namespace block_sibcms\output;

defined('MOODLE_INTERNAL') || die();

class form_assigns_data_table implements \renderable
{
    public $table_head;
    public $table_size;
    public $table_data;

    /**
     * Assigns_data_table constructor.
     * @param $course_data
     */
    public function __construct($course_data)
    {
        //print_object($course_data);
        if (count($course_data->assigns) == 0) {
            return '';
        }

        $this->table_head = array();
        // Assign name
        $this->table_head[] = get_string('key38', 'block_sibcms');
        // Participant count
        $this->table_head[] = get_string('key39', 'block_sibcms');
        // Submited files
        $this->table_head[] = get_string('key40', 'block_sibcms');
        // Graded files
        $this->table_head[] = get_string('key42', 'block_sibcms');
        // Grade type
        $this->table_head[] = get_string('key44', 'block_sibcms');
        // Feedback types
        $this->table_head[] = get_string('key45', 'block_sibcms');

        $this->table_size = array(
            '40%',
            '10%',
            '10%',
            '10%',
            '20%',
            '20%'
        );

        $this->table_data = array();
        foreach ($course_data->assigns as $assign) {
            $table_row_data = array();
            // Assign name
            $table_row_data[] = $assign->name;
            // Participant count
            $table_row_data[] = $assign->teamsubmission ? '—' : $assign->participants;
            // Submited files
            $table_row_data[] = $assign->teamsubmission ? '—' : $assign->submitted;
            // Graded files
            if (!$assign->teamsubmission && !$assign->nograde) {
                $table_row_data[] = $assign->graded;
            } else {
                $table_row_data[] = '-';
            }
            // Grade type
            $table_row_data[] = $assign->grade;
            // Feedback types
            $feedbacks = $assign->feedbacks;
            $feedbacks_names = array();
            foreach ($feedbacks as $feedback) {
                $feedbacks_names[] = $feedback->get_name();
            }
            $table_row_data[] = \html_writer::alist($feedbacks_names);
            // Write data
            $this->table_data[] = $table_row_data;
        }

    }

}
