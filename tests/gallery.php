<?php
/** Run: php plugins/blocks/sponsors/tests/gallery.php
 * Uses real OJS forms and file handling with in-memory settings/request doubles.
 */
require __DIR__ . '/grid.php';

$catalog = $plugin->getLogoCatalog();
check(!array_diff(array('boai', 'doaj', 'crossref', 'google-scholar', 'capes', 'cnpq', 'fapesp', 'scielo'), array_keys($catalog)), 'Initial catalog incomplete');
$catalogHashes = array();
foreach ($catalog as $id => $entry) {
	$path = dirname(__DIR__) . '/logos/' . $entry['file'];
	$catalogHashes[$id] = hash_file('sha256', $path);
	$size = getimagesizefromstring(SponsorsBlockImage::resize($path));
	check($size[0] === 150 && $size[2] === IMAGETYPE_PNG, 'Catalog image cannot be imported: ' . $id);
}

$plugin = new GridTestPlugin();
$plugin->pluginPath = dirname(__DIR__);
$plugin->settings = array(1 => array('title' => 'Keep title', 'logos' => array('existing' => $logo)), 2 => array('logos' => array('foreign' => $logo)));
$request->vars = array();
$request->contextId = 1;
$grid = new SponsorsBlockGridHandler($plugin);
$grid->initialize($request);
$actions = $grid->getActions();
check(isset($actions['chooseLogo'], $actions['addLogo']), 'Gallery and upload actions must coexist');
foreach (array(null, '', 'unknown', '../logos/capes.png', array('capes')) as $id) {
	$form = new SponsorsBlockLogoForm($plugin, 1, null, true);
	$form->setData('galleryId', $id);
	check(!$form->validate(false), 'Invalid catalog ID accepted');
}
foreach (array('post', 'csrf') as $guard) {
	$request->$guard = false;
	$form = new SponsorsBlockLogoForm($plugin, 1, null, true);
	$form->setData('galleryId', 'capes');
	check(!$form->validate(false), 'Unprotected gallery submission accepted');
	$request->$guard = true;
}
echo "PASS: importable catalog images, both actions, invalid IDs and POST/CSRF guards\n";

Config::$publicDir = sys_get_temp_dir() . '/sponsors-gallery-' . bin2hex(random_bytes(6));
mkdir(Config::$publicDir . '/journals/1', 0777, true);
try {
	foreach ($catalog as $catalogId => $entry) {
		$form = new SponsorsBlockLogoForm($plugin, 1, null, true);
		$request->vars = array('galleryId' => $catalogId, 'name' => 'Tampered', 'url' => 'javascript:alert(1)');
		$form->readInputData();
		check($form->validate(false) && $form->execute(), 'Catalog selection failed');
		$logos = $plugin->getLogos(1);
		$id = array_key_last($logos);
		$added = $logos[$id];
		check($added['name'] === $entry['name'] && $added['url'] === $entry['url'], 'Client overrode catalog metadata');
		$path = Config::$publicDir . '/journals/1/' . $added['file'];
		check(is_file($path) && getimagesize($path)[0] === 150, 'Journal copy missing or incorrectly sized');
		$edit = new SponsorsBlockLogoForm($plugin, 1, $id);
		$edit->setData('name', 'Edited');
		$edit->setData('url', 'https://example.org/journal');
		check($edit->validate(false) && $edit->execute(), 'Imported logo cannot be edited');
		check(array_keys($plugin->getLogos(1)) === array('existing', $id), 'Existing order changed');
		$request->vars = array('logoId' => $id);
		check($grid->deleteLogo(array(), $request)->getStatus() && !is_file($path), 'Imported copy not deleted');
		check(hash_file('sha256', dirname(__DIR__) . '/logos/' . $entry['file']) === $catalogHashes[$catalogId], 'Catalog original modified');
	}
	check($plugin->getLogos(1) === array('existing' => $logo), 'Existing logo changed');
	check($plugin->getSetting(1, 'title') === 'Keep title', 'Title changed');
	check($plugin->getLogos(2) === array('foreign' => $logo), 'Another journal changed');
	echo "PASS: gallery import, server metadata, journal copies, edit/delete, order and catalog isolation\n";
} finally {
	foreach (glob(Config::$publicDir . '/journals/1/*') as $file) { unlink($file); }
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
	$smarty->registerPlugin('function', $tag, function () { return ''; });
}
$smarty->assign(array('logoFormUrl' => '/manage?gridOp=addGalleryLogo', 'logoCatalog' => $catalog,
	'logoCatalogUrl' => '/plugins/blocks/sponsors/logos', 'galleryId' => 'capes', 'isError' => false));
try {
	$html = $smarty->fetch(dirname(__DIR__) . '/templates/logoGallery.tpl');
	check(substr_count($html, 'type="radio"') === count($catalog), 'Gallery did not render all logos');
	check(preg_match('/value="capes"\s+checked/', $html) === 1, 'Selection lost on redisplay');
	$catalog['capes']['name'] = '<script>bad</script>';
	$smarty->assign('logoCatalog', $catalog);
	check(strpos($smarty->fetch(dirname(__DIR__) . '/templates/logoGallery.tpl'), '<script>bad</script>') === false, 'Catalog label not escaped');
	echo "PASS: gallery Smarty rendering, selection retention and escaped labels\n";
} finally {
	foreach (glob($compileDirectory . '/*') as $file) { unlink($file); }
	rmdir($compileDirectory);
}
