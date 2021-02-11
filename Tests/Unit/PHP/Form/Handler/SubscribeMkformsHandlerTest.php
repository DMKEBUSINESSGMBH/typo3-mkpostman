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
class SubscribeMkformsHandlerTest extends \DMK\Mkpostman\Tests\BaseTestCase
{
    /**
     * Test the fillData method.
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
            ['getFeUserData', 'multipleTableStructure2FlatArray', 'getForm'],
            [],
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
            ->will(self::returnValue([]));
        $handler
            ->expects(self::once())
            ->method('multipleTableStructure2FlatArray')
            ->with($this->equalTo(['subscriber' => []]));

        $this->callInaccessibleMethod($handler, 'fillForm', []);
    }

    /**
     * Test the fillData method.
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
            ['getFeUserData', 'multipleTableStructure2FlatArray', 'getForm'],
            [],
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
}
