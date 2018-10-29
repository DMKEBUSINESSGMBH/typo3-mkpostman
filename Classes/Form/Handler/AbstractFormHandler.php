<?php
namespace DMK\Mkpostman\Form\Handler;

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
 * MK Postman abstract form handler
 *
 * @package TYPO3
 * @subpackage DMK\Mkpostman
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
abstract class AbstractFormHandler
{
    /**
     * @var \DMK\Mkpostman\Action\SubscribeAction
     */
    private $controller;

    public function __construct(
        \DMK\Mkpostman\Action\SubscribeAction $controller
    ) {
        $this->controller = $controller;
    }

    /**
     * Returns configurations object
     *
     * @return \Tx_Rnbase_Configuration_ProcessorInterface
     */
    public function getConfigurations()
    {
        return $this->controller->getConfigurations();
    }

    /**
     * Returns configurations object
     *
     * @return string
     */
    public function getConfId()
    {
        return $this->controller->getConfId();
    }

    /**
     * Returns request parameters
     *
     * @return tx_rnbase_IParameters
     */
    public function getParameters()
    {
        return $this->getConfigurations()->getParameters();
    }

    /**
     * Sets some data to the view
     *
     * @param string $name
     * @param mixed $data
     *
     * @return void
     */
    protected function setToView($name, $data)
    {
        $this->getConfigurations()->getViewData()->offsetSet($name, $data);
    }
}
