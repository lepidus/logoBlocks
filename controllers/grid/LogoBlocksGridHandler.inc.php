<?php

import('lib.pkp.classes.controllers.grid.GridHandler');
require_once __DIR__ . '/LogoBlocksGridRow.inc.php';

/** Block administration uses the same authorized endpoint as logo editing. */
class LogoBlocksGridHandler extends GridHandler
{
    private $plugin;

    public function __construct($plugin)
    {
        parent::__construct();
        $this->plugin = $plugin;
    }

    public function initialize($request, $args = null)
    {
        parent::initialize($request, $args);
        $this->setId('sponsorsBlock-blocks');
        $this->setTemplate($this->plugin->getTemplateResource('blocksGrid.tpl'));
        $this->setTitle('plugins.block.logoBlocks.blocks');
        $contextId = $request->getContext()->getId();
        $data = array();
        foreach ($this->plugin->getBlockIds($contextId) as $id) {
            $block = $this->plugin->forBlock($contextId, $id);
            $data[$id] = array(
                'title' => $block->getBlockTitle($contextId),
                'visible' => $block->isBlockVisible($contextId),
                'visibilityUrl' => $this->plugin->getManageUrl($request, 'setVisibility', array('blockId' => $id)),
                'csrfToken' => $request->getSession()->getCSRFToken(),
            );
        }
        $this->setGridDataElements($data);
        import('lib.pkp.classes.linkAction.request.AjaxModal');
        $this->addAction(new LinkAction('addBlock', new AjaxModal(
            $this->plugin->getManageUrl($request, 'addBlock'),
            __('plugins.block.logoBlocks.addBlock'),
            'modal_add_item'
        ), __('plugins.block.logoBlocks.addBlock'), 'add_item'));
        $this->addColumn(new GridColumn('title', 'plugins.block.logoBlocks.title'));
    }

    public function setUrls($request, $extraUrls = array())
    {
        foreach (array('fetchGrid', 'fetchRows', 'fetchRow', 'saveSequence') as $operation) {
            $extraUrls[$operation . 'Url'] = $this->plugin->getManageUrl($request, 'blocks', array('gridOp' => $operation));
        }
        parent::setUrls($request, $extraUrls);
    }

    public function getJSHandler()
    {
        return '$.pkp.controllers.grid.LogoBlocksGridHandler';
    }

    public function saveSequence($args, $request)
    {
        if (!$request->isPost() || !$request->checkCSRF()) {
            return new JSONMessage(false);
        }
        $input = $request->getUserVar('data');
        $order = is_string($input) ? json_decode($input) : null;
        $contextId = $request->getContext()->getId();
        $ids = $this->plugin->getBlockIds($contextId);
        if (!is_array($order) || count($order) !== count($ids)) {
            return new JSONMessage(false);
        }
        $seen = array();
        foreach ($order as $id) {
            if (!is_string($id) || !in_array($id, $ids, true) || isset($seen[$id])) {
                return new JSONMessage(false);
            }
            $seen[$id] = true;
        }
        $this->plugin->updateSetting($contextId, 'logoBlockOrder', $order, 'object');
        return new JSONMessage(true);
    }

    public function getRowInstance()
    {
        return new LogoBlocksGridRow($this->plugin);
    }
}
