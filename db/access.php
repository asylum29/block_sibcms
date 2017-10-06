<?php

defined('MOODLE_INTERNAL') || die();

$capabilities = array(

	'block/sibcms:myaddinstance' => array(
		'captype' => 'write',
		'contextlevel' => CONTEXT_SYSTEM,
		'archetypes' => array(
			'user' => CAP_ALLOW
		)
	),

	'block/sibcms:addinstance' => array(
		'riskbitmask' => RISK_SPAM | RISK_XSS,	
		'captype' => 'write',
		'contextlevel' => CONTEXT_BLOCK,
		'archetypes' => array(
			'editingteacher' => CAP_ALLOW,
			'manager' => CAP_ALLOW
		)
	)

);
