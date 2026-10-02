<?php

/** Focused tests with real OJS grid/form classes and in-memory request/settings doubles.
 * Run from any directory: php plugins/blocks/sponsors/tests/grid.php
 * These tests do not replace a browser or database integration test.
 */
define('BASE_SYS_DIR', dirname(__DIR__, 4));
define('SESSION_DISABLE_INIT', true);
define('LOCALE_COMPONENT_PKP_GRID', 1);
define('LOCALE_COMPONENT_APP_COMMON', 2);
require BASE_SYS_DIR . '/lib/pkp/includes/functions.inc.php';
require BASE_SYS_DIR . '/lib/pkp/lib/vendor/autoload.php';
import('lib.pkp.classes.core.Registry');
import('lib.pkp.classes.core.PKPString');
import('lib.pkp.classes.plugins.HookRegistry');
import('lib.pkp.classes.core.PKPComponentRouter');
import('lib.pkp.classes.db.DAO');
require dirname(__DIR__) . '/SponsorsBlockPlugin.inc.php';
require dirname(__DIR__) . '/SponsorsBlockLogoForm.inc.php';
require dirname(__DIR__) . '/SponsorsBlockSettingsForm.inc.php';
require dirname(__DIR__) . '/controllers/grid/SponsorsBlockGridHandler.inc.php';

function __($key, $params = array(), $locale = null)
{
    return $key;
}
class AppLocale
{
    public static $locale = 'en_US';
    public static function getPrimaryLocale()
    {
        return 'en_US';
    }
    public static function getLocale()
    {
        return self::$locale;
    }
    public static function getSupportedFormLocales()
    {
        return array('en_US' => 'English', 'pt_BR' => 'Português');
    }
    public static function getAllLocales()
    {
        return self::getSupportedFormLocales();
    }
    public static function requireComponents(...$components)
    {
    }
}
class Config
{
    public static $publicDir;
    public static function getVar($section, $key)
    {
        return $key === 'public_files_dir' ? self::$publicDir : null;
    }
}
class Application
{
    public static $request;
    public static function get()
    {
        return new self();
    }
    public function getRequest()
    {
        return self::$request;
    }
}
class GridTestRouter extends PKPComponentRouter
{
    public function getRequestedComponent($request)
    {
        return 'grid.settings.plugins.SettingsPluginGridHandler';
    }
    public function url($request, $newContext = null, $component = null, $op = null, $path = null, $params = null, $anchor = null, $escape = false)
    {
        return '/manage?' . http_build_query(array_merge(array('op' => $op), $params ?: array()));
    }
}
class GridTestRequest
{
    public $vars = array();
    public $post = true;
    public $csrf = true;
    public $contextId = 1;
    public function getRouter()
    {
        return new GridTestRouter();
    }
    public function getContext()
    {
        return new class ($this->contextId) {
            private $id;
            public function __construct($id)
            {
                $this->id = $id;
            }
            public function getId()
            {
                return $this->id;
            }
        };
    }
    public function getUserVar($name)
    {
        return isset($this->vars[$name]) ? $this->vars[$name] : null;
    }
    public function isPost()
    {
        return $this->post;
    }
    public function checkCSRF()
    {
        return $this->csrf;
    }
    public function getSession()
    {
        return new class () {
            public function getCSRFToken()
            {
                return 'test-token';
            }
        };
    }
}
class GridTestPlugin extends SponsorsBlockPlugin
{
    public $settings = array();
    public function __construct()
    {
        // Explicitly scope this fixture; the production plugin has no default block.
        $id = new ReflectionProperty(SponsorsBlockPlugin::class, 'blockId');
        $id->setAccessible(true);
        $id->setValue($this, 'existing-block');
    }
    public function getSetting($contextId, $name)
    {
        return isset($this->settings[$contextId][$name]) ? $this->settings[$contextId][$name] : null;
    }
    public function updateSetting($contextId, $name, $value, $type = null)
    {
        $this->settings[$contextId][$name] = $value;
    }
    public function deleteBlockSetting($contextId, $name)
    {
        unset($this->settings[$contextId]['logoBlock_' . $this->getBlockId() . '_' . $name]);
        return true;
    }
    public function getEnabled($contextId = null)
    {
        return true;
    }
    public function getTemplateResource($template = null, $inCore = false)
    {
        return dirname(__DIR__) . '/templates/' . $template;
    }
    public function getPublicUrl($request, $contextId)
    {
        return '/public/journals/' . $contextId;
    }
}
function check($condition, $message)
{
    if (!$condition) {
        throw new RuntimeException($message);
    }
}
$request = new GridTestRequest();
Application::$request = $request;
$plugin = new GridTestPlugin();
$plugin->pluginPath = dirname(__DIR__);
$logo = array('name' => 'Original', 'url' => '', 'file' => 'sponsorsBlock-original.png');
$plugin->settings = array(1 => array('logoBlocks' => array('existing-block'), 'logoBlock_existing-block_title' => 'Index to:', 'logoBlock_existing-block_logos' => array('first' => $logo)),
    2 => array('logoBlock_existing-block_logos' => array('foreign' => $logo)));
