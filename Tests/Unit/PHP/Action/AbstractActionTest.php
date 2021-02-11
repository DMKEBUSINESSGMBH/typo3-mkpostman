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
        \tx_rnbase::load('DMK\\Mkpostman\\Action\\AbstractAction');
        $action = $this->getMockForAbstractClass('DMK\\Mkpostman\\Action\\AbstractAction');
        $storage = $this->callInaccessibleMethod($action, 'getStorage');
        $this->assertInstanceOf('Tx_Rnbase_Domain_Model_Data', $storage);
    }

    /**
     * Test the handleRequest method.
     *
     * @group unit
     * @test
     */
    public function testHandleRequestCallsDoRequest()
    {
        \tx_rnbase::load('DMK\\Mkpostman\\Action\\AbstractAction');
        $action = $this->getMockForAbstractClass('DMK\\Mkpostman\\Action\\AbstractAction');

        $action
            ->expects(self::once())
            ->method('doRequest')
            ->with()
            ->will(self::returnArgument(0));

        $dummy = new \ArrayObject();

        $reflectionObject = new \ReflectionObject($action);
        $reflectionMethod = $reflectionObject->getMethod('handleRequest');
        $reflectionMethod->setAccessible(true);

        $ret = $reflectionMethod->invokeArgs(
            $action,
            [&$dummy, &$dummy, &$dummy]
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
        \tx_rnbase::load('DMK\\Mkpostman\\Action\\AbstractAction');
        $action = $this->getMockForAbstractClass('DMK\\Mkpostman\\Action\\AbstractAction');

        $configuration = \tx_rnbase::makeInstance('Sys25\\RnBase\\Configuration\\Processor');
        $action->setConfigurations($configuration);

        $this->callInaccessibleMethod($action, 'setToView', 'test', '57');

        $this->assertSame('57', $configuration->getViewData()->offsetGet('test'));
    }
}
