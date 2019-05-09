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
 * Subscriber model test.
 *
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SubscriberRepositoryTest extends \DMK\Mkpostman\Tests\BaseTestCase
{
    /**
     * Test the getSearchClass method.
     *
     *
     * @group unit
     * @test
     */
    public function testGetSearchClassShouldBeGeneric()
    {
        $this->assertEquals(
            'tx_rnbase_util_SearchGeneric',
            $this->callInaccessibleMethod(
                $this->getSubscriberRepository(),
                'getSearchClass'
            )
        );
    }

    /**
     * Test the getEmptyModel method.
     *
     *
     * @group unit
     * @test
     */
    public function testGetEmptyModelShouldBeBaseModelWithPageTable()
    {
        $model = $this->callInaccessibleMethod(
            $this->getSubscriberRepository(),
            'getEmptyModel'
        );
        $this->assertInstanceOf(
            'DMK\\Mkpostman\\Domain\\Model\\SubscriberModel',
            $model
        );
        $this->assertSame(
            'tx_mkpostman_subscribers',
            $model->getTablename()
        );
    }

    /**
     * Test the findByEmail method.
     *
     *
     * @group unit
     * @test
     */
    public function testFindByEmailCallsSearchCorrectly()
    {
        $that = $this; // php 3.5 compatibility!
        $mail = 'mwagner@localhost.net';
        $repo = $this->getSubscriberRepository();
        $searcher = $this->callInaccessibleMethod($repo, 'getSearcher');

        $searcher
            ->expects(self::once())
            ->method('search')
            ->with(
                $this->callback(
                    function ($fields) use ($that, $mail) {
                        $that->assertTrue(is_array($fields));

                        // only the mail should be filtered
                        $that->assertCount(1, $fields);
                        $that->assertArrayHasKey('SUBSCRIBER.email', $fields);
                        $that->assertTrue(is_array($fields['SUBSCRIBER.email']));

                        // only the eq str should be performed
                        $that->assertCount(1, $fields['SUBSCRIBER.email']);
                        $that->assertArrayHasKey(OP_EQ, $fields['SUBSCRIBER.email']);
                        $that->assertSame($mail, $fields['SUBSCRIBER.email'][OP_EQ]);

                        return true;
                    }
                ),
                $this->callback(
                    function ($options) use ($that) {
                        $that->assertTrue(is_array($options));

                        // the limit should be set, the mail in uniq!
                        $that->assertArrayHasKey('limit', $options);
                        $that->assertSame(1, $options['limit']);

                        // enablefields be are set, we want disabled/inactive subscribers!
                        $that->assertArrayHasKey('enablefieldsbe', $options);
                        $that->assertTrue($options['enablefieldsbe']);

                        return true;
                    }
                )
            );

        $repo->findByEmail($mail);
    }

    /**
     * Test the findByEmail method.
     *
     *
     * @group unit
     * @test
     */
    public function testaddToCategories()
    {
        $that = $this; // php 3.5 compatibility!
        $categories = [10, 11];
        $subscriber = $this->getModel(
            array(
                'uid' => 5,
            ),
            'DMK\\Mkpostman\\Domain\\Model\\SubscriberModel'
        );
        $repo = $this->getSubscriberRepository();

        $connection = $this->getMock(
            'Tx_Rnbase_Database_Connection',
            array('doDelete', 'doInsert')
        );

        $connection
            ->expects(self::once())
            ->method('doDelete')
            ->with(
                $this->callback(
                    function ($tablename) use ($that) {
                        $that->assertSame('sys_category_record_mm', $tablename);

                        return true;
                    }
                ),
                $this->callback(
                    function ($where) use ($that, $subscriber) {
                        $that->assertSame('uid_foreign = '.$subscriber->getUid(), $where);

                        return true;
                    }
                )
            );

        $connection
            ->expects(self::exactly(2))
            ->method('doInsert')
            ->with(
                $this->callback(
                    function ($tablename) use ($that) {
                        $that->assertSame('sys_category_record_mm', $tablename);

                        return true;
                    }
                ),
                $this->callback(
                    function ($values) use ($that, $categories, $subscriber) {
                        $that->assertTrue(is_array($values));

                        $that->assertCount(4, $values);
                        $that->assertArrayHasKey('uid_local', $values);
                        $that->assertTrue(in_array($values['uid_local'], $categories));
                        $that->assertArrayHasKey('uid_foreign', $values);
                        $that->assertSame($subscriber->getUid(), $values['uid_foreign']);
                        $that->assertArrayHasKey('tablenames', $values);
                        $that->assertSame('tx_mkpostman_subscribers', $values['tablenames']);
                        $that->assertArrayHasKey('fieldname', $values);
                        $that->assertSame('categories', $values['fieldname']);

                        return true;
                    }
                )
            );

        $repo
            ->expects(self::once())
            ->method('getDbConnection')
            ->will(self::returnValue($connection));

        $repo->addToCategories($subscriber, $categories);
    }

    /**
     * Test the findByUid method.
     *
     *
     * @group unit
     * @test
     */
    public function testFindByUidCallsSearchCorrectly()
    {
        $that = $this; // php 3.5 compatibility!
        $repo = $this->getSubscriberRepository();
        $searcher = $this->callInaccessibleMethod($repo, 'getSearcher');

        $searcher
            ->expects(self::once())
            ->method('search')
            ->with(
                $this->callback(
                    function ($fields) use ($that) {
                        $that->assertTrue(is_array($fields));

                        // only the mail should be filtered
                        $that->assertCount(1, $fields);
                        $that->assertArrayHasKey('SUBSCRIBER.uid', $fields);
                        $that->assertTrue(is_array($fields['SUBSCRIBER.uid']));

                        // only the eq str should be performed
                        $that->assertCount(1, $fields['SUBSCRIBER.uid']);
                        $that->assertArrayHasKey(OP_EQ_INT, $fields['SUBSCRIBER.uid']);
                        $that->assertSame(7, $fields['SUBSCRIBER.uid'][OP_EQ_INT]);

                        return true;
                    }
                ),
                $this->callback(
                    function ($options) use ($that) {
                        $that->assertTrue(is_array($options));

                        // the limit should be set, the mail in uniq!
                        $that->assertArrayHasKey('limit', $options);
                        $that->assertSame(1, $options['limit']);

                        // enablefields be are set, we want disabled/inactive subscribers!
                        $that->assertArrayHasKey('enablefieldsbe', $options);
                        $that->assertTrue($options['enablefieldsbe']);

                        return true;
                    }
                )
            );

        $repo->findByUid(7);
    }

    /**
     * Test the prepareGenericSearcher method.
     *
     *
     * @group unit
     * @test
     */
    public function testPrepareGenericSearcherShouldBeTheRightSearchdefConfig()
    {
        $that = $this; // php 3.5 compatibility!
        $repo = $this->getSubscriberRepository();
        $searcher = $this->callInaccessibleMethod($repo, 'getSearcher');

        $searcher
            ->expects(self::once())
            ->method('search')
            ->with(
                $this->callback(
                    function ($fields) {
                        return is_array($fields) && empty($fields);
                    }
                ),
                $this->callback(
                    function ($options) use ($that, $repo) {
                        $tablename = $repo->getEmptyModel()->getTableName();
                        $that->assertTrue(is_array($options));

                        $that->assertArrayHasKey('searchdef', $options);
                        $that->assertTrue(is_array($options['searchdef']));

                        $sd = $options['searchdef'];
                        $that->assertArrayHasKey('usealias', $sd);
                        $that->assertSame($sd['usealias'], 1);
                        $that->assertArrayHasKey('basetable', $sd);
                        $that->assertSame($sd['basetable'], $tablename);
                        $that->assertArrayHasKey('basetablealias', $sd);
                        $that->assertSame($sd['basetablealias'], 'SUBSCRIBER');
                        $that->assertArrayHasKey('wrapperclass', $sd);
                        $that->assertSame($sd['wrapperclass'], get_class($repo->getEmptyModel()));

                        $that->assertArrayHasKey('alias', $sd);
                        $that->assertTrue(is_array($sd['alias']));
                        $that->assertArrayHasKey('SUBSCRIBER', $sd['alias']);
                        $that->assertTrue(is_array($sd['alias']['SUBSCRIBER']));
                        $that->assertArrayHasKey('table', $sd['alias']['SUBSCRIBER']);
                        $that->assertSame($sd['alias']['SUBSCRIBER']['table'], $tablename);

                        return true;
                    }
                )
            )
            ->will(self::returnValue(new \ArrayObject()));

        $this->assertInstanceOf('ArrayObject', $repo->findAll());
    }

    /**
     * Test the prepareGenericSearcher method.
     *
     *
     * @group unit
     * @test
     */
    public function testPrepareGenericSearcherShouldUseCollection()
    {
        $that = $this; // php 3.5 compatibility!
        $repo = $this->getSubscriberRepository();
        $searcher = $this->callInaccessibleMethod($repo, 'getSearcher');

        $searcher
            ->expects(self::once())
            ->method('search')
            ->with(
                $this->callback(
                    function ($fields) {
                        return is_array($fields);
                    }
                ),
                $this->callback(
                    function ($options) use ($that) {
                        $that->assertTrue(is_array($options));

                        $that->assertArrayHasKey('collection', $options);
                        $that->assertEquals(
                            'Tx_Rnbase_Domain_Collection_Base',
                            $options['collection']
                        );

                        return true;
                    }
                )
            )
            ->will(self::returnValue(new \ArrayObject()));

        $this->assertInstanceOf('ArrayObject', $repo->findAll());
    }
}