$grid = new SponsorsBlockGridHandler($plugin);
$grid->initialize($request);
$grid->setUrls($request);
check(count($grid->getColumns()) === 3, 'Expected name, preview and link columns');
foreach ($grid->getUrls() as $url) {
    check(strpos($url, 'op=manage') !== false && strpos($url, 'category=blocks') !== false && (strpos($url, 'gridOp=fetch') !== false || strpos($url, 'gridOp=saveSequence') !== false), 'Refresh bypasses plugin endpoint');
}
$row = $grid->getRowInstance();
$row->setId('first');
$row->setData($logo);
$row->initialize($request);
check(count($row->getActions()) === 2, 'Expected edit and delete actions');
echo "PASS: native grid, row actions and authorized refresh URLs\n";

$form = new SponsorsBlockLogoForm($plugin, 1, 'first');
$form->setData('name', 'Updated');
$form->setData('url', 'https://example.org');
check($form->validate(false) && $form->execute(), 'Metadata edit failed');
check($plugin->settings[1]['logoBlock_existing-block_logos']['first']['file'] === $logo['file'], 'Metadata edit replaced image');
check($plugin->settings[1]['logoBlock_existing-block_title'] === 'Index to:', 'Logo edit changed title');
check($plugin->settings[2]['logoBlock_existing-block_logos']['foreign'] === $logo, 'Logo edit changed another journal');
echo "PASS: edit keeps image, title and other journal data\n";

foreach (array('javascript:alert(1)', 'data:text/html,bad', 'ftp://example.org') as $url) {
    $form = new SponsorsBlockLogoForm($plugin, 1, 'first');
    $form->setData('name', 'Name');
    $form->setData('url', $url);
    check(!$form->validate(false), 'Unsafe URL accepted');
}
$form = new SponsorsBlockLogoForm($plugin, 1);
$form->setData('name', 'New logo');
check(!$form->validate(false), 'New logo without image accepted');
$form = new SponsorsBlockLogoForm($plugin, 1, 'foreign');
$form->setData('name', 'Invalid');
check(!$form->validate(false), 'Other journal logo accepted');
foreach (array('post', 'csrf') as $guard) {
    $request->$guard = false;
    $form = new SponsorsBlockLogoForm($plugin, 1, 'first');
    $form->setData('name', 'Name');
    check(!$form->validate(false), 'Missing request protection');
    $request->vars = array('logoId' => 'first');
    check(!$grid->deleteLogo(array(), $request)->getStatus(), 'Unprotected deletion accepted');
    $request->$guard = true;
}
echo "PASS: URL, image, journal ownership, POST and CSRF validation\n";

$titleForm = new SponsorsBlockSettingsForm($plugin, 1);
$titleForm->setData('title', array('en_US' => 'Index to:', 'pt_BR' => 'Indexado em:'));
$before = $plugin->getLogos(1);
check($titleForm->validate(false) && $titleForm->execute(), 'Title save failed');
check($before === $plugin->getLogos(1), 'Title save overwrote grid data');
$request->vars = array('verb' => 'grid', 'blockId' => 'existing-block', 'gridOp' => 'execute');
check(!$plugin->manage(array(), $request)->getStatus(), 'Unlisted grid operation accepted');
$request->vars = array('logoId' => 'foreign');
check(!$grid->deleteLogo(array(), $request)->getStatus(), 'Other journal deletion accepted');
echo "PASS: independent title save and grid operation allowlist\n";

