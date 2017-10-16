<?php

namespace block_sibcms;

require_once($CFG->dirroot.'/mod/assign/locallib.php');
require_once($CFG->dirroot.'/mod/quiz/locallib.php');

class sibcms_api {

    public static function get_couse_feedbacks($courseid) {
        global $DB;

        $feedbacks = $DB->get_records('block_sibcms_feedbacks', array('courseid' => $courseid));

        return $feedbacks;
    }

    public static function get_last_course_feedback($courseid) {
        global $DB;

        $sql = 'SELECT *
                FROM {block_sibcms_feedbacks}
                WHERE courseid = ?
                ORDER BY timecreated DESC';
        $feedback = $DB->get_record_sql($sql, array($courseid));

        return $feedback;

    }

    public static function save_feedback($courseid, $feedback, $comment, $result) {
        global $DB, $USER;
        $record = new \stdClass();
        $record->userid = $USER->id;
        $record->courseid = $courseid;
        $record->timecreated = time();
        $record->result = $result;
        $record->feedback = $feedback;
        $record->comment = $comment;
        return $DB->insert_record('block_sibcms_feedbacks', $record);
    }

    public static function need_attention($courseid) {
        $lastfeedback = sibcms_api::get_last_course_feedback($courseid);
        //Если нет отзыва
        if (!$lastfeedback) {
            return true;
        }
        //Если курс без замечаний - 15 дней
        if ($lastfeedback->result == 0 && time() - $lastfeedback->timecreated > 3600 * 24 * 15) {
            return true;
        }
        //Если курс с незначительными замечаниями - 7 дней
        if ($lastfeedback->result == 1 && time() - $lastfeedback->timecreated > 3600 * 24 * 7) {
            return true;
        }
        //Если курс с критическими замечаниями - 3 дня
        if ($lastfeedback->result == 2 && time() - $lastfeedback->timecreated > 3600 * 24 * 3) {
            return true;
        }
        //Если курс не готов - 3 дня
        if ($lastfeedback->result == 3 && time() - $lastfeedback->timecreated > 3600 * 24 * 3) {
            return true;
        }
        return false;
    }

    public static function get_autohints($coursedata) {
        $hints = array();
        if (count($coursedata->graders) == 0) {
            $hints[] = get_string('key50', 'block_sibcms');
        }
        if (count($coursedata->graders) == $coursedata->participants) {
            $hints[] = get_string('key51', 'block_sibcms');
        }
        if ($coursedata->filescount == 0) {
            $hints[] = get_string('key52', 'block_sibcms');
        }
        if (count($coursedata->assigns) + count($coursedata->quiz) == 0) {
            $hints[] = get_string('key53', 'block_sibcms');
        }

        $allgrading = true;
        foreach ($coursedata->assigns as $assign) {
            $allgrading &= !$assign->nograde;
        }
        if (!$allgrading) {
            $hints[] = get_string('key54', 'block_sibcms');
        }

        $hasquestions = true;
        foreach ($coursedata->quiz as $quiz) {
            $hasquestions &= !$quiz->noquestions;
        }
        if (!$hasquestions) {
            $hints[] = get_string('key55', 'block_sibcms');
        }
        return $hints;
    }

    // TODO: Union all graders and mark their capabilities
    public static function get_course_graders($courseid, $fields = 'u.*') {
        $context = \context_course::instance((int)$courseid);
        $graders = get_enrolled_users($context, 'mod/assign:grade', null, $fields, null, null, null, true);
        foreach ($graders as $grader) {
            if (!is_enrolled($context, $grader, 'mod/quiz:grade', true)) {
                unset($graders[$grader->id]);
            } else {
                $graders[$grader->id]->lastcourseaccess = sibcms_api::get_last_access_to_course($courseid, $grader->id);
            }

        }
        return $graders;
    }

