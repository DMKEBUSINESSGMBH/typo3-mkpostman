<?php
namespace DMK\Mkpostman\Mail\Receiver;

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

// for non composer autoload support
if (!\class_exists('tx_rnbase')) {
	require_once \tx_rnbase_util_Extensions::extPath(
		'rn_base',
		'class.tx_rnbase.php'
	);
}
// for non composer autoload support
if (!\class_exists('DMK\\Mkpostman\\Tests\\BaseTestCase')) {
	require_once \tx_rnbase_util_Extensions::extPath(
		'mkpostman',
		'Tests/Unit/PHP/BaseTestCase.php'
	);
}

/**
 * Subscriber mail receiver test
 *
 * @package TYPO3
 * @subpackage Tx_Hpsplaner
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SubscriberReceiverTest
	extends \DMK\Mkpostman\Tests\BaseTestCase
{
	/**
	 * Test the prepareLinks method
	 *
	 * @return void
	 *
	 * @group unit
	 * @test
	 */
	public function testPrepareLinks()
	{
		$cObject = $this->getMock(
			\tx_rnbase_util_Typo3Classes::getContentObjectRendererClass(),
			array('typolink')
		);

		$cObject
			->expects(self::once())
			->method('typolink')
			->with(
				// only the url, no laben
				$this->equalTo(null),
				$this->callback(
					function($config)
					{
						self::assertTrue(is_array($config));
						self::assertArrayHasKey('additionalParams', $config);
						self::assertContains('mkpostman%5Bkey%5D=', $config['additionalParams']);

						return true;
					}
				)
			)
			->will(self::returnValue('?mkpostman%5Bkey%5D=foo'))
		;

		$configurations = $this->createConfigurations(
			array(
				'mails.' => array(
					'subscriber.' => array(
						'links.' => array(
							'activation.' => array(
								'absurl' => 'https://www.dmk-ebusiness.de/',
							)
						)
					)
				)
			),
			'mkpostman',
			'mkpostman',
			$cObject
		);

		$template = '###SUBSCRIBER_ACTIVATIONLINKURL###';
		$markerArray = array();
		$subpartArray = array();
		$wrappedSubpartArray = array();
		$confId = 'mails.subscriber.';

		$this->callInaccessibleMethod(
			array(
				$this->getReceiver(),
				'prepareLinks'
			),
			array(
				$template,
				&$markerArray,
				&$subpartArray,
				&$wrappedSubpartArray,
				$configurations->getFormatter(),
				$confId
			)
		);

		self::assertTrue(is_array($markerArray));
		self::assertArrayHasKey('###SUBSCRIBER_ACTIVATIONLINKURL###', $markerArray);
		self::assertContains('mkpostman%5Bkey%5D=', $markerArray['###SUBSCRIBER_ACTIVATIONLINKURL###']);
	}

	/**
	 * returns a mock of
	 *
	 * @return PHPUnit_Framework_MockObject_MockObject|\DMK\Mkpostman\Mail\Receiver\SubscriberReceiver
	 */
	protected function getReceiver()
	{
		\tx_rnbase::load('DMK\\Mkpostman\\Mail\\Receiver\\SubscriberReceiver');
		$receiver = $this->getMock(
			'DMK\\Mkpostman\\Mail\\Receiver\\SubscriberReceiver',
			array(),
			array($this->getSubscriberModel())
		);

		return $receiver;
	}
}
