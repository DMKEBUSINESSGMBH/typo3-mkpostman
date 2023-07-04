<?php

namespace DMK\Mkpostman\Mail\Receiver;

use Sys25\RnBase\Utility\Typo3Classes;

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
 * Subscriber mail receiver test.
 *
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SubscriberReceiverTest extends \DMK\Mkpostman\Tests\BaseTestCase
{
    /**
     * Test the prepareLinks method.
     *
     * @group unit
     * @test
     */
    public function testPrepareLinks()
    {
        $that = $this; // php 5.3 compatibility
        $cObject = $this->getMock(
            Typo3Classes::getContentObjectRendererClass(),
            ['typolink']
        );

        $cObject
            ->expects(self::once())
            ->method('typolink')
            ->with(
                // only the url, no laben
                $this->equalTo(null),
                $this->callback(
                    function ($config) use ($that) {
                        $that->assertTrue(is_array($config));
                        $that->assertArrayHasKey('additionalParams', $config);
                        $that->assertContains('mkpostman%5Bkey%5D=', $config['additionalParams']);

                        return true;
                    }
                )
            )
            ->will(self::returnValue('?mkpostman%5Bkey%5D=foo'));

        $configurations = $this->createConfigurations(
            [
                'mails.' => [
                    'subscriber.' => [
                        'links.' => [
                            'activation.' => [
                                'absurl' => 'https://www.dmk-ebusiness.de/',
                            ],
                        ],
                    ],
                ],
            ],
            'mkpostman',
            'mkpostman',
            $cObject
        );

        $template = '###SUBSCRIBER_ACTIVATIONLINKURL###';
        $markerArray = [];
        $subpartArray = [];
        $wrappedSubpartArray = [];
        $confId = 'mails.subscriber.';

        $this->callInaccessibleMethod(
            [
                $this->getReceiver(),
                'prepareLinks',
            ],
            [
                $template,
                &$markerArray,
                &$subpartArray,
                &$wrappedSubpartArray,
                $configurations->getFormatter(),
                $confId,
            ]
        );

        $this->assertTrue(is_array($markerArray));
        $this->assertArrayHasKey('###SUBSCRIBER_ACTIVATIONLINKURL###', $markerArray);
        $this->assertContains('mkpostman%5Bkey%5D=', $markerArray['###SUBSCRIBER_ACTIVATIONLINKURL###']);
    }

    /**
     * returns a mock of.
     *
     * @return PHPUnit_Framework_MockObject_MockObject|\DMK\Mkpostman\Mail\Receiver\SubscriberReceiver
     */
    protected function getReceiver()
    {
        $receiver = $this->getMock(
            'DMK\\Mkpostman\\Mail\\Receiver\\SubscriberReceiver',
            [],
            [$this->getSubscriberModel(['confirmstring' => 'PreventPersist'])]
        );

        return $receiver;
    }
}