    public static function get_course_data($course) {
        $modinfo = get_fast_modinfo($course->id);
        $result = new \stdClass();
        $result->id = $course->id;
        $result->fullname = $course->fullname;
        $result->shortname = $course->shortname;
        $result->graders = sibcms_api::get_course_graders($course->id);
        $result->participants = sibcms_api::get_count_course_participants($course->id);
        $result->filescount = sibcms_api::get_count_course_files($course->id);
        $result->assigns = sibcms_api::get_assign_grades_data($modinfo, 0, true);
        $result->quiz = sibcms_api::get_quiz_grades_data($modinfo, 0, true);
        return $result;
    }

    public static function get_last_access_to_course($courseid, $userid) {
        global $DB;
        return $DB->get_field('user_lastaccess', 'timeaccess', array('courseid' => $courseid, 'userid' => $userid));
    }

    public static function get_assign_grades_data($modinfo, $activitygroup, $onlyvisible = false) {
        global $DB;

        $modules = $modinfo->get_instances_of('assign');
        $course = $modinfo->get_course();

        $result = array();

        foreach ($modules as $module) {

            //$visible = sibcms_api::get_modvisible($module);
            //if ($onlyvisible && !$visible) continue;
            $cm = \context_module::instance($module->id);
            $assign = new \assign($cm, $module, $course);
            $instance = $assign->get_instance();
            $moddata = new \stdClass();

            $moddata->name = $module->name;
            $moddata->teamsubmission = $instance->teamsubmission;
            $moddata->nograde = $instance->grade == 0;
            //$moddata->modvisible = $visible;
            $moddata->visible = has_capability('mod/assign:view', $cm);
            $moddata->gradeitem = $assign->get_grade_item();
            if ($moddata->nograde) {
                $moddata->grade = get_string('key56', 'block_sibcms');
            } else {
                if ($moddata->gradeitem->gradetype == 2) {
                    $moddata->grade = $DB->get_record('scale', array('id' => $moddata->gradeitem->scaleid))->name;
                } else {
                    $points = array('points' => format_float($moddata->gradeitem->grademax, 0));
                    $moddata->grade = get_string('key57', 'block_sibcms', $points);
                }
            }
            $moddata->feedbacks = array();
            $feedbacks = $assign->get_feedback_plugins();
            foreach ($feedbacks as $feedback) {
                $type = "assignfeedback_{$feedback->get_type()}";
                if (!get_config($type, 'disabled') && $feedback->is_enabled()) {
                    $moddata->feedbacks[] = $feedback;
                }
            }

            if ($instance->teamsubmission) { // расчет по правилам Moodle
                $moddata->participants = $assign->count_teams($activitygroup);
                $moddata->submitted = $assign->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_DRAFT) +
                    $assign->count_submissions_with_status(ASSIGN_SUBMISSION_STATUS_SUBMITTED);
                $moddata->need_grading = $assign->count_submissions_need_grading();
            } else { // расчет по собственным правилам
                list($esql, $uparams) = get_enrolled_sql($cm, 'mod/assign:submit', $activitygroup, 'u.*', null, null, null, true);
                $info = new \core_availability\info_module($module);
                list($fsql, $fparams) = $info->get_user_list_sql(true);
                if ($fsql) $uparams = array_merge($uparams, $fparams);
                $psql = "SELECT COUNT(*) FROM {user} u JOIN ($esql) e ON u.id = e.id " . ($fsql ? "JOIN ($fsql) f ON u.id = f.id" : "");
                $moddata->participants = $DB->count_records_sql($psql, $uparams);

                $select = "SELECT COUNT(DISTINCT(s.userid)) ";
                $table = "FROM {assign_submission} s ";
                $ujoin = "JOIN ($esql) e ON s.userid = e.id " . ($fsql ? "JOIN ($fsql) f ON s.userid = f.id " : "");
                $where = "WHERE s.assignment = :assign AND s.timemodified IS NOT NULL AND (s.status = :stat1 OR s.status = :stat2) ";
                $sparams = array(
                    'assign' => $module->instance,
                    'stat1'  => ASSIGN_SUBMISSION_STATUS_SUBMITTED,
                    'stat2'  => ASSIGN_SUBMISSION_STATUS_DRAFT
                );
                $sparams = array_merge($sparams, $uparams);
                $moddata->submitted = $DB->count_records_sql($select . $table . $ujoin . $where, $sparams);

                $select = "SELECT COUNT(s.userid) ";
                $gjoin = "LEFT JOIN {assign_grades} g ON s.assignment = g.assignment AND s.userid = g.userid AND g.attemptnumber = s.attemptnumber ";
                $where .= "AND s.latest = 1 AND (s.timemodified >= g.timemodified OR g.timemodified IS NULL OR g.grade IS NULL)";
                $moddata->need_grading = $DB->count_records_sql($select . $table . $ujoin . $gjoin . $where, $sparams);
                $moddata->graded = $moddata->submitted - $moddata->need_grading;
            }

            $result[$module->id] = $moddata;

        }

