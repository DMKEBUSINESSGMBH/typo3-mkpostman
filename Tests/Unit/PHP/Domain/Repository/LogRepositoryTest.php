<?php

namespace DMK\Mkpostman\Domain\Repository;

use Sys25\RnBase\Domain\Collection\BaseCollection;
use Sys25\RnBase\Search\SearchGeneric;

/***************************************************************
 * Copyright notice
 *
 * (c) 2018 DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
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

/**
 * Subscriber model test.
 *
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class LogRepositoryTest extends \DMK\Mkpostman\Tests\BaseTestCase
{
    /**
     * Test the getSearchClass method.
     *
     * @group unit
     * @test
     */
    public function testGetSearchClassShouldBeGeneric()
    {
        $this->assertEquals(
            SearchGeneric::class,
            $this->callInaccessibleMethod(
                $this->getLogRepository(),
                'getSearchClass'
            )
        );
    }

    /**
     * Test the getEmptyModel method.
     *
     * @group unit
     * @test
     */
    public function testGetEmptyModelShouldBeBaseModelWithPageTable()
    {
        $model = $this->callInaccessibleMethod(
            $this->getLogRepository(),
            'getEmptyModel'
        );
        $this->assertInstanceOf(
            'DMK\\Mkpostman\\Domain\\Model\\LogModel',
            $model
        );
        $this->assertSame(
            'tx_mkpostman_logs',
            $model->getTablename()
        );
    }

    /**
     * Test the findByUid method.
     *
     * @group unit
     * @test
     */
    public function testFindByUidCallsSearchCorrectly()
    {
        $that = $this; // php 3.5 compatibility!
        $repo = $this->getLogRepository();
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
                        $that->assertArrayHasKey('LOG.uid', $fields);
                        $that->assertTrue(is_array($fields['LOG.uid']));

                        // only the eq str should be performed
                        $that->assertCount(1, $fields['LOG.uid']);
                        $that->assertArrayHasKey(OP_EQ_INT, $fields['LOG.uid']);
                        $that->assertSame(14, $fields['LOG.uid'][OP_EQ_INT]);

                        return true;
                    }
                ),
                $this->callback(
                    function ($options) use ($that) {
                        $that->assertTrue(is_array($options));

                        // the limit should be set, the mail in uniq!
                        $that->assertArrayHasKey('limit', $options);
                        $that->assertSame(1, $options['limit']);

                        return true;
                    }
                )
            );

        $repo->findByUid(14);
    }

    /**
     * Test the findBySubscriber method.
     *
     * @group unit
     * @test
     */
    public function testFindBySubscriberCallsSearchCorrectly()
    {
        $subscriber = $this->getSubscriberModel();
        $repo = $this->getLogRepository();
        $searcher = $this->callInaccessibleMethod($repo, 'getSearcher');

        $searcher
            ->expects(self::once())
            ->method('search')
            ->with(
                $this->callback(
                    function ($fields) {
                        $this->assertTrue(is_array($fields));

                        // only the mail should be filtered
                        $this->assertCount(1, $fields);
                        $this->assertArrayHasKey('LOG.subscriber_id', $fields);
                        $this->assertTrue(is_array($fields['LOG.subscriber_id']));

                        // only the eq str should be performed
                        $this->assertCount(1, $fields['LOG.subscriber_id']);
                        $this->assertArrayHasKey(OP_EQ_INT, $fields['LOG.subscriber_id']);
                        $this->assertSame(5, $fields['LOG.subscriber_id'][OP_EQ_INT]);

                        return true;
                    }
                ),
                $this->callback(
                    function ($options) {
                        $this->assertTrue(is_array($options));

                        return true;
                    }
                )
            );

        $repo->findBySubscriber($subscriber);
    }

    /**
     * Test the prepareGenericSearcher method.
     *
     * @group unit
     * @test
     */
    public function testPrepareGenericSearcherShouldBeTheRightSearchdefConfig()
    {
        $that = $this; // php 3.5 compatibility!
        $repo = $this->getLogRepository();
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
                        $that->assertSame($sd['basetablealias'], 'LOG');
                        $that->assertArrayHasKey('wrapperclass', $sd);
                        $that->assertSame($sd['wrapperclass'], get_class($repo->getEmptyModel()));

                        $that->assertArrayHasKey('alias', $sd);
                        $that->assertTrue(is_array($sd['alias']));
                        $that->assertArrayHasKey('LOG', $sd['alias']);
                        $that->assertTrue(is_array($sd['alias']['LOG']));
                        $that->assertArrayHasKey('table', $sd['alias']['LOG']);
                        $that->assertSame($sd['alias']['LOG']['table'], $tablename);

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
     * @group unit
     * @test
     */
    public function testPrepareGenericSearcherShouldUseCollection()
    {
        $that = $this; // php 3.5 compatibility!
        $repo = $this->getLogRepository();
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
                            BaseCollection::class,
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
