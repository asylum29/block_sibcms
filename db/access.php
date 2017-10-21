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
    ),

    'block/sibcms:monitoring' => array(
        'riskbitmask' => RISK_SPAM | RISK_XSS,
        'captype' => 'write',
        'contextlevel' => CONTEXT_SYSTEM
    ),

    'block/sibcms:activity_report' => array(
        'riskbitmask'  => RISK_PERSONAL,
        'captype' 	   => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes'   => array(
            'teacher' 		 => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager' 		 => CAP_ALLOW
        )
    ),

    'block/sibcms:monitoring_report' => array(
        'captype'      => 'read',
        'contextlevel' => CONTEXT_COURSE,
        'archetypes'   => array(
            'teacher'        => CAP_ALLOW,
            'editingteacher' => CAP_ALLOW,
            'manager'        => CAP_ALLOW
        )
    ),

    'block/sibcms:monitoring_report_category' => array(
        'riskbitmask'  => RISK_PERSONAL,
        'captype'      => 'read',
        'contextlevel' => CONTEXT_COURSECAT,
    ),

);
