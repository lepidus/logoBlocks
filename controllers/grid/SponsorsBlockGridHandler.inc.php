<?php
import('lib.pkp.classes.controllers.grid.GridHandler');
require_once __DIR__ . '/SponsorsBlockGridRow.inc.php';
require_once __DIR__ . '/SponsorsBlockGridCellProvider.inc.php';

/** Invoked exclusively by the authorized plugin management endpoint. */
class SponsorsBlockGridHandler extends GridHandler {
    private $plugin;

    function __construct($plugin) {
        parent::__construct();
        $this->plugin = $plugin;
    }

    function initialize($request, $args = null) {
        parent::initialize($request, $args);
        $this->setId('sponsorsBlock-logos');
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
        $this->addAction(new LinkAction('addLogo', new AjaxModal(
            $this->plugin->getGridUrl($request, 'addLogo'),
            __('plugins.block.sponsorsBlock.add'), 'modal_add_item'),
            __('plugins.block.sponsorsBlock.add'), 'add_item'));
        $provider = new SponsorsBlockGridCellProvider();
        $this->addColumn(new GridColumn('name', 'plugins.block.sponsorsBlock.name', null, null, $provider));
        $this->addColumn(new GridColumn('image', 'plugins.block.sponsorsBlock.logo', null,
            $this->plugin->getTemplateResource('logoCell.tpl'), $provider));
        $this->addColumn(new GridColumn('url', 'plugins.block.sponsorsBlock.link', null, null, $provider));
    }

    function setUrls($request, $extraUrls = array()) {
        // Native grid refreshes must also pass through plugin authorization.
        parent::setUrls($request, array_merge($extraUrls, array(
            'fetchGridUrl' => $this->plugin->getGridUrl($request, 'fetchGrid'),
            'fetchRowsUrl' => $this->plugin->getGridUrl($request, 'fetchRows'),
            'fetchRowUrl' => $this->plugin->getGridUrl($request, 'fetchRow'),
        )));
    }

    function getRowInstance() {
        return new SponsorsBlockGridRow($this->plugin);
    }

    function addLogo($args, $request) {
        return $this->showLogoForm($request, null);
    }

    function editLogo($args, $request) {
        $id = $this->getLogoId($request);
        return $id === null ? new JSONMessage(false) : $this->showLogoForm($request, $id);
    }

    private function showLogoForm($request, $id) {
        $this->plugin->import('SponsorsBlockLogoForm');
        $form = new SponsorsBlockLogoForm($this->plugin, $request->getContext()->getId(), $id);
        $form->initData();
        return new JSONMessage(true, $form->fetch($request));
    }

    private function getLogoId($request) {
        $id = $request->getUserVar('logoId');
        $logos = $this->plugin->getLogos($request->getContext()->getId());
        return is_string($id) && isset($logos[$id]) ? $id : null;
    }

    function updateLogo($args, $request) {
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

    function deleteLogo($args, $request) {
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
