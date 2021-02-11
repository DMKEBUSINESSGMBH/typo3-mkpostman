<?php

namespace DMK\Mkpostman\Backend\Lister;

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

\tx_rnbase::load('Tx_Rnbase_Backend_Lister_AbstractLister');

/**
 * Subscriber lister.
 *
 * @author Michael Wagner
 */
class SubscriberLister extends \Tx_Rnbase_Backend_Lister_AbstractLister
{
    /**
     * The Subscriber repository.
     *
     * @return Tx_Rnbase_Domain_Repository_InterfaceSearch
     */
    protected function getRepository()
    {
        return \DMK\Mkpostman\Factory::getSubscriberRepository();
    }

    /**
     * Liefert die Spalten, in denen gesucht werden soll.
     *
     * @return array
     */
    protected function getSearchColumns()
    {
        return [
            'SUBSCRIBER.uid',
            'SUBSCRIBER.first_name',
            'SUBSCRIBER.last_name',
            'SUBSCRIBER.email',
            'SUBSCRIBER.categories',
        ];
    }

    /**
     * Returns the complete search form.
     *
     * @return string
     */
    public function getSearchFormData()
    {
        $data = parent::getSearchFormData();
        $filter = $this->getFilter();

        // override the disabled filter
        $data['disabled'] = [
            'field' => \Tx_Rnbase_Backend_Utility::getFuncMenu(
                $this->getOptions()->getPid(),
                'SET['.$this->getListerId().'Disabled]',
                $filter->getProperty('disabled'),
                [
                    0 => '###LABEL_FILTER_STATE_0###',
                    1 => '###LABEL_FILTER_STATE_1###',
                ]
            ),
            'label' => '###LABEL_FILTER_STATE###',
        ];

        return $data;
    }

    /**
     * Initializes the fields and options for the repository search.
     *
     * @param array $fields
     * @param array $options
     */
    protected function prepareFieldsAndOptions(
        array &$fields,
        array &$options
    ) {
        parent::prepareFieldsAndOptions($fields, $options);

        if ($this->getOptions()->hasPid()) {
            $fields['SUBSCRIBER.pid'][OP_EQ_INT] = $this->getOptions()->getPid();
        }
    }

    /**
     * The decorator to render the rows.
     *
     * @return string
     */
    protected function getDecoratorClass()
    {
        return 'DMK\\Mkpostman\\Backend\\Decorator\\SubscriberDecorator';
    }

    /**
     * Liefert die Spalten für den Decorator.
     *
     * @param array $columns
     *
     * @return array
     */
    protected function addDecoratorColumns(
        array &$columns
    ) {
        ($this
            ->addDecoratorColumnEmail($columns)
            ->addDecoratorColumnName($columns)
            ->addDecoratorColumnCategories($columns)
            ->addDecoratorColumnActions($columns)
        );

        return $columns;
    }

    /**
     * Adds the column 'uid' to the be list.
     *
     * @param array $columns
     *
     * @return SubscriberLister
     */
    protected function addDecoratorColumnEmail(
        array &$columns
    ) {
        $columns['email'] = [
            'title' => 'label_tableheader_email',
            'decorator' => $this->getDecorator(),
        ];

        return $this;
    }

    /**
     * Adds the column 'uid' to the be list.
     *
     * @param array $columns
     *
     * @return SubscriberLister
     */
    protected function addDecoratorColumnName(
        array &$columns
    ) {
        $columns['name'] = [
            'title' => 'label_tableheader_name',
            'decorator' => $this->getDecorator(),
        ];

        return $this;
    }

    /**
     * Adds the column 'categories' to the be list.
     *
     * @param array $columns
     *
     * @return SubscriberLister
     */
    protected function addDecoratorColumnCategories(
        array &$columns
    ) {
        $columns['categories'] = [
            'title' => 'label_tableheader_categories',
            'decorator' => $this->getDecorator(),
        ];

        return $this;
    }

    /**
     * Adds the column 'actions' to the be list.
     * this column contains the edit, hide, remove, ... actions.
     *
     * @param array $columns
     *
     * @return SubscriberLister
     */
    protected function addDecoratorColumnActions(
        array &$columns
    ) {
        $columns['actions'] = [
            'title' => 'label_tableheader_actions',
            'decorator' => $this->getDecorator(),
        ];

        return $this;
    }
}
