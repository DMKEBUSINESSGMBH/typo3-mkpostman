<?php

namespace DMK\Mkpostman\Backend\Lister;

use Sys25\RnBase\Frontend\Marker\IListProvider;
use Sys25\RnBase\Frontend\Marker\ListProvider;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/***************************************************************
 * Copyright notice
 *
 * (c) 2016 DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Subscriber export lister.
 *
 * This lister requires mklib!
 *
 * @author Michael Wagner
 */
class SubscriberExportLister extends SubscriberLister implements \tx_mklib_mod1_export_ISearcher
{
    /**
     * Liefert den List-Provider,
     * welcher die Ausgabe der einzelnen Datensätze generiert
     * und an den Listbuilder übergeben wird.
     *
     * @return IListProvider
     */
    public function getInitialisedListProvider()
    {
        list($fields, $options) = $this->getFieldsAndOptions();

        /* @var $provider ListProvider */
        $provider = GeneralUtility::makeInstance(ListProvider::class);
        $provider->initBySearch(
            [
                $this->getRepository(),
                'search',
            ],
            $fields,
            $options
        );

        return $provider;
    }
}
