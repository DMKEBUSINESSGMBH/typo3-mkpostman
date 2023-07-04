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
     * @group unit
     * @test
     */
    public function fillDataWithoutUser()
    {
        $handler = $this->getMock(
            'DMK\\Mkpostman\\Form\\Handler\\SubscribeHandler',
            [
                'getFeUserData',
                'getParameters',
                'setToView',
                'getFormSelectOptions',
                'getConfigurations',
                'getConfId',
                'isHoneypotEnabled',
            ],
            [],
            '',
            false
        );
        $handler
            ->expects(self::once())
            ->method('getParameters')
            ->will($this->returnValue(\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Sys25\RnBase\Frontend\Request\Parameters::class)));
        $handler
            ->expects(self::once())
            ->method('getFeUserData')
            ->will(self::returnValue([]));
        $handler
            ->expects($this->exactly(1)) // 1x config lesen, 3x label übersetzen
            ->method('isHoneypotEnabled')
            ->will($this->returnValue(false));

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
     * @group unit
     * @test
     */
    public function fillDataWithHoneyPot()
    {
        $handler = $this->getMock(
            'DMK\\Mkpostman\\Form\\Handler\\SubscribeHandler',
            ['getFeUserData',
                'getParameters',
                'setToView',
                'getFormSelectOptions',
                'getConfigurations',
                'getConfId',
                'isHoneypotEnabled',
                'getHoneypotFieldName',
            ],
            [],
            '',
            false
        );
        $handler
            ->expects(self::once())
            ->method('getParameters')
            ->will($this->returnValue(\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Sys25\RnBase\Frontend\Request\Parameters::class)));
        $handler
            ->expects(self::once())
            ->method('getFeUserData')
            ->will(self::returnValue([]));
        $handler
            ->expects($this->exactly(1))
            ->method('isHoneypotEnabled')
            ->will($this->returnValue(true));
        $handler
            ->expects($this->exactly(1))
            ->method('getHoneypotFieldName')
            ->will($this->returnValue('field'));

        $handler->handleForm();
        /* @var $subscriber \DMK\Mkpostman\Domain\Model\SubscriberModel */
        $subscriber = $handler->getSubscriber();

        $this->assertInstanceOf('DMK\\Mkpostman\\Domain\\Model\\SubscriberModel', $subscriber);
        $this->assertCount(2, $subscriber->getProperties());
        $this->assertArrayHasKey('uid', $subscriber->getProperties());
        $this->assertSame($subscriber->getUid(), 0);
    }

    /**
     * Test the fillDataWithUser method.
     *
     * @group unit
     * @test
     */
    public function fillDataWithUser()
    {
        $userdata = [
            'gender' => 1,
            'first_name' => 'Michael',
            'last_name' => 'Wagner',
            'email' => 'mwagner\'s mail',
        ];

        $handler = $this->getMockForAbstract(
            'DMK\\Mkpostman\\Form\\Handler\\SubscribeHandler',
            [
                'getFeUserData',
                'getParameters',
                'setToView',
                'getFormSelectOptions',
                'getConfigurations',
                'getConfId',
                'isHoneypotEnabled',
            ],
            [],
            '',
            false
        );
        $handler
            ->expects(self::once())
            ->method('getParameters')
            ->will($this->returnValue(\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Sys25\RnBase\Frontend\Request\Parameters::class)));
        $handler
            ->expects(self::once())
            ->method('getFeUserData')
            ->will(self::returnValue($userdata));
        $handler
            ->expects($this->exactly(1))
            ->method('isHoneypotEnabled')
            ->will($this->returnValue(false));

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

    /**
     * Test the validateSubscriberData method.
     *
     * @group unit
     * @test
     */
    public function validateSubscriberDataisValid()
    {
        $postData = [
            'email' => 'foo@bar.baz',
            'categories' => [2, 4, 6],
            'field' => '',
        ];

        $configurations = $this->getMock('Sys25\\RnBase\\Configuration\\Processor');
        $configurations->expects($this->exactly(2))->method('getInt')->willReturn(1);
        $configurations->expects($this->exactly(1))->method('get')->willReturn('field');

        $handler = $this->getMock(
            'DMK\\Mkpostman\\Form\\Handler\\SubscribeHandler',
            ['getConfigurations', 'getConfId'],
            [],
            '',
            false
        );

        $handler
            ->expects($this->exactly(3))
            ->method('getConfId')
            ->will($this->returnValue('cid'));
        $handler
            ->expects($this->exactly(3))
            ->method('getConfigurations')
            ->will($this->returnValue($configurations));

        $this->assertTrue(
            $this->callInaccessibleMethod([$handler, 'validateSubscriberData'], [$postData])
        );
    }

    /**
     * Test the validateSubscriberData method.
     *
     * @group unit
     * @test
     */
    public function validateSubscriberDataReportsErrors()
    {
        $postData = [
            'email' => 'invalid mail',
            'categories' => [2, 4, 6],
            'field' => 'test',
        ];

        $configurations = $this->getMock('Sys25\\RnBase\\Configuration\\Processor');
        $configurations->expects($this->exactly(1))->method('getInt')->willReturn(5);
        $configurations->expects($this->exactly(3))->method('getCfgOrLL')->willReturn('tranlsated message');

        $handler = $this->getMock(
            'DMK\\Mkpostman\\Form\\Handler\\SubscribeHandler',
            ['getConfigurations', 'getConfId', 'isHoneypotEnabled', 'getHoneypotFieldName'],
            [],
            '',
            false
        );

        $handler
            ->expects($this->exactly(1))
            ->method('getConfId')
            ->will($this->returnValue('cid'));
        $handler
            ->expects($this->exactly(4)) // 1x config lesen, 3x label übersetzen
            ->method('getConfigurations')
            ->will($this->returnValue($configurations));
        $handler
            ->expects($this->exactly(1)) // 1x config lesen, 3x label übersetzen
            ->method('isHoneypotEnabled')
            ->will($this->returnValue(true));
        $handler
            ->expects($this->exactly(1)) // 1x config lesen, 3x label übersetzen
            ->method('getHoneypotFieldName')
            ->will($this->returnValue('field'));

        $this->assertFalse(
            $this->callInaccessibleMethod([$handler, 'validateSubscriberData'], [$postData])
        );

        $errors = $this->getInaccessibleProperty($handler, 'validationErrors');

        $this->assertEquals(
            $errors,
            [
                'email' => 'tranlsated message',
                'categories' => 'tranlsated message',
                'honeypot' => 'tranlsated message',
            ]
        );
    }

    /**
     * Test the validateSubscriberData method.
     *
     * @group unit
     * @test
     */
    public function validateSubscriberDataCallsSetFieldInvalidCorrectly()
    {
        $postData = [
            'email' => 'invalid mail',
            'categories' => [2, 4, 6],
            'field' => 'test',
        ];

        $configurations = $this->getMock('Sys25\\RnBase\\Configuration\\Processor');
        $configurations->expects($this->exactly(1))->method('getInt')->willReturn(5);

        $handler = $this->getMock(
            'DMK\\Mkpostman\\Form\\Handler\\SubscribeHandler',
            ['getConfigurations', 'getConfId', 'setFieldInvalid', 'isHoneypotEnabled', 'getHoneypotFieldName'],
            [],
            '',
            false
        );

        $handler
            ->expects($this->exactly(1))
            ->method('getConfId')
            ->will($this->returnValue('cid'));
        $handler
            ->expects($this->exactly(1))
            ->method('getConfigurations')
            ->will($this->returnValue($configurations));
        $handler
            ->expects($this->exactly(3))
            ->method('setFieldInvalid')
            ->withConsecutive(
                ['email'],
                ['categories', null, ['%requiredmin%' => 5]],
                ['honeypot']
            );
        $handler
            ->expects($this->exactly(1)) // 1x config lesen, 3x label übersetzen
            ->method('isHoneypotEnabled')
            ->will($this->returnValue(true));
        $handler
            ->expects($this->exactly(1)) // 1x config lesen, 3x label übersetzen
            ->method('getHoneypotFieldName')
            ->will($this->returnValue('field'));

        $this->callInaccessibleMethod([$handler, 'validateSubscriberData'], [$postData]);
    }
}
