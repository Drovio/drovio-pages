<?php
//#section#[header]
// Module Declaration
$moduleID = 160;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Require Importer
use \API\Platform\importer;

// Import Initial Libraries
importer::import("UI", "Html", "DOM");

// New
use \UI\Html\DOM;

// Code Variables
$_ASCOP = $GLOBALS['_ASCOP'];

// If Async Request Pre-Loader exists, Initialize DOM
if (defined("_MDL_PRELOADER_"))
	DOM::initialize();

// Import Packages
importer::import("API", "Resources");
importer::import("UI", "Html");
//#section_end#
//#section#[code]
use \API\Resources\literals\moduleLiteral;
use \UI\Html\HTMLContent;
use \UI\Presentation\togglers\toggler;

// Create Module Page
$content = new HTMLContent();

// Build the content
$content->build("myGeneralSettings", "generalSettings");

// Get a toggler
$toggler = new toggler();

// Get Settings Toggler Function
function getSettingsToggler($toggler, $title, $moduleID, $action, $containerID = "")
{
	// Toggler Header
	$header = DOM::create("div", "", "", "settingsItem");
	DOM::append($header, $title);
	
	// Toggler Body
	$body = HTMLContent::getModuleContainer($moduleID, $action, array(), FALSE, $containerID);
	DOM::appendAttr($body, "class", "settingsContent");
	
	// Build Toggler Item
	$togglerItem = $toggler->build("", $header, $body, $open = FALSE)->get();
	DOM::appendAttr($togglerItem, "class", "settingsGroup");
	
	// Return item
	return $togglerItem;
}

// Personal Info Toggler
$title = moduleLiteral::get($moduleID, "lbl_personalInfo");
$personalInfoToggler = getSettingsToggler($toggler, $title, $moduleID, "personalInfo", "personalSettings");
$content->append($personalInfoToggler);

// Username Manager
$title = moduleLiteral::get($moduleID, "lbl_usernameManager");
$usernameManagerToggler = getSettingsToggler($toggler, $title, $moduleID, "usernameManager");
$content->append($usernameManagerToggler);

// Email Manager Toggler
$title = moduleLiteral::get($moduleID, "lbl_emailManager");
$emailManagerToggler = getSettingsToggler($toggler, $title, $moduleID, "emailManager");
$content->append($emailManagerToggler);

// Language
$title = moduleLiteral::get($moduleID, "lbl_languageSelector");
$languageSelectorToggler = getSettingsToggler($toggler, $title, $moduleID, "languageSelector");
$content->append($languageSelectorToggler);

// Return output
return $content->getReport();
//#section_end#
?>