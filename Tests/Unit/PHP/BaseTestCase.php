<?php

namespace DMK\Mkpostman\Tests;

use Sys25\RnBase\Search\SearchGeneric;

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

/**
 * Basis Testcase.
 *
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
abstract class BaseTestCase extends \Sys25\RnBase\Testing\BaseTestCase
{
    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * Returns a subscriber model mock.
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|\DMK\Mkpostman\Domain\Model\SubscriberModel
     */
    protected function getSubscriberModel(array $record = [])
    {
        return $this->getModel(
            array_merge(
                [
                    'uid' => 5,
                    'pid' => 7,
                    'disabled' => 0,
                    'gender' => 1,
                    'first_name' => 'Michael',
                    'last_name' => 'Wagner',
                    'email' => 'mwagner@localhost.net',
                ],
                $record
            ),
            'DMK\\Mkpostman\\Domain\\Model\\SubscriberModel'
        );
    }

    /**
     * Creates the repo mock.
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function getSubscriberRepository()
    {
        $searcher = $this->getMock(
            SearchGeneric::class,
            ['search']
        );

        $repo = $this->getMock(
            'DMK\\Mkpostman\\Domain\\Repository\\SubscriberRepository',
            ['getSearcher', 'persist', 'getDbConnection']
        );

        $repo
            ->expects(self::any())
            ->method('getSearcher')
            ->will(self::returnValue($searcher));

        return $repo;
    }

    /**
     * Creates the repo mock.
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function getCategoryRepository()
    {
        $searcher = $this->getMock(
            SearchGeneric::class,
            ['search']
        );

        $repo = $this->getMock(
            'DMK\\Mkpostman\\Domain\\Repository\\CategoryRepository',
            ['getSearcher', 'persist']
        );

        $repo
            ->expects(self::any())
            ->method('getSearcher')
            ->will(self::returnValue($searcher));

        return $repo;
    }

    /**
     * Creates the repo mock.
     *
     * @return PHPUnit_Framework_MockObject_MockObject
     */
    protected function getLogRepository()
    {
        $searcher = $this->getMock(
            SearchGeneric::class,
            ['search']
        );

        $repo = $this->getMock(
            'DMK\\Mkpostman\\Domain\\Repository\\LogRepository',
            ['getSearcher', 'persist']
        );

        $repo
            ->expects(self::any())
            ->method('getSearcher')
            ->will(self::returnValue($searcher));

        return $repo;
    }
}
