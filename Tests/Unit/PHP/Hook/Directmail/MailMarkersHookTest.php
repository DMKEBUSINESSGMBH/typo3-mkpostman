<?php
namespace DMK\Mkpostman\Hook\Directmail;

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
 * MailMarkersHook test
 *
 * @package TYPO3
 * @subpackage DMK\Mkpostman
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class MailMarkersHookTest
    extends \DMK\Mkpostman\Tests\BaseTestCase
{
    /**
     * Test the urlEncode method
     *
     * @return string
     *
     * @group unit
     * @test
     */
    public function testMain()
    {
        $record = [
            'uid' => 5,
            'pid' => 7,
            'disabled' => 0,
            'gender' => 1,
            'first_name' => 'Michael',
            'last_name' => 'Wagner',
            'email' => 'mwagner@localhost.net',
        ];
        $recordReference = array_merge([], $record);
        $markerArray = [];

        /* var $hook \DMK\Mkpostman\Hook\Directmail\MailMarkersHook */
        $hook = $this->getMock(
            'DMK\\Mkpostman\\Hook\\Directmail\\MailMarkersHook',
            ['getDoubleOptInUtility']
        );

        $optInUtility = $this->getMock(
            'DMK\\Mkpostman\\Utility\\DoubleOptInUtility',
            [], [], '', false
        );
        $optInUtility
            ->expects($this->once())
            ->method('buildUnsubscribeKey')
            ->with(true)
            ->will($this->returnValue('HASH'));

        $hook
            ->expects($this->once())
            ->method('getDoubleOptInUtility')
            ->with($this->isInstanceOf('DMK\\Mkpostman\\Domain\\Model\\SubscriberModel'))
            ->will($this->returnValue($optInUtility));

        $hook->main(['row' => &$recordReference, 'markers' => &$markerArray]);

        // be sure, the record was not modified
        $this->assertEquals($record, $recordReference);

        $this->assertCount(1, $markerArray);
        $this->assertArrayHasKey('###MKPOSTMAN_UNSUBSCRIBE_PARAMS###', $markerArray);
        $this->assertEquals('&mkpostman[unsubscribe]=HASH', $markerArray['###MKPOSTMAN_UNSUBSCRIBE_PARAMS###']);
    }
}