        return $result;
    }

    public static function get_quiz_grades_data($modinfo, $activitygroup, $onlyvisible = false) {
        global $DB;

        $modules = $modinfo->get_instances_of('quiz');

        $result = array();

        foreach ($modules as $module) {

            //$visible = sibcms_api::get_modvisible($module);
            //if ($onlyvisible && !$visible) continue;
            $cm = \context_module::instance($module->id);
            $quiz = \quiz::create($module->instance);
            $moddata = new \stdClass();

            $moddata->name = $module->name;
            $moddata->id = $module->id;
            $moddata->noquestions = !$quiz->has_questions();
            //$moddata->modvisible = $visible;
            $moddata->visible = has_capability('mod/quiz:view', $cm);

            list($esql, $uparams) = get_enrolled_sql($cm, 'mod/quiz:attempt', $activitygroup, 'u.*', null, null, null, true);
            $info = new \core_availability\info_module($module);
            list($fsql, $fparams) = $info->get_user_list_sql(true);
            if ($fsql) $uparams = array_merge($uparams, $fparams);
            $psql = "SELECT COUNT(*) FROM {user} u JOIN ($esql) e ON u.id = e.id " . ($fsql ? "JOIN ($fsql) f ON u.id = f.id" : "");
            $moddata->countusers = $DB->count_records_sql($psql, $uparams);

            $select = "SELECT COUNT(qg.id) ";
            $table = "FROM {quiz_grades} qg ";
            $ujoin = "JOIN ($esql) e ON qg.userid = e.id " . ($fsql ? "JOIN ($fsql) f ON qg.userid = f.id " : "");
            $where = "WHERE qg.quiz = :quiz";
            $qparams = array_merge(array('quiz' => $module->instance), $uparams);
            $moddata->countgrades = $DB->count_records_sql($select . $table . $ujoin . $where, $qparams);
            $result[$module->id] = $moddata;

        }

        return $result;
    }

    public static function get_count_course_participants($courseid) {
        $context = \context_course::instance($courseid);
        return count_enrolled_users($context);
    }


    public static function get_count_course_files($courseid, $onlyvisible = false) {
        $result = 0;
        $modinfo = get_fast_modinfo($courseid);

        $modules = $modinfo->get_instances_of('resource');
        foreach ($modules as $module) {
            if ($onlyvisible && !$module->visible) continue;
            $result++;
        }
        $fs = get_file_storage();
        $modules = $modinfo->get_instances_of('folder');
        foreach ($modules as $module) {
            if ($onlyvisible && !$module->visible) continue;
            $cm = \context_module::instance($module->id);
            $files = $fs->get_area_files($cm->id, 'mod_folder', 'content', 0, null, false);
            $result += count($files);
        }

        return $result;
    }

/*
    public static function set_modvisible($module, $visible) {
        global $DB;

        $count = $DB->count_records('report_activity_visibility', array('moduleid' => $module->id));
        if ($count > 0) {
            $DB->set_field('report_activity_visibility', 'visible', $visible, array('moduleid' => $module->id));
        } else {
            $DB->execute('INSERT INTO {report_activity_visibility} (courseid, moduleid, visible) VALUES (?, ?, ?)', array($module->course, $module->id, $visible));
        }
    }

    public static function get_modvisible($module) {
        global $DB;

        $record = $DB->get_record('report_activity_visibility', array('moduleid' => $module->id));

        return !$record ? $module->visible : $record->visible;
    }
*/
}
