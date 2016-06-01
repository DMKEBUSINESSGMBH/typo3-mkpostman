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

require_once \t3lib_extMgm::extPath('rn_base', 'class.tx_rnbase.php');
require_once \t3lib_extMgm::extPath('mkpostman', 'Tests/Unit/PHP/BaseTestCase.php');

/**
 * Abstract action test
 *
 * @package TYPO3
 * @subpackage Tx_Hpsplaner
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class AbstractActionTest
	extends \DMK\Mkpostman\Tests\BaseTestCase
{
	/**
	 * Test the getStorage method
	 *
	 * @return void
	 *
	 * @group unit
	 * @test
	 */
	public function testGetStorageReturnsRightInstance()
	{
		\tx_rnbase::load('DMK\\Mkpostman\\Action\\AbstractAction');
		$action = $this->getMockForAbstractClass('DMK\\Mkpostman\\Action\\AbstractAction');
		$storage = $this->callInaccessibleMethod($action, 'getStorage');
		self::assertInstanceOf('Tx_Rnbase_Domain_Model_Data', $storage);
	}

	/**
	 * Test the handleRequest method
	 *
	 * @return void
	 *
	 * @group unit
	 * @test
	 */
	public function testHandleRequestCallsDoRequest()
	{
		\tx_rnbase::load('DMK\\Mkpostman\\Action\\AbstractAction');
		$action = $this->getMockForAbstractClass('DMK\\Mkpostman\\Action\\AbstractAction');

		$action
			->expects(self::once())
			->method('doRequest')
			->with()
			->will(self::returnArgument(0))
		;

		$ret = $this->callInaccessibleMethod($action, 'handleRequest', null, null, null);

		// the handleRequest expects returns the first argument
		// this argument should be null. doRequest has no argument!
		self::assertSame(null, $ret);
	}

	/**
	 * Test the getTableName method
	 *
	 * @return void
	 *
	 * @group unit
	 * @test
	 */
	public function testSetToViewShouldStoreDataCorrectly()
	{
		\tx_rnbase::load('DMK\\Mkpostman\\Action\\AbstractAction');
		$action = $this->getMockForAbstractClass('DMK\\Mkpostman\\Action\\AbstractAction');

		$configuration = \tx_rnbase::makeInstance('tx_rnbase_configurations');
		$action->setConfigurations($configuration);

		$this->callInaccessibleMethod($action, 'setToView', 'test', '57');

		self::assertSame('57', $configuration->getViewData()->offsetGet('test'));
	}
}