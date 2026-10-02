<?php

/** Sidebar logos for OJS 3.3. Distributed under the GNU GPL v3. */
import('lib.pkp.classes.plugins.BlockPlugin');

class SponsorsBlockPlugin extends BlockPlugin
{
    private $blockId = null;

    public function getBlockId()
    {
        return $this->blockId;
    }

    public function getBlockIds($contextId)
    {
        $ids = $this->getSetting($contextId, 'logoBlocks');
        $ids = is_array($ids) ? $ids : array();
        $order = $this->getSetting($contextId, 'logoBlockOrder');
        if (!is_array($order)) {
            return $ids;
        }
        // Include blocks created since the last ordering at the end.
        return array_values(array_unique(array_merge(array_intersect($order, $ids), $ids)));
    }

    /** A scoped copy keeps block identity out of mutable request/global state. */
    public function forBlock($contextId, $blockId)
    {
        if (!is_string($blockId) || !in_array($blockId, $this->getBlockIds($contextId), true)) {
            return null;
        }
        $plugin = clone $this;
        $plugin->blockId = $blockId;
        return $plugin;
    }

    public function getBlockSetting($contextId, $name)
    {
        return $this->getSetting($contextId, 'logoBlock_' . $this->blockId . '_' . $name);
    }

    public function updateBlockSetting($contextId, $name, $value, $type = null)
    {
        return $this->updateSetting($contextId, 'logoBlock_' . $this->blockId . '_' . $name, $value, $type);
    }

    public function isBlockVisible($contextId)
    {
        $visible = $this->getBlockSetting($contextId, 'visible');
        // Existing and newly created blocks remain visible until explicitly disabled.
        return $visible === null || (bool) $visible;
    }

    public function getBlockPlacement($contextId)
    {
        $sidebar = $this->getBlockSetting($contextId, 'showSidebar');
        $footer = (bool) $this->getBlockSetting($contextId, 'showAboveFooter');
        // Preserve the previous sidebar-only / sidebar-and-footer behavior.
        return $footer ? ($sidebar === null || $sidebar ? 'both' : 'footer') : 'sidebar';
    }

    public function getDescriptionPlacement($contextId)
    {
        $placement = $this->getBlockSetting($contextId, 'descriptionPlacement');
        return in_array($placement, array('sidebar', 'footer', 'both'), true) ? $placement : 'none';
    }

    public function registerBlock($contextId)
    {
        $ids = $this->getBlockIds($contextId);
        if (!in_array($this->blockId, $ids, true)) {
            $ids[] = $this->blockId;
            $this->updateSetting($contextId, 'logoBlocks', $ids, 'object');
        }
    }

    public function getManageUrl($request, $verb, $params = array())
    {
        return $request->getRouter()->url(
            $request,
            null,
            'grid.settings.plugins.SettingsPluginGridHandler',
            'manage',
            null,
            array_merge($params, array('category' => 'blocks', 'plugin' => $this->getName(), 'verb' => $verb))
        );
    }

    public function getDisplayName()
    {
        return __('plugins.block.sponsorsBlock.displayName');
    }

    public function getDescription()
    {
        return __('plugins.block.sponsorsBlock.description');
    }

    public function getActions($request, $verb)
    {
        $actions = parent::getActions($request, $verb);
        if ($request->getContext() && $this->getEnabled()) {
            import('lib.pkp.classes.linkAction.request.AjaxModal');
            array_unshift($actions, new LinkAction('settings', new AjaxModal(
                $request->getRouter()->url($request, null, null, 'manage', null, array(
                    'verb' => 'settings', 'plugin' => $this->getName(), 'category' => 'blocks'
                )),
                $this->getDisplayName()
            ), __('manager.plugins.settings'), null));
        }
        return $actions;
    }

