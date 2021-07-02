<?php

namespace srag\DataTableUI\ToGo\Component\Format\Browser\Filter;

use srag\CustomInputGUIs\ToGo\FormBuilder\FormBuilder;
use srag\DataTableUI\ToGo\Component\Format\Browser\BrowserFormat;
use srag\DataTableUI\ToGo\Component\Settings\Settings;
use srag\DataTableUI\ToGo\Component\Table;

/**
 * Interface Factory
 *
 * @package srag\DataTableUI\ToGo\Component\Format\Browser\Filter
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
interface Factory
{

    /**
     * @param BrowserFormat $parent
     * @param Table         $component
     * @param Settings      $settings
     *
     * @return FormBuilder
     */
    public function formBuilder(BrowserFormat $parent, Table $component, Settings $settings) : FormBuilder;
}
