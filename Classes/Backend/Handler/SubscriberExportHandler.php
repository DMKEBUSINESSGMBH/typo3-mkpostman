<?php
namespace DMK\Mkpostman\Backend\Handler;

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

\tx_rnbase::load('DMK\\Mkpostman\\Backend\\Handler\\SubscriberHandler');
\tx_rnbase::load('tx_mklib_mod1_export_IModFunc');

/**
 * Subscriber handler
 *
 * @package TYPO3
 * @subpackage DMK\Mkpostman
 * @author Michael Wagner
 */
class SubscriberExportHandler
	extends SubscriberHandler implements \tx_mklib_mod1_export_IModFunc
{
	/**
	 * The class for the searcher
	 *
	 * @return string
	 */
	protected function getListerClass()
	{
		return 'DMK\\Mkpostman\\Backend\\Lister\\SubscriberExportLister';
	}

	/**
	 * The confid for the modfunc
	 *
	 * Only an alias for the optional mklib based export
	 *
	 * @return string
	 */
	public function getConfId()
	{
		return $this->getSubID() . '.';
	}

	/**
	 * Only an alias for the optional mklib based export
	 *
	 * @return \DMK\Mkpostman\Backend\Lister\SubscriberExportLister
	 */
	public function getSearcher()
	{
		return $this->getLister();
	}

	/**
	 * Base listing
	 *
	 * @param string $template
	 * @param array $markerArray
	 *
	 * @return string
	 */
	protected function showSearch(
		$template,
		array &$markerArray = null,
		array &$subpartArray = null,
		array &$wrappedSubpartArray = null
	) {
		/* @var $exportHandler \tx_mklib_mod1_export_Handler */
		$exportHandler = \tx_rnbase::makeInstance(
			'tx_mklib_mod1_export_Handler',
			$this
		);
		// check for exports
		$exportHandler->handleExport();
		// parse template
		$template = $exportHandler->parseTemplate($template);

		return parent::showSearch($template, $markerArray);
	}
}
