<?php
namespace DMK\Mkpostman\Hook;

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
 * LogWriterHook test
 *
 * @package TYPO3
 * @subpackage DMK\Mkpostman
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class LogWriterHookTest
    extends \DMK\Mkpostman\Tests\BaseTestCase
{
    /**
     * Test the processDatamap_postProcessFieldArray method
     *
     * @return string
     *
     * @group unit
     * @test
     */
    public function testProcessDatamapPostProcessFieldArray()
    {
        $this->markTestIncomplete(
            'This test has not been implemented yet!'
        );
    }

    /**
     * Test the processDatamap_afterAllOperations method
     *
     * @return string
     *
     * @group unit
     * @test
     */
    public function testProcessDatamapAfterAllOperations()
    {
        $hook = $this->getHookMock(['processSubscriberLog']);
        $lastUid = null;
        $hook
            ->expects($this->exactly(4))
            ->method('processSubscriberLog')
            ->with(
                $this->callback(
                    function ($uid) use (&$lastUid) {
                        // only set the uid, the real check is performed in the 2nd callback
                        $lastUid = $uid;
                        return true;
                    }
                ),
                $this->callback(
                    function ($new) use (&$lastUid) {
                        return (
                            ($lastUid === 5 && $new === false) ||
                            ($lastUid === 7 && $new === false) ||
                            ($lastUid === 8 && $new === true) ||
                            ($lastUid === 6 && $new === false)
                        );
                    }
                )
            );

        $dataHandler = new \stdClass();
        $dataHandler->substNEWwithIDs['NEW'] = 8;
        $dataHandler->datamap = [
            'tt_content' => [2 => [], 3 => []], // should be ignored
            'tt_content:4' => -1,  // should be ignored
            'tx_mkpostman_subscribers' => [5 => [], 7 => [], 'NEW' => [], 9 => []], // should be logged
            'tx_mkpostman_subscribers:6' => -1, // should be logged
        ];

        // set uid 9 to be ignored!
        $this->setInaccessibleProperty($hook, 'subscribersProcessed', [9 => true]);

        $hook->processDatamap_afterAllOperations($dataHandler);

    }

    /**
     * Test the processSubscriberLog method
     *
     * @return string
     *
     * @group unit
     * @test
     */
    public function testProcessSubscriberLogForNewDisabledSubscriber()
    {
        $subscriber = $this->getSubscriberModel(
            [
                'uid' => 1001,
                'disabled' => 1,
            ]
        );
        $hook = $this->getHookMock([], $subscriber);
        $logManager = $this->callInaccessibleMethod($hook, 'getLogManager');

        $logManager
            ->expects($this->once())
            ->method('createSubscribedBySubscriber');
        $logManager
            ->expects($this->never())
            ->method('createActivatedBySubscriber');
        $logManager
            ->expects($this->never())
            ->method('createUnsubscribedBySubscriber');

        $this->callInaccessibleMethod(
            [$hook, 'processSubscriberLog'],
            [$subscriber->getUid(), true]
        );
    }

    /**
     * Test the processSubscriberLog method
     *
     * @return string
     *
     * @group unit
     * @test
     */
    public function testProcessSubscriberLogForNewActiveSubscriber()
    {
        $subscriber = $this->getSubscriberModel(
            [
                'uid' => 1002,
                'disabled' => 0,
            ]
        );
        $hook = $this->getHookMock([], $subscriber);
        $logManager = $this->callInaccessibleMethod($hook, 'getLogManager');

        $logManager
            ->expects($this->once())
            ->method('createSubscribedBySubscriber');
        $logManager
            ->expects($this->once())
            ->method('createActivatedBySubscriber');
        $logManager
            ->expects($this->never())
            ->method('createUnsubscribedBySubscriber');

        $this->callInaccessibleMethod(
            [$hook, 'processSubscriberLog'],
            [$subscriber->getUid(), true]
        );
    }

    /**
     * Test the processSubscriberLog method
     *
     * @return string
     *
     * @group unit
     * @test
     */
    public function testProcessSubscriberLogForExistingDisabledSubscriber()
    {
        $subscriber = $this->getSubscriberModel(
            [
                'uid' => 1004,
                'disabled' => 1,
            ]
        );
        $hook = $this->getHookMock([], $subscriber);
        $logManager = $this->callInaccessibleMethod($hook, 'getLogManager');

        $logManager
            ->expects($this->never())
            ->method('createSubscribedBySubscriber');
        $logManager
            ->expects($this->never())
            ->method('createActivatedBySubscriber');
        $logManager
            ->expects($this->once())
            ->method('createUnsubscribedBySubscriber');

        $this->callInaccessibleMethod(
            [$hook, 'processSubscriberLog'],
            [$subscriber->getUid(), false]
        );
    }

    /**
     * Test the processSubscriberLog method
     *
     * @return string
     *
     * @group unit
     * @test
     */
    public function testProcessSubscriberLogForExistingEnabledSubscriber()
    {
        $subscriber = $this->getSubscriberModel(
            [
                'uid' => 1005,
                'disabled' => 0,
            ]
        );
        $hook = $this->getHookMock([], $subscriber);
        $logManager = $this->callInaccessibleMethod($hook, 'getLogManager');

        $logManager
            ->expects($this->never())
            ->method('createSubscribedBySubscriber');
        $logManager
            ->expects($this->once())
            ->method('createActivatedBySubscriber');
        $logManager
            ->expects($this->never())
            ->method('createUnsubscribedBySubscriber');

        $this->callInaccessibleMethod(
            [$hook, 'processSubscriberLog'],
            [$subscriber->getUid(), false]
        );
    }

    /**
     * Creates a mock of the hook to test.
     *
     * @param array $methods
     * @param null $subscriber
     *
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function getHookMock(
        array $methods = [],
        $subscriber = null
    ) {
        if ($subscriber === null) {
            $subscriber = $this->getSubscriberModel(['uid' => rand(100, 199)]);
        }

        \tx_rnbase::load('DMK\\Mkpostman\\Hook\\LogWriterHook');
        $hook = $this->getMock(
            'DMK\\Mkpostman\\Hook\\LogWriterHook',
            array_merge($methods, ['findSubscriberByUid', 'getLogManager'])
        );

        \tx_rnbase::load('DMK\\Mkpostman\\Domain\\Manager\\LogManager');
        $logMnager = $this->getMock(
            'DMK\\Mkpostman\\Domain\\Manager\\LogManager'
        );

        $hook
            ->expects($this->any())
            ->method('getLogManager')
            ->will($this->returnValue($logMnager));

        $hook
            ->expects($this->any())
            ->method('findSubscriberByUid')
            ->with($this->equalTo($subscriber->getUid()))
            ->will($this->returnValue($subscriber));

        return $hook;
    }
}
