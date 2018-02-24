<?php
//#section#[header]
// Module Declaration
$moduleID = 339;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Platform classes
use \API\Platform\importer;
use \API\Platform\engine;

// Increase module's loading depth
importer::import("ESS", "Protocol", "loaders::ModuleLoader");
use \ESS\Protocol\loaders\ModuleLoader;
ModuleLoader::incLoadingDepth();

// Import Initial Libraries
importer::import("UI", "Html", "DOM");
importer::import("UI", "Html", "HTML");
importer::import("DEV", "Profiler", "logger");

// Use
use \UI\Html\DOM;
use \UI\Html\HTML;
use \DEV\Profiler\logger;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_") && ModuleLoader::getLoadingDepth() === 1)
	DOM::initialize();

// Import Packages
importer::import("API", "Literals");
importer::import("API", "Model");
importer::import("SYS", "Resources");
importer::import("UI", "Forms");
importer::import("UI", "Interactive");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \SYS\Resources\settings\genericSettings;
use \API\Literals\moduleLiteral;
use \API\Model\modules\mGroup;
use \API\Model\modules\module;
use \UI\Modules\MContent;
use \UI\Forms\templates\simpleForm;
use \UI\Forms\formReport\formNotification;
use \UI\Interactive\forms\formAutoComplete;

// Create Module Page
$pageContent = new MContent($moduleID);
$actionFactory = $pageContent->getActionFactory();

// Initialize generic settings
$settings = new genericSettings();

if (engine::isPost())
{
	// Set values
	$pages = array();
	$pages[] = "page_nf";
	$pages[] = "page_uc";
	$pages[] = "page_ad";
	foreach ($pages as $pageName)
		$settings->set($pageName, $_POST[$pageName]);
	
	$succFormNtf = new formNotification();
	$succFormNtf->build($type = formNotification::SUCCESS, $header = TRUE, $timeout = TRUE, $disposable = TRUE);
	
	// Notification Message
	$errorMessage = $succFormNtf->getMessage("success", "success.save_success");
	$succFormNtf->append($errorMessage);
	return $succFormNtf->getReport(FALSE);
}

// Build the module content
$pageContent->build("", "errorPagesContainer", TRUE);

$formContainer = HTML::select(".errorPagesContainer .formContainer")->item(0);
$form = new simpleForm();
$pagesForm = $form->build()->engageModule($moduleID)->get();
DOM::append($formContainer, $pagesForm);


// Page Not Found
$title = moduleLiteral::get($moduleID, "hd_pageNotFound");
$hd = DOM::create("h3", $title, "", "hd");
$form->append($hd);

$mSelector = getModuleSelector($moduleID, $form, "page_nf", $module_id = $settings->get("PAGE_NF"));
$form->append($mSelector);

// Page Under Construction
$title = moduleLiteral::get($moduleID, "hd_pageUC");
$hd = DOM::create("h3", $title, "", "hd");
$form->append($hd);

$mSelector = getModuleSelector($moduleID, $form, "page_uc", $module_id = $settings->get("PAGE_UC"));
$form->append($mSelector);

// Page Access Denied
$title = moduleLiteral::get($moduleID, "hd_pageAccessDenied");
$hd = DOM::create("h3", $title, "", "hd");
$form->append($hd);

$mSelector = getModuleSelector($moduleID, $form, "page_ad", $module_id = $settings->get("PAGE_AD"));
$form->append($mSelector);

// Return output
return $pageContent->getReport();


function getModuleSelector($moduleID, $form, $inputName, $module_id = "")
{
	// Module selector container
	$mSelector = DOM::create("div", "", "", "mSelector");
	
	// Get module info
	$moduleInfo = module::info($module_id);
	
	// Get all module groups
	$mGroups = mGroup::getAllGroups();
	$mGroupResource = array();
	foreach ($mGroups as $mGroup)
	{
		$mGroupResource[$mGroup['id']] = $mGroup['description'];
		$moduleGroups_depths[$mGroup['id']] = $mGroup['depth'];
	}
	foreach ($moduleGroups_depths as $id => $depth)
	{
		$tabs = str_repeat(" - ", $depth);
		$mGroupResource[$id] = $tabs.$mGroupResource[$id];
	}
	
	$title = moduleLiteral::get($moduleID, "lbl_moduleGroup");
	$moduleGroupInput = $form->getResourceSelect($name = "mgrp".rand(), $multiple = FALSE, $class = "", $mGroupResource, $selectedValue = $moduleInfo['group_id']);
	$fRow = $form->buildRow($title, $moduleGroupInput, $required = TRUE, $notes = "");
	DOM::append($mSelector, $fRow);
	
	// Module
	
	// Get modules
	$modules = module::getAllModules($moduleInfo['group_id']);
	$modulesResource = array();
	foreach ($modules as $mdl)
		$modulesResource[$mdl['id']] = $mdl['title'];
	
	$title = moduleLiteral::get($moduleID, "lbl_module");
	$moduleInput = $form->getResourceSelect($name = $inputName, $multiple = FALSE, $class = "", $modulesResource, $selectedValue = $module_id);
	$fRow = $form->buildRow($title, $moduleInput, $required = TRUE, $notes = "");
	DOM::append($mSelector, $fRow);
	
	// Auto Complete
	$autoComplete = new formAutoComplete();
	$populate = array();
	$populate[] = $moduleInput->getAttribute("id");
	$autoComplete->engage($moduleGroupInput, "/ajax/modules/groupModules.php", $fill = array(), $hide = array(), $populate, $mode = "lenient");
	
	
	// Return module selector container
	return $mSelector;
}
//#section_end#
?>