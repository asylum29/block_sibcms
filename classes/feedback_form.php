<?php

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

        // Course link
        $course_url = new \moodle_url('/course/view.php', array('id' => $course_data->id));
        $course_link = \html_writer::link($course_url, $course_data->fullname);
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
        $this->add_action_buttons($cancel = true, $submitlabel = get_string('key32', 'block_sibcms'));
    }

    function validation($data, $files)
    {
        $errors = parent::validation($data, $files);
        return $errors;
    }

}