// Exercise file persistence with prepared PNG data. HTTP upload is not simulated.
Config::$publicDir = sys_get_temp_dir() . '/sponsors-grid-' . bin2hex(random_bytes(6));
mkdir(Config::$publicDir . '/journals/1', 0777, true);
try {
    $directory = Config::$publicDir . '/journals/1/';
    file_put_contents($directory . $logo['file'], 'old image');
    $image = imagecreatetruecolor(150, 60);
    ob_start();
    imagepng($image);
    $png = ob_get_clean();
    imagedestroy($image);
    $form = new SponsorsBlockLogoForm($plugin, 1, 'first');
    $form->setData('name', 'Replaced');
    $form->setData('url', '');
    $property = new ReflectionProperty(SponsorsBlockLogoForm::class, 'image');
    $property->setAccessible(true);
    $property->setValue($form, $png);
    check($form->validate(false) && $form->execute(), 'Replacement failed');
    $replacement = $plugin->getLogos(1)['first']['file'];
    check(is_file($directory . $replacement) && !is_file($directory . $logo['file']), 'Replacement did not clean up old file');
    $form = new SponsorsBlockLogoForm($plugin, 1);
    $form->setData('name', 'Added');
    $form->setData('url', '');
    $property->setValue($form, $png);
    check($form->execute(), 'Prepared new image save failed');
    check(count($plugin->getLogos(1)) === 2, 'Adding logo overwrote an existing entry');
    $request->vars = array('logoId' => 'first');
    check($grid->deleteLogo(array(), $request)->getStatus(), 'Deletion failed');
    check(count($plugin->getLogos(1)) === 1 && !is_file($directory . $replacement), 'Deletion did not remove the selected record and file');
    check($plugin->settings[2]['logoBlock_existing-block_logos']['foreign'] === $logo, 'Deletion affected another journal');
    echo "PASS: add, replacement and confirmed deletion file lifecycle with prepared image data\n";
} finally {
    foreach (glob(Config::$publicDir . '/journals/1/*') as $file) {
        unlink($file);
    }
    rmdir(Config::$publicDir . '/journals/1');
    rmdir(Config::$publicDir . '/journals');
    rmdir(Config::$publicDir);
}

$defaults = array();
check($plugin->getBlockTitles(99) === $defaults, 'Default titles incorrect');
$plugin->settings[99]['logoBlock_existing-block_title'] = array('pt_BR' => 'Personalizado');
check($plugin->getBlockTitles(99) === array_replace($defaults, array('pt_BR' => 'Personalizado')), 'Missing translations or custom title lost');
$plugin->settings[1]['logoBlock_existing-block_title'] = 'Existing title';
$titleForm = new SponsorsBlockSettingsForm($plugin, 1);
$titleForm->initData();
check($titleForm->getData('title') === array_replace($defaults, array('en_US' => 'Existing title')), 'Legacy title was lost');
$titleForm->setData('title', array('en_US' => ' Index to: ', 'pt_BR' => ' Indexado em: '));
check($titleForm->validate(false) && $titleForm->execute(), 'Multilingual title save failed');
check($plugin->getBlockTitle(1) === 'Index to:', 'English title incorrect');
AppLocale::$locale = 'pt_BR';
check($plugin->getBlockTitle(1) === 'Indexado em:', 'Portuguese title incorrect');
AppLocale::$locale = 'fr_CA';
check($plugin->getBlockTitle(1) === 'Index to:', 'French locale did not fall back to primary title');
AppLocale::$locale = 'es_ES';
check($plugin->getBlockTitle(1) === 'Index to:', 'Spanish locale did not fall back to primary title');
AppLocale::$locale = 'de_DE';
check($plugin->getBlockTitle(1) === 'Index to:', 'Missing translation did not fall back to primary locale');
foreach (array(array('en_US' => '', 'pt_BR' => 'Título'), array('en_US' => 'Valid', 'pt_BR' => str_repeat('x', 201)), array('en_US' => array('invalid')), 'not-localized') as $titles) {
    $titleForm = new SponsorsBlockSettingsForm($plugin, 1);
    $titleForm->setData('title', $titles);
    check(!$titleForm->validate(false), 'Invalid localized title accepted');
}
$titleForm = new SponsorsBlockSettingsForm($plugin, 1);
$titleForm->setData('title', array('en_US' => 'Primary', 'pt_BR' => ''));
check($titleForm->validate(false) && $titleForm->execute(), 'Optional empty translation rejected');
AppLocale::$locale = 'pt_BR';
check($plugin->getBlockTitle(1) === 'Primary', 'Empty translation did not fall back');
AppLocale::$locale = 'en_US';
echo "PASS: multilingual titles, legacy data, required primary locale and language fallback\n";

