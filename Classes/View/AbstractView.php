<?php

namespace DMK\Mkpostman\View;

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

\tx_rnbase::load('tx_rnbase_view_Base');

/**
 * MK Postman abstract view.
 *
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
abstract class AbstractView extends \tx_rnbase_view_Base
{
    /**
     * Entry point for child classes.
     *
     * @param string                     $template
     * @param \ArrayObject               $viewData
     * @param \tx_rnbase_configurations  $configurations
     * @param \tx_rnbase_util_FormatUtil $formatter
     *
     * @return string
     */
    // @codingStandardsIgnoreStart (interface/abstract mistake)
    public function createOutput($template, &$viewData, &$configurations, &$formatter)
    {
        // @codingStandardsIgnoreEnd
        $markArray = $subpartArray = $wrappedSubpartArray = array();

        return \tx_rnbase_util_Templates::substituteMarkerArrayCached(
            $this->parseTemplate(
                $this->prepareMarkerArrays(
                    $template,
                    $markArray,
                    $subpartArray,
                    $wrappedSubpartArray
                )
            ),
            $markArray,
            $subpartArray,
            $wrappedSubpartArray
        );
    }

    /**
     * Parses the current view template.
     *
     * @param string $template
     *
     * @return string
     */
    abstract protected function parseTemplate($template);

    /*{
        $viewData = $this->getViewData();
        $configurations =$this->getConfigurations();
        $formatter =$this->getFormatter();
        $confId = $this->getConfId();
    }*/

    /**
     * Fills the marker arrays for the template.
     *
     * @param string $template
     * @param array  $markArray
     * @param array  $subpartArray
     * @param array  $wrappedSubpartArray
     *
     * @return string The template to render
     */
    protected function prepareMarkerArrays(
        $template,
        array &$markArray,
        array &$subpartArray,
        array &$wrappedSubpartArray
    ) {
        $renderData = array();

        // check viewdata for scalars to render
        foreach ($this->getViewData() as $viewDataName => $value) {
            if (!is_scalar($value)) {
                continue;
            }
            // set value to data array
            $renderData[$viewDataName] = $value;
            // check value and remove or leave subpart
            $subMarker = strtoupper($viewDataName).'_VISIBLE';

            if (empty($renderData[$viewDataName])) {
                // remove subpart, if viewdata is empty
                $subpartArray[$subMarker] = '';
            } else {
                // render subpart, if viewdata is filled
                $wrappedSubpartArray[$subMarker] = array('', '');
            }
        }

        // render the $renderData to the marker array
        $markArray = array_merge(
            $markArray,
            $this->getFormatter()->getItemMarkerArrayWrapped(
                $renderData,
                $this->getConfId()
            )
        );

        return $template;
    }

    /**
     * Checks, if the marker exists at the template.
     *
     * @param string $template
     * @param string $markerPrefix
     *
     * @return bool
     */
    protected function containsMarker($template, $markerPrefix)
    {
        \tx_rnbase::load('tx_rnbase_util_BaseMarker');

        return \tx_rnbase_util_BaseMarker::containsMarker(
            $template,
            $markerPrefix.'_'
        );
    }

    /**
     * Returns configurations object.
     *
     * @return tx_rnbase_configurations
     */
    protected function getConfigurations()
    {
        return $this->getController()->getConfigurations();
    }

    /**
     * Returns confid for the view.
     *
     * @return string
     */
    protected function getConfId()
    {
        return $this->getController()->getConfId();
    }

    /**
     * Returns view data.
     *
     * @return ArrayObject
     */
    protected function getViewData()
    {
        return $this->getController()->getViewData();
    }

    /**
     * Returns configurations object.
     *
     * @return tx_rnbase_util_FormatUtil
     */
    protected function getFormatter()
    {
        return $this->getConfigurations()->getFormatter();
    }

    /**
     * Get some data from the view.
     *
     * @param string $name
     *
     * @return mixed
     */
    protected function getFromView($name)
    {
        return $this->getViewData()->offsetGet($name);
    }
}
