<?php

import('lib.pkp.classes.form.Form');

/** The block title is saved independently from the grid's individual logos. */
class SponsorsBlockSettingsForm extends Form
{
    private $plugin;
    private $contextId;

    public function __construct($plugin, $contextId)
    {
        $this->plugin = $plugin;
        $this->contextId = $contextId;
        parent::__construct($plugin->getTemplateResource('settingsForm.tpl'));
        $this->addCheck(new FormValidatorPost($this));
        $this->addCheck(new FormValidatorCSRF($this));
        $this->addCheck(new FormValidatorLocale($this, 'title', 'required', 'plugins.block.sponsorsBlock.requiredName'));
    }

    public function initData()
    {
        $this->setData('title', $this->plugin->getBlockTitles($this->contextId));
        $this->setData('grayscaleLogos', (bool) $this->plugin->getSetting($this->contextId, 'grayscaleLogos'));
        $this->setData('showAboveFooter', (bool) $this->plugin->getSetting($this->contextId, 'showAboveFooter'));
    }

    public function readInputData()
    {
        $this->readUserVars(array('title', 'showAboveFooter', 'grayscaleLogos'));
    }

    public function validate($callHooks = true)
    {
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
            'pluginName' => $this->plugin->getName(),
            'sponsorsBlockGridUrl' => $this->plugin->getGridUrl($request, 'fetchGrid'),
        ));
        return parent::fetch($request, $template, $display);
    }

    public function execute(...$functionArgs)
    {
        // Retain translations for languages temporarily disabled in the journal.
        $titles = array_replace($this->plugin->getBlockTitles($this->contextId), $this->getData('title'));
        $this->plugin->updateSetting($this->contextId, 'title', $titles, 'object');
        $this->plugin->updateSetting(
            $this->contextId,
            'showAboveFooter',
            in_array($this->getData('showAboveFooter'), array('1', 1, true), true),
            'bool'
        );
        $this->plugin->updateSetting(
            $this->contextId,
            'grayscaleLogos',
            in_array($this->getData('grayscaleLogos'), array('1', 1, true), true),
            'bool'
        );
        return true;
    }
}
