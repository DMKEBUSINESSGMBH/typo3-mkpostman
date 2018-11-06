<?php
namespace DMK\Mkpostman\Domain\Model;

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
 * Log model test
 *
 * @package TYPO3
 * @subpackage DMK\Mkpostman
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class LogModelTest
    extends \DMK\Mkpostman\Tests\BaseTestCase
{
    /**
     * Test the getTableName method
     *
     * @return void
     *
     * @group unit
     * @test
     */
    public function testGetTableName()
    {
        $this->assertSame(
            'tx_mkpostman_logs',
            $this->getModelMock()->getTableName()
        );
    }

    /**
     * Test the getTableName method
     *
     * @return void
     *
     * @group unit
     * @test
     */
    public function testSetSubscriber()
    {
        $log = $this->getModelMock();
        $subscriber = $this->getModel(
            ['uid' => 15],
            'DMK\\Mkpostman\\Domain\\Model\\SubscriberModel'
        );

        $log->setSubscriber($subscriber);

        $this->assertSame(
            15,
            $log->getSubscriberId()
        );
    }

    /**
     * returns a mock of
     *
     * @param array $record
     * @param string $class
     * @return tx_rnbase_model_base|PHPUnit_Framework_MockObject_MockObject
     */
    protected function getModelMock(
        $record = null,
        array $methods = array()
    ) {
    
        \tx_rnbase::load('DMK\\Mkpostman\\Domain\\Model\\LogModel');

        return $this->getModel(
            $record,
            'DMK\\Mkpostman\\Domain\\Model\\LogModel',
            $methods
        );
    }
}
