<?php

defined('TYPO3_MODE') || exit('Access denied.');

/* *** **************** *** *
 * *** Register Actions *** *
 * *** **************** *** */
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['tx_mkpostman'] = 'pi_flexform';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['tx_mkpostman'] = 'layout,select_key,pages';
tx_rnbase_util_Extensions::addPiFlexFormValue(
    'tx_mkpostman',
    'FILE:EXT:mkpostman/Configuration/Flexform/Main.xml'
);
tx_rnbase_util_Extensions::addPlugin(
    [
        'LLL:EXT:mkpostman/Resources/Private/Language/Flexform.xlf:plugin.mkpostman.label',
        'tx_mkpostman',
        'EXT:mkpostman/ext_icon.gif',
    ],
    'list_type',
    'mkpostman'
);
tx_rnbase_util_Extensions::addStaticFile(
    'mkpostman',
    'Configuration/TypoScript/Base/',
    'MK Postman (Base)'
);

if (TYPO3_MODE == 'BE') {
    // add be module ts and wizard config
    tx_rnbase_util_Extensions::addPageTSConfig(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:mkpostman/Configuration/TypoScript/Backend/pageTSconfig.txt">'
    );

    if (tx_rnbase_util_TYPO3::isTYPO80OrHigher()) {
        // register icon
        Tx_Rnbase_Backend_Utility_Icons::getIconRegistry()->registerIcon(
            'ext-mkpostman-wizard-icon',
            'TYPO3\\CMS\Core\\Imaging\\IconProvider\\BitmapIconProvider',
            ['source' => 'EXT:mkpostman/ext_icon.gif']
        );
    } else {
        // register wizzard the old way
        \DMK\Mkpostman\Utility\WizIconUtility::addWizicon(
            'DMK\Mkpostman\Utility\WizIconUtility',
            tx_rnbase_util_Extensions::extPath(
                'mkpostman',
                'Classes/Utility/WizIconUtility.php'
            )
        );
    }

    // register web_MkpostmanBackend
    tx_rnbase_util_Extensions::registerModule(
        'mkpostman',
        'web',
        'backend',
        'bottom',
        [
        ],
        [
            'access' => 'user,group',
            'routeTarget' => 'DMK\\Mkpostman\\Backend\\ModuleBackend',
            'icon' => 'EXT:mkpostman/ext_icon.gif',
            'labels' => 'LLL:EXT:mkpostman/Resources/Private/Language/Backend.xlf',
        ]
    );

    // register subscriber be module
    tx_rnbase_util_Extensions::insertModuleFunction(
        'web_MkpostmanBackend',
        'DMK\\Mkpostman\\Backend\\Module\\SubscriberModule',
        null,
        'LLL:EXT:mkpostman/Resources/Private/Language/Backend.xlf:label_func_subscriber'
    );
}
