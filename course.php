<?php

require_once('../../config.php');
require_once('locallib.php');
require_once("$CFG->libdir/formslib.php");
require_once('classes/feedback_form.php');

$courseid = required_param('id', PARAM_INT);

$PAGE->set_url(new moodle_url('/blocks/sibcms/course.php', array('id' => $courseid)));

require_login(1);

$course = $DB->get_record('course', array('id' => $courseid));
$category = $DB->get_record('course_categories', array('id' => $course->category));

$PAGE->navbar->add(get_string('key5', 'block_sibcms'), new moodle_url('/blocks/sibcms/categories.php'));

$PAGE->set_heading($course->fullname);
$path = block_sibcms\sibcms_api::get_category_link_path($category->id);
foreach ($path as $node) {
    $PAGE->navbar->add($node->name, $node->has_subcategories ? new moodle_url('/blocks/sibcms/categories.php', array('id' => $node->id)) : null);
}
$PAGE->navbar->add(get_string('key8', 'block_sibcms'), new moodle_url('/blocks/sibcms/category.php', array('id' => $category->id)));
$PAGE->navbar->add($course->fullname, new moodle_url('/blocks/sibcms/course.php', array('id' => $courseid)));


$output = $PAGE->get_renderer('block_sibcms');

echo $output->header();

	$customData = array('user' => optional_param('user', '', PARAM_RAW_TRIMMED));
	$mform = new block_sibcms\feedbackForm(null, $customData);
	if ($mform->is_cancelled()) {

		redirect(new moodle_url('/blocks/sibcms/category.php', array('id' => $category->id)));
		
	} else if ($data = $mform->get_data()) {
	/*
		$curatorId = required_param('curator', PARAM_INT);
		block_sibportfolio_users::add_curator($curatorId, $groupId);
		$claims = block_sibportfolio_claims::get_wait_claims($curatorId)->claims;
		foreach ($claims as $claim) {
			$logData = array(
				'userid' 	  => $curatorId,
				'curatorid'   => $curatorId,
				'description' => $claim->claimtype == 1 ? $claim->description : $claim->description2,
				'filename'    => block_sibportfolio_files::get_file_by_claim_id($claim->id)->filename,
				'claimtype'   => $claim->claimtype,
				'claimstatus' => 1,
				'timecreated' => time(),
				'usercomment' => $claim->usercomment,
				'comment' 	  => get_string_manager()->get_string('key97', 'block_sibportfolio', null, $CFG->lang),
				'itemid'	  => $claim->id
			);
			block_sibportfolio_claims::write_log($logData);
		
			block_sibportfolio_claims::handle_claim($claim);
		}*/
		redirect(new moodle_url('/blocks/sibcms/category.php', array('id' => $category->id)));
	
	} else {

		$mform->set_data(array(
			'id' => $courseid
		));
        $mform->display();
		
	}

echo $output->footer();
