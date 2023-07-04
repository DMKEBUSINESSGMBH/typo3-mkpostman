<?php

namespace DMK\Mkpostman\Action;

use Sys25\RnBase\Domain\Model\DataModel;
use Sys25\RnBase\Frontend\Request\RequestInterface;

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
 * Abstract action test.
 *
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class AbstractActionTest extends \DMK\Mkpostman\Tests\BaseTestCase
{
    /**
     * Test the getStorage method.
     *
     * @group unit
     * @test
     */
    public function testGetStorageReturnsRightInstance()
    {
        $action = $this->getMockForAbstractClass('DMK\\Mkpostman\\Action\\AbstractAction');
        $storage = $this->callInaccessibleMethod($action, 'getStorage');
        $this->assertInstanceOf(DataModel::class, $storage);
    }

    /**
     * Test the handleRequest method.
     *
     * @group unit
     * @test
     */
    public function testHandleRequestCallsDoRequest()
    {
        $action = $this->getMockForAbstractClass('DMK\\Mkpostman\\Action\\AbstractAction');

        $action
            ->expects(self::once())
            ->method('doRequest')
            ->with()
            ->will(self::returnArgument(0));

        $dummy = $this->prophesize(RequestInterface::class);

        $reflectionObject = new \ReflectionObject($action);
        $reflectionMethod = $reflectionObject->getMethod('handleRequest');
        $reflectionMethod->setAccessible(true);

        $ret = $reflectionMethod->invokeArgs(
            $action,
            [$dummy->reveal()]
        );

        // the handleRequest expects returns the first argument
        // this argument should be null. doRequest has no argument!
        $this->assertSame(null, $ret);
    }

    /**
     * Test the getTableName method.
     *
     * @group unit
     * @test
     */
    public function testSetToViewShouldStoreDataCorrectly()
    {
        $action = $this->getMockForAbstractClass(
            'DMK\\Mkpostman\\Action\\AbstractAction',
            [],
            '',
            true,
            true,
            true,
            ['getConfigurations']
        );

        $configuration = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('Sys25\\RnBase\\Configuration\\Processor');
        $action
            ->expects(self::once())
            ->method('getConfigurations')
            ->with()
            ->will(self::returnValue($configuration));

        $this->callInaccessibleMethod($action, 'setToView', 'test', '57');

        $this->assertSame('57', $configuration->getViewData()->offsetGet('test'));
    }
}
