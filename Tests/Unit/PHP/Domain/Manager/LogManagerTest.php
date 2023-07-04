<?php

namespace DMK\Mkpostman\Domain\Manager;

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
 * Log manager test.
 *
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class LogManagerTest extends \DMK\Mkpostman\Tests\BaseTestCase
{
    /**
     * Test the createSubscribedBySubscriber method.
     *
     * @group unit
     * @test
     */
    public function testCreateSubscribedBySubscriber()
    {
        $subscriber = $this->getSubscriberModel();
        $manager = $this->getManager(['createLogBySubscriber']);
        $manager
            ->expects($this->once())
            ->method('createLogBySubscriber')
            ->with(
                $this->equalTo($subscriber),
                $this->equalTo(\DMK\Mkpostman\Domain\Model\LogModel::STATE_SUBSCRIBED)
            );

        $manager->createSubscribedBySubscriber($subscriber);
    }

    /**
     * Test the createActivatedBySubscriber method.
     *
     * @group unit
     * @test
     */
    public function testCreateActivatedBySubscriber()
    {
        $subscriber = $this->getSubscriberModel();
        $manager = $this->getManager(['createLogBySubscriber']);
        $manager
            ->expects($this->once())
            ->method('createLogBySubscriber')
            ->with(
                $this->equalTo($subscriber),
                $this->equalTo(\DMK\Mkpostman\Domain\Model\LogModel::STATE_ACTIVATED)
            );

        $manager->createActivatedBySubscriber($subscriber);
    }

    /**
     * Test the createUnsubscribedBySubscriber method.
     *
     * @group unit
     * @test
     */
    public function testCreateUnsubscribedBySubscriber()
    {
        $subscriber = $this->getSubscriberModel();
        $manager = $this->getManager(['createLogBySubscriber']);
        $manager
            ->expects($this->once())
            ->method('createLogBySubscriber')
            ->with(
                $this->equalTo($subscriber),
                $this->equalTo(\DMK\Mkpostman\Domain\Model\LogModel::STATE_UNSUBSCRIBED)
            );

        $manager->createUnsubscribedBySubscriber($subscriber);
    }

    /**
     * Test the createUnsubscribedBySubscriber method.
     *
     * @group unit
     * @test
     */
    public function testCreateLogBySubscriber()
    {
        $subscriber = $this->getSubscriberModel();
        $manager = $this->getManager(['createDescription']);
        $repo = $this->callInaccessibleMethod($manager, 'getRepository');
        $manager
            ->expects($this->once())
            ->method('createDescription')
            ->with($this->isInstanceOf('DMK\\Mkpostman\\Domain\\Model\\LogModel'))
            ->will($this->returnValue('dscr'));

        $repo
            ->expects($this->once())
            ->method('persist')
            ->with(
                $this->callback(
                    function (\DMK\Mkpostman\Domain\Model\LogModel $log) use ($subscriber) {
                        $this->assertSame(7, $log->getPid());
                        $this->assertSame(2, $log->getState());
                        $this->assertSame('dscr', $log->getDescription());
                        $this->assertSame(5, $log->getSubscriberId());
                        $this->assertSame($subscriber, $log->getSubscriber());

                        return true;
                    }
                )
            );

        $this->callInaccessibleMethod([$manager, 'createLogBySubscriber'], [$subscriber, 2]);
    }

    /**
     * Test the createDescription method.
     *
     * @group unit
     * @test
     * @dataProvider getCreateDescriptionData
     */
    public function testCreateDescription($logRecord, $expectedDescription)
    {
        $subscriber = $this->getSubscriberModel();
        $manager = $this->getManager(['getBeUserName', 'getStateLabel']);
        $manager
            ->expects($logRecord['cruser_id'] ? $this->once() : $this->never())
            ->method('getBeUserName')
            ->with($this->equalTo($logRecord['cruser_id']))
            ->will($this->returnValue($logRecord['cruser_id']));
        $manager
            ->expects($this->once())
            ->method('getStateLabel')
            ->with($this->equalTo($logRecord['state']))
            ->will($this->returnValue($logRecord['state']));

        $logEntry = $this->getModel(
            $logRecord,
            'DMK\\Mkpostman\\Domain\\Model\\LogModel',
            ['getSubscriber']
        );
        $logEntry
            ->expects($this->once())
            ->method('getSubscriber')
            ->will($this->returnValue($this->getSubscriberModel(['email' => $logRecord['email']])));

        $this->assertSame(
            $expectedDescription,
            $this->callInaccessibleMethod([$manager, 'createDescription'], [$logEntry])
        );
    }

    /**
     * Returns the data for the createDescription test.
     *
     * @return array
     */
    public function getCreateDescriptionData()
    {
        return [
            // '%1$s has %2$s for %3$s'
            __LINE__ => [
                'record' => [
                    'cruser_id' => 1,
                    'state' => 1,
                    'email' => 'foo@bar.baz', // used only for testing, not a column
                ],
                'description' => 'BE-User (1) has foo@bar.baz 1',
            ],
            __LINE__ => [
                'record' => [
                    'cruser_id' => 0,
                    'state' => 2,
                    'email' => 'sub@scri.ber', // used only for testing, not a column
                ],
                'description' => 'Subscriber has sub@scri.ber 2',
            ],
        ];
    }

    /**
     * Test the getStateLabel method.
     *
     * @group unit
     * @test
     * @dataProvider getStateLabelData
     */
    public function testGetStateLabel($sate, $expectedLabel)
    {
        $this->assertSame(
            $expectedLabel,
            $this->callInaccessibleMethod(
                [$this->getManager(), 'getStateLabel'],
                [$sate]
            )
        );
    }

    /**
     * Returns the data for the getStateLabel test.
     *
     * @return array
     */
    public function getStateLabelData()
    {
        return [
            __LINE__ => [
                'state' => 815,
                'label' => '',
            ],
            __LINE__ => [
                'state' => 0,
                'label' => '',
            ],
            __LINE__ => [
                'state' => 1,
                'label' => 'Subscribed',
            ],
            __LINE__ => [
                'state' => 2,
                'label' => 'Activated',
            ],
            __LINE__ => [
                'state' => 3,
                'label' => 'Unsubscribed',
            ],
        ];
    }

    /**
     * Returns a mock of.
     *
     * @param array $record
     *
     * @return \PHPUnit_Framework_MockObject_MockObject|LogManager
     */
    protected function getManager(
        array $methods = []
    ) {
        $manager = $this->getMock(
            'DMK\\Mkpostman\\Domain\\Manager\\LogManager',
            array_merge(['getRepository'], $methods)
        );

        $manager
            ->expects($this->any())
            ->method('getRepository')
            ->will($this->returnValue($this->getLogRepository()));

        return $manager;
    }
}
