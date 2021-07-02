<?php

namespace srag\Plugins\ToGo\Template;

use ilToGoPlugin;
use srag\DIC\ToGo\DICTrait;
use srag\Plugins\ToGo\Utils\SrTileTrait;

/**
 * Class Factory
 *
 * @package srag\Plugins\ToGo\Template
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
final class Factory
{
    use DICTrait;
    use SrTileTrait;
    const PLUGIN_CLASS_NAME = ilToGoPlugin::class;
    /**
     * @var self|null
     */
    protected static $instance = null;


    /**
     * @return self
     */
    public static function getInstance() : self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    /**
     * Factory constructor
     */
    private function __construct()
    {
    }


    /**
     * @return Template
     */
    public function newInstance() : Template
    {
        $template = new Template();

        return $template;
    }


    /**
     * @param TemplatesConfigGUI $parent
     * @param string             $cmd
     *
     * @return TemplatesTableGUI
     */
    public function newTableInstance(TemplatesConfigGUI $parent, string $cmd = TemplatesConfigGUI::CMD_LIST_TEMPLATES) : TemplatesTableGUI
    {
        $table = new TemplatesTableGUI($parent, $cmd);

        return $table;
    }


    /**
     * @param TemplateConfigGUI $parent
     * @param Template          $template
     *
     * @return TemplateFormGUI
     */
    public function newFormInstance(TemplateConfigGUI $parent, Template $template) : TemplateFormGUI
    {
        $form = new TemplateFormGUI($parent, $template);

        return $form;
    }
}
