<?php

defined('TYPO3_MODE') or die('Access denied.');

return [
    'ctrl' => [
        'title' => 'LLL:EXT:mkpostman/Resources/Private/Language/Tca.xlf:tx_mkpostman_logs',
        'label' => 'description',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'searchFields' => 'description',
        'iconfile' => 'EXT:mkpostman/Resources/Public/Media/Icons/tx_mkpostman_logs.gif',
        'dividers2tabs' => true,
    ],
    'interface' => [
        'showRecordFieldList' => 'description',
    ],
    'columns' => [
        'crdate' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mkpostman/Resources/Private/Language/Tca.xlf:tx_mkpostman_logs.crdate',
            'config' => [
                'type' => 'input',
                'format' => 'datetime',
                'readOnly' => true,
            ],
        ],
        'cruser_id' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mkpostman/Resources/Private/Language/Tca.xlf:tx_mkpostman_logs.cruser_id',
            'config' => [
                'type' => 'select',
                'foreign_table' => 'be_users',
                'readOnly' => true,
            ],
        ],
        'subscriber_id' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mkpostman/Resources/Private/Language/Tca.xlf:tx_mkpostman_logs.subscriber_id',
            'config' => [
                'type' => 'select',
                'foreign_table' => 'tx_mkpostman_subscribers',
                'readOnly' => true,
            ],
        ],
        'state' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mkpostman/Resources/Private/Language/Tca.xlf:tx_mkpostman_logs.state',
            'config' => [
                'type' => 'select',
                'items' => [
                    [
                        '0',
                        '0',
                    ],
                    [
                        'Subscribed',
                        '1',
                    ],
                    [
                        'Activated',
                        '2',
                    ],
                    [
                        'Unsubscribed',
                        '3',
                    ],
                ],
                'readOnly' => true,
            ],
        ],
        'description' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:mkpostman/Resources/Private/Language/Tca.xlf:tx_mkpostman_logs.description',
            'config' => [
                'type' => 'input',
                'size' => '20',
                'max' => '255',
                'eval' => 'trim',
                'readOnly' => true,
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => 'crdate, cruser_id, subscriber_id, state, description',
        ],
    ],
];
