<?php
namespace DMK\Mkpostman\Domain\Model;

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

\tx_rnbase::load('Tx_Rnbase_Domain_Model_Base');

/**
 * Subscriber Model
 *
 * @method int getGender()
 * @method Tx_Rnbase_Domain_Model_Data setGender() setGender(int $gender)
 * @method bool hasGender()
 * @method Tx_Rnbase_Domain_Model_Data unsGender()
 *
 * @method string getFirstName()
 * @method Tx_Rnbase_Domain_Model_Data setFirstName() setFirstName(string $firstname)
 * @method bool hasFirstName()
 * @method Tx_Rnbase_Domain_Model_Data unsFirstName()
 *
 * @method string getLastName()
 * @method Tx_Rnbase_Domain_Model_Data setLastName() setLastName(string $lastname)
 * @method bool hasLastName()
 * @method Tx_Rnbase_Domain_Model_Data unsLastName()
 *
 * @method string getEmail()
 * @method Tx_Rnbase_Domain_Model_Data setEmail() setEmail(string $email)
 * @method bool hasEmail()
 * @method Tx_Rnbase_Domain_Model_Data unsEmail()
 *
 * @method string getConfirmstring()
 * @method Tx_Rnbase_Domain_Model_Data setConfirmstring() setConfirmstring(string $confirmstring)
 * @method bool hasConfirmstring()
 * @method Tx_Rnbase_Domain_Model_Data unsConfirmstring()
 *
 * @method int getDisabled()
 * @method Tx_Rnbase_Domain_Model_Data setDisabled() setDisabled(int $disabled)
 *
 * @method int getDeleted()
 * @method Tx_Rnbase_Domain_Model_Data setDeleted() setDeleted(int $deleted)
 *
 * @package TYPO3
 * @subpackage DMK\Mkpostman
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SubscriberModel
	extends \Tx_Rnbase_Domain_Model_Base
{
	/**
	 * Liefert den aktuellen Tabellenname
	 *
	 * @return Tabellenname als String
	 */
	public function getTableName()
	{
		return 'tx_mkpostman_subscribers';
	}
}
