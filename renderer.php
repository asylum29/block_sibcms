<?php

defined('MOODLE_INTERNAL') || die();

class block_sibcms_renderer extends plugin_renderer_base {

    public function render_categories_statistic_table($widget) {
        global $CFG, $OUTPUT;
        
        $result = '';


        if ($widget->recursive) {
            $result .= html_writer::tag('a', get_string('key15', 'block_sibcms'), 
                array(
                    'href' => new moodle_url('/blocks/sibcms/categories.php', array('id' => $widget->categoryid, 'recursive' => 0)),
                    'class' => 'btn'
                )
            );
        } else {
            $result .= html_writer::tag('a', get_string('key14', 'block_sibcms'),
                array(
                    'href' => new moodle_url('/blocks/sibcms/categories.php', array('id' => $widget->categoryid, 'recursive' => 1)),
                    'class' => 'btn'
                )
            );
        }

        if (count($widget->categories) > 0) {
            $table = new html_table();
            $table->head = array(
                get_string('key2', 'block_sibcms'),
                get_string('key3', 'block_sibcms'),
                get_string('key4', 'block_sibcms')
            );
            
            foreach ($widget->categories as $category) {
                $categorystr = $category->name;

                if ($category->has_subcategories) {
                    $categorystr = html_writer::tag('a', $category->name, 
                        array('href' => new moodle_url('/blocks/sibcms/categories.php', array('id' => $category->id, 'recursive' => $widget->recursive))));
                }

                $coursesstr = count($category->courses) . ' ('. html_writer::tag('a', get_string('key9', 'block_sibcms'), 
                        array('href' => new moodle_url('/blocks/sibcms/category.php', array('id' => $category->id)))) . ')';

                $table->data[] = array(
                    $categorystr,
                    $coursesstr , 
                    $category->courses_need_attention);
            }
            
            $result .= html_writer::table($table);
        } else {
            $result .= $OUTPUT->heading(get_string('key6', 'block_sibcms'));
        }
        return $result;
    }
    
    public function render_category_courses_table($widget) {
        global $CFG, $OUTPUT;
        
        $result = '';

        if (count($widget->courses) > 0) {
            $table = new html_table();
            $table->head = array(
                '',
                get_string('key10', 'block_sibcms'),
                get_string('key11', 'block_sibcms'),
                get_string('key12', 'block_sibcms'),
                get_string('key13', 'block_sibcms')
            );
            
            foreach ($widget->courses as $course) {
                $coursename = $category->fullname;
                $lastfeedbackstr = get_string('never');
                if ($course->last_feedback) {
                    $lastfeedbackstr = format_time(time() - $course->last_feedback->timecreated);
                }

                $resultstr = '';
                if ($course->last_feedback) {
                    if ($course->last_feedback->result == 0) {
                        $resultstr = html_writer::span(get_string('key16', 'block_sibcms'), 'green');
                    }
                    if ($course->last_feedback->result == 1) {
                        $resultstr = html_writer::span(get_string('key17', 'block_sibcms'));
                    }
                    if ($course->last_feedback->result == 2) {
                        $resultstr = html_writer::span(get_string('key18', 'block_sibcms'), 'red');
                    }
                }

                $table->data[] = array(
                    $course->need_attention ? html_writer::span('!', 'bold red') : '',
                    $course->fullname,
                    $lastfeedbackstr,
                    $resultstr,
                    html_writer::tag('a', get_string('key19', 'block_sibcms'), 
                        array(
                            'href' => new moodle_url('/blocks/sibcms/course.php', array('id' => $course->id)),
                            'class' => 'btn'
                        )
                    )
                );           
            }
            
            $result .= html_writer::table($table);
        } else {
            $result .= $OUTPUT->heading(get_string('key6', 'block_sibcms'));
        }
        return $result;

    }

}

