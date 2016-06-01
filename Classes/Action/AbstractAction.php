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

\tx_rnbase::load('tx_rnbase_action_BaseIOC');

/**
 * Abstract base action
 *
 * @package TYPO3
 * @subpackage DMK\Mkpostman
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
abstract class AbstractAction
	extends \tx_rnbase_action_BaseIOC
{
	/**
	 * A storage object
	 *
	 * @var Tx_Rnbase_Domain_Model_Data
	 */
	private $storage = null;

	/**
	 * Lets do the magic
	 *
	 * @param tx_rnbase_IParameters $parameters
	 * @param tx_rnbase_configurations $configurations
	 * @param ArrayObject $viewdata
	 *
	 * @return string Errorstring or NULL
	 */
	// @codingStandardsIgnoreStart (interface/abstract mistake)
	protected function handleRequest(&$parameters, &$configurations, &$viewdata)
	{
		// @codingStandardsIgnoreEnd
		return $this->doRequest();
	}

	/**
	 * Wrapper method clean code
	 *
	 * @return string Errorstring or NULL
	 */
	abstract protected function doRequest();
	/*{
		$parameters = $this->getParameters();
		$configurations = $this->getConfigurations();
		$viewdata = $this->getViewData();

		return null;
	}*/

	/**
	 * A Storage object for the childclasses
	 *
	 * @return Tx_Rnbase_Domain_Model_Data
	 */
	protected function getStorage()
	{
		if ($this->storage === null) {
			\tx_rnbase::load('Tx_Rnbase_Domain_Model_Data');
			$this->storage = \Tx_Rnbase_Domain_Model_Data::getInstance();
		}

		return $this->storage;
	}

	/**
	 * Sets some data to the view
	 *
	 * @param string $name
	 * @param mixed $data
	 *
	 * @return void
	 */
	protected function setToView($name, $data)
	{
		$this->getViewData()->offsetSet($name, $data);
	}
}
