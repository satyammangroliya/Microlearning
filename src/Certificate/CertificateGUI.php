<?php

namespace srag\Plugins\ToGo\Certificate;

use ilObjUser;
use ilToGoPlugin;
use srag\DIC\ToGo\DICTrait;
use srag\Plugins\ToGo\Tile\Tile;
use srag\Plugins\ToGo\Tile\TileGUI;
use srag\Plugins\ToGo\Utils\SrTileTrait;

/**
 * Class CertificateGUI
 *
 * @package srag\Plugins\ToGo\Certificate
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 */
class CertificateGUI
{
    use DICTrait;
    use SrTileTrait;
    const PLUGIN_CLASS_NAME = ilToGoPlugin::class;
    /**
     * @var ilObjUser
     */
    protected $user;
    /**
     * @var Tile
     */
    protected $tile;


    /**
     * CertificateGUI constructor
     *
     * @param ilObjUser $user
     * @param Tile      $tile
     */
    public function __construct(ilObjUser $user, Tile $tile)
    {
        $this->user = $user;
        $this->tile = $tile;
    }


    /**
     * @return string
     */
    public function render() : string
    {
        $certificates = self::srTile()->ilias()->certificates($this->user, $this->tile);

        $link = $certificates->getCertificateDownloadLink();

        if (empty($link)) {
            return '';
        }

        $tpl = self::plugin()->template("Certificate/certificate.html");

        $tpl->setVariableEscaped("CERTIFICATE_LINK", $link);
        $tpl->setVariableEscaped("CERTIFICATE_TEXT", self::plugin()->translate("download_certificate", TileGUI::LANG_MODULE));
        $tpl->setVariableEscaped("CERTIFICATE_IMAGE_PATH", self::plugin()->directory() . "/templates/images/certificate.svg");

        return self::output()->getHTML($tpl);
    }
}
