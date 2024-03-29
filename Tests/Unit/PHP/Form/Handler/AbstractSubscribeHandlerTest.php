<?php

namespace DMK\Mkpostman\Form\Handler;

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
 * Subscribtion action test.
 *
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class AbstractSubscribeHandlerTest extends \DMK\Mkpostman\Tests\BaseTestCase
{
    /**
     * Test the findOrCreateSubscriber method.
     *
     * @group unit
     * @test
     */
    public function testFindOrCreateSubscriberForNewUser()
    {
        $repo = $this->getMock(
            'Mkpostman_Tests_DomainRepositorySubscriberRepository',
            \get_class_methods('DMK\\Mkpostman\\Domain\\Repository\\SubscriberRepository')
        );
        $repo
            ->expects(self::once())
            ->method('findByEmail')
            ->with($this->equalTo('mwagner@localhost.net'))
            ->will(self::returnValue(null));
        $repo
            ->expects(self::once())
            ->method('createNewModel')
            ->will(
                self::returnValue(
                    $this->getModel(
                        [],
                        'DMK\\Mkpostman\\Domain\\Model\\SubscriberModel'
                    )
                )
            );

        $configuration = $this->createConfigurations(
            [
                'subscribe.' => [
                    'subscriber.' => [
                        'storage' => 14,
                    ],
                ],
            ],
            'mkpostman'
        );

        $handler = $this->getMockForAbstract(
            'DMK\\Mkpostman\\Form\\Handler\\AbstractSubscribeHandler',
            ['getSubscriberRepository', 'getConfigurations', 'getConfId'],
            [],
            '',
            false
        );

        $handler
            ->expects(self::once())
            ->method('getSubscriberRepository')
            ->will(self::returnValue($repo));
        $handler
            ->expects(self::once())
            ->method('getConfigurations')
            ->will(self::returnValue($configuration));
        $handler
            ->expects(self::once())
            ->method('getConfId')
            ->will(self::returnValue('subscribe.'));

        $model = $this->callInaccessibleMethod(
            $handler,
            'findOrCreateSubscriber',
            [
                'email' => 'mwagner@localhost.net',
            ]
        );

        $this->assertInstanceOf('DMK\\Mkpostman\\Domain\\Model\\SubscriberModel', $model);

        // the created model should have a pid and should be disabled, nothing else
        $this->assertCount(2, $model->getProperty());
        $this->assertSame(1, $model->getDisabled());
        $this->assertSame(14, $model->getpid());
    }

    /**
     * Test the findOrCreateSubscriber method.
     *
     * @group unit
     * @test
     */
    public function testFindOrCreateSubscriberForExistingUser()
    {
        $subscriber = $this->getModel(
            [
                'uid' => 5,
                'pid' => 7,
                'disabled' => 0,
                'gender' => 1,
                'first_name' => 'Michael',
                'last_name' => 'Wagner',
                'email' => 'mwagner@localhost.net',
            ],
            'DMK\\Mkpostman\\Domain\\Model\\SubscriberModel'
        );
        $repo = $this->getMock(
            'Mkpostman_Tests_DomainRepositorySubscriberRepository',
            \get_class_methods('DMK\\Mkpostman\\Domain\\Repository\\SubscriberRepository')
        );
        $repo
            ->expects(self::once())
            ->method('findByEmail')
            ->with($this->equalTo('mwagner@localhost.net'))
            ->will(self::returnValue($subscriber));
        $repo
            ->expects(self::never())
            ->method('createNewModel');

        $handler = $this->getMockForAbstract(
            'DMK\\Mkpostman\\Form\\Handler\\AbstractSubscribeHandler',
            ['getSubscriberRepository', 'getConfigurations', 'getConfId'],
            [],
            '',
            false
        );

        $handler
            ->expects(self::once())
            ->method('getSubscriberRepository')
            ->will(self::returnValue($repo));
        $handler
            ->expects(self::never())
            ->method('getConfId');

        $model = $this->callInaccessibleMethod(
            $handler,
            'findOrCreateSubscriber',
            [
                'email' => 'mwagner@localhost.net',
            ]
        );

        $this->assertInstanceOf('DMK\\Mkpostman\\Domain\\Model\\SubscriberModel', $model);

        $this->assertSame($model->getProperty(), $subscriber->getProperty());
    }

    /**
     * Test the findOrCreateSubscriber method.
     *
     * @group unit
     * @test
     */
    public function testProcessSubscriberData()
    {
        $categories = [11, 10];
        $subscriber = $this->getModel(
            [
                'uid' => 5,
                'pid' => 7,
                'disabled' => 0,
                'gender' => 1,
                'first_name' => 'Michael',
                'last_name' => 'Wagner',
                'categories' => $categories,
                'email' => 'mwagner@localhost.net',
            ],
            'DMK\\Mkpostman\\Domain\\Model\\SubscriberModel'
        );
        $repo = $this->getMock(
            'Mkpostman_Tests_DomainRepositorySubscriberRepository',
            \get_class_methods('DMK\\Mkpostman\\Domain\\Repository\\SubscriberRepository')
        );
        $repo
            ->expects(self::once())
            ->method('persist')
            ->with($this->equalTo($subscriber));
        $repo
            ->expects(self::once())
            ->method('addToCategories')
            ->with($this->equalTo($subscriber), $this->equalTo($categories));

        $logManager = $this->getMock(
            'DMK\\Mkpostman\\Domain\\Manager\\LogManager'
        );
        $logManager
            ->expects($this->once())
            ->method('createSubscribedBySubscriber')
            ->with($subscriber);

        $handler = $this->getMockForAbstract(
            'DMK\\Mkpostman\\Form\\Handler\\AbstractSubscribeHandler',
            ['findOrCreateSubscriber', 'getSubscriberRepository', 'getLogManager', 'setSubscriber'],
            [],
            '',
            false
        );

        $handler
            ->expects(self::once())
            ->method('findOrCreateSubscriber')
            ->with($this->equalTo(['email' => 'mwagner@localhost.net']))
            ->will(self::returnValue($subscriber));
        $handler
            ->expects(self::once())
            ->method('getSubscriberRepository')
            ->will(self::returnValue($repo));
        $handler
            ->expects(self::once())
            ->method('getLogManager')
            ->will(self::returnValue($logManager));
        $handler
            ->expects(self::once())
            ->method('setSubscriber')
            ->with($this->equalTo($subscriber));

        $this->callInaccessibleMethod(
            $handler,
            'processSubscriberData',
            [
                'email' => 'mwagner@localhost.net',
            ]
        );
    }
}
