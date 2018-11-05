<?php
defined('TYPO3_MODE') or die('Access denied.');

return array(
    'ctrl' => array(
        'title' => 'LLL:EXT:mkpostman/Resources/Private/Language/Tca.xlf:tx_mkpostman_logs',
        'label' => 'description',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'searchFields' => 'description',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('mkpostman') .
            'Resources/Public/Media/Icons/tx_mkpostman_logs.gif',
        'dividers2tabs' => true
    ),
    'interface' => array(
        'showRecordFieldList' => 'description'
    ),
    'columns' => array(
        'crdate' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mkpostman/Resources/Private/Language/Tca.xlf:tx_mkpostman_logs.crdate',
            'config' => array(
                'type' => 'input',
                'format' => 'datetime',
                'readOnly' => true,
            )
        ),
        'cruser_id' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mkpostman/Resources/Private/Language/Tca.xlf:tx_mkpostman_logs.cruser_id',
            'config' => array(
                'type' => 'select',
                'foreign_table' => 'be_users',
                'readOnly' => true,
            )
        ),
        'subscriber_id' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mkpostman/Resources/Private/Language/Tca.xlf:tx_mkpostman_logs.subscriber_id',
            'config' => array(
                'type' => 'select',
                'foreign_table' => 'tx_mkpostman_subscribers',
                'readOnly' => true,
            )
        ),
        'state' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mkpostman/Resources/Private/Language/Tca.xlf:tx_mkpostman_logs.state',
            'config' => array(
                'type' => 'select',
                'items' => array(
                    array(
                        '0',
                        '0'
                    ),
                    array(
                        'Subscribed',
                        '1'
                    ),
                    array(
                        'Activated',
                        '2'
                    ),
                    array(
                        'Unsubscribed',
                        '3'
                    ),
                ),
                'readOnly' => true,
            )
        ),
        'description' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mkpostman/Resources/Private/Language/Tca.xlf:tx_mkpostman_logs.description',
            'config' => array(
                'type' => 'input',
                'size' => '20',
                'max' => '255',
                'eval' => 'trim',
                'readOnly' => true,
            )
        )
    ),
    'types' => array(
        '0' => array(
            'showitem' => 'crdate, cruser_id, subscriber_id, state, description'
        )
    )
);
