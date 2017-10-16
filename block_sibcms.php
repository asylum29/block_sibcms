<?php

defined('MOODLE_INTERNAL') || die();

class block_sibcms extends block_list {

	function init() {
		$this->title = get_string('pluginname', 'block_sibcms');
	}

	function instance_allow_multiple() {
		return false;
	}
	
	function instance_allow_config() {
		return false;
	}
	
	function has_config() {
		return true;
	}
	
	function get_content() {
		global $CFG, $PAGE;

		if (!isloggedin() || isguestuser() || !is_siteadmin()) {
			$this->content = '';
			return $this->content;
		}
		
		if ($this->content !== null) {
			return $this->content;
		}
		
		$this->content = new stdClass();
		$this->content->items = array();
		$this->content->icons = array();
		$this->content->footer = '';
		 
		$this->content->items[] = html_writer::tag('a', get_string('key1', 'block_sibcms'), array('href' => $CFG->wwwroot.'/blocks/sibcms/category.php'));
		//$this->content->icons[] = html_writer::empty_tag('img', array('src' => $CFG->wwwroot.'/blocks/sibportfolio/pix/sibport.png'));
				 
		return $this->content;
	}

}