// Render a full page through the bundled Smarty engine. The block registers its
// output filter during the sidebar fetch, as in the OJS frontend footer.
$smarty = new Smarty();
$compileDirectory = sys_get_temp_dir() . '/sponsors-footer-' . bin2hex(random_bytes(6));
mkdir($compileDirectory);
$smarty->setCompileDir($compileDirectory);
$smarty->registerPlugin('function', 'sponsors_test_sidebar', function ($params, $template) use ($plugin, $request) {
    return $plugin->getContents($template, $request);
});
$page = 'string:<html><body><aside>{sponsors_test_sidebar}</aside><div class="pkp_structure_footer_wrapper" role="contentinfo">Footer</div></body></html>';
try {
    $form = new SponsorsBlockSettingsForm($plugin, 1);
    $form->initData();
    check($form->getData('placement') === 'sidebar', 'Footer option should default to off');
    $form->setData('placement', 'both');
    check($form->validate(false) && $form->execute(), 'Could not save footer option');
    $html = $smarty->fetch($page);
    check(substr_count($html, 'id="sponsorsBlockAboveFooter"') === 1, 'Footer strip missing or duplicated');
    check(strpos($html, 'id="sponsorsBlockAboveFooter"') < strpos($html, 'class="pkp_structure_footer_wrapper"'), 'Strip is not before footer');
    check(preg_match('/<aside>.*?<img /s', $html) === 1, 'Sidebar logos were removed');
    check($plugin->displayAboveFooter($html, $smarty) === $html, 'Repeated filtering duplicated strip');
    $fragment = '<div class="pkp_structure_footer_wrapper">Fragment</div>';
    check($plugin->displayAboveFooter($fragment, $smarty) === $fragment, 'AJAX fragment was modified');
    $customPage = '<html><body><footer>Custom footer</footer></body></html>';
    check($plugin->displayAboveFooter($customPage, $smarty) === $customPage, 'Unsupported footer was modified');
    $request->contextId = 2;
    check(strpos($smarty->fetch($page), 'id="sponsorsBlockAboveFooter"') === false, 'Option leaked into another journal');
    $request->contextId = 1;
    $form->setData('placement', 'sidebar');
    check($form->validate(false) && $form->execute(), 'Could not disable footer option');
    $html = $smarty->fetch($page);
    check(strpos($html, 'id="sponsorsBlockAboveFooter"') === false && strpos($html, '<img ') !== false, 'Disabling option affected sidebar');
    echo "PASS: optional footer strip rendered by Smarty, correct position, sidebar retained and journal isolation\n";
} finally {
    foreach (glob($compileDirectory . '/*') as $file) {
        unlink($file);
    }
    rmdir($compileDirectory);
}

// The enabled-plugin loader resolves the directory from version.xml's product.
import('lib.pkp.classes.plugins.PluginRegistry');
$manifest = simplexml_load_file(dirname(__DIR__) . '/version.xml');
check((string) $manifest->application === basename(dirname(__DIR__)), 'Manifest product does not match plugin directory');
$loadedPlugin = PluginRegistry::_instantiatePlugin(
    'blocks',
    dirname(__DIR__, 2),
    (string) $manifest->application,
    (string) $manifest->class
);
check($loadedPlugin instanceof SponsorsBlockPlugin, 'OJS cannot instantiate the plugin from its registered product');
check($loadedPlugin->getName() === 'sponsorsblockplugin', 'Renaming the folder changed the settings key');
echo "PASS: manifest directory, native plugin loader and existing settings identity\n";

