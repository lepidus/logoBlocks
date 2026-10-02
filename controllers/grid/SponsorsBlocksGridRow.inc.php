<?php

import('lib.pkp.classes.controllers.grid.GridRow');

class SponsorsBlocksGridRow extends GridRow
{
    private $plugin;

    public function __construct($plugin)
    {
        parent::__construct();
        $this->plugin = $plugin;
    }

    public function initialize($request, $template = null)
    {
        parent::initialize($request, $this->plugin->getTemplateResource('blocksRow.tpl'));
        import('lib.pkp.classes.linkAction.request.AjaxModal');
        $this->addAction(new LinkAction(
            'editBlock',
            new AjaxModal(
                $this->plugin->getManageUrl($request, 'editBlock', array('blockId' => $this->getId())),
                $this->plugin->getDisplayName(),
                'modal_edit',
                true
            ),
            '<span class="fa fa-pencil" aria-hidden="true" style="color: #777;"></span><span class="pkp_screen_reader">'
            . htmlspecialchars(__('plugins.block.sponsorsBlock.editBlock'), ENT_QUOTES, 'UTF-8') . '</span>',
            null,
            __('plugins.block.sponsorsBlock.editBlock')
        ));
        import('lib.pkp.classes.linkAction.request.RemoteActionConfirmationModal');
        $this->addAction(new LinkAction(
            'deleteBlock',
            new RemoteActionConfirmationModal(
                $request->getSession(),
                __('plugins.block.sponsorsBlock.confirmDeleteBlock'),
                __('plugins.block.sponsorsBlock.deleteBlock'),
                $this->plugin->getManageUrl($request, 'deleteBlock', array('blockId' => $this->getId())),
                'modal_delete'
            ),
            '<span class="fa fa-trash" aria-hidden="true" style="color: #777;"></span><span class="pkp_screen_reader">'
            . htmlspecialchars(__('plugins.block.sponsorsBlock.deleteBlock'), ENT_QUOTES, 'UTF-8') . '</span>',
            null,
            __('plugins.block.sponsorsBlock.deleteBlock')
        ));
    }
}
