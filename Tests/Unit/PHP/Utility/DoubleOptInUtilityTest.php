<?php

namespace DMK\Mkpostman\Utility;

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
 * DoubleOptInUtility test.
 *
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class DoubleOptInUtilityTest extends \DMK\Mkpostman\Tests\BaseTestCase
{
    /**
     * Test the constructor method.
     *
     *
     * @group unit
     * @test
     */
    public function testConstructorWithModel()
    {
        $this->getMock(
            'DMK\\Mkpostman\\Utility\\DoubleOptInUtility',
            array(),
            array($this->getSubscriberModel())
        );
    }

    /**
     * Test the constructor method.
     *
     *
     * @group unit
     * @test
     */
    public function testConstructorWithKey()
    {
        $that = $this; // php 3.5 compatibility!
        $util = $this->getMock(
            'DMK\\Mkpostman\\Utility\\DoubleOptInUtility',
            array('findSubscriberByKey'),
            array(),
            '',
            false
        );

        $util
            ->expects(self::once())
            ->method('findSubscriberByKey')
            ->with(
                self::callback(
                    function ($keyData) use ($that) {
                        $that->assertInstanceOf('Tx_Rnbase_Domain_Model_Data', $keyData);
                        $that->assertSame('5', $keyData->getUid());
                        $that->assertSame('abcdef1234567890', $keyData->getConfirmstring());
                        $that->assertSame('123456789abcdef', $keyData->getMailHash());

                        return true;
                    }
                )
            )
            ->will(self::returnValue($this->getSubscriberModel()));

        // now call the constructor
        $reflectedClass = new \ReflectionClass(
            'DMK\\Mkpostman\\Utility\\DoubleOptInUtility'
        );
        $constructor = $reflectedClass->getConstructor();
        $constructor->invoke($util, '5:abcdef1234567890:123456789abcdef');
    }

    /**
     * Test the constructor method.
     *
     *
     * @group unit
     * @test
     */
    public function testConstructorWithInvalidData()
    {
        $this->setExpectedException(
            'BadMethodCallException',
            '',
            1464951846
        );
        $this->getMock(
            'DMK\\Mkpostman\\Utility\\DoubleOptInUtility',
            array(),
            array('')
        );
    }

    /**
     * Test the createConfirmString method.
     *
     *
     * @group unit
     * @test
     */
    public function testCreateConfirmString()
    {
        $confirmString = $this->callInaccessibleMethod(
            $this->getUtility(),
            'createConfirmString'
        );

        // we only check for length and containing chars
        $this->assertRegExp('/^[a-f0-9]{32}$/', $confirmString);

        return $confirmString;
    }

    /**
     * Test the updateConfirmString method.
     *
     *
     * @depends testCreateConfirmString
     * @group unit
     * @test
     */
    public function testUpdateConfirmString(
        $confirmString
    ) {
        $util = $this->getUtility(array('createConfirmString'));
        $subscriber = $this->callInaccessibleMethod($util, 'getSubscriber');
        $repo = $this->callInaccessibleMethod($util, 'getRepository');

        $util
            ->expects(self::once())
            ->method('createConfirmString')
            ->will(self::returnValue($confirmString));

        $repo
            ->expects(self::once())
            ->method('persist')
            ->with(self::equalTo($subscriber));

        $this->callInaccessibleMethod($util, 'updateConfirmString');

        // we only check for length and containing chars
        $this->assertSame($confirmString, $subscriber->getConfirmstring());
    }

    /**
     * Test the buildActivationKey method.
     *
     *
     * @depends testCreateConfirmString
     * @group unit
     * @test
     */
    public function testBuildActivationKey(
        $confirmString
    ) {
        $util = $this->getUtility(array('createConfirmString'));
        $subscriber = $this->callInaccessibleMethod($util, 'getSubscriber');

        $subscriber->setConfirmstring($confirmString);

        $key = $this->callInaccessibleMethod($util, 'buildActivationKey', false);

        // the key should contain the uid, confirmstring and the md5 of the mail
        $this->assertSame(2, substr_count($key, ':'));
        $parts = explode(':', $key);
        $this->assertCount(3, $parts);

        $this->assertEquals($subscriber->getUid(), $parts[0]);
        $this->assertEquals($subscriber->getConfirmstring(), $parts[1]);
        $this->assertEquals(md5($subscriber->getEmail()), $parts[2]);

        return array($confirmString, $key);
    }

    /**
     * Test the buildActivationKey method.
     *
     *
     * @depends testCreateConfirmString
     * @group unit
     * @test
     */
    public function testBuildActivationKeyEncoded(
        $confirmString
    ) {
        $util = $this->getUtility(array('createConfirmString'));
        $subscriber = $this->callInaccessibleMethod($util, 'getSubscriber');

        $subscriber->setConfirmstring($confirmString);

        $key = $this->callInaccessibleMethod($util, 'buildActivationKey', true);

        $key = \base64_decode(\urldecode($key));

        // the key should contain the uid, confirmstring and the md5 of the mail
        $this->assertSame(2, substr_count($key, ':'));
        $parts = explode(':', $key);
        $this->assertCount(3, $parts);

        $this->assertEquals($subscriber->getUid(), $parts[0]);
        $this->assertEquals($subscriber->getConfirmstring(), $parts[1]);
        $this->assertEquals(md5($subscriber->getEmail()), $parts[2]);
    }

    /**
     * Test the decodeActivationKey method.
     *
     *
     * @group unit
     * @test
     */
    public function testDecodeActivationKey()
    {
        $util = $this->getUtility();
        $keyData = $this->callInaccessibleMethod(
            $util,
            'decodeActivationKey',
            'firstIsUid:secondIsConfirmstring:thirdIsMailHash'
        );

        $this->assertInstanceOf('Tx_Rnbase_Domain_Model_Data', $keyData);
        $this->assertSame('firstIsUid', $keyData->getUid());
        $this->assertSame('secondIsConfirmstring', $keyData->getConfirmstring());
        $this->assertSame('thirdIsMailHash', $keyData->getMailHash());
    }

    /**
     * Test the validateActivationKey method.
     *
     *
     * @depends testBuildActivationKey
     * @group unit
     * @test
     */
    public function testValidateActivationKey(
        array $params = array()
    ) {
        list($confirmString, $activationKey) = $params;

        $util = $this->getUtility(array('createConfirmString'));
        $subscriber = $this->callInaccessibleMethod($util, 'getSubscriber');

        $subscriber->setConfirmstring($confirmString);

        $this->assertTrue(
            $this->callInaccessibleMethod($util, 'validateActivationKey', $activationKey)
        );
    }

    /**
     * Test the validateActivationKey method.
     *
     *
     * @depends testBuildActivationKey
     * @group unit
     * @test
     */
    public function testValidateActivationKeyEncoded(
        array $params = array()
    ) {
        list($confirmString, $activationKey) = $params;

        $util = $this->getUtility(array('createConfirmString'));
        $subscriber = $this->callInaccessibleMethod($util, 'getSubscriber');

        $subscriber->setConfirmstring($confirmString);

        $activationKey = \urlencode(\base64_encode($activationKey));

        $this->assertTrue(
            $this->callInaccessibleMethod($util, 'validateActivationKey', $activationKey)
        );
    }

    /**
     * Test the activateByKey method.
     *
     *
     * @depends testBuildActivationKey
     * @group unit
     * @test
     */
    public function testActivateByKeyWithValidKey(
        array $params = array()
    ) {
        list($confirmString, $activationKey) = $params;

        $util = $this->getUtility(array('createConfirmString'));
        $subscriber = $this->callInaccessibleMethod($util, 'getSubscriber');
        $logManager = $this->callInaccessibleMethod($util, 'getLogManager');
        $repo = $this->callInaccessibleMethod($util, 'getRepository');

        $logManager
            ->expects(self::once())
            ->method('createActivatedBySubscriber')
            ->with(self::equalTo($subscriber));

        $repo
            ->expects(self::once())
            ->method('persist')
            ->with(self::equalTo($subscriber));

        $subscriber->setDisabled(1);
        $subscriber->setConfirmstring($confirmString);

        $this->assertTrue($util->activateByKey($activationKey));

        $this->assertSame('', $subscriber->getConfirmstring());
        $this->assertSame(0, $subscriber->getDisabled());
    }

    /**
     * Test the activateByKey method.
     *
     *
     * @depends testCreateConfirmString
     * @group unit
     * @test
     */
    public function testActivateByKeyWithInvalidKey(
        $confirmString
    ) {
        $util = $this->getUtility(array('createConfirmString'));
        $subscriber = $this->callInaccessibleMethod($util, 'getSubscriber');
        $logManager = $this->callInaccessibleMethod($util, 'getLogManager');
        $repo = $this->callInaccessibleMethod($util, 'getRepository');

        $logManager
            ->expects(self::never())
            ->method('createUnsubscribedBySubscriber')
            ->with(self::equalTo($subscriber));

        $repo
            ->expects(self::never())
            ->method('persist');

        $subscriber->setDisabled(1);
        $subscriber->setConfirmstring($confirmString);

        $this->assertFalse($util->activateByKey('5:in:valid'));

        $this->assertSame($confirmString, $subscriber->getConfirmstring());
        $this->assertSame(1, $subscriber->getDisabled());
    }

    /**
     * Creates a util instace mock.
     *
     * @return PHPUnit_Framework_MockObject_MockObject|DMK\Mkpostman\Utility\DoubleOptInUtility
     */
    protected function getUtility(
        array $methods = array()
    ) {
        $subscriberModel = $this->getSubscriberModel();
        \tx_rnbase::load('DMK\\Mkpostman\\Utility\\DoubleOptInUtility');
        $util = $this->getMock(
            'DMK\\Mkpostman\\Utility\\DoubleOptInUtility',
            array_merge(
                array('getRepository', 'getLogManager'),
                $methods
            ),
            array($subscriberModel)
        );

        \tx_rnbase::load('DMK\\Mkpostman\\Domain\\Manager\\LogManager');
        $logManager = $this->getMock(
            'DMK\\Mkpostman\\Domain\\Manager\\LogManager'
        );
        $util
            ->expects(self::any())
            ->method('getLogManager')
            ->will(self::returnValue($logManager));

        $util
            ->expects(self::any())
            ->method('getRepository')
            ->will(self::returnValue($this->getSubscriberRepository()));

        return $util;
    }
}
