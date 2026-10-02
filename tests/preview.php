<?php

/** Run: php plugins/blocks/logoBlocks/tests/preview.php */
require __DIR__ . '/grid.php';

$plugin = new GridTestPlugin();
$plugin->pluginPath = dirname(__DIR__);
$request->contextId = 1;
$request->vars = array();
$grid = new LogoBlocksLogoGridHandler($plugin);
$form = new LogoBlocksLogoForm($plugin, 1);
$form->initData();
check($form->getData('crop') === false, 'Cropping must start unchecked');
$catalog = $plugin->getLogoCatalog();
$entryId = array_key_first($catalog);
$entryPath = dirname(__DIR__) . '/logos/' . $catalog[$entryId]['file'];
Config::$publicDir = sys_get_temp_dir() . '/sponsors-preview-' . bin2hex(random_bytes(6));
mkdir(Config::$publicDir . '/journals/1', 0777, true);
try {
    foreach (array(null, '1', '0', array('1')) as $crop) {
        $request->vars = array('galleryId' => $entryId, 'crop' => $crop);
        $preview = $grid->previewLogo(array(), $request);
        check($preview->getStatus(), 'Preview rejected');
        $bytes = base64_decode(substr($preview->getContent(), strlen('data:image/png;base64,')), true);
        check($bytes === LogoBlocksImage::resize($entryPath, false), 'Gallery must ignore crop requests');
        check(!$plugin->getLogos(1) && !glob(Config::$publicDir . '/journals/1/*'), 'Preview persisted data');
        $form = new LogoBlocksLogoForm($plugin, 1, null, true);
        $form->readInputData();
        check($form->validate(false) && $form->execute(), 'Saving previewed image failed');
        $logos = $plugin->getLogos(1);
        $id = array_key_first($logos);
        $saved = Config::$publicDir . '/journals/1/' . $logos[$id]['file'];
        check(file_get_contents($saved) === $bytes, 'Saved bytes differ from preview');
        $request->vars = array('logoId' => $id);
        check($grid->deleteLogo(array(), $request)->getStatus(), 'Cleanup failed');
    }
    foreach (array('post', 'csrf') as $guard) {
        $request->vars = array('galleryId' => $entryId);
        $request->$guard = false;
        check(!$grid->previewLogo(array(), $request)->getStatus(), 'Preview bypasses ' . $guard);
        $request->$guard = true;
    }
    foreach (array(array(), array('galleryId' => '../meta.jpg'), array('galleryId' => array('x'))) as $vars) {
        $request->vars = $vars;
        check(!$grid->previewLogo(array(), $request)->getStatus(), 'Invalid preview input accepted');
    }
    echo "PASS: upload crop default, gallery crop disabled, preview/save byte equality, no preview persistence, POST/CSRF and invalid inputs\n";
} finally {
    foreach (glob(Config::$publicDir . '/journals/1/*') as $file) {
        unlink($file);
    }
    rmdir(Config::$publicDir . '/journals/1');
    rmdir(Config::$publicDir . '/journals');
    rmdir(Config::$publicDir);
}
