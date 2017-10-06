<?php

namespace block_sibcms;

defined('MOODLE_INTERNAL') || die();

class feedbackForm extends \moodleform {
	public function definition() {
		global $OUTPUT;
		$mform = $this->_form;
		
		$mform->addElement('hidden', 'id', null);
		$mform->setType('id', PARAM_INT);	
			
		$mform->addElement('static', 'autohints', 'Заголовок?', 'Сообщение?');
		
		
        $results = array();
        $results[0] = 'Без зам';
        $results[1] = 'некрит';
        $results[2] = 'крит';

		$select = $mform->addElement('select', 'result', 'Резульатат проверки', $results, null);
		$mform->addRule('result', 'Ошибка1', 'required', null, 'server');
		$mform->addRule('result', 'Ошибка2', 'numeric');
		$select->setMultiple(false);
				
		$this->add_action_buttons($cancel = true, $submitlabel = 'Подтверждеие');
	}
	
	function validation($data, $files) {		
		$errors = array();
		
		
		return $errors;
	}
}
