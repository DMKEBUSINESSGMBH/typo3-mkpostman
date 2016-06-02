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
	'dependencies' => 'rn_base,mkforms',
	'version' => '0.0.0',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => 'beta',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'constraints' => array(
		'depends' => array(
			'mkforms' => '2.0.5-',
			'rn_base' => '1.0.10-',
			'typo3' => '6.2.10-',
		),
		'conflicts' => array(),
		'suggests' => array()
	)
);
