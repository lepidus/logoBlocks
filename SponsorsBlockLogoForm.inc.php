<?php

import('lib.pkp.classes.form.Form');
require_once __DIR__ . '/SponsorsBlockImage.inc.php';

/** Add or edit one logo without overwriting the title or unrelated logos. */
class SponsorsBlockLogoForm extends Form
{
    private $plugin;
    private $contextId;
    private $logoId;
    private $image;
    private $gallery;

    public function __construct($plugin, $contextId, $logoId = null, $gallery = false)
    {
        $this->plugin = $plugin;
        $this->contextId = $contextId;
        $this->logoId = $logoId;
        $this->gallery = $gallery;
        parent::__construct($plugin->getTemplateResource($gallery ? 'logoGallery.tpl' : 'logoForm.tpl'));
        $this->addCheck(new FormValidatorPost($this));
        $this->addCheck(new FormValidatorCSRF($this));
    }

    public function initData()
    {
        $this->setData('galleryId', '');
        $this->setData('crop', false);
        $logos = $this->plugin->getLogos($this->contextId);
        $logo = $this->logoId !== null && isset($logos[$this->logoId]) ? $logos[$this->logoId] : array('name' => '', 'url' => '');
        $this->setData('name', $logo['name']);
        $this->setData('acronym', isset($logo['acronym']) ? $logo['acronym'] : '');
        $this->setData('url', $logo['url']);
        $this->setData('description', isset($logo['description']) ? $logo['description'] : '');
    }

    public function readInputData()
    {
        $this->readUserVars(array('name', 'acronym', 'description', 'url', 'galleryId', 'crop'));
    }

    public function validate($callHooks = true)
    {
        if (!parent::validate($callHooks)) {
            return false;
        }
        if (!$this->prepareImage()) {
            return false;
        }
        foreach (array('name', 'acronym', 'description', 'url') as $field) {
            $value = $this->getData($field);
            $this->setData($field, is_string($value) ? trim($value) : '');
        }
        if (mb_strlen($this->getData('acronym')) > 30) {
            $this->addError('acronym', __('plugins.block.sponsorsBlock.invalidAcronym'));
        }
        if (mb_strlen($this->getData('description')) > 100) {
            $this->addError('description', __('plugins.block.sponsorsBlock.invalidDescription'));
        }
        $name = $this->getData('name');
        $url = $this->getData('url');
        if ($name === '' || mb_strlen($name) > 200) {
            $this->addError('name', __('plugins.block.sponsorsBlock.invalidEntry'));
        }
        if ($url !== '' && (!filter_var($url, FILTER_VALIDATE_URL)
            || !in_array(strtolower(parse_url($url, PHP_URL_SCHEME)), array('http', 'https'), true))) {
            $this->addError('url', __('plugins.block.sponsorsBlock.invalidEntry'));
        }
        $logos = $this->plugin->getLogos($this->contextId);
        if ($this->logoId !== null && !isset($logos[$this->logoId])) {
            $this->addError('name', __('plugins.block.sponsorsBlock.notFound'));
        }
        return $this->isValid();
    }

    /** Preview uses the same validation and image bytes as saving, without persistence. */
    public function getImagePreview()
    {
        if (!parent::validate() || !$this->prepareImage() || $this->image === null) {
            return null;
        }
        return 'data:image/png;base64,' . base64_encode($this->image);
    }

