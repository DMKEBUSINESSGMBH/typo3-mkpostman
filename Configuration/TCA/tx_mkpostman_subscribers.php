<?php
defined('TYPO3_MODE') or die('Access denied.');

return array(
    'ctrl' => array(
        'title' => 'LLL:EXT:mkpostman/Resources/Private/Language/Tca.xlf:tx_mkpostman_subscribers',
        'label' => 'email',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'default_sortby' => 'ORDER BY email',
        'enablecolumns' => array(
            'disabled' => 'disabled'
        ),
        'searchFields' => 'name,',
        'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('mkpostman') .
            'Resources/Public/Media/Icons/tx_mkpostman_subscribers.gif',
        'dividers2tabs' => false,
    ),
    'interface' => array(
        'showRecordFieldList' => 'email'
    ),
    'columns' => array(
        'disabled' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.disable',
            'config' => array(
                'type' => 'check',
                'default' => '0'
            )
        ),
        'confirmstring' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mkpostman/Resources/Private/Language/Tca.xlf:tx_mkpostman_subscribers.confirmstring',
            'config' => array(
                'type' => 'input',
                'readOnly' => true
            )
        ),
        'gender' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mkpostman/Resources/Private/Language/Tca.xlf:tx_mkpostman_subscribers.gender',
            'config' => array(
                'type' => 'radio',
                'items' => array(
                    array(
                        'LLL:EXT:mkpostman/Resources/Private/Language/Tca.xlf:tx_mkpostman_subscribers.gender.0',
                        '0'
                    ),
                    array(
                        'LLL:EXT:mkpostman/Resources/Private/Language/Tca.xlf:tx_mkpostman_subscribers.gender.1',
                        '1'
                    )
                )
            )
        ),
        'first_name' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mkpostman/Resources/Private/Language/Tca.xlf:tx_mkpostman_subscribers.first_name',
            'config' => array(
                'type' => 'input',
                'size' => '20',
                'max' => '60',
                'eval' => 'trim'
            )
        ),
        'last_name' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mkpostman/Resources/Private/Language/Tca.xlf:tx_mkpostman_subscribers.last_name',
            'config' => array(
                'type' => 'input',
                'size' => '20',
                'max' => '60',
                'eval' => 'trim'
            )
        ),
        'email' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:mkpostman/Resources/Private/Language/Tca.xlf:tx_mkpostman_subscribers.email',
            'config' => array(
                'type' => 'input',
                'size' => '20',
                'max' => '255',
                'eval' => 'trim,required,unique'
            )
        ),
        'module_sys_dmail_category' => array(
            'displayCond' => 'EXT:direct_mail:LOADED:TRUE',
            'exclude' => 1,
            'label' => 'LLL:EXT:direct_mail/Resources/Private/Language/locallang_tca.xlf:module_sys_dmail_group.category',
            'config' => array(
                'type' => 'select',
                'foreign_table' => 'sys_dmail_category',
                'foreign_table_where' => 'AND sys_dmail_category.l18n_parent=0 AND sys_dmail_category.pid IN (###PAGE_TSCONFIG_IDLIST###) ORDER BY sys_dmail_category.sorting',
                'itemsProcFunc' => 'DirectMailTeam\\DirectMail\\SelectCategories->get_localized_categories',
                'itemsProcFunc_config' => array(
                    'table' => 'sys_dmail_category',
                    'indexField' => 'uid',
                ),
                'size' => 5,
                'minitems' => 0,
                'maxitems' => 60,
                'renderMode' => 'checkbox',
                'MM' => 'tx_mkpostman_subscribers_dmail_category_mm',
            )
        ),
        'module_sys_dmail_html' => array(
            'displayCond' => 'EXT:direct_mail:LOADED:TRUE',
            'exclude' => 1,
            'label' => 'LLL:EXT:direct_mail/Resources/Private/Language/locallang_tca.xlf:module_sys_dmail_group.htmlemail',
            'config' => array(
                'type' => 'check'
            )
        )
    ),
    'types' => array(
        '0' => array(
            'showitem' => 'disabled, email, gender, first_name, last_name, confirmstring, module_sys_dmail_category, module_sys_dmail_html'
        )
    )
);
