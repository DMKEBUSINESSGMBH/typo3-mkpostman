<?php

namespace DMK\Mkpostman\Mail\Receiver;

use Sys25\RnBase\Frontend\Marker\BaseMarker;
use Sys25\RnBase\Frontend\Marker\FormatUtil;
use Sys25\RnBase\Frontend\Marker\SimpleMarker;
use TYPO3\CMS\Core\Utility\GeneralUtility;

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
 * MK Postman subscriber mail receiver.
 *
 * @author Michael Wagner
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SubscriberReceiver extends \tx_mkmailer_receiver_BaseTemplate
{
    /**
     * The current Subscriber.
     *
     * @var \DMK\Mkpostman\Domain\Model\SubscriberModel
     */
    private $subscriber;

    /**
     * The Constructor.
     *
     * The subsciber model is optionaly.
     * You can set the subscriber by calling setValueString($uid) after initialisation.
     *
     * @param \DMK\Mkpostman\Domain\Model\SubscriberModel $subscriber
     */
    public function __construct(
        \DMK\Mkpostman\Domain\Model\SubscriberModel $subscriber = null
    ) {
        $this->subscriber = $subscriber;
    }

    /**
     * Returns the current subscriber.
     *
     * @return \DMK\Mkpostman\Domain\Model\SubscriberModel
     */
    protected function getSubscriber()
    {
        return $this->subscriber;
    }

    /**
     * The confid for this receiver.
     *
     * @return string
     */
    protected function getConfId()
    {
        return 'subscriber.';
    }

    /**
     * Initializes the receiver with the subscriber object.
     *
     * @param string $value
     */
    public function setValueString(
        $value
    ) {
        $repo = \DMK\Mkpostman\Factory::getSubscriberRepository();
        $this->subscriber = $repo->findByUid((int) $value);
    }

    /**
     * Returns the Current Subscriber as string.
     *
     * @return string
     */
    public function getValueString()
    {
        return (string) $this->getSubscriber()->getUid();
    }

    /**
     * Returns the number of receivers.
     *
     * @return int
     */
    public function getAddressCount()
    {
        return $this->getSubscriber() ? 1 : 0;
    }

    /**
     * Returns an Array with the  addresses.
     *
     * @return array
     */
    public function getAddresses()
    {
        $addresses = [];

        if ($this->getSubscriber()) {
            $addresses[] = $this->getSubscriber()->getEmail();
        }

        return $addresses;
    }

    /**
     * Returns a name for receiver or receiver group.
     *
     * @return string
     */
    public function getName()
    {
        $name = [];

        $subscriber = $this->getSubscriber();

        if ($subscriber) {
            if ($subscriber->getFirstName()) {
                $name[] = $subscriber->getFirstName();
            }
            if ($subscriber->getLastName()) {
                $name[] = $subscriber->getLastName();
            }
        }

        $name = implode(' ', $name);

        return $name;
    }

    /**
     * The current mail address Ids.
     *
     * @param int $idx
     *
     * @return array
     */
    public function getSingleAddress($idx)
    {
        $ret = [];
        $ret['address'] = reset($this->getAddresses());
        $ret['addressName'] = $this->getName();
        $ret['addressid'] = $ret['address'].'_'.$ret['addressName'];

        return $ret;
    }

    /**
     * The marker to use for rendering.
     *
     * @return SimpleMarker
     */
    protected function getMarkerInstance()
    {
        return GeneralUtility::makeInstance(SimpleMarker::class);
    }

    /**
     * Parse the subscriber into the mail.
     *
     * @param string     $mailText
     * @param string     $mailHtml
     * @param string     $mailSubject
     * @param FormatUtil $formatter
     * @param string     $confId
     * @param int        $idx         Index des Empfängers von 0 bis (getAddressCount() - 1)
     */
    // @codingStandardsIgnoreStart (interface/abstract mistake)
    protected function addAdditionalData(
        &$mailText,
        &$mailHtml,
        &$mailSubject,
        /* \tx_rnbase_util_FormatUtil */ $formatter,
        $confId,
        $idx
    ) {
        // @codingStandardsIgnoreEnd (interface/abstract mistake)

        $subscriber = $this->getSubscriber();
        $marker = $this->getMarkerInstance();

        $mailText = $marker->parseTemplate(
            $mailText,
            $subscriber,
            $formatter,
            $confId.'subscriberText.',
            'SUBSCRIBER'
        );
        $mailHtml = $marker->parseTemplate(
            $mailHtml,
            $subscriber,
            $formatter,
            $confId.'subscriberHtml.',
            'SUBSCRIBER'
        );
        $mailSubject = $marker->parseTemplate(
            $mailSubject,
            $subscriber,
            $formatter,
            $confId.'subscriberSubject.',
            'SUBSCRIBER'
        );
    }

    /**
     * Calls modul subparts, module markers and substitutes the marker arrays.
     *
     * @param string                     $template
     * @param array                      $markerArray
     * @param array                      $subpartArray
     * @param array                      $wrappedSubpartArray
     * @param array                      $params
     * @param \tx_rnbase_util_FormatUtil $formatter
     * @param string                     $confId
     *
     * @return string
     */
    protected function substituteMarkerArray(
        $template,
        array $markerArray,
        array $subpartArray,
        array $wrappedSubpartArray,
        array $params,
        \tx_rnbase_util_FormatUtil $formatter,
        $confId
    ) {
        $this->prepareLinks(
            $template,
            $markerArray,
            $subpartArray,
            $wrappedSubpartArray,
            $formatter,
            $confId
        );

        return parent::substituteMarkerArray(
            $template,
            $markerArray,
            $subpartArray,
            $wrappedSubpartArray,
            $params,
            $formatter,
            $confId
        );
    }

    /**
     * Ads some special links to the template.
     *
     * @param string                     $template
     * @param array                      $markerArray
     * @param array                      $subpartArray
     * @param array                      $wrappedSubpartArray
     * @param FormatUtil                 $formatter
     * @param string                     $confId
     *
     * @TODO: create a marker class and move this
     */
    protected function prepareLinks(
        $template,
        array &$markerArray,
        array &$subpartArray,
        array &$wrappedSubpartArray,
        FormatUtil $formatter,
        $confId
    ) {
        $doubleOptInUtil = \DMK\Mkpostman\Factory::getDoubleOptInUtility(
            $this->getSubscriber()
        );

        // prepare the activation link
        BaseMarker::initLink(
            $markerArray,
            $subpartArray,
            $wrappedSubpartArray,
            $formatter,
            $confId,
            'activation',
            'SUBSCRIBER',
            ['key' => $doubleOptInUtil->buildActivationKey(true)],
            $template
        );
    }
}
