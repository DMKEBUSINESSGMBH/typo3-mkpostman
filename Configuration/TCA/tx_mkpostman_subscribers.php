<?php

defined('TYPO3_MODE') or exit('Access denied.');

return call_user_func(
    function () {
        $lllFile = 'LLL:EXT:mkpostman/Resources/Private/Language/Tca.xlf:';
        $lllTable = $lllFile.'tx_mkpostman_subscribers.';

        $tca = [
            'ctrl' => [
                'title' => $lllFile.'tx_mkpostman_subscribers',
                'label' => 'email',
                'tstamp' => 'tstamp',
                'crdate' => 'crdate',
                'cruser_id' => 'cruser_id',
                'delete' => 'deleted',
                'default_sortby' => 'ORDER BY email',
                'enablecolumns' => [
                    'disabled' => 'disabled',
                ],
                'searchFields' => 'name,',
                'iconfile' => 'EXT:mkpostman/Resources/Public/Media/Icons/tx_mkpostman_subscribers.gif',
                'dividers2tabs' => true,
            ],
            'interface' => [
                'showRecordFieldList' => 'email',
            ],
            'columns' => [
                'disabled' => [
                    'exclude' => 1,
                    'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.disable',
                    'config' => [
                        'type' => 'check',
                        'default' => '0',
                    ],
                ],
                'confirmstring' => [
                    'exclude' => 1,
                    'label' => $lllTable.'confirmstring',
                    'config' => [
                        'type' => 'input',
                        'readOnly' => true,
                    ],
                ],
                'gender' => [
                    'exclude' => 1,
                    'label' => $lllTable.'gender',
                    'config' => [
                        'type' => 'radio',
                        'items' => [
                            [
                                $lllTable.'gender.0',
                                '0',
                            ],
                            [
                                $lllTable.'gender.1',
                                '1',
                            ],
                        ],
                    ],
                ],
                'first_name' => [
                    'exclude' => 1,
                    'label' => $lllTable.'first_name',
                    'config' => [
                        'type' => 'input',
                        'size' => '20',
                        'max' => '60',
                        'eval' => 'trim',
                    ],
                ],
                'last_name' => [
                    'exclude' => 1,
                    'label' => $lllTable.'last_name',
                    'config' => [
                        'type' => 'input',
                        'size' => '20',
                        'max' => '60',
                        'eval' => 'trim',
                    ],
                ],
                'email' => [
                    'exclude' => 1,
                    'label' => $lllTable.'email',
                    'config' => [
                        'type' => 'input',
                        'size' => '20',
                        'max' => '255',
                        'eval' => 'trim,required,unique',
                    ],
                ],
                'categories' => [
                    'exclude' => true,
                    'l10n_mode' => 'mergeIfNotBlank',
                    'label' => $lllTable.'categories',
                    'config' => [
                        'type' => 'select',
                        'renderType' => 'selectTree',
                        'treeConfig' => [
                            'parentField' => 'parent',
                            'appearance' => [
                                'expandAll' => true,
                            ],
                        ],
                        'MM' => 'sys_category_record_mm',
                        'MM_match_fields' => [
                            'fieldname' => 'categories',
                            'tablenames' => 'tx_mkpostman_subscribers',
                        ],
                        'MM_opposite_field' => 'items',
                        'foreign_table' => 'sys_category',
                        'size' => 10,
                        'minitems' => 0,
                        'maxitems' => 99,
                    ],
                ],
                'logs' => [
                    'exclude' => 1,
                    'label' => $lllTable.'logs',
                    'config' => [
                        'type' => 'user',
                        'size' => '20',
                        'userFunc' => 'DMK\\Mkpostman\\Utility\\TcaUtility->getLogsForSubscriber',
                    ],
                ],
            ],
            'types' => [
                '0' => [
                    'showitem' => '
                        --div--;'.$lllFile.'tx_mkpostman_subscribers.tab.general,
                        disabled, email, gender, first_name, last_name, categories, confirmstring, 
                        --div--;'.$lllFile.'tx_mkpostman_subscribers.tab.log,
                        logs,
                    ',
                ],
            ],
        ];

        // add the direct mail columns, if direct mail is installed
        if (\tx_rnbase_util_Extensions::isLoaded('direct_mail')) {
            $dmLllFile = 'LLL:EXT:direct_mail/Resources/Private/Language/locallang_tca.xlf:';
            $tca['columns']['module_sys_dmail_category'] = [
                'displayCond' => 'EXT:direct_mail:LOADED:TRUE',
                'exclude' => 1,
                'label' => $dmLllFile.'module_sys_dmail_group.category',
                'config' => [
                    'type' => 'select',
                    'foreign_table' => 'sys_dmail_category',
                    'foreign_table_where' => 'AND sys_dmail_category.l18n_parent=0'.
                        ' AND sys_dmail_category.pid IN (###PAGE_TSCONFIG_IDLIST###)'.
                        ' ORDER BY sys_dmail_category.sorting',
                    'itemsProcFunc' => 'DirectMailTeam\\DirectMail\\SelectCategories->get_localized_categories',
                    'itemsProcFunc_config' => [
                        'table' => 'sys_dmail_category',
                        'indexField' => 'uid',
                    ],
                    'size' => 5,
                    'minitems' => 0,
                    'maxitems' => 60,
                    'renderMode' => 'checkbox',
                    'MM' => 'tx_mkpostman_subscribers_dmail_category_mm',
                ],
            ];
            $tca['columns']['module_sys_dmail_html'] = [
                'displayCond' => 'EXT:direct_mail:LOADED:TRUE',
                'exclude' => 1,
                'label' => $dmLllFile.'module_sys_dmail_group.htmlemail',
                'config' => [
                    'type' => 'check',
                ],
            ];

            $tca['types']['0']['showitem'] .= '
                --div--;'.$lllFile.'tx_mkpostman_subscribers.tab.directmail,
                module_sys_dmail_category, module_sys_dmail_html,
            ';
        }

        return $tca;
    }
);
