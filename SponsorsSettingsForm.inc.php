<?php

/**
 * @file SponsorsSettingsForm.inc.php
 *
 * @class SponsorsSettingsForm
 * @ingroup plugins_citationFormats_abnt
 *
 * @brief Form for journal managers to modify ABNT Citation plugin settings
 */

import('lib.pkp.classes.form.Form');

class SponsorsSettingsForm extends Form {

	/** @var $journalId int */
	var $journalId;

	/** @var $plugin object */
	var $plugin;

	/**
	 * Constructor
	 * @param $plugin object
	 * @param $journalId int
	 */
	function SponsorsSettingsForm(&$plugin, $journalId) {
		$this->journalId = $journalId;
		$this->plugin =& $plugin;

		parent::Form($plugin->getTemplatePath() . 'settingsForm.tpl');
	}

	/**
	 * Initialize form data.
	 */
	function initData() {
		$journalId = $this->journalId;
		$plugin =& $this->plugin;

		$this->_data = array(
 			'sponsors' => $plugin->getSetting($journalId, "sponsors"),
 			'link' => ""
		);
	}

	function getFieldNames() {
		return array("sponsors", "link");
	}

	/**
	 * Assign form data to user-submitted data.
	 */
	function readInputData() {
 		$this->readUserVars(array('sponsors', 'link'));
	}


	/**
	 * Display the form.
	 */
	function display() {
        $fileManager = new PublicFileManager();
		$journalId =& Request::getJournal()->getId();

		$templateMgr =& TemplateManager::getManager();
		$templateMgr->addJavaScript('lib/pkp/js/jquery.tablednd_0_5.js');
		$templateMgr->assign('sponsorsDir', Request::getBasePath() . '/' . $fileManager->getJournalFilesPath($journalId) . '/sponsors'); 

		parent::display();
	}

	/**
	 * Save settings.
	 */
	function execute() {
		$journalId =& Request::getJournal()->getId();
		$plugin =& $this->plugin;

		$value = $this->getData('sponsors');
		// TODO: validar valores do array
		if (is_array($value)) {
	      	$this->plugin->updateSetting($journalId, 'sponsors', $value, 'object');
		}
	}

	function upload() {
		import('classes.file.PublicFileManager');
        $fileManager = new PublicFileManager();
        $site =& Request::getSite();  
   		$journalId =& Request::getJournal()->getId();
        if ($fileManager->uploadedFileExists('logo')) {
            $type = $fileManager->getUploadedFileType('logo');
            $extension = $fileManager->getImageExtension($type);
            if (!$extension) {
            	return false;
            }

            $fileName = $fileManager->getUploadedFileName('logo');

            if($fileManager->uploadJournalFile($journalId, "logo", "sponsors/$fileName")) {
               	$value = $this->plugin->getSetting($journalId, "sponsors");
               	if (!$this->searchArray($fileName, $value, "logo")) {
               		$value[] = array("logo" => $fileName, "link" => $this->getData("link"));
               	}
               	$this->plugin->updateSetting($journalId, 'sponsors', $value, 'object');
            }
            return true;
        }
        return false;
    }

    private function searchArray($needle, $haystack, $column) {
    	foreach ($haystack as $value) {
    		if ($value[$column] == $needle)
    			return true;
    	}
    	return false;
    }
}

?>
