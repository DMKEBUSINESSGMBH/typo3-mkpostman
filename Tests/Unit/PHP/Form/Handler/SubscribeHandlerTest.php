<?php

namespace DMK\Mkpostman\Form\Handler;

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
 * Subscribtion action test.
 *
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SubscribeHandlerTest extends \DMK\Mkpostman\Tests\BaseTestCase
{
    /**
     * Test the fillData method.
     *
     *
     * @group unit
     * @test
     */
    public function testFillDataWithoutUser()
    {
        \tx_rnbase::load('DMK\\Mkpostman\\Form\\Handler\\SubscribeHandler');
        $handler = $this->getMock(
            'DMK\\Mkpostman\\Form\\Handler\\SubscribeHandler',
            array('getFeUserData', 'getParameters', 'setToView', 'getFormSelectOptions'),
            array(),
            '',
            false
        );
        $handler
            ->expects(self::once())
            ->method('getParameters')
            ->will($this->returnValue(\tx_rnbase::makeInstance('tx_rnbase_parameters')));
        $handler
            ->expects(self::once())
            ->method('getFeUserData')
            ->will(self::returnValue(array()));

        $handler->handleForm();
        /* @var $subscriber \DMK\Mkpostman\Domain\Model\SubscriberModel */
        $subscriber = $handler->getSubscriber();

        $this->assertInstanceOf('DMK\\Mkpostman\\Domain\\Model\\SubscriberModel', $subscriber);
        $this->assertCount(2, $subscriber->getProperties());
        $this->assertArrayHasKey('uid', $subscriber->getProperties());
        $this->assertSame($subscriber->getUid(), 0);
    }

    /**
     * Test the fillData method.
     *
     *
     * @group unit
     * @test
     */
    public function testFillDataWithUser()
    {
        $userdata = [
            'gender' => 1,
            'first_name' => 'Michael',
            'last_name' => 'Wagner',
            'email' => 'mwagner\'s mail',
        ];

        \tx_rnbase::load('DMK\\Mkpostman\\Form\\Handler\\SubscribeHandler');
        $handler = $this->getMockForAbstract(
            'DMK\\Mkpostman\\Form\\Handler\\SubscribeHandler',
            array('getFeUserData', 'getParameters', 'setToView', 'getFormSelectOptions'),
            array(),
            '',
            false
        );
        $handler
            ->expects(self::once())
            ->method('getParameters')
            ->will($this->returnValue(\tx_rnbase::makeInstance('tx_rnbase_parameters')));
        $handler
            ->expects(self::once())
            ->method('getFeUserData')
            ->will(self::returnValue($userdata));

        $handler->handleForm();
        /* @var $subscriber \DMK\Mkpostman\Domain\Model\SubscriberModel */
        $subscriber = $handler->getSubscriber();

        $this->assertInstanceOf('DMK\\Mkpostman\\Domain\\Model\\SubscriberModel', $subscriber);
        $this->assertCount(6, $subscriber->getProperties());
        $this->assertArrayHasKey('uid', $subscriber->getProperties());
        $this->assertArrayHasKey('gender', $subscriber->getProperties());
        $this->assertArrayHasKey('first_name', $subscriber->getProperties());
        $this->assertArrayHasKey('last_name', $subscriber->getProperties());
        $this->assertArrayHasKey('categories', $subscriber->getProperties());
        $this->assertArrayHasKey('email', $subscriber->getProperties());
        $this->assertSame($subscriber->getProperties(), array_merge(['uid' => 0, 'categories' => []], $userdata));
    }
}
