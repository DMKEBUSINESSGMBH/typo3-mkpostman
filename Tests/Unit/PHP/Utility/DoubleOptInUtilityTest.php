<?php
namespace DMK\Mkpostman\Utility;

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
 * DoubleOptInUtility test
 *
 * @package TYPO3
 * @subpackage Tx_Hpsplaner
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class DoubleOptInUtilityTest
	extends \DMK\Mkpostman\Tests\BaseTestCase
{
	/**
	 * Test the createConfirmString method
	 *
	 * @return void
	 *
	 * @group unit
	 * @test
	 */
	public function testCreateConfirmString()
	{
		$confirmString = $this->callInaccessibleMethod(
			$this->getUtility(),
			'createConfirmString'
		);

		// we only check for length and containing chars
		self::assertRegExp('/^[a-f0-9]{32}$/', $confirmString);

		return $confirmString;
	}

	/**
	 * Test the updateConfirmString method
	 *
	 * @return void
	 *
	 * @depends testCreateConfirmString
	 * @group unit
	 * @test
	 */
	public function testUpdateConfirmString(
		$confirmString
	) {
		$util = $this->getUtility(array('createConfirmString'));
		$subscriber = $this->callInaccessibleMethod($util, 'getSubscriber');

		$util
			->expects(self::once())
			->method('createConfirmString')
			->will(self::returnValue($confirmString))
		;

		$this->callInaccessibleMethod($util, 'updateConfirmString');

		// we only check for length and containing chars
		self::assertSame($confirmString, $subscriber->getConfirmstring());
	}

	/**
	 * Test the buildActivationKey method
	 *
	 * @return void
	 *
	 * @depends testCreateConfirmString
	 * @group unit
	 * @test
	 */
	public function testBuildActivationKey(
		$confirmString
	) {
		$util = $this->getUtility(array('createConfirmString'));
		$subscriber = $this->callInaccessibleMethod($util, 'getSubscriber');

		$subscriber->setConfirmstring($confirmString);

		$key = $this->callInaccessibleMethod($util, 'buildActivationKey', false);

		// the key should contain the uid, confirmstring and the md5 of the mail
		self::assertSame(2, substr_count($key, ':'));
		$parts = explode(':', $key);
		self::assertCount(3, $parts);

		self::assertEquals($subscriber->getUid(), $parts[0]);
		self::assertEquals($subscriber->getConfirmstring(), $parts[1]);
		self::assertEquals(md5($subscriber->getEmail()), $parts[2]);

		return array($confirmString, $key);
	}

	/**
	 * Test the buildActivationKey method
	 *
	 * @return void
	 *
	 * @depends testCreateConfirmString
	 * @group unit
	 * @test
	 */
	public function testBuildActivationKeyEncoded(
		$confirmString
	) {
		$util = $this->getUtility(array('createConfirmString'));
		$subscriber = $this->callInaccessibleMethod($util, 'getSubscriber');

		$subscriber->setConfirmstring($confirmString);

		$key = $this->callInaccessibleMethod($util, 'buildActivationKey', true);

		$key = \base64_decode(\urldecode($key));

		// the key should contain the uid, confirmstring and the md5 of the mail
		self::assertSame(2, substr_count($key, ':'));
		$parts = explode(':', $key);
		self::assertCount(3, $parts);

		self::assertEquals($subscriber->getUid(), $parts[0]);
		self::assertEquals($subscriber->getConfirmstring(), $parts[1]);
		self::assertEquals(md5($subscriber->getEmail()), $parts[2]);
	}

	/**
	 * Test the validateActivationKey method
	 *
	 * @return void
	 *
	 * @depends testBuildActivationKey
	 * @group unit
	 * @test
	 */
	public function testValidateActivationKey(
		array $params = array()
	) {
		list ($confirmString, $activationKey) = $params;

		$util = $this->getUtility(array('createConfirmString'));
		$subscriber = $this->callInaccessibleMethod($util, 'getSubscriber');

		$subscriber->setConfirmstring($confirmString);

		self::assertTrue(
			$this->callInaccessibleMethod($util, 'validateActivationKey', $activationKey)
		);
	}

	/**
	 * Test the validateActivationKey method
	 *
	 * @return void
	 *
	 * @depends testBuildActivationKey
	 * @group unit
	 * @test
	 */
	public function testValidateActivationKeyEncoded(
		array $params = array()
	) {
		list ($confirmString, $activationKey) = $params;

		$util = $this->getUtility(array('createConfirmString'));
		$subscriber = $this->callInaccessibleMethod($util, 'getSubscriber');

		$subscriber->setConfirmstring($confirmString);

		$activationKey = \urlencode(\base64_encode($activationKey));

		self::assertTrue(
			$this->callInaccessibleMethod($util, 'validateActivationKey', $activationKey)
		);
	}

	/**
	 * Creates a util instace mock
	 *
	 * @return PHPUnit_Framework_MockObject_MockObject|DMK\Mkpostman\Utility\DoubleOptInUtility
	 */
	protected function getUtility(
		array $methods = array()
	) {
		\tx_rnbase::load('DMK\\Mkpostman\\Utility\\DoubleOptInUtility');
		return $this->getMock(
			'DMK\\Mkpostman\\Utility\\DoubleOptInUtility',
			$methods,
			array($this->getSubscriberModel())
		);
	}
}
