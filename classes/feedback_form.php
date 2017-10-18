<?php

namespace block_sibcms;

defined('MOODLE_INTERNAL') || die();

class feedback_form extends \moodleform
{

    public function definition()
    {
        $mform = $this->_form;
        $course_data = $this->_customdata['course_data'];

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
            $assign_table = new \html_table();
            $assign_table->head = array(
                get_string('key38', 'block_sibcms'),
                get_string('key39', 'block_sibcms'),
                get_string('key40', 'block_sibcms'),
                get_string('key41', 'block_sibcms'),
                get_string('key42', 'block_sibcms'),
                get_string('key43', 'block_sibcms'),
                get_string('key44', 'block_sibcms'),
                get_string('key45', 'block_sibcms')
            );
            $assign_table->size[0] = '20%';
            $assign_table->size[1] = '10%';
            $assign_table->size[2] = '10%';
            $assign_table->size[3] = '10%';
            $assign_table->size[4] = '10%';
            $assign_table->size[5] = '10%';
            $assign_table->size[6] = '15%';
            $assign_table->size[7] = '15%';
            foreach ($assigns as $assign) {
                $feedbacks = $assign->feedbacks;
                $feedbacks_names = array();
                foreach ($feedbacks as $feedback) {
                    $feedbacks_names[] = $feedback->get_name();
                }
                $submit_persent = ($assign->participants == 0) ?
                    '0%' :
                    format_float(100 * $assign->submitted / $assign->participants, 2) . '%';
                $graded_persent = ($assign->participants == 0) ?
                    '0%' :
                    format_float(100 * $assign->graded / $assign->participants, 2) . '%';
                $assign_table->data[] = array(
                    $assign->name,
                    $assign->participants,
                    $assign->submitted,
                    $submit_persent,
                    $assign->graded,
                    $graded_persent,
                    $assign->grade,
                    \html_writer::alist($feedbacks_names)
                );
            }
            $assign_table_str = \html_writer::table($assign_table);
            $mform->addElement('static', 'assign_table',
                get_string('key37', 'block_sibcms'),
                $assign_table_str
            );
        }

        // Quiz information
        $quiz = $course_data->quiz;
        if (count($quiz)) {
            $quiz_table = new \html_table();
            $quiz_table->head = array(
                get_string('key46', 'block_sibcms'),
                get_string('key47', 'block_sibcms'),
                get_string('key48', 'block_sibcms'),
                get_string('key49', 'block_sibcms')
            );
            $quiz_table->size[0] = '70%';
            $quiz_table->size[1] = '10%';
            $quiz_table->size[2] = '10%';
            $quiz_table->size[3] = '10%';
            foreach ($quiz as $test) {
                $name_str = $test->name;
                if ($test->noquestions) {
                    $name_str .= \html_writer::span('!', 'red');
                }
                $persents = ($test->countgrades == 0) ? '0%' : format_float(100 * $test->countgrades / $test->countusers, 2) . '%';
                $quiz_table->data[] = array(
                    $name_str,
                    $test->countusers,
                    $test->countgrades,
                    $persents
                );
            }
            $quiz_table_text = \html_writer::table($quiz_table);
            $mform->addElement('static', 'quiz_table', get_string('key36', 'block_sibcms'), $quiz_table_text);
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
