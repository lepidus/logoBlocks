<?php
/** Focused tests with real OJS grid/form classes and in-memory request/settings doubles.
 * Run from any directory: php plugins/blocks/sponsorsBlock/tests/grid.php
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

function __($key, $params = array(), $locale = null) { return $key; }
class AppLocale {
    static $locale = 'en_US';
    static function getPrimaryLocale() { return 'en_US'; }
    static function getLocale() { return self::$locale; }
    static function getSupportedFormLocales() { return array('en_US' => 'English', 'pt_BR' => 'Português'); }
    static function getAllLocales() { return self::getSupportedFormLocales(); }
    static function requireComponents(...$components) {}
}
class Config {
    static $publicDir;
    static function getVar($section, $key) { return $key === 'public_files_dir' ? self::$publicDir : null; }
}
class Application {
    static $request;
    static function get() { return new self(); }
    function getRequest() { return self::$request; }
}
class GridTestRouter extends PKPComponentRouter {
    function getRequestedComponent($request) { return 'grid.settings.plugins.SettingsPluginGridHandler'; }
    function url($request, $newContext = null, $component = null, $op = null, $path = null, $params = null, $anchor = null, $escape = false) {
        return '/manage?' . http_build_query(array_merge(array('op' => $op), $params ?: array()));
    }
}
class GridTestRequest {
    public $vars = array();
    public $post = true;
    public $csrf = true;
    public $contextId = 1;
    function getRouter() { return new GridTestRouter(); }
    function getContext() { return new class($this->contextId) {
        private $id;
        function __construct($id) { $this->id = $id; }
        function getId() { return $this->id; }
    }; }
    function getUserVar($name) { return isset($this->vars[$name]) ? $this->vars[$name] : null; }
    function isPost() { return $this->post; }
    function checkCSRF() { return $this->csrf; }
    function getSession() { return new class { function getCSRFToken() { return 'test-token'; } }; }
}
class GridTestPlugin extends SponsorsBlockPlugin {
    public $settings = array();
    function getSetting($contextId, $name) { return isset($this->settings[$contextId][$name]) ? $this->settings[$contextId][$name] : null; }
    function updateSetting($contextId, $name, $value, $type = null) { $this->settings[$contextId][$name] = $value; }
    function getEnabled($contextId = null) { return true; }
    function getTemplateResource($template = null, $inCore = false) { return dirname(__DIR__) . '/templates/' . $template; }
    function getPublicUrl($request, $contextId) { return '/public/journals/' . $contextId; }
}
function check($condition, $message) {
    if (!$condition) { throw new RuntimeException($message); }
}
$request = new GridTestRequest();
Application::$request = $request;
$plugin = new GridTestPlugin();
$plugin->pluginPath = dirname(__DIR__);
$logo = array('name' => 'Original', 'url' => '', 'file' => 'sponsorsBlock-original.png');
$plugin->settings = array(1 => array('title' => 'Index to:', 'logos' => array('first' => $logo)),
    2 => array('logos' => array('foreign' => $logo)));
$grid = new SponsorsBlockGridHandler($plugin);
$grid->initialize($request);
$grid->setUrls($request);
check(count($grid->getColumns()) === 3, 'Expected name, preview and link columns');
foreach ($grid->getUrls() as $url) {
    check(strpos($url, 'op=manage') !== false && strpos($url, 'category=blocks') !== false && strpos($url, 'gridOp=fetch') !== false, 'Refresh bypasses plugin endpoint');
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
check($plugin->settings[1]['logos']['first']['file'] === $logo['file'], 'Metadata edit replaced image');
check($plugin->settings[1]['title'] === 'Index to:', 'Logo edit changed title');
check($plugin->settings[2]['logos']['foreign'] === $logo, 'Logo edit changed another journal');
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
$request->vars = array('verb' => 'grid', 'gridOp' => 'execute');
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
    check($plugin->settings[2]['logos']['foreign'] === $logo, 'Deletion affected another journal');
    echo "PASS: add, replacement and confirmed deletion file lifecycle with prepared image data\n";
} finally {
    foreach (glob(Config::$publicDir . '/journals/1/*') as $file) { unlink($file); }
    rmdir(Config::$publicDir . '/journals/1');
    rmdir(Config::$publicDir . '/journals');
    rmdir(Config::$publicDir);
}

$plugin->settings[1]['title'] = 'Existing title';
$titleForm = new SponsorsBlockSettingsForm($plugin, 1);
$titleForm->initData();
check($titleForm->getData('title') === array('en_US' => 'Existing title'), 'Legacy title was lost');
$titleForm->setData('title', array('en_US' => ' Index to: ', 'pt_BR' => ' Indexado em: '));
check($titleForm->validate(false) && $titleForm->execute(), 'Multilingual title save failed');
check($plugin->getBlockTitle(1) === 'Index to:', 'English title incorrect');
AppLocale::$locale = 'pt_BR';
check($plugin->getBlockTitle(1) === 'Indexado em:', 'Portuguese title incorrect');
AppLocale::$locale = 'fr_CA';
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
    check($form->getData('showAboveFooter') === false, 'Footer option should default to off');
    $form->setData('showAboveFooter', '1');
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
    $form->setData('showAboveFooter', null);
    check($form->validate(false) && $form->execute(), 'Could not disable footer option');
    $html = $smarty->fetch($page);
    check(strpos($html, 'id="sponsorsBlockAboveFooter"') === false && strpos($html, '<img ') !== false, 'Disabling option affected sidebar');
    echo "PASS: optional footer strip rendered by Smarty, correct position, sidebar retained and journal isolation\n";
} finally {
    foreach (glob($compileDirectory . '/*') as $file) { unlink($file); }
    rmdir($compileDirectory);
}
