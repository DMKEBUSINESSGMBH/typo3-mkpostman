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

require_once \t3lib_extMgm::extPath('rn_base', 'class.tx_rnbase.php');
require_once \t3lib_extMgm::extPath('mkpostman', 'Tests/Unit/PHP/BaseTestCase.php');

/**
 * Subscribtion view test
 *
 * @package TYPO3
 * @subpackage Tx_Hpsplaner
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SubscribeViewTest
	extends \DMK\Mkpostman\Tests\BaseTestCase
{
	/**
	 * Test the getMainSubpart method
	 *
	 * @return void
	 *
	 * @group unit
	 * @test
	 */
	public function testGetMainSubpart()
	{
		$configuration = $this->createConfigurations(array(), 'mkpostman');

		$action = $this->getMock(
			'DMK\\Mkpostman\\Action\\SubscribeAction',
			array('getConfigurations')
		);
		$action
			->expects(self::exactly(2))
			->method('getConfigurations')
			->will(self::returnValue($configuration))
		;

		$view = $this->getMock(
			'DMK\\Mkpostman\\View\\SubscribeView',
			array('getController')
		);
		$view
			->expects(self::exactly(2))
			->method('getController')
			->will(self::returnValue($action))
		;

		// formwrap by default
		self::assertSame('###SUBSCRIBE_FORMWRAP###', $view->getMainSubpart());

		// test subpart by viewdata
		$configuration->getViewData()->offsetSet('main_view_key', 'foo');
		self::assertSame('###SUBSCRIBE_FOO###', $view->getMainSubpart());
	}
}