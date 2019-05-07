<?php
defined('TYPO3_MODE') or die('Access denied.');

return call_user_func(
    function () {
        $lllFile = 'LLL:EXT:mkpostman/Resources/Private/Language/Tca.xlf:';
        $lllTable = $lllFile . 'tx_mkpostman_subscribers.';

        $tca = array(
            'ctrl' => array(
                'title' => $lllFile . 'tx_mkpostman_subscribers',
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
                'iconfile' => \tx_rnbase_util_Extensions::extRelPath('mkpostman') .
                    'Resources/Public/Media/Icons/tx_mkpostman_subscribers.gif',
                'dividers2tabs' => true,
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
                    'label' => $lllTable . 'confirmstring',
                    'config' => array(
                        'type' => 'input',
                        'readOnly' => true
                    )
                ),
                'gender' => array(
                    'exclude' => 1,
                    'label' => $lllTable . 'gender',
                    'config' => array(
                        'type' => 'radio',
                        'items' => array(
                            array(
                                $lllTable . 'gender.0',
                                '0'
                            ),
                            array(
                                $lllTable . 'gender.1',
                                '1'
                            )
                        )
                    )
                ),
                'first_name' => array(
                    'exclude' => 1,
                    'label' => $lllTable . 'first_name',
                    'config' => array(
                        'type' => 'input',
                        'size' => '20',
                        'max' => '60',
                        'eval' => 'trim'
                    )
                ),
                'last_name' => array(
                    'exclude' => 1,
                    'label' => $lllTable . 'last_name',
                    'config' => array(
                        'type' => 'input',
                        'size' => '20',
                        'max' => '60',
                        'eval' => 'trim'
                    )
                ),
                'email' => array(
                    'exclude' => 1,
                    'label' => $lllTable . 'email',
                    'config' => array(
                        'type' => 'input',
                        'size' => '20',
                        'max' => '255',
                        'eval' => 'trim,required,unique'
                    )
                ),
                'categories' => array(
                    'exclude' => true,
                    'l10n_mode' => 'mergeIfNotBlank',
                    'label' => $lllTable . 'categories',
                    'config' => array(
                        'type' => 'select',
                        'renderType' => 'selectTree',
                        'treeConfig' => array(
                            'parentField' => 'parent',
                            'appearance' => array(
                                'expandAll' => true,
                            ),
                        ),
                        'MM' => 'sys_category_record_mm',
                        'MM_match_fields' => array(
                            'fieldname' => 'categories',
                            'tablenames' => 'tx_mkpostman_subscribers',
                        ),
                        'MM_opposite_field' => 'items',
                        'foreign_table' => 'sys_category',
                        'size' => 10,
                        'minitems' => 0,
                        'maxitems' => 99,
                    )
                ),
                'logs' => array(
                    'exclude' => 1,
                    'label' => $lllTable . 'logs',
                    'config' => array(
                        'type' => 'user',
                        'size' => '20',
                        'userFunc' => 'DMK\\Mkpostman\\Utility\\TcaUtility->getLogsForSubscriber'
                    )
                ),
            ),
            'types' => array(
                '0' => array(
                    'showitem' => '
                        --div--;' . $lllFile . 'tx_mkpostman_subscribers.tab.general,
                        disabled, email, gender, first_name, last_name, categories, confirmstring, 
                        --div--;' . $lllFile . 'tx_mkpostman_subscribers.tab.log,
                        logs,
                    '
                )
            )
        );

        // add the direct mail columns, if direct mail is installed
        if (\tx_rnbase_util_Extensions::isLoaded('direct_mail')) {
            $dmLllFile = 'LLL:EXT:direct_mail/Resources/Private/Language/locallang_tca.xlf:';
            $tca['columns']['module_sys_dmail_category'] = array(
                'displayCond' => 'EXT:direct_mail:LOADED:TRUE',
                'exclude' => 1,
                'label' => $dmLllFile . 'module_sys_dmail_group.category',
                'config' => array(
                    'type' => 'select',
                    'foreign_table' => 'sys_dmail_category',
                    'foreign_table_where' => 'AND sys_dmail_category.l18n_parent=0' .
                        ' AND sys_dmail_category.pid IN (###PAGE_TSCONFIG_IDLIST###)' .
                        ' ORDER BY sys_dmail_category.sorting',
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
            );
            $tca['columns']['module_sys_dmail_html'] = array(
                'displayCond' => 'EXT:direct_mail:LOADED:TRUE',
                'exclude' => 1,
                'label' => $dmLllFile . 'module_sys_dmail_group.htmlemail',
                'config' => array(
                    'type' => 'check'
                )
            );

            $tca['types']['0']['showitem'] .= '
                --div--;' . $lllFile . 'tx_mkpostman_subscribers.tab.directmail,
                module_sys_dmail_category, module_sys_dmail_html,
            ';
        }

        return $tca;
    }
);
