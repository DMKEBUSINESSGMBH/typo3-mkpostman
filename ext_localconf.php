<?php
defined('TYPO3_MODE') || die('Access denied.');

tx_rnbase::load('DMK\Mkpostman\Factory');

// add some parameters to chash exclude
tx_rnbase::load('Tx_Rnbase_Utility_Cache');
Tx_Rnbase_Utility_Cache::addExcludedParametersForCacheHash(array(
    'mkpostman[key]',
    'mkpostman[success]'
));
