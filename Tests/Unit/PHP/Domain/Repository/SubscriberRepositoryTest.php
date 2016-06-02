<?php
namespace DMK\Mkpostman\Domain\Repository;

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
 * Subscriber model test
 *
 * @package TYPO3
 * @subpackage Tx_Hpsplaner
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SubscriberRepositoryTest
	extends \DMK\Mkpostman\Tests\BaseTestCase
{
	/**
	 * Test the getSearchClass method.
	 *
	 * @return void
	 *
	 * @group unit
	 * @test
	 */
	public function testGetSearchClassShouldBeGeneric() {
		self::assertEquals(
			'tx_rnbase_util_SearchGeneric',
			$this->callInaccessibleMethod(
				$this->getRepository(),
				'getSearchClass'
			)
		);
	}

	/**
	 * Test the getEmptyModel method.
	 *
	 * @return void
	 *
	 * @group unit
	 * @test
	 */
	public function testGetEmptyModelShouldBeBaseModelWithPageTable() {
		$model = $this->callInaccessibleMethod(
			$this->getRepository(),
			'getEmptyModel'
		);
		self::assertInstanceOf(
			'DMK\\Mkpostman\\Domain\\Model\\SubscriberModel',
			$model
		);
		self::assertSame(
			'tx_mkpostman_subscribers',
			$model->getTablename()
		);
	}

	/**
	 * Test the getEmptyModel method.
	 *
	 * @return void
	 *
	 * @group unit
	 * @test
	 */
	public function testfindByEmailCallsSearchCorectly()
	{
		$mail = 'mwagner@localhost.net';
		$repo = $this->getRepository();
		$searcher = $this->callInaccessibleMethod($repo, 'getSearcher');

		$searcher
			->expects(self::once())
			->method('search')
			->with(
				$this->callback(
					function($fields) use ($mail)
					{
						self::assertTrue(is_array($fields));

						// only the mail should be filtered
						self::assertCount(1, $fields);
						self::assertArrayHasKey('SUBSCRIBER.email', $fields);
						self::assertTrue(is_array($fields['SUBSCRIBER.email']));

						// only the eq str should be performed
						self::assertCount(1, $fields['SUBSCRIBER.email']);
						self::assertArrayHasKey(OP_EQ, $fields['SUBSCRIBER.email']);
						self::assertSame($mail, $fields['SUBSCRIBER.email'][OP_EQ]);

						return true;
					}
				),
				$this->callback(
					function($options)
					{
						self::assertTrue(is_array($options));

						// the limit should be set, the mail in uniq!
						self::assertArrayHasKey('limit', $options);
						self::assertSame(1, $options['limit']);

						// enablefields be are set, we want hidden/inactive subscribers!
						self::assertArrayHasKey('enablefieldsbe', $options);
						self::assertTrue($options['enablefieldsbe']);

						return true;
					}
				)
			)
		;

		$repo->findByEmail($mail);
	}

	/**
	 * Test the prepareGenericSearcher method.
	 *
	 * @return void
	 *
	 * @group unit
	 * @test
	 */
	public function testPrepareGenericSearcherShouldBeTheRightSearchdefConfig()
	{
		$repo = $this->getRepository();
		$searcher = $this->callInaccessibleMethod($repo, 'getSearcher');

		$searcher
			->expects(self::once())
			->method('search')
			->with(
				$this->callback(
					function($fields)
					{
						return is_array($fields) && empty($fields);
					}
				),
				$this->callback(
					function($options) use ($repo)
					{
						$tablename = $repo->getEmptyModel()->getTableName();
						self::assertTrue(is_array($options));

						self::assertArrayHasKey('searchdef', $options);
						self::assertTrue(is_array($options['searchdef']));

						$sd = $options['searchdef'];
						self::assertArrayHasKey('usealias', $sd);
						self::assertSame($sd['usealias'], 1);
						self::assertArrayHasKey('basetable', $sd);
						self::assertSame($sd['basetable'], $tablename);
						self::assertArrayHasKey('basetablealias', $sd);
						self::assertSame($sd['basetablealias'], 'SUBSCRIBER');
						self::assertArrayHasKey('wrapperclass', $sd);
						self::assertSame($sd['wrapperclass'], get_class($repo->getEmptyModel()));

						self::assertArrayHasKey('alias', $sd);
						self::assertTrue(is_array($sd['alias']));
						self::assertArrayHasKey('SUBSCRIBER', $sd['alias']);
						self::assertTrue(is_array($sd['alias']['SUBSCRIBER']));
						self::assertArrayHasKey('table', $sd['alias']['SUBSCRIBER']);
						self::assertSame($sd['alias']['SUBSCRIBER']['table'], $tablename);

						return true;
					}
				)
			)
			->will(self::returnValue(new \ArrayObject()))
		;

		self::assertInstanceOf('ArrayObject', $repo->findAll());
	}

	/**
	 * Test the prepareGenericSearcher method.
	 *
	 * @return void
	 *
	 * @group unit
	 * @test
	 */
	public function testPrepareGenericSearcherShouldUseArrayObject()
	{
		$repo = $this->getRepository();
		$searcher = $this->callInaccessibleMethod($repo, 'getSearcher');

		$searcher
			->expects(self::once())
			->method('search')
			->with(
				$this->callback(
					function($fields)
					{
						return is_array($fields);
					}
				),
				$this->callback(
					function($options) use ($repo)
					{
						self::assertTrue(is_array($options));

						self::assertArrayHasKey('array_object', $options);
						self::assertTrue($options['array_object']);

						return true;
					}
				)
			)
			->will(self::returnValue(new \ArrayObject()))
		;

		self::assertInstanceOf('ArrayObject', $repo->findAll());
	}

	/**
	 * Creates the repo mock
	 *
	 * @return PHPUnit_Framework_MockObject_MockObject
	 */
	protected function getRepository()
	{
		\tx_rnbase::load('tx_rnbase_util_SearchGeneric');
		$searcher = $this->getMock(
			'tx_rnbase_util_SearchGeneric',
			array('search')
		);

		\tx_rnbase::load('DMK\\Mkpostman\\Domain\\Repository\\SubscriberRepository');
		$repo = $this->getMock(
			'DMK\\Mkpostman\\Domain\\Repository\\SubscriberRepository',
			array('getSearcher')
		);

		$repo
			->expects(self::any())
			->method('getSearcher')
			->will(self::returnValue($searcher))
		;

		return $repo;
	}
}
