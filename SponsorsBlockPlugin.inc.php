<?php
/** Sidebar logos for OJS 3.3. Distributed under the GNU GPL v3. */
import('lib.pkp.classes.plugins.BlockPlugin');

class SponsorsBlockPlugin extends BlockPlugin {
    function getDisplayName() {
        return __('plugins.block.sponsorsBlock.displayName');
    }

    function getDescription() {
        return __('plugins.block.sponsorsBlock.description');
    }

    function getActions($request, $verb) {
        $actions = parent::getActions($request, $verb);
        if ($request->getContext() && $this->getEnabled()) {
            import('lib.pkp.classes.linkAction.request.AjaxModal');
            array_unshift($actions, new LinkAction('settings', new AjaxModal(
                $request->getRouter()->url($request, null, null, 'manage', null, array(
                    'verb' => 'settings', 'plugin' => $this->getName(), 'category' => 'blocks'
                )), $this->getDisplayName()), __('manager.plugins.settings'), null));
        }
        return $actions;
    }

    function manage($args, $request) {
        if (!in_array($request->getUserVar('verb'), array('settings', 'grid'), true)) {
            return parent::manage($args, $request);
        }
        if (!$request->getContext() || !$this->getEnabled()) {
            return new JSONMessage(false);
        }
        if ($request->getUserVar('verb') === 'grid') {
            // Block plugins are not loaded before component routing. Use the
            // plugin management endpoint, already authorized by PluginAccessPolicy.
            $this->import('controllers.grid.SponsorsBlockGridHandler');
            $grid = new SponsorsBlockGridHandler($this);
            $operation = $request->getUserVar('gridOp');
            if (!is_string($operation) || !in_array($operation, array('fetchGrid', 'fetchRows', 'fetchRow', 'addLogo', 'editLogo', 'updateLogo', 'deleteLogo', 'saveSequence'), true)) {
                return new JSONMessage(false);
            }
            $grid->initialize($request);
            return $grid->$operation($args, $request);
        }
        $this->import('SponsorsBlockSettingsForm');
        $form = new SponsorsBlockSettingsForm($this, $request->getContext()->getId());
        if ($request->getUserVar('save')) {
            $form->readInputData();
            if ($form->validate() && $form->execute()) {
                return new JSONMessage(true);
            }
        } else {
            $form->initData();
        }
        return new JSONMessage(true, $form->fetch($request));
    }

    function getLogos($contextId) {
        $logos = $this->getSetting($contextId, 'logos');
        return is_array($logos) ? $logos : array();
    }

    function getGridUrl($request, $operation, $params = array()) {
        return $request->getRouter()->url($request, null,
            'grid.settings.plugins.SettingsPluginGridHandler', 'manage', null,
            array_merge($params, array('category' => 'blocks', 'plugin' => $this->getName(),
                'verb' => 'grid', 'gridOp' => $operation)));
    }

    function getBlockTitles($contextId) {
        $defaults = array(
            'en_US' => 'Indexers',
            'es_ES' => 'Indexadores',
            'fr_CA' => 'Indexeurs',
            'pt_BR' => 'Indexadores',
        );
        $title = $this->getSetting($contextId, 'title');
        if (is_array($title)) {
            return array_replace($defaults, $title);
        }
        // Preserve titles saved before multilingual settings were introduced.
        if ($title && !in_array($title, array('Sponsors Block', 'Bloco de patrocinadores'), true)) {
            $defaults[AppLocale::getPrimaryLocale()] = $title;
        }
        return $defaults;
    }

    function getBlockTitle($contextId) {
        $titles = $this->getBlockTitles($contextId);
        foreach (array(AppLocale::getLocale(), AppLocale::getPrimaryLocale()) as $locale) {
            if (isset($titles[$locale]) && is_string($titles[$locale]) && trim($titles[$locale]) !== '') {
                return $titles[$locale];
            }
        }
        return 'Indexers';
    }

    function getPublicUrl($request, $contextId) {
        import('classes.file.PublicFileManager');
        $manager = new PublicFileManager();
        return $request->getBaseUrl() . '/' . $manager->getContextFilesPath($contextId);
    }

    function getContents($templateMgr, $request = null) {
        $request = $request ?: Application::get()->getRequest();
        $context = $request->getContext();
        if (!$context || !$this->getEnabled($context->getId())) {
            return '';
        }
        $logos = $this->getLogos($context->getId());
        if (!$logos) {
            return '';
        }
        if ($this->getSetting($context->getId(), 'showAboveFooter')) {
            // Block plugins load when the sidebar is rendered. An output filter
            // can insert the additional strip before the native footer afterward.
            $templateMgr->registerFilter('output', array($this, 'displayAboveFooter'));
        }
        $templateMgr->assign(array(
            'sponsorsBlockTitle' => $this->getBlockTitle($context->getId()),
            'sponsorsBlockLogos' => $logos,
            'sponsorsBlockPublicUrl' => $this->getPublicUrl($request, $context->getId()),
        ));
        return parent::getContents($templateMgr, $request);
    }

    function displayAboveFooter($output, $templateMgr) {
        // Ignore AJAX fragments and nested fetches (including our own template).
        if (stripos($output, '</body>') === false || strpos($output, 'id="sponsorsBlockAboveFooter"') !== false) {
            return $output;
        }
        $request = Application::get()->getRequest();
        $context = $request->getContext();
        if (!$context || !$this->getEnabled($context->getId())
            || !$this->getSetting($context->getId(), 'showAboveFooter')) {
            return $output;
        }
        $logos = $this->getLogos($context->getId());
        // Use the footer wrapper from OJS 3.3's frontend footer template.
        $pattern = '/<div\\b[^>]*\\bclass=["\'][^"\']*\\bpkp_structure_footer_wrapper\\b[^"\']*["\'][^>]*>/i';
        if (!$logos || !preg_match($pattern, $output, $match, PREG_OFFSET_CAPTURE)) {
            return $output;
        }
        $templateMgr->assign(array(
            'sponsorsBlockTitle' => $this->getBlockTitle($context->getId()),
            'sponsorsBlockLogos' => $logos,
            'sponsorsBlockPublicUrl' => $this->getPublicUrl($request, $context->getId()),
        ));
        $strip = $templateMgr->fetch($this->getTemplateResource('aboveFooter.tpl'));
        return substr_replace($output, $strip, $match[0][1], 0);
    }
}
