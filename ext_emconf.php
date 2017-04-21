<?php
### #####################################################################
### Extension Manager/Repository config file for ext "hpsplaner".
### #####################################################################
$EM_CONF[$_EXTKEY] = array(
	'title' => 'MK Postman',
	'description' => '',
	'category' => 'misc',
	'author' => 'DMK E-BUSINESS GmbH',
	'author_email' => 'dev@dmk-ebusiness.de',
	'author_company' => 'DMK E-BUSINESS GmbH',
	'shy' => '',
	'dependencies' => 'rn_base,mkforms,mkmailer',
	'version' => '1.0.4',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'stable',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'constraints' => array(
		'depends' => array(
			'mkmailer' => '2.0.8-',
			'mkforms' => '2.0.11-',
			'rn_base' => '1.2.5-',
			'typo3' => '6.2.10-8.7.99',
		),
		'conflicts' => array(),
		'suggests' => array(
			'mklib' => '2.0.4-',
		)
	),
	'autoload' => array(
		'psr-4' => array('DMK\\Mkpostman\\' => 'Classes')
	),
);
