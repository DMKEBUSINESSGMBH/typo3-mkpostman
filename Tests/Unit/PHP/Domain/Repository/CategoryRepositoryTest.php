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
 * Category repository test.
 *
 * @author Markus Crasser
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class CategoryRepositoryTest extends \DMK\Mkpostman\Tests\BaseTestCase
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
            $this->getCategoryRepository(),
            'getEmptyModel'
        );
        $this->assertInstanceOf(
            'DMK\\Mkpostman\\Domain\\Model\\CategoryModel',
            $model
        );
        $this->assertSame(
            'sys_category',
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
    public function testFindBySubscriberIdCallsSearchCorrectly()
    {
        $that = $this; // php 3.5 compatibility!
        $subscriberId = 7;
        $repo = $this->getCategoryRepository();
        $searcher = $this->callInaccessibleMethod($repo, 'getSearcher');

        $searcher
            ->expects(self::once())
            ->method('search')
            ->with(
                $this->callback(
                    function ($fields) use ($that, $subscriberId) {
                        $that->assertTrue(is_array($fields));

                        // only the mail should be filtered
                        $that->assertCount(2, $fields);
                        $that->assertArrayHasKey('CATEGORYMM.uid_foreign', $fields);
                        $that->assertTrue(is_array($fields['CATEGORYMM.uid_foreign']));
                        $that->assertArrayHasKey('CATEGORYMM.tablenames', $fields);
                        $that->assertTrue(is_array($fields['CATEGORYMM.tablenames']));

                        $that->assertCount(1, $fields['CATEGORYMM.uid_foreign']);
                        $that->assertArrayHasKey(OP_EQ_INT, $fields['CATEGORYMM.uid_foreign']);
                        $that->assertSame($subscriberId, $fields['CATEGORYMM.uid_foreign'][OP_EQ_INT]);
                        $that->assertCount(1, $fields['CATEGORYMM.tablenames']);
                        $that->assertArrayHasKey(OP_EQ, $fields['CATEGORYMM.tablenames']);
                        $that->assertSame('tx_mkpostman_subscribers', $fields['CATEGORYMM.tablenames'][OP_EQ]);

                        return true;
                    }
                ),
                $this->callback(
                    function ($options) use ($that) {
                        $that->assertTrue(is_array($options));

                        // enablefields be are set, we want disabled/inactive subscribers!
                        $that->assertArrayHasKey('enablefieldsbe', $options);
                        $that->assertTrue($options['enablefieldsbe']);

                        return true;
                    }
                )
            );

        $repo->findBySubscriberId($subscriberId);
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
        $repo = $this->getCategoryRepository();
        $searcher = $this->callInaccessibleMethod($repo, 'getSearcher');

        $searcher
            ->expects(self::once())
            ->method('search')
            ->with(
                $this->callback(
                    function ($fields) use ($that) {
                        $that->assertTrue(is_array($fields));

                        $that->assertCount(1, $fields);
                        $that->assertArrayHasKey('CATEGORY.uid', $fields);
                        $that->assertTrue(is_array($fields['CATEGORY.uid']));

                        $that->assertCount(1, $fields['CATEGORY.uid']);
                        $that->assertArrayHasKey(OP_EQ_INT, $fields['CATEGORY.uid']);
                        $that->assertSame(7, $fields['CATEGORY.uid'][OP_EQ_INT]);

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
        $repo = $this->getCategoryRepository();
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
                        $that->assertSame($sd['basetablealias'], 'CATEGORY');
                        $that->assertArrayHasKey('wrapperclass', $sd);
                        $that->assertSame($sd['wrapperclass'], get_class($repo->getEmptyModel()));

                        $that->assertArrayHasKey('alias', $sd);
                        $that->assertTrue(is_array($sd['alias']));
                        $that->assertArrayHasKey('CATEGORY', $sd['alias']);
                        $that->assertTrue(is_array($sd['alias']['CATEGORY']));
                        $that->assertArrayHasKey('table', $sd['alias']['CATEGORY']);
                        $that->assertSame($sd['alias']['CATEGORY']['table'], $tablename);
                        $that->assertArrayHasKey('CATEGORYMM', $sd['alias']);
                        $that->assertTrue(is_array($sd['alias']['CATEGORYMM']));
                        $that->assertArrayHasKey('table', $sd['alias']['CATEGORYMM']);
                        $that->assertSame($sd['alias']['CATEGORYMM']['table'], 'sys_category_record_mm');
                        $that->assertArrayHasKey('join', $sd['alias']['CATEGORYMM']);
                        $that->assertSame(
                            $sd['alias']['CATEGORYMM']['join'],
                            'JOIN sys_category_record_mm AS CATEGORYMM ON CATEGORY.uid = CATEGORYMM.uid_local'
                        );

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
        $repo = $this->getCategoryRepository();
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
