<?php

defined('TYPO3_MODE') || exit('Access denied.');

// add some parameters to chash exclude
\Sys25\RnBase\Utility\CHashUtility::addExcludedParametersForCacheHash([
    'mkpostman[key]',
    'mkpostman[success]',
    'mkpostman[unsubscribe]',
]);

// add directmailo hook
if (\Sys25\RnBase\Utility\TYPO3::isExtLoaded('direct_mail')) {
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/direct_mail']['res/scripts/class.dmailer.php']['mailMarkersHook']['mkpostman'] = 'DMK\\Mkpostman\\Hook\\Directmail\\MailMarkersHook->main';
}

// add process datamap
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
    'DMK\\Mkpostman\\Hook\\LogWriterHook';

$GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry']['mkpostman_subscriberlog'] = [
    'nodeName' => 'subscriberLog',
    'priority' => 40,
    'class' => \DMK\Mkpostman\Utility\TcaUtility::class,
];
