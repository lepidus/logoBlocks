<?php

/** Run: php plugins/blocks/sponsors/tests/blocks.php
 * Real OJS forms/grids and Smarty; request and persistence are test doubles.
 */
require __DIR__ . '/grid.php';
require dirname(__DIR__) . '/controllers/grid/SponsorsBlocksGridHandler.inc.php';

$settings = array(1 => array('title' => array('en_US' => 'Indexers'), 'logos' => array('existing' => $logo)),
    2 => array('logos' => array('foreign' => $logo)));
$plugin = new GridTestPlugin();
$plugin->pluginPath = dirname(__DIR__);
// Scoped plugin copies share the persistence double, just as they share the DAO.
$plugin->settings =& $settings;
$request->contextId = 1;
$request->vars = array('verb' => 'addBlock', 'save' => '1', 'placement' => 'sidebar', 'title' => array('en_US' => 'Sponsors', 'pt_BR' => 'Patrocinadores'));
$before = $settings;
foreach (array('post', 'csrf') as $guard) {
    $request->$guard = false;
    $form = new SponsorsBlockSettingsForm($plugin, 1, true);
    $form->readInputData();
    check(!$form->validate(false), 'Unprotected block creation accepted');
    check($settings === $before, 'Invalid creation changed data');
    $request->$guard = true;
}
check($plugin->manage(array(), $request)->getStatus(), 'Block creation failed');
$ids = $plugin->getBlockIds(1);
check(count($ids) === 2 && $ids[0] === 'indexers', 'Missing original or new block');
$id = $ids[1];
$block = $plugin->forBlock(1, $id);
check($block->getBlockTitle(1) === 'Sponsors', 'New title missing');
check($block->getLogos(1) === array(), 'New block inherited old logos');
check($plugin->getLogos(1) === $before[1]['logos'], 'Creating block changed old logos');
check($plugin->getBlockTitles(1)['en_US'] === 'Indexers', 'Creating block changed old title');
check($plugin->forBlock(2, $id) === null, 'Other journal block accepted');
foreach (array(null, array($id), 'missing') as $invalid) {
    $request->vars = array('verb' => 'grid', 'blockId' => $invalid, 'gridOp' => 'deleteLogo', 'logoId' => 'existing');
    check(!$plugin->manage(array(), $request)->getStatus(), 'Invalid block identifier accepted');
}
echo "PASS: block creation, preservation of legacy data, POST/CSRF and journal/block ownership\n";

$defaultLogoGrid = new SponsorsBlockGridHandler($plugin);
$defaultLogoGrid->initialize($request);
$defaultLogoActions = $defaultLogoGrid->getActions();
check(isset($defaultLogoActions['chooseLogo'], $defaultLogoActions['addLogo']), 'Default block lost its gallery or upload action');
$block->updateBlockSetting(1, 'logos', array('a' => $logo, 'b' => array_merge($logo, array('name' => 'Second'))), 'object');
$grid = new SponsorsBlockGridHandler($block);
$grid->initialize($request);
$grid->setUrls($request);
$customLogoActions = $grid->getActions();
check(!isset($customLogoActions['chooseLogo']) && isset($customLogoActions['addLogo']), 'Custom block exposed the indexer gallery action');
$request->vars = array();
check(!$grid->chooseLogo(array(), $request)->getStatus(), 'Custom block gallery endpoint was accessible');
foreach ($grid->getUrls() as $url) {
    check(strpos($url, 'blockId=' . $id) !== false, 'Grid lost selected block');
}
$request->vars = array('data' => '["b","a"]');
check($grid->saveSequence(array(), $request)->getStatus(), 'New block order save failed');
check(array_keys($block->getLogos(1)) === array('b', 'a'), 'New block order lost');
$form = new SponsorsBlockLogoForm($block, 1, 'a');
$form->setData('name', 'Changed sponsor');
$form->setData('url', 'https://example.org');
check($form->validate(false) && $form->execute(), 'New block logo edit failed');
check($block->getLogos(1)['a']['name'] === 'Changed sponsor', 'Edited wrong logo');
$request->vars = array('logoId' => 'existing');
check(!$grid->deleteLogo(array(), $request)->getStatus(), 'Other block deletion accepted');
$titleForm = new SponsorsBlockSettingsForm($block, 1);
$titleForm->setData('title', array('en_US' => 'Partners <test>'));
$titleForm->setData('grayscaleLogos', '1');
$titleForm->setData('placement', 'both');
check($titleForm->validate(false) && $titleForm->execute(), 'New block settings save failed');
check($plugin->getLogos(1) === $before[1]['logos'] && $settings[2] === $before[2], 'Block operation affected old data');
check(!$plugin->getBlockSetting(1, 'grayscaleLogos') && !$plugin->getBlockSetting(1, 'showAboveFooter'), 'Display settings leaked to original block');
echo "PASS: selected block retained in URLs; independent logo edits, order, title and display settings\n";

