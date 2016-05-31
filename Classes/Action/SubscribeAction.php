<?php
namespace DMK\Mkpostman\Action;

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

\tx_rnbase::load('DMK\\Mkpostman\\Action\\AbstractAction');

/**
 * MK Postman subscribe action
 *
 * @package TYPO3
 * @subpackage DMK\Mkpostman
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SubscribeAction
	extends \DMK\Mkpostman\Action\AbstractAction
{
	/**
	 * Do the magic
	 *
	 * @return string Errorstring or NULL
	 */
	protected function doRequest()
	{
		$parameters = $this->getParameters();
		$configurations = $this->getConfigurations();
		$viewdata = $this->getViewData();

		return null;
	}

	/**
	 * Templatename and confid
	 *
	 * @return string
	 */
	protected function getTemplateName()
	{
		return 'subscribe';
	}

	/**
	 * Viewclassname
	 *
	 * @return string
	 */
	protected function getViewClassName()
	{
		return 'DMK\\Mkpostman\\View\\SubscribeView';
	}
}
