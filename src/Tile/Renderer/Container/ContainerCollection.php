<?php

namespace minervis\ToGo\Tile\Renderer\Container;

use minervis\ToGo\Tile\Renderer\AbstractCollection;

/**
 * Class ContainerCollection
 *
 * @package minervis\ToGo\Tile\Renderer\Container
 *
 * @author  studer + raimann ag - Team Custom 1 <support-custom1@studer-raimann.ch>
 * @author  studer + raimann ag - Martin Studer <ms@studer-raimann.ch>
 */
class ContainerCollection extends AbstractCollection
{

    /**
     * @var string
     */
    protected $html;


    /**
     * ContainerCollection constructor
     *
     * @param string $html
     */
    public function __construct(string $html)
    {
        $this->html = $html;
        

        parent::__construct();
    }


    /**
     * @inheritDoc
     */
    protected function initObjRefIds() /*: void*/
    {
        $obj_ref_ids = [];

        preg_match_all('/\\s+id\\s*=\\s*["\']{1}lg_div_([0-9]+)/', $this->html, $obj_ref_ids);

        if (is_array($obj_ref_ids) && count($obj_ref_ids) > 1 && is_array($obj_ref_ids[1]) && count($obj_ref_ids[1]) > 0) {
            $this->obj_ref_ids = array_map("intval", $obj_ref_ids[1]);
        }
        if (count($this->obj_ref_ids) <= 0){
            $home_tile = self::togo()->tiles()->getInstanceForObjRefId(self::togo()->config()->getHomeRefId());
            $sub_items = $this->sortCardSubItems($home_tile->_getIlObject()
                    ->getSubItems(true)["_all"]);
            $this->obj_ref_ids = array_map(function ($item){
                return $item['ref_id'];
            }, $sub_items);
        }


    }

    /**
     * @param $sub_items
     * @return mixed
     */
    private function sortCardSubItems($sub_items) : array
    {
        $sorted_sub_items = $sub_items;
        usort($sorted_sub_items, function($a, $b) {
            // First compare by type
            $typeComparison = strcmp($a['type'], $b['type']);
            if ($typeComparison !== 0) {
                return $typeComparison;
            }

            // Then by title, strip tags to compare without HTML tags
            $titleA = strip_tags($a['title']);
            $titleB = strip_tags($b['title']);
            $titleComparison = strcmp($titleA, $titleB);
            if ($titleComparison !== 0) {
                return $titleComparison;
            }

            // Lastly by create_date
            if ($a['create_date'] === $b['create_date']) {
                return 0;
            }
            return ($a['create_date'] < $b['create_date']) ? -1 : 1;
        });
        return $sorted_sub_items;
    }
}