$orderPlugin = new GridTestPlugin();
$orderPlugin->settings = array(1 => array('logoBlock_existing-block_logos' => array('a' => $logo, 'b' => array_merge($logo, array('name' => 'Second')), 'c' => array_merge($logo, array('name' => 'Third')))), 2 => array('logoBlock_existing-block_logos' => array('foreign' => $logo)));
$orderGrid = new SponsorsBlockGridHandler($orderPlugin);
$request->vars = array('data' => '["c","a","b"]');
$original = $orderPlugin->getLogos(1);
check($orderGrid->saveSequence(array(), $request)->getStatus(), 'Reordering failed');
check(array_keys($orderPlugin->getLogos(1)) === array('c', 'a', 'b'), 'Sequence was not saved');
foreach ($original as $id => $entry) {
    check($orderPlugin->getLogos(1)[$id] === $entry, 'Reordering changed logo metadata');
}
check($orderPlugin->getLogos(2) === array('foreign' => $logo), 'Reordering changed another journal');
$saved = $orderPlugin->getLogos(1);
foreach (array('invalid', '{}', '["a","a","b"]', '["foreign","a","b"]', '["a","b"]', '["a","b","c","new"]', '[[],"a","b"]') as $input) {
    $request->vars = array('data' => $input);
    check(!$orderGrid->saveSequence(array(), $request)->getStatus(), 'Invalid or stale sequence accepted');
    check($orderPlugin->getLogos(1) === $saved, 'Rejected request changed the sequence');
}
$request->vars = array('data' => '["a","b","c"]');
foreach (array('post', 'csrf') as $guard) {
    $request->$guard = false;
    check(!$orderGrid->saveSequence(array(), $request)->getStatus(), 'Unprotected ordering accepted');
    check($orderPlugin->getLogos(1) === $saved, 'Unprotected request changed the sequence');
    $request->$guard = true;
}
echo "PASS: automatic sequence persistence, metadata preservation, stale/foreign IDs and POST/CSRF\n";

// Exercise optional acronym round trips with a legacy logo lacking the field.
$acronymPlugin = new GridTestPlugin();
$acronymPlugin->settings = array(1 => array('logoBlock_existing-block_logos' => array('legacy' => $logo)));
$form = new SponsorsBlockLogoForm($acronymPlugin, 1, 'legacy');
$form->initData();
check($form->getData('acronym') === '', 'Legacy acronym must default to empty');
foreach (array('  EX  ' => 'EX', '' => '', '0' => '0') as $input => $expected) {
    $request->vars = array('name' => 'Example name', 'acronym' => (string) $input, 'url' => '');
    $form->readInputData();
    check($form->validate(false) && $form->execute(), 'Acronym edit failed');
    $form->initData();
    check($form->getData('acronym') === $expected, 'Acronym was not trimmed, saved or cleared');
}
$form->setData('acronym', str_repeat('á', 30));
check($form->validate(false), 'Acronym with 30 characters rejected');
$form->setData('acronym', str_repeat('á', 31));
check(!$form->validate(false), 'Oversized acronym accepted');
echo "PASS: legacy logos, optional acronym input, persistence, clearing and length limit\n";

$smarty = new Smarty();
$compileDirectory = sys_get_temp_dir() . '/sponsors-name-' . bin2hex(random_bytes(6));
mkdir($compileDirectory);
$smarty->setCompileDir($compileDirectory);
try {
    $provider = new SponsorsBlockGridCellProvider();
    foreach (array(
        array('name' => 'Full name', 'acronym' => 'ABC'),
        array('name' => 'Full name', 'acronym' => ''),
        array('name' => 'Legacy name'),
        array('name' => '<script>name</script>', 'acronym' => '<script>sigla</script>')
    ) as $entry) {
        $row->setData($entry);
        $smarty->assign($provider->getTemplateVarsFromRowColumn($row, $grid->getColumn('name')));
        $html = $smarty->fetch(dirname(__DIR__) . '/templates/logoNameCell.tpl');
        $bold = isset($entry['acronym']) && $entry['acronym'] !== '' ? $entry['acronym'] : $entry['name'];
        check(strpos($html, '<strong>' . htmlspecialchars($bold, ENT_QUOTES, 'UTF-8') . '</strong>') !== false, 'Wrong bold label');
        check(strpos($html, '<script>') === false, 'Name or acronym is not escaped');
        if ($bold !== $entry['name']) {
            check(strpos($html, '<strong>' . $entry['name'] . '</strong>') === false, 'Expanded name must not be bold');
        }
    }
    echo "PASS: Smarty renders bold acronyms or full names, including legacy and escaped labels\n";
} finally {
    foreach (glob($compileDirectory . '/*') as $file) {
        unlink($file);
    }
    rmdir($compileDirectory);
}
