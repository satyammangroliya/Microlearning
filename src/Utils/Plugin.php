<?php

namespace minervis\ToGo\Utils;

use ILIAS\UI\Component\Component;
use ILIAS\UI\Implementation\Render\Template;
use ilTable2GUI;
use ilTemplate;
use ilLanguage;
use ilPlugin;


/**
 * Class Plugin
 *
 * @package minervis\ToGo\Utils
 *
 * @author  Jephte Abijuru <jephte.abijuru@minervis.com>
 */
final class Plugin 
{
    use ToGoTrait;

    /**
     * @var ilLanguage[]
     */
    private static $languages = [];
    /**
     * @var ilPlugin
     */
    private $plugin_object;


    /**
     * Plugin constructor
     *
     * @param ilPlugin $plugin_object
     */
    public function __construct($plugin_object)
    {
        $this->plugin_object = $plugin_object;
    }


    /**
     * @inheritDoc
     */
    public function directory() : string
    {
        return $this->plugin_object->getDirectory();
    }


    /**
     * @inheritDoc
     */
    public function template(string $template_file, bool $remove_unknown_variables = true, bool $remove_empty_blocks = true, bool $plugin = true) : ilTemplate
    {
        return new ilTemplate($this->directory() . "/templates/" . $template_file, $remove_unknown_variables, $remove_empty_blocks);
    }


    /**
     * @inheritDoc
     */
    public function translate(string $key, string $module = "", array $placeholders = [], bool $plugin = true, string $lang = "", string $default = "MISSING %s") : string
    {
        if (!empty($module)) {
            $key = $module . "_" . $key;
        }

        if (!empty($lang)) {
            $lng = self::getLanguage($lang);
        } else {
            $lng = self::ildic()->language();
        }

        if ($plugin) {
            $lng->loadLanguageModule($this->plugin_object->getPrefix());

            if ($lng->exists($this->plugin_object->getPrefix() . "_" . $key)) {
                $txt = $lng->txt($this->plugin_object->getPrefix() . "_" . $key);
            } else {
                $txt = "";
            }
        } else {
            if (!empty($module)) {
                $lng->loadLanguageModule($module);
            }

            if ($lng->exists($key)) {
                $txt = $lng->txt($key);
            } else {
                $txt = "";
            }
        }

        $txt = strval($txt);

        $txt = str_replace("\\n", "\n", $txt);

        return $txt;
    }


    /**
     * @inheritDoc
     */
    public function getPluginObject() : ilPlugin
    {
        return $this->plugin_object;
    }


    /**
     * @param string $lang
     *
     * @return ilLanguage
     */
    private static final function getLanguage(string $lang) : ilLanguage
    {
        if (!isset(self::$languages[$lang])) {
            self::$languages[$lang] = new ilLanguage($lang);
        }

        return self::$languages[$lang];
    }

    public function output($value, bool $show = false, bool $main_template = true)/*: void*/
    {
        $html = $this->getHTML($value);
        if (self::ildic()->ctrl()->isAsynch()) {
            echo $html;

            exit;
        } else {
            if ($main_template) {
                self::ildic()->ui()->mainTemplate()->getStandardTemplate(); //i iliAS 6 its called loadStandardTemplate
            }

            self::ildic()->ui()->mainTemplate()->setLocator();

            if (!empty($html)) {
                self::ildic()->ui()->mainTemplate()->setContent($html);
            }

            if ($show) {
                self::ildic()->ui()->mainTemplate()->show(); //ilias6 self::dic()->ui()->mainTemplate()->printToStdout()
            }
        }
    }
    /**
     * @inheritDoc
     */
    public function getHTML($value) : string
    {
        if (is_array($value)) {
            $html = "";
            foreach ($value as $gui) {
                $html .= $this->getHTML($gui);
            }
        } else {
            switch (true) {
                // HTML
                case (is_string($value)):
                    $html = $value;
                    break;

                // Component instance
                case ($value instanceof Component):
                    $html = self::ildic()->ui()->renderer()->render($value);
                    break;

                // ilTable2GUI instance
                case ($value instanceof ilTable2GUI):
                    $html = $value->getHTML();
                    break;

                // GUI instance
                case method_exists($value, "render"):
                    $html = $value->render();
                    break;
                case method_exists($value, "getHTML"):
                    $html = $value->getHTML();
                    break;

                // Template instance
                case ($value instanceof ilTemplate):
                case ($value instanceof Template):
                    $html = $value->get();
                    break;
            }
        }

        return strval($html);
    }
}
