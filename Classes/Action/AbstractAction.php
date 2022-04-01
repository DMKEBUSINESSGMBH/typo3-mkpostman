<?php

namespace DMK\Mkpostman\Action;

use Sys25\RnBase\Configuration\ConfigurationInterface;
use Sys25\RnBase\Frontend\Request\ParametersInterface;
use Sys25\RnBase\Frontend\Request\RequestInterface;
use Sys25\RnBase\Frontend\View\ContextInterface;

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
 * Abstract base action.
 *
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
abstract class AbstractAction extends \Sys25\RnBase\Frontend\Controller\AbstractAction
{
    use \Sys25\RnBase\Domain\Model\StorageTrait;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * Lets do the magic.
     *
     * @param RequestInterface $request
     *
     * @return string Errorstring or NULL
     */
    // @codingStandardsIgnoreStart (interface/abstract mistake)
    protected function handleRequest(RequestInterface $request)
    {
        $this->request = $request;

        // @codingStandardsIgnoreEnd
        return $this->doRequest();
    }

    /**
     * Returns request parameters.
     *
     * @return ParametersInterface
     */
    public function getParameters()
    {
        return $this->request->getParameters();
    }

    /**
     * Returns configurations object.
     *
     * @return ConfigurationInterface
     */
    public function getConfigurations()
    {
        return $this->request->getConfigurations();
    }

    /**
     * Returns view data.
     *
     * @return ContextInterface
     */
    public function getViewData()
    {
        return $this->getConfigurations()->getViewData();
    }

    /**
     * Wrapper method clean code.
     *
     * @return string Errorstring or NULL
     */
    abstract protected function doRequest();

    /*
     * {
     * $parameters = $this->getParameters();
     * $configurations = $this->getConfigurations();
     * $viewdata = $this->getViewData();
     *
     * return null;
     * }
     */

    /**
     * Sets some data to the view.
     *
     * @param string $name
     * @param mixed  $data
     */
    protected function setToView($name, $data)
    {
        $this->getViewData()->offsetSet($name, $data);
    }
}
