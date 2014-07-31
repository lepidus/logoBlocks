<?php

import('plugins.BlockPlugin');

class SponsorsBlockPlugin extends BlockPlugin {

	function register($category, $path) {
		$success = parent::register($category, $path);
		if ($success && $this->getEnabled()) {
			HookRegistry::register('Templates::Manager::Index::ManagementPages', array($this, 'addManagementLink'));
		}

		return $success;
	}

	function addManagementLink($hookName, $params) {
		$smarty =& $params[1];
		$output =& $params[2];

		$output .= '<li>&#187; <a href="' . $this->smartyPluginUrl(array('op'=>'plugin', 'path'=>"settings"), $smarty) . '" >' . TemplateManager::smartyTranslate(array('key'=>'plugins.block.sponsors.manager.settingsLink'), $smarty) . '</a></li>';

		return false;
	}


	/**
	 * Install default settings on journal creation.
	 * @return string
	 */
	function getContextSpecificPluginSettingsFile() {
		return $this->getPluginPath() . '/settings.xml';
	}

	/**
	 * Get the display name of this plugin.
	 * @return String
	 */
	function getDisplayName() {
		return __('plugins.block.sponsors.displayName');
	}

	/**
	 * Get a description of the plugin.
	 */
	function getDescription() {
		return __('plugins.block.sponsors.description');
	}

	/**
	 * Display verbs for the management interface.
	 * @return array
	 */
	function getManagementVerbs() {
		return array(
			array(
				'settings',
				__('plugins.block.sponsors.settings')
			)
		);
	}

	/**
	 * Execute a management verb on this plugin
	 * @param $verb string
	 * @param $args array
	 * @param $message string If a message is returned from this by-ref
	 *  argument then it will be displayed as a notification if (and only
	 *  if) the method returns false.
	 * @return boolean will redirect to the plugin category page if false,
	 *  otherwise will remain on the same page
	 */
	function manage($verb, $args, &$message) {
		switch ($verb) {
			case 'settings':
				$templateMgr =& TemplateManager::getManager();
				$templateMgr->register_function('plugin_url', array(&$this, 'smartyPluginUrl'));
				$journal =& Request::getJournal();

				$this->import('SponsorsSettingsForm');
				$form = new SponsorsSettingsForm($this, $journal->getId());
				if (Request::getUserVar('uploadLogo')) {
					$form->readInputData();
					if (!$form->upload()) {
						$form->addError('logo', __('plugins.block.sponsors.settings.logoImageInvalid'));
					}
					$form->initData();
					$form->display();
				} else if (Request::getUserVar('saveOrder')) {
					$form->readInputData();
					if ($form->validate()) {
						$form->execute();

						$form->initData();
						$form->display();
						return false;
					}
				} else if (Request::getUserVar('createGroup')) {
					$form->readInputData();
					if ($form->validate()) {
						$form->createGroup();
					}
					$form->initData();
					$form->display();
				} else {
					$form->initData();
					$form->display();
				}
				return true;
			case 'remover':
				import('classes.file.PublicFileManager');
		        $fileManager = new PublicFileManager();
				$arr = Request::getQueryArray();// sponsors={$i}
				$journal =& Request::getJournal();
				$journalId =& $journal->getId();
				$sponsor = intval($arr["sponsors"]);

				$value = $this->getSetting($journalId, "sponsors");

				if (array_key_exists('logo', $value[$sponsor])) {
					$fileManager->removeJournalFile($journalId, "sponsors/".$value[$sponsor]["logo"]);
				}
				unset($value[$sponsor]);
				$this->updateSetting($journalId, 'sponsors', $value, 'object');

				Request::redirect(null, 'manager', 'plugin', array($this->getCategory(), $this->getName(), "settings"));
				return false;
			default:
				// Unknown management verb, delegate to parent
				return parent::manage($verb, $args, $message);
		}
	}

	/**
	 * Extend the {url ...} smarty to support this plugin.
	 */
	// function smartyPluginUrl($params, &$smarty) {
	// 	$path = array($this->getCategory(), $this->getName());
	// 	if (is_array($params['path'])) {
	// 		$params['path'] = array_merge($path, $params['path']);
	// 	} elseif (!empty($params['path'])) {
	// 		$params['path'] = array_merge($path, array($params['path']));
	// 	} else {
	// 		$params['path'] = $path;
	// 	}

	// 	return $smarty->smartyUrl($params, $smarty);
	// }

	/**
	* Get the contents for this block.
	* @param $templateMgr object
	* @return array
	**/
	function getContents(&$templateMgr) {
		$fileManager = new PublicFileManager();
		$journal =& Request::getJournal();
		if ($journal){
			$journalId =& $journal->getId();
			$sponsors = $this->getSetting($journalId, "sponsors");
			if ($sponsors){
				$path = Request::getBaseUrl() . '/' . $this->getPluginPath() . '/sponsors.css';
				$templateMgr->addStyleSheet($path);

				$templateMgr->assign_by_ref('sponsors', $sponsors);
				$templateMgr->assign('sponsorsDir', Request::getBasePath() . '/' . $fileManager->getJournalFilesPath($journalId) . '/sponsors'); 
				return parent::getContents($templateMgr);
			}
		}
	}
}

?>
