<?php

// ## #####################################################################
// ## Extension Manager/Repository config file for ext "hpsplaner".
// ## #####################################################################

$EM_CONF['mkpostman'] = [
    'title' => 'MK Postman',
    'description' => '',
    'category' => 'misc',
    'author' => 'DMK E-BUSINESS GmbH',
    'author_email' => 'dev@dmk-ebusiness.de',
    'author_company' => 'DMK E-BUSINESS GmbH',
    'shy' => '',
    'dependencies' => 'rn_base,mkforms,mkmailer',
    'version' => '10.0.0',
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
    'constraints' => [
        'depends' => [
            'rn_base' => '1.10.0-',
            'typo3' => '9.5.0-10.4.99',
            'mkmailer' => '9.0.0-',
        ],
        'conflicts' => [],
        'suggests' => [
            'mkforms' => '9.5.0-',
            'mklib' => '9.5.0-',
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'DMK\\Mkpostman\\' => 'Classes',
        ],
    ],
];
