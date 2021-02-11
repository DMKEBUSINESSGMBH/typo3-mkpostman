<?php

defined('TYPO3_MODE') || exit('Access denied.');

tx_rnbase::load('DMK\Mkpostman\Factory');

// add some parameters to chash exclude
tx_rnbase::load('Tx_Rnbase_Utility_Cache');
Tx_Rnbase_Utility_Cache::addExcludedParametersForCacheHash([
    'mkpostman[key]',
    'mkpostman[success]',
    'mkpostman[unsubscribe]',
]);

// add directmailo hook
if (\tx_rnbase_util_TYPO3::isExtLoaded('direct_mail')) {
    tx_rnbase::load('DMK\\Mkpostman\\Hook\\Directmail\\MailMarkersHook');
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/direct_mail']['res/scripts/class.dmailer.php']['mailMarkersHook']['mkpostman'] = 'DMK\\Mkpostman\\Hook\\Directmail\\MailMarkersHook->main';
}

// add process datamap
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] =
    'DMK\\Mkpostman\\Hook\\LogWriterHook';