// The bundled catalog belongs only to the original block, including direct POSTs.
check(count($plugin->getLogoCatalog()) > 0, 'Default indexer catalog missing');
check($block->getLogoCatalog() === array(), 'New block inherited the bundled catalog');
$galleryForm = new SponsorsBlockLogoForm($block, 1, null, true);
$galleryForm->setData('galleryId', 'doaj');
$galleryForm->setData('url', '');
check(!$galleryForm->validate(false), 'New block accepted a default catalog entry');

$blocksGrid = new SponsorsBlocksGridHandler($plugin);
$blocksGrid->initialize($request);
$blocksGrid->setUrls($request);
check(strpos($blocksGrid->getUrls()['saveSequenceUrl'], 'verb=blocks') !== false, 'Block ordering uses wrong endpoint');
$beforeOrder = $settings;
$request->vars = array('verb' => 'blocks', 'gridOp' => 'saveSequence', 'data' => json_encode(array($id, 'indexers')));
check($plugin->manage(array(), $request)->getStatus(), 'Block ordering failed');
check($plugin->getBlockIds(1) === array($id, 'indexers'), 'Default block cannot be moved');
check($plugin->getLogos(1) === $before[1]['logos'] && $settings[2] === $before[2], 'Ordering changed logos or another journal');
foreach (array('invalid', '{}', '["indexers"]', json_encode(array($id, $id)), json_encode(array($id, 'foreign')), json_encode(array($id, array()))) as $invalid) {
    $request->vars['data'] = $invalid;
    check(!$plugin->manage(array(), $request)->getStatus(), 'Invalid block order accepted');
    check($plugin->getBlockIds(1) === array($id, 'indexers'), 'Rejected block order changed data');
}
$request->vars['data'] = json_encode(array('indexers', $id));
foreach (array('post', 'csrf') as $guard) {
    $request->$guard = false;
    check(!$plugin->manage(array(), $request)->getStatus(), 'Unprotected block ordering accepted');
    $request->$guard = true;
}
$placementForm = new SponsorsBlockSettingsForm($block, 1);
$placementForm->initData();
foreach (array(null, 'nowhere', array('sidebar')) as $invalid) {
    $invalidForm = new SponsorsBlockSettingsForm($block, 1);
    $invalidForm->initData();
    $invalidForm->setData('placement', $invalid);
    check(!$invalidForm->validate(false), 'Invalid placement accepted');
}
echo "PASS: block ordering, invalid/stale order and request guards; bundled catalog restricted to indexers\n";

