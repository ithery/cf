<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_List_ActionRowList extends CElement_List_ActionList {
    public function __construct($id = null) {
        parent::__construct($id);
    }

    public static function factory($id = null) {
        /** @phpstan-ignore-next-line */
        return new static($id);
    }

    protected function applyStyleToChild() {
        $this->apply('style', $this->style, [CElement_Component_ActionRow::class]);
    }

    /**
     * @param null|string $id
     *
     * @return CElement_Component_ActionRow
     */
    public function addAction($id = null) {
        $act = new CElement_Component_ActionRow($id);
        $this->wrapper->add($act);

        return $act;
    }
}
