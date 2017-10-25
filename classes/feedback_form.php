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

namespace block_sibcms;

use block_sibcms\output\form_assigns_data_table;
use block_sibcms\output\form_quiz_data_table;

defined('MOODLE_INTERNAL') || die();

class feedback_form extends \moodleform
{

    public function definition()
    {
        global $PAGE;
        $mform = $this->_form;
        $course_data = $this->_customdata['course_data'];
        $renderer = $PAGE->get_renderer('block_sibcms');

        // Course ID
        $mform->addElement('hidden', 'id', null);
        $mform->setType('id', PARAM_INT);

        // Category ID
        $mform->addElement('hidden', 'category', null);
        $mform->setType('category', PARAM_INT);

        // Return url
        $mform->addElement('hidden', 'returnurl', null);
        $mform->setType('returnurl', PARAM_URL);

        // Course link
        $course_url = new \moodle_url('/course/view.php', array('id' => $course_data->id));
        $course_link = \html_writer::link($course_url, $course_data->fullname, array('target' => '_blank'));
        $mform->addElement('static', 'course_fullname', get_string('key27', 'block_sibcms'), $course_link);

        // Graders
        $graders = $course_data->graders;
        if (count($graders)) {
            $graders_str = \html_writer::start_tag('ul');
            foreach ($graders as $grader) {
                $grader_link = \html_writer::link(
                    new \moodle_url('/user/profile.php', array('id' => $grader->id)),
                    fullname($grader));
                $time_ago = get_string('never');
                if ($grader->lastcourseaccess) {
                    $time_ago = format_time(time() - $grader->lastcourseaccess);
                }
                $grader_text = "$grader_link ($time_ago)";
                $graders_str .= \html_writer::tag('li', $grader_text);
            }
            $graders_str .= \html_writer::end_tag('ul');
            $mform->addElement('static', 'graders',
                get_string('key28', 'block_sibcms'),
                $graders_str
            );
        }

        // Hints
        $hints = sibcms_api::get_hints($course_data);
        $hints_str = '';
        if (count($hints)) {
            $hints_str = \html_writer::alist($hints);
        } else {
            $hints_str .= get_string('key58', 'block_sibcms');
        }
        $mform->addElement('static', 'autohints',
            get_string('key29', 'block_sibcms'),
            $hints_str
        );

        // Assigns information
        $assigns = $course_data->assigns;
        if (count($assigns)) {
            $assign_table = new form_assigns_data_table($course_data);
            $assign_table_str = $renderer->render($assign_table);
            $mform->addElement('static', 'assign_table',
                get_string('key37', 'block_sibcms'),
                $assign_table_str
            );
        }

        // Quiz information
        $quiz = $course_data->quiz;
        if (count($quiz)) {

            $quiz_table = new form_quiz_data_table($course_data);
            $quiz_table_str = $renderer->render($quiz_table);
            $mform->addElement('static', 'quiz_table',
                get_string('key37', 'block_sibcms'),
                $quiz_table_str
            );

        }

        // Course status
        $results = array();
        $results['none'] = get_string('key31', 'block_sibcms');
        $results[0] = get_string('key23', 'block_sibcms');
        $results[1] = get_string('key24', 'block_sibcms');
        $results[2] = get_string('key25', 'block_sibcms');
        $results[3] = get_string('key26', 'block_sibcms');

        $select = $mform->addElement('select', 'result', get_string('key33', 'block_sibcms'), $results, null);
        $mform->addRule('result', get_string('key30', 'block_sibcms'), 'numeric', null, 'server');
        $mform->addRule('result', get_string('key30', 'block_sibcms'), 'required', null, 'server');
        $select->setMultiple(false);

        // Feedback textarea
        $mform->addElement('textarea', 'feedback', get_string('key34', 'block_sibcms'), 'wrap="virtual" cols="50" rows="8"');

        // Comment textarea
        $mform->addElement('textarea', 'comment', get_string('key35', 'block_sibcms'), 'wrap="virtual" cols="50" rows="3"');

        // Buttons
        $buttonarray = array();
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton', get_string('key32', 'block_sibcms'));
        $buttonarray[] = &$mform->createElement('submit', 'submitbutton2', get_string('key84', 'block_sibcms'));
        $buttonarray[] = &$mform->createElement('cancel');
        $mform->addGroup($buttonarray, 'buttonar', '', array(' '), false);
        $mform->setType('buttonar', PARAM_RAW);
        $mform->closeHeaderBefore('buttonar');
    }

    function validation($data, $files)
    {
        $errors = parent::validation($data, $files);
        return $errors;
    }

}
