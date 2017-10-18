<?php


namespace block_sibcms\output;

defined('MOODLE_INTERNAL') || die();

class renderer extends \plugin_renderer_base
{

    /**
     * Render the table with subcategories and course count for selected category
     * @param category_statistic_table $widget
     * @return string
     */
    public function render_category_statistic_table(category_statistic_table $widget)
    {
        global $OUTPUT;
        $result = '';
        if (count($widget->categories) > 0) {
            $table = new \html_table();
            $table->head = array(
                get_string('key2', 'block_sibcms'),
                get_string('key3', 'block_sibcms'),
                get_string('key4', 'block_sibcms')
            );
            foreach ($widget->categories as $category) {
                $category_str = $category->name;
                if ($category->has_subcategories) {
                    $category_url = new \moodle_url('/blocks/sibcms/category.php', array('id' => $category->id));
                    $category_str = \html_writer::tag('a', $category->name, array('href' => $category_url));
                }
                $courses_url = new \moodle_url('/blocks/sibcms/courses.php', array('category' => $category->id));
                $courses_link = \html_writer::tag('a',
                    get_string('key9', 'block_sibcms'),
                    array('href' => $courses_url)
                );
                $courses_str = "{$category->courses_total} ($courses_link)";
                $table->data[] = array(
                    $category_str,
                    $courses_str,
                    $category->courses_require_attention
                );
            }
            $result .= \html_writer::table($table);
        } else {
            $result .= $OUTPUT->heading(get_string('key6', 'block_sibcms'));
        }
        return $result;
    }

    /**
     * Render the table with course feedback data for the selected category
     * @param category_courses_table $widget
     * @return string
     */
    public function render_category_courses_table(category_courses_table $widget)
    {
        global $OUTPUT;
        $result = '';
        if (count($widget->courses) > 0) {
            $table = new \html_table();
            $table->head = array(
                '',
                get_string('key10', 'block_sibcms'),
                get_string('key11', 'block_sibcms'),
                get_string('key12', 'block_sibcms'),
                get_string('key13', 'block_sibcms')
            );
            foreach ($widget->courses as $course) {
                $feedback = \block_sibcms\sibcms_api::get_last_course_feedback($course->id);
                $time_ago = get_string('never');
                if (!empty($feedback)) {
                    $time_ago = format_time(time() - $feedback->timecreated);
                }
                $status = '—';
                if ($feedback) {
                    if ($feedback->result == 0) {
                        $status = \html_writer::span(get_string('key23', 'block_sibcms'), 'green');
                    }
                    if ($feedback->result == 1) {
                        $status = \html_writer::span(get_string('key24', 'block_sibcms'));
                    }
                    if ($feedback->result == 2) {
                        $status = \html_writer::span(get_string('key25', 'block_sibcms'), 'red');
                    }
                    if ($feedback->result == 3) {
                        $status = \html_writer::span(get_string('key26', 'block_sibcms'), 'red');
                    }
                }
                $table->align[0] = 'center';
                $table->data[] = array(
                    \block_sibcms\sibcms_api::require_attention($course->id) ?
                        \html_writer::span('!', 'bold red text-center') : '',
                    $course->fullname,
                    $time_ago,
                    $status,
                    \html_writer::tag('a', get_string('key19', 'block_sibcms'),
                        array(
                            'href' => new \moodle_url('/blocks/sibcms/course.php',
                                array('id' => $course->id, 'category' => $widget->category_id))
                        )
                    )
                );
            }
            $result .= \html_writer::table($table);
            $result .= $OUTPUT->paging_bar($widget->courses_count, $widget->page, 20,
                new \moodle_url('/blocks/sibcms/courses.php', array('category' => $widget->category_id)));
        } else {
            $result .= $OUTPUT->heading(get_string('key6', 'block_sibcms'));
        }
        return $result;
    }

}
