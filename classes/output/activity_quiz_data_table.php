<?php

namespace block_sibcms\output;

defined('MOODLE_INTERNAL') || die();

class activity_quiz_data_table implements \renderable
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
        global $OUTPUT, $CFG;

        $this->table_head = array();
        $this->table_head[] = get_string('key46', 'block_sibcms');
        $this->table_head[] = get_string('key47', 'block_sibcms');
        $this->table_head[] = get_string('key48', 'block_sibcms');
        $this->table_head[] = get_string('key49', 'block_sibcms');

        $this->table_size = array('70%', '10%', '10%', '10%');

        $this->table_data = array();
        foreach ($course_data->quiz as $id => $quiz) {
            $table_row_data = array();

            $content = $OUTPUT->pix_icon('icon', '', 'quiz', array('class' => 'icon')) . $quiz->name;
            if ($quiz->visible) {
                $quizurl = "$CFG->wwwroot/mod/quiz/view.php?id=$id";
                $content = \html_writer::link($quizurl, $content) . '&nbsp;';
            }
            if ($quiz->noquestions)
                $content .= $OUTPUT->pix_icon('noquestions', get_string('key74', 'block_sibcms'), 'block_sibcms', array('class' => 'iconsmall'));
            $table_row_data[] = $content;

            $table_row_data[] = $quiz->participants;
            $table_row_data[] = $quiz->submitted;
            $table_row_data[] = $this->percentformat_value($quiz->submitted_persent, false);
            $this->table_data[] = $table_row_data;
        }

        $result_row = array();
        $result_row[] = get_string('key63', 'block_sibcms');
        $result_row[] = $course_data->quiz_results->participants;
        $result_row[] = $course_data->quiz_results->submitted;
        $result_row[] = $this->percentformat_value($course_data->quiz_results->submitted_persent);
        $this->table_data[] = $result_row;
    }

    private function percentformat_value($value, $color = true) {
        $class = '';
        $value *= 100;
        if ($color) {
            if ($value < 50) {
                $class = 'block_sibcms_red';
            } else if ($value < 85) {
                $class = 'block_sibcms_yellow';
            } else {
                $class = 'block_sibcms_green';
            }
        }
        return \html_writer::start_span($class) . format_float($value, 2, true, true) . '%' . \html_writer::end_span();
    }
}
