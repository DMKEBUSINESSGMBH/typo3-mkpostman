<?php
defined('TYPO3_MODE') || die('Access denied.');

/* *** **************** *** *
 * *** Register Actions *** *
 * *** **************** *** */
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['tx_mkpostman'] = 'pi_flexform';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['tx_mkpostman'] = 'layout,select_key,pages';
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
	'tx_mkpostman',
	'FILE:EXT:mkpostman/Configuration/Flexform/Main.xml'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPlugin(
	array(
		'LLL:EXT:mkpostman/Resources/Private/Language/Flexform.xlf:plugin.mkpostman.label',
		'tx_mkpostman',
		'EXT:mkpostman/ext_icon.gif',
	)
);
tx_rnbase_util_Extensions::addStaticFile(
	'mkpostman',
	'Configuration/TypoScript/Base/',
	'MK Postman (Base)'
);

if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['DMK\Mkpostman\Utility\WizIconUtility']
		= tx_rnbase_util_Extensions::extPath(
			'mkpostman',
			'Classes/Utility/WizIconUtility.php'
		);
}
