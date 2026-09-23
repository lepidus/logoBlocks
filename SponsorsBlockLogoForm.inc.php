<?php
import('lib.pkp.classes.form.Form');
require_once __DIR__ . '/SponsorsBlockImage.inc.php';

/** Add or edit one logo without overwriting the title or unrelated logos. */
class SponsorsBlockLogoForm extends Form {
    private $plugin;
    private $contextId;
    private $logoId;
    private $image;

    function __construct($plugin, $contextId, $logoId = null) {
        $this->plugin = $plugin;
        $this->contextId = $contextId;
        $this->logoId = $logoId;
        parent::__construct($plugin->getTemplateResource('logoForm.tpl'));
        $this->addCheck(new FormValidatorPost($this));
        $this->addCheck(new FormValidatorCSRF($this));
    }

    function initData() {
        $logos = $this->plugin->getLogos($this->contextId);
        $logo = $this->logoId !== null && isset($logos[$this->logoId]) ? $logos[$this->logoId] : array('name' => '', 'url' => '');
        $this->setData('name', $logo['name']);
        $this->setData('url', $logo['url']);
    }

    function readInputData() {
        $this->readUserVars(array('name', 'url'));
    }

    function validate($callHooks = true) {
        if (!parent::validate($callHooks)) {
            return false;
        }
        foreach (array('name', 'url') as $field) {
            $value = $this->getData($field);
            $this->setData($field, is_string($value) ? trim($value) : '');
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
        $upload = isset($_FILES['logo']) ? $_FILES['logo'] : null;
        if ($upload && (!isset($upload['error']) || $upload['error'] !== UPLOAD_ERR_NO_FILE)) {
            if (!isset($upload['error'], $upload['tmp_name']) || $upload['error'] !== UPLOAD_ERR_OK
                || !is_string($upload['tmp_name']) || !is_uploaded_file($upload['tmp_name'])) {
                $this->addError('logo', __('plugins.block.sponsorsBlock.invalidImage'));
            } else {
                try {
                    $this->image = SponsorsBlockImage::resize($upload['tmp_name']);
                } catch (RuntimeException $e) {
                    $this->addError('logo', __($e->getMessage()));
                }
            }
        } elseif ($this->logoId === null) {
            $this->addError('logo', __('plugins.block.sponsorsBlock.imageRequired'));
        }
        return $this->isValid();
    }

    function fetch($request, $template = null, $display = false) {
        $logos = $this->plugin->getLogos($this->contextId);
        $logo = $this->logoId !== null && isset($logos[$this->logoId]) ? $logos[$this->logoId] : null;
        TemplateManager::getManager($request)->assign(array(
            'logoFormUrl' => $this->plugin->getGridUrl($request, 'updateLogo',
                $this->logoId === null ? array() : array('logoId' => $this->logoId)),
            'currentLogoUrl' => $logo ? $this->plugin->getPublicUrl($request, $this->contextId) . '/' . $logo['file'] : '',
        ));
        return parent::fetch($request, $template, $display);
    }

    function execute(...$functionArgs) {
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
        $logos[$id] = array('file' => $file, 'name' => $this->getData('name'), 'url' => $this->getData('url'));
        $this->plugin->updateSetting($this->contextId, 'logos', $logos, 'object');
        if ($oldFile !== null && $oldFile !== $file) {
            $manager->removeContextFile($this->contextId, $oldFile);
        }
        return true;
    }
}
