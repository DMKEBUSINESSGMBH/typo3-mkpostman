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
	 * Test the constructor method
	 *
	 * @return void
	 *
	 * @group unit
	 * @test
	 */
	public function testConstructorWithModel()
	{
		$this->getMock(
			'DMK\\Mkpostman\\Utility\\DoubleOptInUtility',
			array(),
			array($this->getSubscriberModel())
		);
	}

	/**
	 * Test the constructor method
	 *
	 * @return void
	 *
	 * @group unit
	 * @test
	 */
	public function testConstructorWithKey()
	{
		$util = $this->getMock(
			'DMK\\Mkpostman\\Utility\\DoubleOptInUtility',
			array('findSubscriberByKey'),
			array(),
			'',
			false
		);

		$util
			->expects(self::once())
			->method('findSubscriberByKey')
			->with(
				self::callback(
					function ($keyData) {

						self::assertInstanceOf('Tx_Rnbase_Domain_Model_Data', $keyData);
						self::assertSame('5', $keyData->getUid());
						self::assertSame('abcdef1234567890', $keyData->getConfirmstring());
						self::assertSame('123456789abcdef', $keyData->getMailHash());

						return true;
					}
				)
			)
			->will(self::returnValue($this->getSubscriberModel()))
		;

		// now call the constructor
		$reflectedClass = new \ReflectionClass(
			'DMK\\Mkpostman\\Utility\\DoubleOptInUtility'
		);
		$constructor = $reflectedClass->getConstructor();
		$constructor->invoke($util, '5:abcdef1234567890:123456789abcdef');
	}

	/**
	 * Test the constructor method
	 *
	 * @return void
	 *
	 * @group unit
	 * @test
	 */
	public function testConstructorWithInvalidData()
	{
		$this->setExpectedException(
			'BadMethodCallException',
			'',
			1464951846
		);
		$this->getMock(
			'DMK\\Mkpostman\\Utility\\DoubleOptInUtility',
			array(),
			array('')
		);
	}

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
		$repo = $this->callInaccessibleMethod($util, 'getRepository');

		$util
			->expects(self::once())
			->method('createConfirmString')
			->will(self::returnValue($confirmString))
		;

		$repo
			->expects(self::once())
			->method('persist')
			->with(self::equalTo($subscriber))
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
	 * Test the decodeActivationKey method
	 *
	 * @return void
	 *
	 * @group unit
	 * @test
	 */
	public function testDecodeActivationKey()
	{
		$util = $this->getUtility();
		$keyData = $this->callInaccessibleMethod(
			$util,
			'decodeActivationKey',
			'firstIsUid:secondIsConfirmstring:thirdIsMailHash'
		);

		self::assertInstanceOf('Tx_Rnbase_Domain_Model_Data', $keyData);
		self::assertSame('firstIsUid', $keyData->getUid());
		self::assertSame('secondIsConfirmstring', $keyData->getConfirmstring());
		self::assertSame('thirdIsMailHash', $keyData->getMailHash());
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
	 * Test the activateByKey method
	 *
	 * @return void
	 *
	 * @depends testBuildActivationKey
	 * @group unit
	 * @test
	 */
	public function testActivateByKeyWithValidKey(
		array $params = array()
	) {
		list ($confirmString, $activationKey) = $params;

		$util = $this->getUtility(array('createConfirmString'));
		$subscriber = $this->callInaccessibleMethod($util, 'getSubscriber');
		$repo = $this->callInaccessibleMethod($util, 'getRepository');

		$repo
			->expects(self::once())
			->method('persist')
			->with(self::equalTo($subscriber))
		;

		$subscriber->setHidden(1);
		$subscriber->setConfirmstring($confirmString);

		self::assertTrue($util->activateByKey($activationKey));

		self::assertSame('', $subscriber->getConfirmstring());
		self::assertSame(0, $subscriber->getHidden());
	}

	/**
	 * Test the activateByKey method
	 *
	 * @return void
	 *
	 * @depends testCreateConfirmString
	 * @group unit
	 * @test
	 */
	public function testActivateByKeyWithInvalidKey(
		$confirmString
	) {

		$util = $this->getUtility(array('createConfirmString'));
		$subscriber = $this->callInaccessibleMethod($util, 'getSubscriber');
		$repo = $this->callInaccessibleMethod($util, 'getRepository');

		$repo
			->expects(self::never())
			->method('persist')
		;

		$subscriber->setHidden(1);
		$subscriber->setConfirmstring($confirmString);

		self::assertFalse($util->activateByKey('5:in:valid'));

		self::assertSame($confirmString, $subscriber->getConfirmstring());
		self::assertSame(1, $subscriber->getHidden());
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
		$util = $this->getMock(
			'DMK\\Mkpostman\\Utility\\DoubleOptInUtility',
			array_merge(
				array('getRepository'),
				$methods
			),
			array($this->getSubscriberModel())
		);

		$util
			->expects(self::any())
			->method('getRepository')
			->will(self::returnValue($this->getSubscriberRepository()))
		;

		return $util;
	}
}
