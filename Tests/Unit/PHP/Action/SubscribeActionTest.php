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
 * Subscribtion action test
 *
 * @package TYPO3
 * @subpackage Tx_Hpsplaner
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SubscribeActionTest
	extends \DMK\Mkpostman\Tests\BaseTestCase
{
	/**
	 * Test the handleRequest method
	 *
	 * @return void
	 *
	 * @group unit
	 * @test
	 */
	public function testHandeRequestForm()
	{
		\tx_rnbase::load('DMK\\Mkpostman\\Action\\SubscribeAction');
		$action = $this->getMock(
			'DMK\\Mkpostman\\Action\\SubscribeAction',
			array('handleForm', 'handleActivation', 'handleSuccess')
		);

		$action
			->expects(self::never())
			->method('handleActivation')
		;
		$action
			->expects(self::never())
			->method('handleSuccess')
		;
		$action
			->expects(self::once())
			->method('handleForm')
		;

		$null = null;
		self::assertSame(
			null,
			$action->handleRequest(
				$this->getMock('tx_rnbase_parameters'),
				$null,
				$null
			)
		);
	}

	/**
	 * Test the handleRequest method
	 *
	 * @return void
	 *
	 * @group unit
	 * @test
	 */
	public function testHandeRequestActivationWithValidKey()
	{
		\tx_rnbase::load('DMK\\Mkpostman\\Action\\SubscribeAction');
		$action = $this->getMock(
			'DMK\\Mkpostman\\Action\\SubscribeAction',
			array('handleForm', 'handleActivation', 'handleSuccess')
		);


		$action
			->expects(self::once())
			->method('handleActivation')
			->with(self::equalTo('valid'))
			->will(self::returnValue(true))
		;
		$action
			->expects(self::never())
			->method('handleSuccess')
		;
		$action
			->expects(self::never())
			->method('handleForm')
		;
		/* @var $parameters \tx_rnbase_parameters */
		$parameters = \tx_rnbase::makeInstance('tx_rnbase_parameters');
		$parameters->offsetSet('key', 'valid');
		$null = null;
		self::assertSame(
			null,
			$action->handleRequest(
				$parameters,
				$null,
				$null
			)
		);
	}

	/**
	 * Test the handleRequest method
	 *
	 * @return void
	 *
	 * @group unit
	 * @test
	 */
	public function testHandeRequestActivationWithInvalidKey()
	{
		\tx_rnbase::load('DMK\\Mkpostman\\Action\\SubscribeAction');
		$action = $this->getMock(
			'DMK\\Mkpostman\\Action\\SubscribeAction',
			array('handleForm', 'handleActivation', 'handleSuccess')
		);


		$action
			->expects(self::once())
			->method('handleActivation')
			->with(self::equalTo('invalid'))
			->will(self::returnValue(false))
		;
		$action
			->expects(self::never())
			->method('handleSuccess')
		;
		$action
			->expects(self::once())
			->method('handleForm')
		;
		/* @var $parameters \tx_rnbase_parameters */
		$parameters = \tx_rnbase::makeInstance('tx_rnbase_parameters');
		$parameters->offsetSet('key', 'invalid');
		$null = null;
		self::assertSame(
			null,
			$action->handleRequest(
				$parameters,
				$null,
				$null
			)
		);
	}

	/**
	 * Test the handleRequest method
	 *
	 * @return void
	 *
	 * @group unit
	 * @test
	 */
	public function testHandeRequestSuccessWithValidKey()
	{
		\tx_rnbase::load('DMK\\Mkpostman\\Action\\SubscribeAction');
		$action = $this->getMock(
			'DMK\\Mkpostman\\Action\\SubscribeAction',
			array('handleForm', 'handleActivation', 'handleSuccess')
		);

		$action
			->expects(self::never())
			->method('handleActivation')
		;
		$action
			->expects(self::once())
			->method('handleSuccess')
			->with(self::equalTo('referer:7'))
			->will(self::returnValue(true))
		;
		$action
			->expects(self::never())
			->method('handleForm')
		;
		/* @var $parameters \tx_rnbase_parameters */
		$parameters = \tx_rnbase::makeInstance('tx_rnbase_parameters');
		$parameters->offsetSet('success', 'referer:7');
		$null = null;
		self::assertSame(
			null,
			$action->handleRequest(
				$parameters,
				$null,
				$null
			)
		);
	}

	/**
	 * Test the handleRequest method
	 *
	 * @return void
	 *
	 * @group unit
	 * @test
	 */
	public function testHandeRequestSuccessWithInvalidKey()
	{
		\tx_rnbase::load('DMK\\Mkpostman\\Action\\SubscribeAction');
		$action = $this->getMock(
			'DMK\\Mkpostman\\Action\\SubscribeAction',
			array('handleForm', 'handleActivation', 'handleSuccess')
		);

		$action
			->expects(self::never())
			->method('handleActivation')
		;
		$action
			->expects(self::once())
			->method('handleSuccess')
			->with(self::equalTo('invalid'))
			->will(self::returnValue(false))
		;
		$action
			->expects(self::once())
			->method('handleForm')
		;
		/* @var $parameters \tx_rnbase_parameters */
		$parameters = \tx_rnbase::makeInstance('tx_rnbase_parameters');
		$parameters->offsetSet('success', 'invalid');
		$null = null;
		self::assertSame(
			null,
			$action->handleRequest(
				$parameters,
				$null,
				$null
			)
		);
	}

	/**
	 * Test the handleActivation method
	 *
	 * @return void
	 *
	 * @group unit
	 * @test
	 */
	public function testHandleActivation()
	{
		// @TODO check if $doubleOptInUtil->activateByKey was called correctly!
		// @TODO check if the performSuccessRedirect was called correctly
		$this->markTestIncomplete();
	}

	/**
	 * Test the handleSuccess method
	 *
	 * @return void
	 *
	 * @group unit
	 * @test
	 */
	public function testHandleSuccess()
	{
		$this->markTestIncomplete();
	}

	/**
	 * Test the fillData method
	 *
	 * @return void
	 *
	 * @group unit
	 * @test
	 */
	public function testFillDataWithoutUser()
	{
		\tx_rnbase::load('DMK\\Mkpostman\\Action\\SubscribeAction');
		$action = $this->getMockForAbstract(
			'DMK\\Mkpostman\\Action\\SubscribeAction',
			array('getFeUserData')
		);
		$action
			->expects(self::once())
			->method('getFeUserData')
			->will(self::returnValue(array()))
		;

		$data = $this->callInaccessibleMethod($action, 'fillData', array());

		self::assertTrue(is_array($data));
		self::assertArrayHasKey('subscriber', $data);
		self::assertTrue(is_array($data['subscriber']));
		self::assertTrue(empty($data['subscriber']));
	}

	/**
	 * Test the fillData method
	 *
	 * @return void
	 *
	 * @group unit
	 * @test
	 */
	public function testFillDataWithUser()
	{
		$userdata = array(
			'gender' => 1,
			'first_name' => 'Michael',
			'last_name' => 'Wagner',
			'email' => 'mwagner\'s mail',
		);
		\tx_rnbase::load('DMK\\Mkpostman\\Action\\SubscribeAction');
		$action = $this->getMockForAbstract(
			'DMK\\Mkpostman\\Action\\SubscribeAction',
			array('getFeUserData')
		);
		$action
			->expects(self::once())
			->method('getFeUserData')
			->will(self::returnValue($userdata))
		;

		$data = $this->callInaccessibleMethod($action, 'fillData', array());

		self::assertTrue(is_array($data));
		self::assertArrayHasKey('subscriber', $data);
		self::assertTrue(is_array($data['subscriber']));
		self::assertSame(
			\array_map('strval', $userdata),
			$data['subscriber']
		);
	}

	/**
	 * Test the findOrCreateSubscriber method
	 *
	 * @return void
	 *
	 * @group unit
	 * @test
	 */
	public function testFindOrCreateSubscriberForNewUser()
	{
		\tx_rnbase::load('DMK\\Mkpostman\\Domain\\Model\\SubscriberModel');
		\tx_rnbase::load('DMK\\Mkpostman\\Domain\\Repository\\SubscriberRepository');
		$repo = $this->getMock(
			'Mkpostman_Tests_DomainRepositorySubscriberRepository',
			\get_class_methods('DMK\\Mkpostman\\Domain\\Repository\\SubscriberRepository')
		);
		$repo
			->expects(self::once())
			->method('findByEmail')
			->with($this->equalTo('mwagner@localhost.net'))
			->will(self::returnValue(null))
		;
		$repo
			->expects(self::once())
			->method('createNewModel')
			->will(
				self::returnValue(
					$this->getModel(
						array(),
						'DMK\\Mkpostman\\Domain\\Model\\SubscriberModel'
					)
				)
			)
		;

		$configuration = $this->createConfigurations(
			array(
				'subscribe.' => array(
					'subscriber.' => array(
						'storage' => 14,
					),
				),
			),
			'mkpostman'
		);

		$action = $this->getMockForAbstract(
			'DMK\\Mkpostman\\Action\\SubscribeAction',
			array('getSubscriberRepository', 'getConfigurations')
		);
		$action
			->expects(self::once())
			->method('getSubscriberRepository')
			->will(self::returnValue($repo))
		;
		$action
			->expects(self::once())
			->method('getConfigurations')
			->will(self::returnValue($configuration))
		;

		$model = $this->callInaccessibleMethod(
			$action,
			'findOrCreateSubscriber',
			array(
				'email' => 'mwagner@localhost.net',
			)
		);

		self::assertInstanceOf('DMK\\Mkpostman\\Domain\\Model\\SubscriberModel', $model);

		// the created model should have a pid and should be hidden, nothing else
		self::assertCount(2, $model->getProperty());
		self::assertSame(1, $model->getHidden());
		self::assertSame(14, $model->getpid());
	}

	/**
	 * Test the findOrCreateSubscriber method
	 *
	 * @return void
	 *
	 * @group unit
	 * @test
	 */
	public function testFindOrCreateSubscriberForExistingUser()
	{
		\tx_rnbase::load('DMK\\Mkpostman\\Domain\\Model\\SubscriberModel');
		$subscriber = $this->getModel(
			array(
				'uid' => 5,
				'pid' => 7,
				'hidden' => 0,
				'gender' => 1,
				'first_name' => 'Michael',
				'last_name' => 'Wagner',
				'email' => 'mwagner@localhost.net',
			),
			'DMK\\Mkpostman\\Domain\\Model\\SubscriberModel'
		);
		\tx_rnbase::load('DMK\\Mkpostman\\Domain\\Repository\\SubscriberRepository');
		$repo = $this->getMock(
			'Mkpostman_Tests_DomainRepositorySubscriberRepository',
			\get_class_methods('DMK\\Mkpostman\\Domain\\Repository\\SubscriberRepository')
		);
		$repo
			->expects(self::once())
			->method('findByEmail')
			->with($this->equalTo('mwagner@localhost.net'))
			->will(self::returnValue($subscriber))
		;
		$repo
			->expects(self::never())
			->method('createNewModel')
		;

		$action = $this->getMockForAbstract(
			'DMK\\Mkpostman\\Action\\SubscribeAction',
			array('getSubscriberRepository', 'getConfigurations')
		);
		$action
			->expects(self::once())
			->method('getSubscriberRepository')
			->will(self::returnValue($repo))
		;

		$model = $this->callInaccessibleMethod(
			$action,
			'findOrCreateSubscriber',
			array(
				'email' => 'mwagner@localhost.net',
			)
		);

		self::assertInstanceOf('DMK\\Mkpostman\\Domain\\Model\\SubscriberModel', $model);

		self::assertSame($model->getProperty(), $subscriber->getProperty());
	}

	/**
	 * Test the getConfId method
	 *
	 * @return void
	 *
	 * @group unit
	 * @test
	 */
	public function testPerformSuccessRedirect()
	{
		$this->markTestIncomplete();
	}

	/**
	 * Test the getConfId method
	 *
	 * @return void
	 *
	 * @group unit
	 * @test
	 */
	public function testGetConfIdShouldReturnsRightValue()
	{
		\tx_rnbase::load('DMK\\Mkpostman\\Action\\SubscribeAction');
		$action = $this->getMockForAbstractClass('DMK\\Mkpostman\\Action\\SubscribeAction');
		self::assertSame('subscribe.', $action->getConfId());
	}

	/**
	 * Test the getTemplateName method
	 *
	 * @return void
	 *
	 * @group unit
	 * @test
	 */
	public function testGetTemplateNameShouldReturnsRightValue()
	{
		\tx_rnbase::load('DMK\\Mkpostman\\Action\\SubscribeAction');
		$action = $this->getMockForAbstractClass('DMK\\Mkpostman\\Action\\SubscribeAction');
		$name = $this->callInaccessibleMethod($action, 'getTemplateName');
		self::assertSame('subscribe', $name);
	}

	/**
	 * Test the getViewClassName method
	 *
	 * @return void
	 *
	 * @group unit
	 * @test
	 */
	public function testGetViewClassShouldReturnsRightValue()
	{
		\tx_rnbase::load('DMK\\Mkpostman\\Action\\SubscribeAction');
		$action = $this->getMockForAbstractClass('DMK\\Mkpostman\\Action\\SubscribeAction');
		$name  = $this->callInaccessibleMethod($action, 'getViewClassName');
		self::assertSame('DMK\\Mkpostman\\View\\SubscribeView', $name);
	}
}
