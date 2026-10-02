<?php

/** Run: php plugins/blocks/logoBlocks/tests/gallery.php
 * Uses real OJS forms and file handling with in-memory settings/request doubles.
 */
require __DIR__ . '/grid.php';

$catalog = $plugin->getLogoCatalog();
$required = array('base', 'diadorim', 'dialnet', 'dimensions', 'doaj', 'ebsco', 'erihplus', 'google-scholar', 'latindex', 'lilacs', 'livre', 'miar', 'miguilim', 'oasisbr', 'openaire', 'openalex', 'psycinfo', 'pubmed-medline', 'redalyc', 'repec', 'road', 'scielo', 'scopus', 'sherpa-romeo', 'web-of-science');
check(!array_diff($required, array_keys($catalog)) && count($catalog) === count($required), 'Requested indexers missing');
$names = array_column($catalog, 'name');
check(count($names) === count(array_unique($names)), 'Duplicate catalog names');
$sorted = $names;
usort($sorted, 'strnatcasecmp');
check($names === $sorted, 'Gallery catalog is not alphabetical');
check($catalog['google-scholar']['name'] === 'Google Scholar', 'Existing Google Scholar name changed');
check($catalog['doaj']['acronym'] === 'DOAJ' && $catalog['doaj']['name'] === 'Directory of Open Access Journals', 'Catalog full name missing');
foreach ($catalog as $entry) {
    check(isset($entry['acronym']) && is_string($entry['acronym']), 'Catalog acronym field missing');
    check(mb_strlen($entry['name']) <= 200, 'Catalog name exceeds form limit');
}
echo "PASS: all 25 requested indexers, acronyms, full names and alphabetical gallery order\n";
$catalogHashes = array();
foreach ($catalog as $id => $entry) {
    check(!array_key_exists('url', $entry), 'Catalog must not contain predefined links');
    $path = dirname(__DIR__) . '/logos/' . $entry['file'];
    $catalogHashes[$id] = hash_file('sha256', $path);
    $size = getimagesizefromstring(LogoBlocksImage::resize($path));
    check($size[0] === 150 && $size[2] === IMAGETYPE_PNG, 'Catalog image cannot be imported: ' . $id);
}

$plugin = new GridTestPlugin();
$plugin->pluginPath = dirname(__DIR__);
$plugin->settings = array(1 => array('logoBlocks' => array('existing-block'), 'logoBlock_existing-block_title' => 'Keep title', 'logoBlock_existing-block_logos' => array('existing' => $logo)), 2 => array('logoBlock_existing-block_logos' => array('foreign' => $logo)));
$request->vars = array();
$request->contextId = 1;
$grid = new LogoBlocksLogoGridHandler($plugin);
$grid->initialize($request);
$actions = $grid->getActions();
check(isset($actions['chooseLogo'], $actions['addLogo']), 'Gallery and upload actions must coexist');
foreach (array(null, '', 'unknown', '../logos/capes.png', array('doaj')) as $id) {
    $form = new LogoBlocksLogoForm($plugin, 1, null, true);
    $form->setData('galleryId', $id);
    check(!$form->validate(false), 'Invalid catalog ID accepted');
}
foreach (array('post', 'csrf') as $guard) {
    $request->$guard = false;
    $form = new LogoBlocksLogoForm($plugin, 1, null, true);
    $form->setData('galleryId', 'doaj');
    check(!$form->validate(false), 'Unprotected gallery submission accepted');
    $request->$guard = true;
}
echo "PASS: importable catalog images, both actions, invalid IDs and POST/CSRF guards\n";

foreach (array('javascript:alert(1)', 'ftp://example.org', 'invalid', 'https://') as $url) {
    $form = new LogoBlocksLogoForm($plugin, 1, null, true);
    $form->setData('galleryId', 'doaj');
    $form->setData('url', $url);
    check(!$form->validate(false), 'Invalid gallery page link accepted');
    check($form->getData('url') === $url, 'Invalid link lost on redisplay');
}
foreach (array('', '  https://example.org/journal  ', null) as $url) {
    $form = new LogoBlocksLogoForm($plugin, 1, null, true);
    $form->setData('galleryId', 'doaj');
    $form->setData('url', $url);
    check($form->validate(false), 'Optional or valid gallery link rejected');
    check($form->getData('url') === ($url === null ? '' : trim($url)), 'Gallery link default or explicit value lost');
}
echo "PASS: editor-provided and empty gallery links; unsafe URLs rejected\n";

