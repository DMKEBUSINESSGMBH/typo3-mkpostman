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

/**
 * Subscribtion action test.
 *
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SubscribeActionTest extends \DMK\Mkpostman\Tests\BaseTestCase
{
    /**
     * Test the handleRequest method.
     *
     * @group unit
     * @test
     */
    public function testHandeRequestForm()
    {
        $action = $this->getMock(
            'DMK\\Mkpostman\\Action\\SubscribeAction',
            ['getParameters', 'handleForm', 'handleActivation', 'handleSuccess']
        );

        $action
            ->expects(self::once())
            ->method('getParameters')
            ->will($this->returnValue(\TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Sys25\RnBase\Frontend\Request\Parameters::class)));
        $action
            ->expects(self::never())
            ->method('handleActivation');
        $action
            ->expects(self::never())
            ->method('handleSuccess');
        $action
            ->expects(self::once())
            ->method('handleForm');

        $this->assertSame(
            null,
            $this->callInaccessibleMethod($action, 'doRequest')
        );
    }

    /**
     * Test the handleRequest method.
     *
     * @group unit
     * @test
     */
    public function testHandeRequestActivationWithValidKey()
    {
        $action = $this->getMock(
            'DMK\\Mkpostman\\Action\\SubscribeAction',
            ['getParameters', 'handleForm', 'handleActivation', 'handleSuccess']
        );

        /* @var $parameters \Sys25\RnBase\Frontend\Request\Parameters */
        $parameters = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Sys25\RnBase\Frontend\Request\Parameters::class);
        $parameters->offsetSet('key', 'valid');
        $action
            ->expects(self::once())
            ->method('getParameters')
            ->will($this->returnValue($parameters));
        $action
            ->expects(self::once())
            ->method('handleActivation')
            ->with(self::equalTo('valid'))
            ->will(self::returnValue(true));
        $action
            ->expects(self::never())
            ->method('handleSuccess');
        $action
            ->expects(self::never())
            ->method('handleForm');

        $this->assertSame(
            null,
            $this->callInaccessibleMethod($action, 'doRequest')
        );
    }

    /**
     * Test the handleRequest method.
     *
     * @group unit
     * @test
     */
    public function testHandeRequestActivationWithInvalidKey()
    {
        $action = $this->getMock(
            'DMK\\Mkpostman\\Action\\SubscribeAction',
            ['getParameters', 'handleForm', 'handleActivation', 'handleSuccess']
        );

        /* @var $parameters \Sys25\RnBase\Frontend\Request\Parameters */
        $parameters = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Sys25\RnBase\Frontend\Request\Parameters::class);
        $parameters->offsetSet('key', 'invalid');
        $action
            ->expects(self::once())
            ->method('getParameters')
            ->will($this->returnValue($parameters));

        $action
            ->expects(self::once())
            ->method('handleActivation')
            ->with(self::equalTo('invalid'))
            ->will(self::returnValue(false));
        $action
            ->expects(self::never())
            ->method('handleSuccess');
        $action
            ->expects(self::once())
            ->method('handleForm');

        $this->assertSame(
            null,
            $this->callInaccessibleMethod($action, 'doRequest')
        );
    }

    /**
     * Test the handleRequest method.
     *
     * @group unit
     * @test
     */
    public function testHandeRequestSuccessWithValidKey()
    {
        $action = $this->getMock(
            'DMK\\Mkpostman\\Action\\SubscribeAction',
            ['getParameters', 'handleForm', 'handleActivation', 'handleSuccess']
        );

        /* @var $parameters \Sys25\RnBase\Frontend\Request\Parameters */
        $parameters = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Sys25\RnBase\Frontend\Request\Parameters::class);
        $parameters->offsetSet('success', 'referrer:7');
        $action
            ->expects(self::once())
            ->method('getParameters')
            ->will($this->returnValue($parameters));

        $action
            ->expects(self::never())
            ->method('handleActivation');
        $action
            ->expects(self::once())
            ->method('handleSuccess')
            ->with(self::equalTo('referrer:7'))
            ->will(self::returnValue(true));
        $action
            ->expects(self::never())
            ->method('handleForm');

        $this->assertSame(
            null,
            $this->callInaccessibleMethod($action, 'doRequest')
        );
    }

    /**
     * Test the handleRequest method.
     *
     * @group unit
     * @test
     */
    public function testHandeRequestSuccessWithInvalidKey()
    {
        $action = $this->getMock(
            'DMK\\Mkpostman\\Action\\SubscribeAction',
            ['getParameters', 'handleForm', 'handleActivation', 'handleSuccess']
        );

        /* @var $parameters \Sys25\RnBase\Frontend\Request\Parameters */
        $parameters = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\Sys25\RnBase\Frontend\Request\Parameters::class);
        $parameters->offsetSet('success', 'invalid');
        $action
            ->expects(self::once())
            ->method('getParameters')
            ->will($this->returnValue($parameters));

        $action
            ->expects(self::never())
            ->method('handleActivation');
        $action
            ->expects(self::once())
            ->method('handleSuccess')
            ->with(self::equalTo('invalid'))
            ->will(self::returnValue(false));
        $action
            ->expects(self::once())
            ->method('handleForm');

        $this->assertSame(
            null,
            $this->callInaccessibleMethod($action, 'doRequest')
        );
    }

    /**
     * Test the handleActivation method.
     *
     * @group unit
     * @test
     */
    public function testHandleActivation()
    {
        // @TODO check if $doubleOptInUtil->activateByKey was called correctly!
        // @TODO check if the performSuccessRedirect was called correctly
        $this->markTestIncomplete();
    }

    /**
     * Test the handleSuccess method.
     *
     * @group unit
     * @test
     */
    public function testHandleSuccess()
    {
        $this->markTestIncomplete();
    }

    /**
     * Test the getConfId method.
     *
     * @group unit
     * @test
     */
    public function testPerformSuccessRedirect()
    {
        $this->markTestIncomplete();
    }

    /**
     * Test the getConfId method.
     *
     * @group unit
     * @test
     */
    public function testGetConfIdShouldReturnsRightValue()
    {
        $action = $this->getMockForAbstractClass('DMK\\Mkpostman\\Action\\SubscribeAction');
        $this->assertSame('subscribe.', $action->getConfId());
    }

    /**
     * Test the getTemplateName method.
     *
     * @group unit
     * @test
     */
    public function testGetTemplateNameShouldReturnsRightValueForLegacyTemplate()
    {
        $action = $this->getMock(
            'DMK\\Mkpostman\\Action\\SubscribeAction',
            ['isLegacyTemplate']
        );
        $name = $this->callInaccessibleMethod($action, 'getTemplateName');
        $this->assertSame('subscribe', $name);
    }

    /**
     * Test the getViewClassName method.
     *
     * @group unit
     * @test
     */
    public function testGetViewClassShouldReturnsRightValueForLegacyTemplate()
    {
        $action = $this->getMock(
            'DMK\\Mkpostman\\Action\\SubscribeAction',
            ['isLegacyTemplate']
        );
        $action
            ->expects($this->once())
            ->method('isLegacyTemplate')
            ->will($this->returnValue(true));
        $name = $this->callInaccessibleMethod($action, 'getViewClassName');
        $this->assertSame('DMK\\Mkpostman\\View\\SubscribeView', $name);
    }
}