    private function prepareImage()
    {
        $crop = $this->getData('crop') === '1';
        if ($this->gallery) {
            $catalog = $this->plugin->getLogoCatalog();
            $id = $this->getData('galleryId');
            if (!is_string($id) || !isset($catalog[$id])) {
                $this->addError('galleryId', __('plugins.block.sponsorsBlock.galleryRequired'));
                return false;
            }
            $entry = $catalog[$id];
            $this->setData('name', $entry['name']);
            $this->setData('acronym', isset($entry['acronym']) ? $entry['acronym'] : '');
            try {
                $this->image = SponsorsBlockImage::resize(__DIR__ . '/logos/' . $entry['file'], false);
            } catch (RuntimeException $e) {
                $this->addError('galleryId', __($e->getMessage()));
                return false;
            }
            return true;
        }
        $upload = isset($_FILES['logo']) ? $_FILES['logo'] : null;
        if ($upload && (!isset($upload['error']) || $upload['error'] !== UPLOAD_ERR_NO_FILE)) {
            if (!isset($upload['error'], $upload['tmp_name']) || $upload['error'] !== UPLOAD_ERR_OK
                || !is_string($upload['tmp_name']) || !is_uploaded_file($upload['tmp_name'])) {
                $this->addError('logo', __('plugins.block.sponsorsBlock.invalidImage'));
            } else {
                try {
                    $this->image = SponsorsBlockImage::resize($upload['tmp_name'], $crop);
                } catch (RuntimeException $e) {
                    $this->addError('logo', __($e->getMessage()));
                }
            }
        } elseif ($this->logoId === null) {
            $this->addError('logo', __('plugins.block.sponsorsBlock.imageRequired'));
        }
        return $this->isValid();
    }

    public function fetch($request, $template = null, $display = false)
    {
        $logos = $this->plugin->getLogos($this->contextId);
        $logo = $this->logoId !== null && isset($logos[$this->logoId]) ? $logos[$this->logoId] : null;
        TemplateManager::getManager($request)->assign(array(
            'logoFormUrl' => $this->plugin->getGridUrl(
                $request,
                $this->gallery ? 'addGalleryLogo' : 'updateLogo',
                $this->logoId === null ? array() : array('logoId' => $this->logoId)
            ),
            'logoPreviewUrl' => $this->plugin->getGridUrl($request, 'previewLogo'),
            'logoPreviewTemplate' => $this->plugin->getTemplateResource('logoPreview.tpl'),
            'currentLogoUrl' => $logo ? $this->plugin->getPublicUrl($request, $this->contextId) . '/' . $logo['file'] : '',
        ));
        if ($this->gallery) {
            TemplateManager::getManager($request)->assign(array(
                'logoCatalog' => $this->plugin->getLogoCatalog(),
                'logoCatalogUrl' => $request->getBaseUrl() . '/' . $this->plugin->getPluginPath() . '/logos',
            ));
        }
        return parent::fetch($request, $template, $display);
    }

    public function execute(...$functionArgs)
    {
        // Reload here so saving one item preserves the other current entries.
        $logos = $this->plugin->getLogos($this->contextId);
        if ($this->logoId !== null && !isset($logos[$this->logoId])) {
            $this->addError('name', __('plugins.block.sponsorsBlock.notFound'));
            return false;
        }
        import('classes.file.PublicFileManager');
        $manager = new PublicFileManager();
        $id = $this->logoId === null ? bin2hex(random_bytes(16)) : $this->logoId;
        $oldFile = $this->logoId === null ? null : $logos[$id]['file'];
        $file = $oldFile;
        if ($this->image !== null) {
            $file = 'sponsorsBlock-' . bin2hex(random_bytes(16)) . '.png';
            if (!$manager->writeContextFile($this->contextId, $file, $this->image)) {
                $manager->removeContextFile($this->contextId, $file);
                $this->addError('logo', __('plugins.block.sponsorsBlock.writeError'));
                return false;
            }
        }
        $logos[$id] = array('file' => $file, 'name' => $this->getData('name'), 'acronym' => $this->getData('acronym'), 'url' => $this->getData('url'), 'description' => $this->getData('description'));
        $this->plugin->updateBlockSetting($this->contextId, 'logos', $logos, 'object');
        if ($oldFile !== null && $oldFile !== $file) {
            $manager->removeContextFile($this->contextId, $oldFile);
        }
        return true;
    }
}
