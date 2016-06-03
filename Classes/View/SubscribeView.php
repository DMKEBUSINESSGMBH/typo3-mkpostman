<?php
namespace DMK\Mkpostman\View;

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

\tx_rnbase::load('tx_mkforms_view_Form');

/**
 * MK Postman subscribe view
 *
 * @package TYPO3
 * @subpackage Tx_Hpsplaner
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SubscribeView
	extends \tx_mkforms_view_Form
{
	/**
	 * The subpart for this view
	 *
	 * @param \ArrayObject $viewData
	 *
	 * @return string
	 */
	public function getMainSubpart(\ArrayObject $viewData)
	{
		$subPartKey = $viewData->offsetGet('main_subpart_key');
		if (empty($subPartKey)) {
			$subPartKey = 'FORMWRAP';
		}

		return '###SUBSCRIBE_' . \strtoupper($subPartKey) . '###';
	}
}