$smarty = new Smarty();
$compileDirectory = sys_get_temp_dir() . '/sponsors-blocks-' . bin2hex(random_bytes(6));
mkdir($compileDirectory);
$smarty->setCompileDir($compileDirectory);
try {
    $html = $plugin->getContents($smarty, $request);
    check(substr_count($html, '<h2') === 2, 'Both blocks should render');
    check(strpos($html, 'Partners &lt;test&gt;') < strpos($html, '>Indexers<'), 'Sidebar ignored block order');
    check(strpos($html, 'Partners &lt;test&gt;') !== false, 'New block title is not escaped');
    check(substr_count($html, 'block_sponsorsBlock_grayscale"') === 1, 'Grayscale leaked to other block');
    $page = '<html><body>' . $html . '<div class="pkp_structure_footer_wrapper">Footer</div></body></html>';
    $page = $plugin->displayAboveFooter($page, $smarty);
    check(strpos($page, 'id="sponsorsBlockAboveFooter-' . $id . '"') !== false, 'New block footer missing');
    check(strpos($page, 'id="sponsorsBlockAboveFooter-indexers"') === false, 'Disabled block footer rendered');
    check($plugin->displayAboveFooter($page, $smarty) === $page, 'Footer duplicated');
    $plugin->updateBlockSetting(1, 'showAboveFooter', true, 'bool');
    $twoFooters = $plugin->displayAboveFooter('<html><body><div class="pkp_structure_footer_wrapper">Footer</div></body></html>', $smarty);
    check(substr_count($twoFooters, '<section id="sponsorsBlockAboveFooter-') === 2, 'Multiple enabled footers not rendered');
    check(substr_count($twoFooters, 'id="sponsorsBlockAboveFooter"') === 1, 'Footer wrapper duplicated');
    check(substr_count($twoFooters, '<hr ') === 1, 'Footer blocks need one top separator');
    check(strpos($twoFooters, '<hr ') < strpos($twoFooters, '<section id="sponsorsBlockAboveFooter-'), 'Separator is not above the block group');
    $savedLogos = $block->getLogos(1);
    check($block->isBlockVisible(1) && $plugin->isBlockVisible(1), 'Blocks should be visible by default');
    $request->vars = array('verb' => 'setVisibility', 'blockId' => $id, 'visible' => '0');
    foreach (array('post', 'csrf') as $guard) {
        $request->$guard = false;
        check(!$plugin->manage(array(), $request)->getStatus(), 'Unprotected visibility change accepted');
        check($block->isBlockVisible(1), 'Rejected request hid block');
        $request->$guard = true;
    }
    foreach (array(null, 'yes', array('0')) as $invalid) {
        $request->vars['visible'] = $invalid;
        check(!$plugin->manage(array(), $request)->getStatus(), 'Invalid visibility accepted');
    }
    $request->vars['visible'] = '0';
    $request->contextId = 2;
    check(!$plugin->manage(array(), $request)->getStatus(), 'Foreign journal block visibility changed');
    $request->contextId = 1;
    check($plugin->manage(array(), $request)->getStatus(), 'Could not disable block');
    check(!$block->isBlockVisible(1) && $plugin->isBlockVisible(1), 'Wrong block visibility changed');
    check($block->getLogos(1) === $savedLogos && $settings[2] === $before[2], 'Visibility change modified logo or journal data');
    $hiddenHtml = $plugin->getContents($smarty, $request);
    check(substr_count($hiddenHtml, '<h2') === 1 && strpos($hiddenHtml, 'Partners &lt;test&gt;') === false, 'Disabled block still in sidebar');
    $footerPage = '<html><body><div class="pkp_structure_footer_wrapper">Footer</div></body></html>';
    $hiddenFooter = $plugin->displayAboveFooter($footerPage, $smarty);
    check(strpos($hiddenFooter, 'id="sponsorsBlockAboveFooter-' . $id . '"') === false, 'Disabled block still above footer');
    check(strpos($hiddenFooter, 'id="sponsorsBlockAboveFooter-indexers"') !== false, 'Other footer disappeared');
    $request->vars['visible'] = '1';
    check($plugin->manage(array(), $request)->getStatus(), 'Could not enable block');
    check(substr_count($plugin->getContents($smarty, $request), '<h2') === 2, 'Reenabled block not restored');
    check(strpos($plugin->displayAboveFooter($footerPage, $smarty), 'id="sponsorsBlockAboveFooter-' . $id . '"') !== false, 'Reenabled footer not restored');
    $request->vars = array('verb' => 'setVisibility', 'blockId' => 'indexers', 'visible' => '0');
    check($plugin->manage(array(), $request)->getStatus(), 'Could not disable original block');
    check(!$plugin->isBlockVisible(1) && $plugin->getEnabled(), 'Original block visibility affected plugin enablement');
    check(strpos($plugin->getContents($smarty, $request), 'Partners &lt;test&gt;') !== false, 'Disabling original hid other blocks');
    $request->vars['visible'] = '1';
    check($plugin->manage(array(), $request)->getStatus(), 'Could not restore original block');
    echo "PASS: visibility toggle persists per block, preserves data, hides/restores sidebar and footer, and checks POST/CSRF/ownership\n";
    check(strpos($twoFooters, 'id="sponsorsBlockAboveFooter-' . $id . '"') < strpos($twoFooters, 'id="sponsorsBlockAboveFooter-indexers"'), 'Footer ignored block order');
    $smarty->registerPlugin('function', 'blocks_test_sidebar', function ($params, $template) use ($plugin, $request) {
        return $plugin->getContents($template, $request);
    });
    $plugin->updateBlockSetting(1, 'visible', false, 'bool');
    $fullPage = 'string:<html><body><aside>{blocks_test_sidebar}</aside><div class="pkp_structure_footer_wrapper">Footer</div></body></html>';
    foreach (array('sidebar', 'footer', 'both') as $placement) {
        $placementForm = new SponsorsBlockSettingsForm($block, 1);
        $placementForm->initData();
        $placementForm->setData('placement', $placement);
        check($placementForm->validate(false) && $placementForm->execute(), 'Placement save failed');
        check($block->getBlockPlacement(1) === $placement, 'Placement was not persisted');
        $renderedPage = $smarty->fetch($fullPage);
        preg_match('/<aside>(.*?)<\/aside>/s', $renderedPage, $sidebar);
        check((strpos($sidebar[1], '<img ') !== false) === ($placement !== 'footer'), 'Wrong sidebar placement');
        check((strpos($renderedPage, 'id="sponsorsBlockAboveFooter-' . $id . '"') !== false) === ($placement !== 'sidebar'), 'Wrong footer placement');
        if ($placement === 'footer') {
            check(strpos($renderedPage, 'filter: grayscale(100%)') !== false, 'Footer-only grayscale missing');
        }
    }
    $plugin->updateBlockSetting(1, 'visible', true, 'bool');
    echo "PASS: sidebar/footer/both rendered through the full-page filter, public ordering and footer-only grayscale\n";
    $plugin->updateBlockSetting(1, 'logos', array(), 'object');
    check(strpos($plugin->getContents($smarty, $request), 'Partners &lt;test&gt;') !== false, 'Empty original block hid the new block');
    $plugin->updateBlockSetting(1, 'logos', $before[1]['logos'], 'object');
    $block->updateBlockSetting(1, 'logos', array(), 'object');
    check(substr_count($plugin->getContents($smarty, $request), '<h2') === 1, 'Empty block rendered');
    echo "PASS: multiple blocks rendered through Smarty, independent footer/grayscale, escaped title and hidden empty blocks\n";
} finally {
    foreach (glob($compileDirectory . '/*') as $file) {
        unlink($file);
    }
    rmdir($compileDirectory);
}

