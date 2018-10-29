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
 * Subscribtion action test
 *
 * @package TYPO3
 * @subpackage Tx_Hpsplaner
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SubscribeMkformsHandlerTest
    extends \DMK\Mkpostman\Tests\BaseTestCase
{
    /**
     * Test the fillData method
     *
     * @return void
     *
     * @group unit
     * @test
     */
    public function testFillDataWithoutUser()
    {
        \tx_rnbase::load('tx_mkforms_forms_Base');
        $form = $this->getMock('tx_mkforms_forms_Base');

        \tx_rnbase::load('DMK\\Mkpostman\\Form\\Handler\\SubscribeMkformsHandler');
        $handler = $this->getMock(
            'DMK\\Mkpostman\\Form\\Handler\\SubscribeMkformsHandler',
            array('getFeUserData', 'multipleTableStructure2FlatArray', 'getForm'),
            array(),
            '',
            false
        );
        $handler
            ->expects(self::any())
            ->method('getForm')
            ->will(self::returnValue($form));
        $handler
            ->expects(self::once())
            ->method('getFeUserData')
            ->will(self::returnValue(array()));
        $handler
            ->expects(self::once())
            ->method('multipleTableStructure2FlatArray')
            ->with($this->equalTo(['subscriber' => []]));

        $this->callInaccessibleMethod($handler, 'fillForm', []);
    }

    /**
     * Test the fillData method
     *
     * @return void
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

        \tx_rnbase::load('tx_mkforms_forms_Base');
        $form = $this->getMock('tx_mkforms_forms_Base');

        \tx_rnbase::load('DMK\\Mkpostman\\Form\\Handler\\SubscribeMkformsHandler');
        $handler = $this->getMockForAbstract(
            'DMK\\Mkpostman\\Form\\Handler\\SubscribeMkformsHandler',
            array('getFeUserData', 'multipleTableStructure2FlatArray', 'getForm'),
            array(),
            '',
            false
        );

        $handler
            ->expects(self::any())
            ->method('getForm')
            ->will(self::returnValue($form));
        $handler
            ->expects(self::once())
            ->method('getFeUserData')
            ->will(self::returnValue($userdata));

        $handler
            ->expects(self::once())
            ->method('multipleTableStructure2FlatArray')
            ->with($this->equalTo(['subscriber' => $userdata]));

        $this->callInaccessibleMethod($handler, 'fillForm', []);
    }

    /**
     * Test the findOrCreateSubscriber method
     *
     * @return void
     *
     * @group unit
     * @test
     */
    public function testFindOrCreateSubscriberForNewUser()
    {
        \tx_rnbase::load('DMK\\Mkpostman\\Domain\\Model\\SubscriberModel');
        \tx_rnbase::load('DMK\\Mkpostman\\Domain\\Repository\\SubscriberRepository');
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
                        array(),
                        'DMK\\Mkpostman\\Domain\\Model\\SubscriberModel'
                    )
                )
            );

        $configuration = $this->createConfigurations(
            array(
                'subscribe.' => array(
                    'subscriber.' => array(
                        'storage' => 14,
                    ),
                ),
            ),
            'mkpostman'
        );

        \tx_rnbase::load('DMK\\Mkpostman\\Form\\Handler\\SubscribeMkformsHandler');
        $handler = $this->getMockForAbstract(
            'DMK\\Mkpostman\\Form\\Handler\\SubscribeMkformsHandler',
            array('getSubscriberRepository', 'getConfigurations', 'getConfId'),
            array(),
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
            array(
                'email' => 'mwagner@localhost.net',
            )
        );

        $this->assertInstanceOf('DMK\\Mkpostman\\Domain\\Model\\SubscriberModel', $model);

        // the created model should have a pid and should be disabled, nothing else
        $this->assertCount(2, $model->getProperty());
        $this->assertSame(1, $model->getDisabled());
        $this->assertSame(14, $model->getpid());
    }

    /**
     * Test the findOrCreateSubscriber method
     *
     * @return void
     *
     * @group unit
     * @test
     */
    public function testFindOrCreateSubscriberForExistingUser()
    {
        \tx_rnbase::load('DMK\\Mkpostman\\Domain\\Model\\SubscriberModel');
        $subscriber = $this->getModel(
            array(
                'uid' => 5,
                'pid' => 7,
                'disabled' => 0,
                'gender' => 1,
                'first_name' => 'Michael',
                'last_name' => 'Wagner',
                'email' => 'mwagner@localhost.net',
            ),
            'DMK\\Mkpostman\\Domain\\Model\\SubscriberModel'
        );
        \tx_rnbase::load('DMK\\Mkpostman\\Domain\\Repository\\SubscriberRepository');
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


        \tx_rnbase::load('DMK\\Mkpostman\\Form\\Handler\\SubscribeMkformsHandler');
        $handler = $this->getMockForAbstract(
            'DMK\\Mkpostman\\Form\\Handler\\SubscribeMkformsHandler',
            array('getSubscriberRepository', 'getConfigurations', 'getConfId'),
            array(),
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
            array(
                'email' => 'mwagner@localhost.net',
            )
        );

        $this->assertInstanceOf('DMK\\Mkpostman\\Domain\\Model\\SubscriberModel', $model);

        $this->assertSame($model->getProperty(), $subscriber->getProperty());
    }
}
