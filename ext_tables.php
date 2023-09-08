<?php

defined('TYPO3_MODE') || exit('Access denied.');

/* *** **************** *** *
 * *** Register Actions *** *
 * *** **************** *** */
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist']['tx_mkpostman'] = 'pi_flexform';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['tx_mkpostman'] = 'layout,select_key,pages';
\Sys25\RnBase\Utility\Extensions::addPiFlexFormValue(
    'tx_mkpostman',
    'FILE:EXT:mkpostman/Configuration/Flexform/Main.xml'
);
\Sys25\RnBase\Utility\Extensions::addPlugin(
    [
        'LLL:EXT:mkpostman/Resources/Private/Language/Flexform.xlf:plugin.mkpostman.label',
        'tx_mkpostman',
        'EXT:mkpostman/Resources/Public/Icons/Extension.svg',
    ],
    'list_type',
    'mkpostman'
);
\Sys25\RnBase\Utility\Extensions::addStaticFile(
    'mkpostman',
    'Configuration/TypoScript/Base/',
    'MK Postman (Base)'
);

if (TYPO3_MODE == 'BE') {
    // add be module ts and wizard config
    \Sys25\RnBase\Utility\Extensions::addPageTSConfig(
        '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:mkpostman/Configuration/TypoScript/Backend/pageTSconfig.txt">'
    );

    if (\Sys25\RnBase\Utility\TYPO3::isTYPO80OrHigher()) {
        // register icon
        \Sys25\RnBase\Backend\Utility\Icons::getIconRegistry()->registerIcon(
            'ext-mkpostman-wizard-icon',
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:mkpostman/Resources/Public/Icons/Extension.svg']
        );
    } else {
        // register wizzard the old way
        \DMK\Mkpostman\Utility\WizIconUtility::addWizicon(
            'DMK\Mkpostman\Utility\WizIconUtility',
            \Sys25\RnBase\Utility\Extensions::extPath(
                'mkpostman',
                'Classes/Utility/WizIconUtility.php'
            )
        );
    }

    // register web_MkpostmanBackend
    \Sys25\RnBase\Utility\Extensions::registerModule(
        'mkpostman',
        'web',
        'backend',
        'bottom',
        [
        ],
        [
            'access' => 'user,group',
            'routeTarget' => 'DMK\\Mkpostman\\Backend\\ModuleBackend',
            'icon' => 'EXT:mkpostman/Resources/Public/Icons/Extension.svg',
            'labels' => 'LLL:EXT:mkpostman/Resources/Private/Language/Backend.xlf',
        ]
    );

    // register subscriber be module
    \Sys25\RnBase\Utility\Extensions::insertModuleFunction(
        'web_MkpostmanBackend',
        'DMK\\Mkpostman\\Backend\\Module\\SubscriberModule',
        null,
        'LLL:EXT:mkpostman/Resources/Private/Language/Backend.xlf:label_func_subscriber'
    );
}
