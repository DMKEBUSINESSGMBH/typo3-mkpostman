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
    'version' => '3.1.1',
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
            'rn_base' => '1.4.0-',
            'typo3' => '6.2.14-8.7.99',
            'mkmailer' => '3.0.0-',
            'rn_base' => '1.2.5-',
        ),
        'conflicts' => array(),
        'suggests' => array(
            'mkforms' => '3.0.0-',
            'mklib' => '3.0.0-',
        ),
    ),
    'autoload' => array(
        'psr-4' => array(
            'DMK\\Mkpostman\\' => 'Classes',
        ),
    ),
);