$defaultRow = new SponsorsBlocksGridRow($plugin);
$defaultRow->setId('indexers');
$defaultRowData = array('title' => 'Indexers');
$defaultRow->setData($defaultRowData);
$defaultRow->initialize($request);
$defaultActions = array_values($defaultRow->getActions());
check(count($defaultActions) === 1 && $defaultActions[0]->getId() === 'editBlock', 'Default block should only be editable');
check($defaultActions[0]->getToolTip() === 'plugins.block.sponsorsBlock.editBlock', 'Edit tooltip missing');
$customRow = new SponsorsBlocksGridRow($plugin);
$customRow->setId($id);
$customRowData = array('title' => 'Partners');
$customRow->setData($customRowData);
$customRow->initialize($request);
$customActions = array_values($customRow->getActions());
check(count($customActions) === 2 && $customActions[1]->getId() === 'deleteBlock', 'Custom block delete action missing');
check($customActions[1]->getToolTip() === 'plugins.block.sponsorsBlock.deleteBlock', 'Delete tooltip missing');
echo "PASS: edit/delete tooltips and protected default block action\n";

Config::$publicDir = sys_get_temp_dir() . '/sponsors-block-delete-' . bin2hex(random_bytes(6));
mkdir(Config::$publicDir . '/journals/1', 0777, true);
try {
    $firstFile = 'sponsorsBlock-delete-first.png';
    $secondFile = 'sponsorsBlock-delete-second.png';
    file_put_contents(Config::$publicDir . '/journals/1/' . $firstFile, 'first');
    file_put_contents(Config::$publicDir . '/journals/1/' . $secondFile, 'second');
    $block->updateBlockSetting(1, 'logos', array(
        'first' => array('name' => 'First', 'url' => '', 'file' => $firstFile),
        'second' => array('name' => 'Second', 'url' => '', 'file' => $secondFile),
    ), 'object');
    $request->vars = array('verb' => 'deleteBlock', 'blockId' => 'indexers');
    check(!$plugin->manage(array(), $request)->getStatus(), 'Default block deletion accepted');
    check(in_array('indexers', $plugin->getBlockIds(1), true), 'Default block was removed');
    $request->vars['blockId'] = $id;
    foreach (array('post', 'csrf') as $guard) {
        $request->$guard = false;
        check(!$plugin->manage(array(), $request)->getStatus(), 'Unprotected block deletion accepted');
        check(in_array($id, $plugin->getBlockIds(1), true), 'Rejected request removed block');
        $request->$guard = true;
    }
    $request->contextId = 2;
    check(!$plugin->manage(array(), $request)->getStatus(), 'Other journal block deletion accepted');
    $request->contextId = 1;
    check($plugin->manage(array(), $request)->getStatus(), 'Custom block deletion failed');
    check($plugin->getBlockIds(1) === array('indexers'), 'Deleted block remains registered or ordered');
    check(!is_file(Config::$publicDir . '/journals/1/' . $firstFile)
        && !is_file(Config::$publicDir . '/journals/1/' . $secondFile), 'Deleted block files remain public');
    foreach (array('title', 'logos', 'visible', 'showSidebar', 'showAboveFooter', 'grayscaleLogos') as $name) {
        check(!isset($settings[1]['logoBlock_' . $id . '_' . $name]), 'Deleted block setting remains: ' . $name);
    }
    check($settings[2] === $before[2], 'Block deletion changed another journal');
    echo "PASS: custom block deletion removes registration, order, settings and files with POST/CSRF/journal guards\n";
} finally {
    foreach (glob(Config::$publicDir . '/journals/1/*') as $file) {
        unlink($file);
    }
    rmdir(Config::$publicDir . '/journals/1');
    rmdir(Config::$publicDir . '/journals');
    rmdir(Config::$publicDir);
}
