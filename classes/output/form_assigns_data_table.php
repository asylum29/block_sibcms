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
                $table_row_data[] = '—';
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