Config::$publicDir = sys_get_temp_dir() . '/sponsors-gallery-' . bin2hex(random_bytes(6));
mkdir(Config::$publicDir . '/journals/1', 0777, true);
try {
    foreach ($catalog as $catalogId => $entry) {
        $form = new LogoBlocksLogoForm($plugin, 1, null, true);
        $request->vars = array('galleryId' => $catalogId, 'name' => 'Tampered', 'acronym' => 'Tampered', 'url' => 'https://example.org/journal/' . $catalogId);
        $form->readInputData();
        check($form->validate(false) && $form->execute(), 'Catalog selection failed');
        $logos = $plugin->getLogos(1);
        $id = array_key_last($logos);
        $added = $logos[$id];
        check($added['acronym'] === $entry['acronym'], 'Catalog acronym not preserved');
        check($added['name'] === $entry['name'] && $added['url'] === 'https://example.org/journal/' . $catalogId, 'Catalog name or custom page link not preserved');
        $path = Config::$publicDir . '/journals/1/' . $added['file'];
        check(is_file($path) && getimagesize($path)[0] === 150, 'Journal copy missing or incorrectly sized');
        $edit = new LogoBlocksLogoForm($plugin, 1, $id);
        $edit->initData();
        check($edit->getData('acronym') === $entry['acronym'], 'Acronym not loaded for editing');
        $edit->setData('name', 'Edited');
        $edit->setData('url', 'https://example.org/journal');
        check($edit->validate(false) && $edit->execute(), 'Imported logo cannot be edited');
        check($plugin->getLogos(1)[$id]['acronym'] === $entry['acronym'], 'Editing lost acronym');
        check(array_keys($plugin->getLogos(1)) === array('existing', $id), 'Existing order changed');
        $request->vars = array('logoId' => $id);
        check($grid->deleteLogo(array(), $request)->getStatus() && !is_file($path), 'Imported copy not deleted');
        check(hash_file('sha256', dirname(__DIR__) . '/logos/' . $entry['file']) === $catalogHashes[$catalogId], 'Catalog original modified');
    }
    check($plugin->getLogos(1) === array('existing' => $logo), 'Existing logo changed');
    check($plugin->getBlockSetting(1, 'title') === 'Keep title', 'Title changed');
    check($plugin->getLogos(2) === array('foreign' => $logo), 'Another journal changed');
    echo "PASS: gallery import, server metadata, journal copies, edit/delete, order and catalog isolation\n";
} finally {
    foreach (glob(Config::$publicDir . '/journals/1/*') as $file) {
        unlink($file);
    }
    rmdir(Config::$publicDir . '/journals/1');
    rmdir(Config::$publicDir . '/journals');
    rmdir(Config::$publicDir);
}

$smarty = new Smarty();
$compileDirectory = sys_get_temp_dir() . '/sponsors-gallery-tpl-' . bin2hex(random_bytes(6));
mkdir($compileDirectory);
$smarty->setCompileDir($compileDirectory);
$smarty->setTemplateDir(BASE_SYS_DIR . '/lib/pkp/templates');
foreach (array('csrf', 'translate', 'fbvFormButtons') as $tag) {
    $smarty->registerPlugin('function', $tag, function () {
        return '';
    });
}
$smarty->assign(array('logoFormUrl' => '/manage?gridOp=addGalleryLogo', 'logoCatalog' => $catalog,
    'logoPreviewTemplate' => dirname(__DIR__) . '/templates/logoPreview.tpl',
    'logoPreviewUrl' => '/manage?gridOp=previewLogo', 'crop' => false, 'currentLogoUrl' => '',
    'logoCatalogUrl' => '/plugins/blocks/sponsors/logos', 'galleryId' => 'doaj', 'description' => 'Descrição <teste>', 'url' => 'https://example.org/journal', 'isError' => false));
try {
    $html = $smarty->fetch(dirname(__DIR__) . '/templates/logoGallery.tpl');
    check(preg_match('/<input\b[^>]*name="crop"/', $html) === 0 && strpos($html, 'sponsors-preview-result') === false, 'Gallery must not render an extra preview or crop control');
    check(substr_count($html, 'type="radio"') === count($catalog), 'Gallery did not render all logos');
    check(preg_match('/value="doaj"[^>]*\schecked/', $html) === 1, 'Selection lost on redisplay');
    check(strpos($html, 'value="https://example.org/journal"') !== false, 'Page link lost on redisplay');
    check(strpos($html, 'name="description" maxlength="100" value="Descrição &lt;teste&gt;"') !== false, 'Description limit, escaping or redisplay failed');
    check(strpos($html, 'id="sponsorsGalleryUrl"') < strpos($html, 'id="sponsorsGalleryDescription"'), 'Description must follow the link field');
    check(strpos($html, '<strong>DOAJ</strong>') !== false, 'Gallery acronym missing');
    check(strpos($html, 'Directory of Open Access Journals') === false, 'Gallery must not show expanded names');
    $catalog['doaj']['name'] = '<script>bad</script>';
    $catalog['doaj']['acronym'] = '<script>bad</script>';
    $smarty->assign('logoCatalog', $catalog);
    check(strpos($smarty->fetch(dirname(__DIR__) . '/templates/logoGallery.tpl'), '<script>bad</script>') === false, 'Catalog label not escaped');
    echo "PASS: gallery Smarty rendering, selection retention and escaped labels\n";
} finally {
    foreach (glob($compileDirectory . '/*') as $file) {
        unlink($file);
    }
    rmdir($compileDirectory);
}
