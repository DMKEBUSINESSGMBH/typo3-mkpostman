<?php
defined('TYPO3_MODE') || die ('Access denied.');

return array(
	'ctrl' => array(
		'title' => 'LLL:EXT:mkpostman/Resources/Private/Language/Tca.xlf:tx_mkpostman_subscribers',
		'label' => 'name',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'delete' => 'deleted',
		'default_sortby' => 'ORDER BY name',
		'enablecolumns' => array(
			'disabled' => 'hidden',
		),
		'searchFields' => 'name,',
		'iconfile' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('mkpostman') . 'Resources/Public/Media/Icons/tx_mkpostman_subscribers.gif',
		'dividers2tabs' => TRUE,
	),
	'interface' => array(
		'showRecordFieldList' => 'name,hidden,contact,vatregno'
	),
	'columns' => array(
		'hidden' => array(
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
		'gender' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:mkpostman/Resources/Private/Language/Tca.xlf:tx_mkpostman_subscribers.gender',
			'config' => Array (
				'type' => 'radio',
				'items' => Array (
					Array('LLL:EXT:mkpostman/Resources/Private/Language/Tca.xlf:tx_mkpostman_subscribers.gender.0', '0'),
					Array('LLL:EXT:mkpostman/Resources/Private/Language/Tca.xlf:tx_mkpostman_subscribers.gender.1', '1'),
				),
			)
		),
		'first_name' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:mkpostman/Resources/Private/Language/Tca.xlf:tx_mkpostman_subscribers.first_name',
			'config' => array(
				'type' => 'input',
				'size' => '20',
				'max' => '60',
				'eval' => 'trim'
			)
		),
		'last_name' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:mkpostman/Resources/Private/Language/Tca.xlf:tx_mkpostman_subscribers.last_name',
			'config' => array(
				'type' => 'input',
				'size' => '20',
				'max' => '60',
				'eval' => 'trim'
			)
		),
		'email' => array(
			'exclude' => 0,
			'label' => 'LLL:EXT:mkpostman/Resources/Private/Language/Tca.xlf:tx_mkpostman_subscribers.email',
			'config' => array(
				'type' => 'input',
				'size' => '20',
				'max' => '255',
				'eval' => 'trim,required'
			)
		),
	),
	'types' => array(
		'0' => array('showitem' => 'hidden, email, gender, first_name, last_name, confirmstring')
	)
);