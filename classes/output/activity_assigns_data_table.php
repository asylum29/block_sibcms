<?php

namespace block_sibcms\output;

defined('MOODLE_INTERNAL') || die();

class activity_assigns_data_table implements \renderable
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
        $this->table_head[] = get_string('key38', 'block_sibcms');
        $this->table_head[] = get_string('key39', 'block_sibcms');
        $this->table_head[] = get_string('key40', 'block_sibcms');
        $this->table_head[] = get_string('key41', 'block_sibcms');
        $this->table_head[] = get_string('key42', 'block_sibcms');
        $this->table_head[] = get_string('key43', 'block_sibcms');

        $this->table_size = array('50%', '10%', '10%', '10%', '10%', '10%');

        $this->table_data = array();
        foreach ($course_data->assigns as $id => $assign) {
            $table_row_data = array();

            $content = $OUTPUT->pix_icon('icon', '', 'assign', array('class' => 'icon')) . $assign->name;
            if ($assign->visible) {
                $assignurl = "$CFG->wwwroot/mod/assign/view.php?id=$id";
                $content = \html_writer::link($assignurl, $content) . '&nbsp;';
            }
            if ($assign->nograde)
                $content .= $OUTPUT->pix_icon('nograde', '', 'block_sibcms', array('class' => 'iconsmall'));
            if ($assign->teamsubmission)
                $content .= $OUTPUT->pix_icon('i/users', '', '', array('class' => 'iconsmall'));
            $table_row_data[] = $content;

            $table_row_data[] = $assign->teamsubmission ? '—' : $assign->participants;
            $table_row_data[] = $assign->teamsubmission ? '—' : $assign->submitted;
            if (!$assign->teamsubmission) {
                $table_row_data[] = $this->percentformat_value($assign->submitted_persent, false);
            } else {
                $table_row_data[] = '—';
            }
            if (!$assign->teamsubmission && !$assign->nograde) {
                $content = $assign->graded;
                if ($assign->need_grading > 0) {
                    $content .= '&nbsp;' . $OUTPUT->pix_icon('alert', '', 'block_sibcms', array('class' => 'icon'));
                }
                $table_row_data[] = $content;
                $table_row_data[] = $this->percentformat_value($assign->graded_persent, false);
            } else {
                $table_row_data[] = '—';
                $table_row_data[] = '—';
            }
            $this->table_data[] = $table_row_data;
        }

        $result_row = array();
        $result_row[] = get_string('key63', 'block_sibcms');
        $result_row[] = $course_data->assigns_results->participants;
        $result_row[] = $course_data->assigns_results->submitted;
        $result_row[] = $this->percentformat_value($course_data->assigns_results->submitted_persent);
        $result_row[] = $course_data->assigns_results->graded;
        $result_row[] = $this->percentformat_value($course_data->assigns_results->graded_persent);
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
