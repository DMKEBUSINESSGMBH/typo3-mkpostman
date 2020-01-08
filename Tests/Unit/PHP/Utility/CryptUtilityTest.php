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
 * CryptUtility test.
 *
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class CryptUtilityTest extends \DMK\Mkpostman\Tests\BaseTestCase
{
    /**
     * Test the urlEncode method.
     *
     * @return string
     *
     * @group unit
     * @test
     */
    public function urlEncode()
    {
        /* @var $util \DMK\Mkpostman\Utility\CryptUtility */
        $util = \tx_rnbase::makeInstance(
            'DMK\\Mkpostman\\Utility\\CryptUtility'
        );
        $encoded = $util->urlEncode($this->getTestValue());

        $this->assertSame(
            \urlencode(\base64_encode($this->getTestValue())),
            $encoded
        );

        return $encoded;
    }

    /**
     * Test the urlDencode method.
     *
     * @param string $encoded
     *
     * @depends urlEncode
     * @group unit
     * @test
     */
    public function urlDencode(
        $encoded
    ) {
        /* @var $util \DMK\Mkpostman\Utility\CryptUtility */
        $util = \tx_rnbase::makeInstance(
            'DMK\\Mkpostman\\Utility\\CryptUtility'
        );

        $this->assertSame(
            $this->getTestValue(),
            $util->urlDencode($encoded)
        );
    }

    /**
     * The Value To test :).
     *
     * @return string
     */
    protected function getTestValue()
    {
        return 'H31l0 Wor1d$!';
    }
}