    public function manage($args, $request)
    {
        $verb = $request->getUserVar('verb');
        if (!in_array($verb, array('settings', 'blocks', 'editBlock', 'addBlock', 'deleteBlock', 'grid', 'setVisibility'), true)) {
            return parent::manage($args, $request);
        }
        if (!$request->getContext() || !$this->getEnabled()) {
            return new JSONMessage(false);
        }
        if ($verb === 'settings' || $verb === 'blocks') {
            $this->import('controllers.grid.SponsorsBlocksGridHandler');
            $grid = new SponsorsBlocksGridHandler($this);
            $operation = $verb === 'settings' ? 'fetchGrid' : $request->getUserVar('gridOp');
            if (!in_array($operation, array('fetchGrid', 'fetchRows', 'fetchRow', 'saveSequence'), true)) {
                return new JSONMessage(false);
            }
            $grid->initialize($request);
            return $grid->$operation($args, $request);
        }
        $contextId = $request->getContext()->getId();
        if ($verb === 'addBlock') {
            $plugin = clone $this;
            $plugin->blockId = bin2hex(random_bytes(16));
        } else {
            $plugin = $this->forBlock($contextId, $request->getUserVar('blockId'));
            if (!$plugin) {
                return new JSONMessage(false);
            }
        }
        if ($verb === 'setVisibility') {
            $visible = $request->getUserVar('visible');
            if (!$request->isPost() || !$request->checkCSRF() || !in_array($visible, array('0', '1'), true)) {
                return new JSONMessage(false);
            }
            $plugin->updateBlockSetting($contextId, 'visible', $visible === '1', 'bool');
            return new JSONMessage(true);
        }
        if ($verb === 'deleteBlock') {
            if (!$request->isPost() || !$request->checkCSRF() || !$plugin->deleteBlock($contextId)) {
                return new JSONMessage(false);
            }
            return DAO::getDataChangedEvent();
        }
        if ($verb === 'grid') {
            // Use the management endpoint protected by PluginAccessPolicy.
            $this->import('controllers.grid.SponsorsBlockGridHandler');
            $grid = new SponsorsBlockGridHandler($plugin);
            $operation = $request->getUserVar('gridOp');
            if (!is_string($operation) || !in_array($operation, array('fetchGrid', 'fetchRows', 'fetchRow', 'addLogo', 'chooseLogo', 'addGalleryLogo', 'previewLogo', 'editLogo', 'updateLogo', 'deleteLogo', 'saveSequence'), true)) {
                return new JSONMessage(false);
            }
            $grid->initialize($request);
            return $grid->$operation($args, $request);
        }
        $this->import('SponsorsBlockSettingsForm');
        $form = new SponsorsBlockSettingsForm($plugin, $contextId, $verb === 'addBlock');
        if ($request->getUserVar('save')) {
            $form->readInputData();
            if ($form->validate() && $form->execute()) {
                return DAO::getDataChangedEvent();
            }
        } else {
            $form->initData();
        }
        return new JSONMessage(true, $form->fetch($request));
    }

    public function getLogos($contextId)
    {
        $logos = $this->getBlockSetting($contextId, 'logos');
        return is_array($logos) ? $logos : array();
    }

    /** Delete a block and the public image copies that belong to it. */
    public function deleteBlock($contextId)
    {
        if (!in_array($this->blockId, $this->getBlockIds($contextId), true)) {
            return false;
        }
        $logos = $this->getLogos($contextId);
        $ids = array_values(array_diff($this->getBlockIds($contextId), array($this->blockId)));
        $this->updateSetting($contextId, 'logoBlocks', $ids, 'object');
        $this->updateSetting($contextId, 'logoBlockOrder', $ids, 'object');
        foreach (array('title', 'logos', 'visible', 'showSidebar', 'showAboveFooter', 'grayscaleLogos', 'descriptionPlacement') as $name) {
            $this->deleteBlockSetting($contextId, $name);
        }
        import('classes.file.PublicFileManager');
        $manager = new PublicFileManager();
        foreach ($logos as $logo) {
            if (isset($logo['file']) && is_string($logo['file'])) {
                $manager->removeContextFile($contextId, $logo['file']);
            }
        }
        return true;
    }

    public function deleteBlockSetting($contextId, $name)
    {
        $settingsDao = DAORegistry::getDAO('PluginSettingsDAO');
        return $settingsDao->deleteSetting(
            $contextId,
            $this->getName(),
            'logoBlock_' . $this->blockId . '_' . $name
        );
    }

    /** Bundled catalog: request parameters can select IDs, never file paths. */
    public function getLogoCatalog()
    {
        $catalog = json_decode(file_get_contents(__DIR__ . '/logos/catalog.json'), true);
        if (!is_array($catalog)) {
            return array();
        }
        uasort($catalog, function ($first, $second) {
            return strnatcasecmp($first['name'], $second['name']);
        });
        return $catalog;
    }

