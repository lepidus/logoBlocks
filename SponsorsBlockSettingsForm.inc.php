<?php

import('lib.pkp.classes.form.Form');

/** The block title is saved independently from the grid's individual logos. */
class SponsorsBlockSettingsForm extends Form
{
    private $plugin;
    private $contextId;
    private $newBlock;

    public function __construct($plugin, $contextId, $newBlock = false)
    {
        $this->plugin = $plugin;
        $this->newBlock = $newBlock;
        $this->contextId = $contextId;
        parent::__construct($plugin->getTemplateResource('settingsForm.tpl'));
        $this->setData('placement', $plugin->getBlockPlacement($contextId));
        $this->addCheck(new FormValidatorPost($this));
        $this->addCheck(new FormValidatorCSRF($this));
        $this->addCheck(new FormValidatorLocale($this, 'title', 'required', 'plugins.block.sponsorsBlock.requiredName'));
    }

    public function initData()
    {
        $this->setData('title', $this->plugin->getBlockTitles($this->contextId));
        $this->setData('grayscaleLogos', (bool) $this->plugin->getBlockSetting($this->contextId, 'grayscaleLogos'));
        $this->setData('placement', $this->plugin->getBlockPlacement($this->contextId));
    }

    public function readInputData()
    {
        $this->readUserVars(array('title', 'placement', 'grayscaleLogos'));
    }

    public function validate($callHooks = true)
    {
        if (!in_array($this->getData('placement'), array('sidebar', 'footer', 'both'), true)) {
            $this->addError('placement', __('plugins.block.sponsorsBlock.placementRequired'));
        }
        $input = $this->getData('title');
        $titles = array();
        foreach ($this->supportedLocales as $locale => $name) {
            $value = is_array($input) && isset($input[$locale]) ? $input[$locale] : '';
            if (!is_string($value) || mb_strlen(trim($value)) > 200) {
                $this->addError('title', __('plugins.block.sponsorsBlock.requiredName'));
            }
            $titles[$locale] = is_string($value) ? trim($value) : '';
        }
        $this->setData('title', $titles);
        return parent::validate($callHooks);
    }

    public function fetch($request, $template = null, $display = false)
    {
        TemplateManager::getManager($request)->assign(array(
            'sponsorsNewBlock' => $this->newBlock,
            'sponsorsBlockPlacementOptions' => array(
                'sidebar' => 'plugins.block.sponsorsBlock.placementSidebar',
                'footer' => 'plugins.block.sponsorsBlock.placementFooter',
                'both' => 'plugins.block.sponsorsBlock.placementBoth',
            ),
            'sponsorsSettingsUrl' => $this->plugin->getManageUrl($request, $this->newBlock ? 'addBlock' : 'editBlock',
                array('blockId' => $this->plugin->getBlockId(), 'save' => 1)),
            'sponsorsBlockGridUrl' => $this->plugin->getGridUrl($request, 'fetchGrid'),
        ));
        return parent::fetch($request, $template, $display);
    }

    public function execute(...$functionArgs)
    {
        // Retain translations for languages temporarily disabled in the journal.
        $titles = array_replace($this->plugin->getBlockTitles($this->contextId), $this->getData('title'));
        $this->plugin->updateBlockSetting($this->contextId, 'title', $titles, 'object');
        $this->plugin->updateBlockSetting(
            $this->contextId,
            'showAboveFooter',
            in_array($this->getData('placement'), array('footer', 'both'), true),
            'bool'
        );
        $this->plugin->updateBlockSetting($this->contextId, 'showSidebar', $this->getData('placement') !== 'footer', 'bool');
        $this->plugin->updateBlockSetting(
            $this->contextId,
            'grayscaleLogos',
            in_array($this->getData('grayscaleLogos'), array('1', 1, true), true),
            'bool'
        );
        if ($this->newBlock) {
            $this->plugin->registerBlock($this->contextId);
        }
        return true;
    }
}
