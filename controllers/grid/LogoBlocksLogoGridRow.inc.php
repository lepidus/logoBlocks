<?php

import('lib.pkp.classes.controllers.grid.GridRow');

class LogoBlocksLogoGridRow extends GridRow
{
    private $plugin;

    public function __construct($plugin)
    {
        parent::__construct();
        $this->plugin = $plugin;
    }

    public function initialize($request, $template = null)
    {
        parent::initialize($request, $this->plugin->getTemplateResource('sortableRow.tpl'));
        $params = array('logoId' => $this->getId());
        import('lib.pkp.classes.linkAction.request.AjaxModal');
        $this->addAction(new LinkAction('editLogo', new AjaxModal(
            $this->plugin->getGridUrl($request, 'editLogo', $params),
            __('grid.action.edit'),
            'modal_edit',
            true
        ), __('grid.action.edit'), 'edit'));
        import('lib.pkp.classes.linkAction.request.RemoteActionConfirmationModal');
        $this->addAction(new LinkAction(
            'deleteLogo',
            new RemoteActionConfirmationModal(
                $request->getSession(),
                __('common.confirmDelete'),
                __('grid.action.delete'),
                $this->plugin->getGridUrl($request, 'deleteLogo', $params),
                'modal_delete'
            ),
            __('grid.action.delete'),
            'delete'
        ));
    }
}