    public function getGridUrl($request, $operation, $params = array())
    {
        return $request->getRouter()->url(
            $request,
            null,
            'grid.settings.plugins.SettingsPluginGridHandler',
            'manage',
            null,
            array_merge($params, array('category' => 'blocks', 'plugin' => $this->getName(),
                'verb' => 'grid', 'gridOp' => $operation, 'blockId' => $this->blockId))
        );
    }

    public function getBlockTitles($contextId)
    {
        $title = $this->getBlockSetting($contextId, 'title');
        if (is_array($title)) {
            return $title;
        }
        // Preserve titles saved before multilingual settings were introduced.
        if (is_string($title) && trim($title) !== '') {
            return array(AppLocale::getPrimaryLocale() => $title);
        }
        return array();
    }

    public function getBlockTitle($contextId)
    {
        $titles = $this->getBlockTitles($contextId);
        foreach (array(AppLocale::getLocale(), AppLocale::getPrimaryLocale()) as $locale) {
            if (isset($titles[$locale]) && is_string($titles[$locale]) && trim($titles[$locale]) !== '') {
                return $titles[$locale];
            }
        }
        return '';
    }

    public function getPublicUrl($request, $contextId)
    {
        import('classes.file.PublicFileManager');
        $manager = new PublicFileManager();
        return $request->getBaseUrl() . '/' . $manager->getContextFilesPath($contextId);
    }

    private function assignBlock($templateMgr, $request, $contextId)
    {
        $templateMgr->assign(array(
            'sponsorsBlockId' => $this->blockId,
            'sponsorsBlockTitle' => $this->getBlockTitle($contextId),
            'sponsorsBlockLogos' => $this->getLogos($contextId),
            'sponsorsDescriptionPlacement' => $this->getDescriptionPlacement($contextId),
            'sponsorsBlockGrayscale' => (bool) $this->getBlockSetting($contextId, 'grayscaleLogos'),
            'sponsorsBlockPublicUrl' => $this->getPublicUrl($request, $contextId),
        ));
    }

    public function getContents($templateMgr, $request = null)
    {
        $request = $request ?: Application::get()->getRequest();
        $context = $request->getContext();
        if (!$context || !$this->getEnabled($context->getId())) {
            return '';
        }
        $html = '';
        foreach ($this->getBlockIds($context->getId()) as $id) {
            $block = $this->forBlock($context->getId(), $id);
            if (!$block->isBlockVisible($context->getId()) || !$block->getLogos($context->getId())) {
                continue;
            }
            if ($block->getBlockPlacement($context->getId()) !== 'sidebar') {
                $templateMgr->registerFilter('output', array($this, 'displayAboveFooter'));
            }
            if ($block->getBlockPlacement($context->getId()) === 'footer') {
                continue;
            }
            $block->assignBlock($templateMgr, $request, $context->getId());
            $html .= parent::getContents($templateMgr, $request);
        }
        return $html;
    }

    public function displayAboveFooter($output, $templateMgr)
    {
        if (stripos($output, '</body>') === false || strpos($output, 'id="sponsorsBlockAboveFooter"') !== false) {
            return $output;
        }
        $request = Application::get()->getRequest();
        $context = $request->getContext();
        if (!$context || !$this->getEnabled($context->getId())) {
            return $output;
        }
        $pattern = '/<div\\b[^>]*\\bclass=["\'][^"\']*\\bpkp_structure_footer_wrapper\\b[^"\']*["\'][^>]*>/i';
        if (!preg_match($pattern, $output, $match, PREG_OFFSET_CAPTURE)) {
            return $output;
        }
        $strip = '';
        foreach ($this->getBlockIds($context->getId()) as $id) {
            $block = $this->forBlock($context->getId(), $id);
            if (!$block->isBlockVisible($context->getId()) || !$block->getLogos($context->getId()) || $block->getBlockPlacement($context->getId()) === 'sidebar') {
                continue;
            }
            $block->assignBlock($templateMgr, $request, $context->getId());
            $strip .= $templateMgr->fetch($this->getTemplateResource('aboveFooter.tpl'));
        }
        $separator = '<hr style="border: 0; border-top: 1px solid #ddd; margin: 0;">';
        return $strip === '' ? $output : substr_replace(
            $output,
            '<div id="sponsorsBlockAboveFooter">' . $separator . $strip . '</div>',
            $match[0][1],
            0
        );
    }
}
