<?php

import('lib.pkp.classes.controllers.grid.GridHandler');
require_once __DIR__ . '/SponsorsBlockGridRow.inc.php';
require_once __DIR__ . '/SponsorsBlockGridCellProvider.inc.php';

/** Invoked exclusively by the authorized plugin management endpoint. */
class SponsorsBlockGridHandler extends GridHandler
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
        $this->setId('sponsorsBlock-logos');
        $this->setTemplate($this->plugin->getTemplateResource('sortableGrid.tpl'));
        $this->setTitle('plugins.block.sponsorsBlock.logos');
        $this->setEmptyRowText('plugins.block.sponsorsBlock.empty');
        $logos = $this->plugin->getLogos($request->getContext()->getId());
        $publicUrl = $this->plugin->getPublicUrl($request, $request->getContext()->getId());
        foreach ($logos as &$logo) {
            $logo['imageUrl'] = $publicUrl . '/' . $logo['file'];
        }
        unset($logo);
        $this->setGridDataElements($logos);
        import('lib.pkp.classes.linkAction.request.AjaxModal');
        $this->addAction(new LinkAction(
            'chooseLogo',
            new AjaxModal(
                $this->plugin->getGridUrl($request, 'chooseLogo'),
                __('plugins.block.sponsorsBlock.choose'),
                'modal_add_item'
            ),
            __('plugins.block.sponsorsBlock.choose'),
            'add_item'
        ));
        $this->addAction(new LinkAction(
            'addLogo',
            new AjaxModal(
                $this->plugin->getGridUrl($request, 'addLogo'),
                __('plugins.block.sponsorsBlock.add'),
                'modal_add_item'
            ),
            __('plugins.block.sponsorsBlock.add'),
            'add_item'
        ));
        $provider = new SponsorsBlockGridCellProvider();
        $nameTitle = str_replace(' (', '<br>(', htmlspecialchars(__('plugins.block.sponsorsBlock.name'), ENT_QUOTES, 'UTF-8'));
        $this->addColumn(new GridColumn('name', 'plugins.block.sponsorsBlock.name', $nameTitle, null, $provider));
        $this->addColumn(new GridColumn(
            'image',
            'plugins.block.sponsorsBlock.logo',
            null,
            $this->plugin->getTemplateResource('logoCell.tpl'),
            $provider
        ));
        $this->addColumn(new GridColumn('url', 'plugins.block.sponsorsBlock.link', null, null, $provider));
    }

    public function setUrls($request, $extraUrls = array())
    {
        // Native grid refreshes must also pass through plugin authorization.
        parent::setUrls($request, array_merge($extraUrls, array(
            'fetchGridUrl' => $this->plugin->getGridUrl($request, 'fetchGrid'),
            'fetchRowsUrl' => $this->plugin->getGridUrl($request, 'fetchRows'),
            'fetchRowUrl' => $this->plugin->getGridUrl($request, 'fetchRow'),
            'saveSequenceUrl' => $this->plugin->getGridUrl($request, 'saveSequence'),
        )));
    }

    public function getJSHandler()
    {
        return '$.pkp.controllers.grid.SponsorsBlockGridHandler';
    }

    public function saveSequence($args, $request)
    {
        if (!$request->isPost() || !$request->checkCSRF()) {
            return new JSONMessage(false);
        }
        $input = $request->getUserVar('data');
        $order = is_string($input) ? json_decode($input) : null;
        $contextId = $request->getContext()->getId();
        $logos = $this->plugin->getLogos($contextId);
        if (!is_array($order) || count($order) !== count($logos)) {
            return new JSONMessage(false, __('plugins.block.sponsorsBlock.invalidOrder'));
        }
        $ordered = array();
        foreach ($order as $id) {
            if (!is_string($id) || !isset($logos[$id]) || isset($ordered[$id])) {
                return new JSONMessage(false, __('plugins.block.sponsorsBlock.invalidOrder'));
            }
            $ordered[$id] = $logos[$id];
        }
        $this->plugin->updateSetting($contextId, 'logos', $ordered, 'object');
        return new JSONMessage(true);
    }

    public function getRowInstance()
    {
        return new SponsorsBlockGridRow($this->plugin);
    }

    public function addLogo($args, $request)
    {
        return $this->showLogoForm($request, null);
    }

    public function chooseLogo($args, $request)
    {
        $this->plugin->import('SponsorsBlockLogoForm');
        $form = new SponsorsBlockLogoForm($this->plugin, $request->getContext()->getId(), null, true);
        $form->initData();
        return new JSONMessage(true, $form->fetch($request));
    }

    public function addGalleryLogo($args, $request)
    {
        $this->plugin->import('SponsorsBlockLogoForm');
        $form = new SponsorsBlockLogoForm($this->plugin, $request->getContext()->getId(), null, true);
        $form->readInputData();
        if ($form->validate() && $form->execute()) {
            return DAO::getDataChangedEvent();
        }
        return new JSONMessage(true, $form->fetch($request));
    }

    public function editLogo($args, $request)
    {
        $id = $this->getLogoId($request);
        return $id === null ? new JSONMessage(false) : $this->showLogoForm($request, $id);
    }

    private function showLogoForm($request, $id)
    {
        $this->plugin->import('SponsorsBlockLogoForm');
        $form = new SponsorsBlockLogoForm($this->plugin, $request->getContext()->getId(), $id);
        $form->initData();
        return new JSONMessage(true, $form->fetch($request));
    }

    private function getLogoId($request)
    {
        $id = $request->getUserVar('logoId');
        $logos = $this->plugin->getLogos($request->getContext()->getId());
        return is_string($id) && isset($logos[$id]) ? $id : null;
    }

    public function updateLogo($args, $request)
    {
        $id = $request->getUserVar('logoId');
        if ($id !== null && $this->getLogoId($request) === null) {
            return new JSONMessage(false);
        }
        $this->plugin->import('SponsorsBlockLogoForm');
        $form = new SponsorsBlockLogoForm($this->plugin, $request->getContext()->getId(), $id);
        $form->readInputData();
        if ($form->validate() && $form->execute()) {
            return DAO::getDataChangedEvent();
        }
        return new JSONMessage(true, $form->fetch($request));
    }

    public function deleteLogo($args, $request)
    {
        if (!$request->isPost() || !$request->checkCSRF()) {
            return new JSONMessage(false);
        }
        $id = $this->getLogoId($request);
        if ($id === null) {
            return new JSONMessage(false);
        }
        $contextId = $request->getContext()->getId();
        $logos = $this->plugin->getLogos($contextId);
        $file = $logos[$id]['file'];
        unset($logos[$id]);
        $this->plugin->updateSetting($contextId, 'logos', $logos, 'object');
        import('classes.file.PublicFileManager');
        $manager = new PublicFileManager();
        $manager->removeContextFile($contextId, $file);
        return DAO::getDataChangedEvent();
    }
}
