<?php

// ## #####################################################################
// ## Extension Manager/Repository config file for ext "hpsplaner".
// ## #####################################################################

$EM_CONF[$_EXTKEY] = array(
    'title' => 'MK Postman',
    'description' => '',
    'category' => 'misc',
    'author' => 'DMK E-BUSINESS GmbH',
    'author_email' => 'dev@dmk-ebusiness.de',
    'author_company' => 'DMK E-BUSINESS GmbH',
    'shy' => '',
    'dependencies' => 'rn_base,mkforms,mkmailer',
    'version' => '9.0.0',
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
            'rn_base' => '1.10.0-',
            'typo3' => '7.6.0-9.5.99',
            'mkmailer' => '9.0.0-',
        ),
        'conflicts' => array(),
        'suggests' => array(
            'mkforms' => '9.5.0-',
            'mklib' => '9.5.0-',
        ),
    ),
    'autoload' => array(
        'psr-4' => array(
            'DMK\\Mkpostman\\' => 'Classes',
        ),
    ),
);
