<?php
//#section#[header]
// Module Declaration
$moduleID = 161;

// Inner Module Codes
$innerModules = array();

// Check Module Preloader Defined in RB Platform (prevent outside executions)
if (!defined("_MDL_PRELOADER_") && !defined("_RB_PLATFORM_"))
	throw new Exception("Module is not loaded properly!");

// Use Importer
use \API\Platform\importer;

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
importer::import("UI", "Presentation");
importer::import("UI", "Modules");
//#section_end#
//#section#[code]
use \API\Literals\moduleLiteral;
use \UI\Modules\MContent;
use \UI\Presentation\togglers\toggler;

// Create Module Page
$content = new MContent($moduleID);

// Build the content
$content->build("", "mySecuritySettings");

// Get a toggler
$toggler = new toggler();

// Password Manager
$title = moduleLiteral::get($moduleID, "lbl_passwordManager");
$passwordManagerToggler = getSettingsToggler($toggler, $title, $moduleID, "passwordManager");
$content->append($passwordManagerToggler);

// Managed Accounts
$title = moduleLiteral::get($moduleID, "lbl_managedAccounts");
$sessionManagerToggler = getSettingsToggler($toggler, $title, $moduleID, "accountManager", "managedAccounts");
$content->append($sessionManagerToggler);

// Account Keys
$title = moduleLiteral::get($moduleID, "lbl_accountKeys");
$sessionManagerToggler = getSettingsToggler($toggler, $title, $moduleID, "accountKeyManager", "accountKeys");
$content->append($sessionManagerToggler);

// Active Sessions
$title = moduleLiteral::get($moduleID, "lbl_sessionManager");
$sessionManagerToggler = getSettingsToggler($toggler, $title, $moduleID, "sessionManager", "securityAccountSessions");
$content->append($sessionManagerToggler);

// Return output
return $content->getReport();

// Get Settings Toggler Function
function getSettingsToggler($toggler, $title, $moduleID, $action, $moduleContainerID = "")
{
	// Toggler Header
	$header = DOM::create("div", "", "", "settingsItem");
	DOM::append($header, $title);
	
	// Toggler Body
	$body = MContent::getModuleContainer($moduleID, $action, array(), FALSE, $moduleContainerID);
	DOM::appendAttr($body, "class", "settingsContent");
	
	// Build Toggler Item
	$togglerItem = $toggler->build("", $header, $body, $open = FALSE)->get();
	DOM::appendAttr($togglerItem, "class", "settingsGroup");
	
	// Return item
	return $togglerItem;
}
//#section_end#
?>